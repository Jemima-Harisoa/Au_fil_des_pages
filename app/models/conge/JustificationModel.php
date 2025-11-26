<?php

namespace app\models\conge;

class JustificationModel {
    // Modèle pour la gestion des justificatifs d'absence
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Méthode pour récupérer les informations d'une absence par son ID
    public function getJustificationById($idAbsence) {
        // Récupérer les informations de l'absence
        $sql = "
            SELECT 
                a.id_abscence,
                a.justificatif,
                a.est_autorise,
                p.nom,
                p.prenom,
                e.poste,
                a.debut,
                a.fin
            FROM abscence a
            LEFT JOIN employes e ON a.id_employe = e.id_employe
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            WHERE a.id_abscence = :id_abscence
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_abscence' => $idAbsence]);
        $absence = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$absence) {
            return ['error' => 'Absence non trouvée'];
        }

        if (empty($absence['justificatif'])) {
            return ['error' => 'Aucun justificatif disponible'];
        }

        $justificatifContent = $absence['justificatif'];
        $uploadDir = __DIR__ . '/../../../public/uploads/justificatifs_absence/';


        // Déterminer le type de justificatif
        if (empty($justificatifContent)) {
            return ['error' => 'Aucun justificatif disponible'];
        }

        // Cas 1: C'est un texte direct (ne commence pas par un chemin de fichier valide)
        if (!$this->isFilePath($justificatifContent)) {
            return [
                'absence' => $absence,
                'type' => 'text',
                'content' => $justificatifContent,
                'hasFiles' => false,
                'isMultiple' => false
            ];
        }

        $fullPath = $uploadDir . $justificatifContent;

        // Cas 2: C'est un dossier (fichiers multiples + texte)
        if (is_dir($fullPath)) {
            return $this->getDossierJustification($fullPath, $absence);
        }

        // Cas 3: Fichier unique
        if (!file_exists($fullPath)) {
            return ['error' => 'Fichier justificatif non trouvé'];
        }

        return $this->getFichierUnique($fullPath, $absence);
    }

    /**
     * Traite un dossier de justification (fichier + texte)
     */
    private function getDossierJustification($dossierPath, $absence) {
        $files = [];
        $texte = '';
        
        if (is_dir($dossierPath)) {
            $scan = scandir($dossierPath);
            foreach ($scan as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $dossierPath . '/' . $file;
                    $fileInfo = pathinfo($filePath);
                    
                    if (is_file($filePath)) {
                        // Vérifier si c'est le fichier de description
                        if ($file === 'description.txt') {
                            $texte = file_get_contents($filePath);
                        } else {
                            $files[] = [
                                'name' => $file,
                                'path' => $filePath,
                                'extension' => strtolower($fileInfo['extension'] ?? ''),
                                'mimeType' => $this->getMimeType($fileInfo['extension'] ?? '')
                            ];
                        }
                    }
                }
            }
        }
        
        return [
            'absence' => $absence,
            'type' => 'dossier',
            'files' => $files,
            'texte' => $texte,
            'isMultiple' => !empty($files),
            'hasFiles' => !empty($files) || !empty($texte)
        ];
    }

    /**
     * Traite un fichier unique
     */
    private function getFichierUnique($filePath, $absence) {
        $fileInfo = pathinfo($filePath);
        $extension = strtolower($fileInfo['extension'] ?? '');
        
        return [
            'absence' => $absence,
            'fileName' => $fileInfo['basename'],
            'filePath' => $filePath,
            'extension' => $extension,
            'mimeType' => $this->getMimeType($extension),
            'type' => 'file',
            'isMultiple' => false,
            'hasFiles' => true
        ];
    }
    /**
     * Détermine si le contenu est un chemin de fichier
     */
    private function isFilePath($content) {
        // Vérifier si c'est un chemin de fichier (contient une extension commune)
        $commonExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'txt', 'doc', 'docx', 'zip'];
        $pattern = '/\.(' . implode('|', $commonExtensions) . ')$/i';
        
        return preg_match($pattern, $content) || is_dir(__DIR__ . '/../../../public/uploads/justificatifs_absence/' . $content);
    }

    /**
     * Récupère les données des fichiers (unique ou multiple)
     */
    private function getFilesData($path, $absence) {
        // Si c'est un dossier, traiter les fichiers multiples
        if (is_dir($path)) {
            $files = [];
            $scan = scandir($path);
            
            foreach ($scan as $file) {
                if ($file !== '.' && $file !== '..' && is_file($path . '/' . $file)) {
                    $fileInfo = pathinfo($file);
                    $files[] = [
                        'name' => $file,
                        'path' => $path . '/' . $file,
                        'extension' => strtolower($fileInfo['extension'] ?? ''),
                        'mimeType' => $this->getMimeType($fileInfo['extension'] ?? '')
                    ];
                }
            }
            
            return [
                'absence' => $absence,
                'files' => $files,
                'type' => 'files',
                'isMultiple' => true,
                'hasFiles' => !empty($files)
            ];
        }
        
        // Cas d'un fichier unique
        $fileInfo = pathinfo($path);
        $extension = strtolower($fileInfo['extension'] ?? '');
        
        return [
            'absence' => $absence,
            'fileName' => $fileInfo['basename'],
            'filePath' => $path,
            'extension' => $extension,
            'mimeType' => $this->getMimeType($extension),
            'type' => 'file',
            'isMultiple' => false,
            'hasFiles' => true
        ];
    }
    
    /**
     * Détermine le type d'affichage pour un fichier
     */
    public function getDisplayType($extension) {
        $extension = strtolower($extension);
        
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $pdfTypes = ['pdf'];
        $wordTypes = ['doc', 'docx'];
        $textTypes = ['txt'];
        $excelTypes = ['xls', 'xlsx'];
        $powerpointTypes = ['ppt', 'pptx'];
        
        if (in_array($extension, $imageTypes)) {
            return 'image';
        } elseif (in_array($extension, $pdfTypes)) {
            return 'pdf';
        } elseif (in_array($extension, $wordTypes)) {
            return 'word';
        } elseif (in_array($extension, $textTypes)) {
            return 'text';
        } elseif (in_array($extension, $excelTypes)) {
            return 'excel';
        } elseif (in_array($extension, $powerpointTypes)) {
            return 'powerpoint';
        } else {
            return 'download';
        }
    }

    /**
     * Détermine le type MIME d'un fichier selon son extension
     */
    private function getMimeType($extension) {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}