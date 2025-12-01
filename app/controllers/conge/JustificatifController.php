<?php

namespace app\controllers\conge;

use Flight;
use app\models\conge\JustificationModel;

class JustificatifController {

    /**
     * Génère le HTML pour l'affichage d'un fichier selon son type
     */
    private function getFileDisplayHtml($file, $baseUploadUrl) {
        $displayType = Flight::Justification()->getDisplayType($file['extension']);
        $fullPath = $baseUploadUrl . $file['name'];
        
        switch ($displayType) {
            case 'image':
                return '
                <div class="file-preview">
                    <img src="' . htmlspecialchars($fullPath) . '" 
                         alt="' . htmlspecialchars($file['name']) . '"
                         onclick="openModal(this.src, this.alt)">
                </div>';
                
            case 'pdf':
                return '
                <div class="file-preview">
                    <iframe src="' . htmlspecialchars($fullPath) . '" 
                            title="' . htmlspecialchars($file['name']) . '"
                            style="width:100%; height:600px;">
                        <p>Votre navigateur ne supporte pas l\'affichage des PDF. 
                           <a href="' . htmlspecialchars($fullPath) . '" download>Télécharger le PDF</a>
                        </p>
                    </iframe>
                </div>';
                
            case 'word':
                // Utilisation de Google Docs Viewer
                $googleViewerUrl = 'https://docs.google.com/gview?url=' . urlencode($fullPath) . '&embedded=true';
                return '
                <div class="file-preview">
                    <iframe src="' . htmlspecialchars($googleViewerUrl) . '" 
                            style="width:100%; height:600px;" 
                            frameborder="0">
                        <p>Votre navigateur ne supporte pas l\'affichage des documents Word.</p>
                        <a href="' . htmlspecialchars($fullPath) . '" class="btn" download>Télécharger le document Word</a>
                    </iframe>
                </div>';
                
            case 'text':
                return '
                <div class="file-preview">
                    <iframe src="' . htmlspecialchars($fullPath) . '" 
                            style="width:100%; height:400px; background: white; border: 1px solid #ddd;">
                        <a href="' . htmlspecialchars($fullPath) . '" download>Télécharger le fichier texte</a>
                    </iframe>
                </div>';
                
            case 'excel':
            case 'powerpoint':
                return '
                <div class="preview-notice">
                    <p>Prévisualisation non disponible directement pour les fichiers ' . strtoupper($file['extension']) . '.</p>
                    <a href="' . htmlspecialchars($fullPath) . '" class="btn" download>Télécharger le fichier</a>
                </div>';
                
            default:
                return '
                <div class="preview-notice">
                    <p>Prévisualisation non disponible pour ce type de fichier (' . htmlspecialchars($file['extension']) . ').</p>
                    <a href="' . htmlspecialchars($fullPath) . '" class="btn" download>Télécharger le fichier</a>
                </div>';
        }
    }

    /**
     * Affiche le justificatif d'absence (support AJAX et normal)
     */
    public function viewJustificatif($idAbsence) {
        // Utiliser le modèle pour récupérer les données
        $data = Flight::Justification()->getJustificationById($idAbsence);

        // Vérifier s'il y a une erreur
        if (isset($data['error'])) {
            if ($this->isAjaxRequest()) {
                Flight::json(['error' => $data['error']], 404);
            } else {
                Flight::json(['error' => $data['error']], 404);
            }
            return;
        }

        // Construire l'URL de base pour les fichiers
        $baseUploadUrl = Flight::get('upload.justificatifs_absence');

        // Si c'est une requête AJAX, retourner le HTML complet
        if ($this->isAjaxRequest()) {
            $this->renderJustificatifForAjax($data, $baseUploadUrl);
        } else {
            // Afficher la vue appropriée selon le type de justificatif (comportement normal)
            switch ($data['type']) {
                case 'text':
                    $this->displayTextJustification($data, $baseUploadUrl);
                    break;
                case 'file':
                    $this->displayFileJustification($data, $baseUploadUrl);
                    break;
                case 'dossier':
                    $this->displayDossierJustification($data, $baseUploadUrl);
                    break;
                default:
                    Flight::json(['error' => 'Type de justificatif non supporté'], 400);
            }
        }
    }

    /**
     * Vérifie si la requête est une requête AJAX
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Rend le justificatif formaté pour les requêtes AJAX
     */
    private function renderJustificatifForAjax($data, $baseUploadUrl) {
        // Ajouter un bouton de fermeture pour l'affichage AJAX
        $closeButton = '';

        // Rendre la vue selon le type
        switch ($data['type']) {
            case 'text':
                ob_start();
                Flight::render('conge\justificatif_view', [
                    'absence' => $data['absence'],
                    'content' => $data['content'],
                    'type' => 'text',
                    'base_upload_url' => $baseUploadUrl,
                    'is_ajax' => true
                ]);
                $content = ob_get_clean();
                echo $closeButton . $content;
                break;
                
            case 'file':
                $filePath = $baseUploadUrl . $data['fileName'];
                $fileInfo = [
                    'fileName' => $data['fileName'],
                    'filePath' => $filePath,
                    'fileStoragePath' => $data['filePath'],
                    'mimeType' => $data['mimeType'],
                    'extension' => $data['extension'],
                    'displayHtml' => $this->getFileDisplayHtml([
                        'name' => $data['fileName'],
                        'extension' => $data['extension']
                    ], $baseUploadUrl)
                ];

                ob_start();
                Flight::render('conge\justificatif_view', [
                    'absence' => $data['absence'],
                    'fileInfo' => $fileInfo,
                    'type' => 'file',
                    'base_upload_url' => $baseUploadUrl,
                    'is_ajax' => true
                ]);
                $content = ob_get_clean();
                echo $closeButton . $content;
                break;
                
            case 'dossier':
                $filesWithPaths = [];
                foreach ($data['files'] as $file) {
                    $file['fullPath'] = $baseUploadUrl . $file['name'];
                    $file['displayHtml'] = $this->getFileDisplayHtml($file, $baseUploadUrl);
                    $filesWithPaths[] = $file;
                }

                ob_start();
                Flight::render('conge\justificatif_view', [
                    'absence' => $data['absence'],
                    'texte' => $data['texte'],
                    'files' => $filesWithPaths,
                    'type' => 'dossier',
                    'base_upload_url' => $baseUploadUrl,
                    'is_ajax' => true
                ]);
                $content = ob_get_clean();
                echo $closeButton . $content;
                break;
                
            default:
                echo '<div class="alert alert-danger">Type de justificatif non supporté</div>';
        }
    }

    /**
     * Affiche un justificatif texte
     */
    private function displayTextJustification($data, $baseUploadUrl) {
        Flight::render('conge\justificatif_view', [
            'absence' => $data['absence'],
            'content' => $data['content'],
            'type' => 'text',
            'base_upload_url' => $baseUploadUrl // Ajout de l'URL de base
        ]);
    }

    /**
     * Affiche un justificatif fichier unique
     */
    private function displayFileJustification($data, $baseUploadUrl) {
        // Construire le chemin complet du fichier
        $filePath = $baseUploadUrl . $data['fileName'];
        
        // Déterminer comment afficher le fichier selon son type
        $fileInfo = [
            'fileName' => $data['fileName'],
            'filePath' => $filePath,
            'fileStoragePath' => $data['filePath'],
            'mimeType' => $data['mimeType'],
            'extension' => $data['extension'],
            'displayHtml' => $this->getFileDisplayHtml([
                'name' => $data['fileName'],
                'extension' => $data['extension']
            ], $baseUploadUrl)
        ];

        Flight::render('conge\justificatif_view', [
            'absence' => $data['absence'],
            'fileInfo' => $fileInfo,
            'type' => 'file',
            'base_upload_url' => $baseUploadUrl
        ]);
    }
    
    /**
     * Affiche un justificatif dossier (multiple fichiers + texte)
     */
    private function displayDossierJustification($data, $baseUploadUrl) {
        // Construire les chemins complets pour tous les fichiers
        $filesWithPaths = [];
        foreach ($data['files'] as $file) {
            $file['fullPath'] = $baseUploadUrl . $file['name'];
            $file['displayHtml'] = $this->getFileDisplayHtml($file, $baseUploadUrl);
            $filesWithPaths[] = $file;
        }

        Flight::render('conge\justificatif_view', [
            'absence' => $data['absence'],
            'texte' => $data['texte'],
            'files' => $filesWithPaths,
            'type' => 'dossier',
            'base_upload_url' => $baseUploadUrl
        ]);
    }

    /**
     * Télécharge un fichier unique
     */
    private function downloadSingleFile($data) {
        // Utiliser le chemin de stockage original pour le téléchargement
        $filePath = $data['filePath'];
        
        if (!file_exists($filePath)) {
            Flight::json(['error' => 'Fichier non trouvé'], 404);
            return;
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }



    /**
     * Télécharge un fichier justificatif
     */
    public function downloadJustificatif($idAbsence) {
        // Utiliser le modèle pour récupérer les données du justificatif
        $data = Flight::Justification()->getJustificationById($idAbsence);

        // Vérifier s'il y a une erreur
        if (isset($data['error'])) {
            Flight::json(['error' => $data['error']], 404);
            return;
        }

        // Gestion du téléchargement selon le type
        switch ($data['type']) {
            case 'text':
                $this->downloadTextJustification($data);
                break;
            case 'file':
                $this->downloadSingleFile($data);
                break;
            case 'dossier':
                $this->downloadMultipleFiles($data);
                break;
            default:
                Flight::json(['error' => 'Type de justificatif non supporté'], 400);
        }
    }

    /**
     * Télécharge un justificatif texte
     */
    private function downloadTextJustification($data) {
        $content = $data['content'];
        $fileName = 'justificatif_absence_' . $data['absence']['id_abscence'] . '.txt';

        header('Content-Description: File Transfer');
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit;
    }

    /**
     * Télécharge plusieurs fichiers (création d'une archive ZIP)
     */
    private function downloadMultipleFiles($data) {
        $files = $data['files'];
        
        if (empty($files)) {
            Flight::json(['error' => 'Aucun fichier à télécharger'], 404);
            return;
        }

        // Créer une archive ZIP temporaire
        $zip = new \ZipArchive();
        $zipFileName = tempnam(sys_get_temp_dir(), 'justificatifs_') . '.zip';
        
        if ($zip->open($zipFileName, \ZipArchive::CREATE) !== TRUE) {
            Flight::json(['error' => 'Impossible de créer l\'archive'], 500);
            return;
        }

        // Ajouter chaque fichier à l'archive
        foreach ($files as $file) {
            if (file_exists($file['path'])) {
                $zip->addFile($file['path'], $file['name']);
            }
        }
        
        $zip->close();

        // Télécharger l'archive
        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="justificatifs_absence_' . $data['absence']['id_abscence'] . '.zip"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // Supprimer le fichier temporaire
        unlink($zipFileName);
        exit;
    }

    /**
     * Affiche un fichier image directement dans le navigateur
     */
    public function viewFile($idAbsence) {
        $data = Flight::Justification()->getJustificationById($idAbsence);

        if (isset($data['error'])) {
            Flight::json(['error' => $data['error']], 404);
            return;
        }

        if ($data['type'] !== 'file') {
            Flight::json(['error' => 'Ce justificatif n\'est pas un fichier unique'], 400);
            return;
        }

        $filePath = $data['filePath'];
        
        if (!file_exists($filePath)) {
            Flight::json(['error' => 'Fichier non trouvé'], 404);
            return;
        }

        // Déterminer le type MIME
        $mimeType = mime_content_type($filePath);
        
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * API pour récupérer les données du justificatif (sans affichage)
     */
    public function getJustificatifData($idAbsence) {
        $data = Flight::Justification()->getJustificationById($idAbsence);
        Flight::json($data);
    }
}