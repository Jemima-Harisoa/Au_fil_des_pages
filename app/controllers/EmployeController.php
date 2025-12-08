<?php 
namespace app\controllers;
use app\models\EmployeModel;

use Flight;

class EmployeController {
    public function redirectEmploye() {
        $data[0] = EmployeModel::listeEmployerV2();    
        $data[1] = EmployeModel::getAllDepartements(); 
        $data[2] = EmployeModel::getAllPoste();
        Flight::render('employe/employeList', ['data' => $data]);
    }   
    
    public function redirectEmployeDetails($id) {
        // === ENDPOINT AJAX pour l'arborescence ===
        if (isset($_GET['action']) && $_GET['action'] === 'arborescence' && isset($_GET['chemin'])) {
            header('Content-Type: application/json');
            $chemin = $_GET['chemin'];
            
            // Fonction récursive pour scanner
            function scanArborescenceAjax($dir, $baseWebPath = '') {
                $result = [];
                if (!is_dir($dir)) return $result;
                
                $items = @scandir($dir);
                if ($items === false) return $result;
                
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..' || $item === 'index.html') continue;
                    
                    $path = $dir . '/' . $item;
                    $webPath = $baseWebPath . '/' . rawurlencode($item);
                    
                    if (is_dir($path)) {
                        $result[] = [
                            'name' => $item,
                            'type' => 'dir',
                            'children' => scanArborescenceAjax($path, $webPath)
                        ];
                    } else {
                        $result[] = [
                            'name' => $item,
                            'type' => 'file',
                            'size' => @filesize($path) ?: 0,
                            'ext' => strtolower(pathinfo($item, PATHINFO_EXTENSION)),
                            'webPath' => $webPath
                        ];
                    }
                }
                return $result;
            }
            
            if (is_dir($chemin)) {
                // Calculer le chemin web relatif
                $projectPublic = dirname(__DIR__, 2) . '/public';
                $relativePath = str_replace($projectPublic, '', $chemin);
                
                $arborescence = scanArborescenceAjax($chemin, $relativePath);
                echo json_encode(['success' => true, 'arborescence' => $arborescence], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dossier introuvable: ' . $chemin]);
            }
            exit;
        }
        
        // Affichage normal de la page
        $historiqueMouvement = EmployeModel::getEmployerHistoriqueMouvement($id);
        $data = EmployeModel::getEmployesWithDetails($id);
        Flight::render('employe/employeDetails', ['data' => $data, 'historiqueMouvement' => $historiqueMouvement]);
    }   
}