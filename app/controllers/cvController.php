<?php
namespace app\controllers;
use app\models\AdminModel;
use app\models\CVModel;


use Flight;

class cvController {
    public function home() {
        Flight::render('CV/temp');
    }
    
    public function redirectCV() {
        Flight::render('CV/redirectCV');
    }

    public function fillCV($idUser, $idAnnonce){
        Flight::render('CV/Postuler/formulaireCV', ['idAnnonce' => $idAnnonce]);
    }


    public function printDataCV() {
        $requete = Flight::request();
    
        // Affichage des informations du formulaire
        echo "<h3>Données personnelles :</h3>";
        echo "Nom : " . htmlspecialchars($requete->data->nom) . "<br>";
        echo "Prénoms : " . htmlspecialchars($requete->data->prenoms) . "<br>";
        echo "Date de naissance : " . htmlspecialchars($requete->data->date) . "<br>";
        echo "Contact : " . htmlspecialchars($requete->data->contact) . "<br>";
    
        echo "<hr>";
    
        // Affichage des informations du fichier uploadé
        if (isset($_FILES['photo_identite'])) {
            $file = $_FILES['photo_identite'];
            $allowedExtensions = ['.png', '.gif', '.jpg', '.jpeg'];
            $dossier = 'img/PHOTO_CV/';
    
            echo "<h3>Photo d'identité :</h3>";
            echo "Nom du fichier : " . htmlspecialchars(basename($file['name'])) . "<br>";
            echo "Taille maximale autorisée : 2 000 000 octets<br>";
            echo "Taille réelle du fichier : " . filesize($file['tmp_name']) . " octets<br>";
            echo "Extensions autorisées : " . implode(', ', $allowedExtensions) . "<br>";
            echo "Extension du fichier : " . strrchr($file['name'], '.') . "<br>";
            echo "Dossier de destination : " . $dossier . "<br>";

            echo "Chemin final du fichier : " . $dossier . htmlspecialchars(basename($file['name'])) . "<br>";
        } else {
            echo "Aucun fichier uploadé.<br>";
        }
    }
    
    public function getDataCV(){
        $requete = Flight::request();
    
        $Nom = $requete->data->nom;
        $Prenoms = $requete->data->prenoms;
        $Date = $requete->data->date;
        $Contact = $requete->data->contact;
                                                                                    
        // Gestion de l'upload de l'image
        $file = $requete->files->photo_identite;
        $dossier = 'img/PHOTO_CV/';
        $fichier = basename($file['name']);
        $extension = strtolower(strrchr($fichier, '.'));
        $taille = $file['size'];
        $tmp_name = $file['tmp_name'];
    
        $erreur = null;
    
        // Vérifications divisées en petites méthodes
        if (!$this->verifExtension($extension)) {
            $erreur = 'Vous devez uploader un fichier de type png, gif, jpg ou jpeg';
        } elseif (!$this->verifTailleIMG($taille)) {
            $erreur = 'Le fichier est trop gros...';
        }
    
        if (!isset($erreur)) {
            // Sanitize le nom du fichier
            $fichier = $this->sanitizeFilename($fichier);
    
            // Ajouter un timestamp avant l'extension
            $timestamp = time();
            $fichier_sans_ext = substr($fichier, 0, strrpos($fichier, '.'));
            $fichier = $fichier_sans_ext . '_' . $timestamp . $extension;
    
            // Upload si tout est OK
            if (move_uploaded_file($tmp_name, $dossier . $fichier)) {
                // Ajout du chemin de la photo dans les données
                $photo_path = $dossier . $fichier;
                $data = [$Nom, $Prenoms, $Date, $Contact, $photo_path];
                
                CVModel::insertCV($data);
                Flight::render('CV/Postuler/confirmation');
            } else {
                Flight::render('CV/Postuler/erreur');
            }
        } else {
            echo $erreur;
        }
    }
    
    private function verifExtension($extension){
        $extensions_autorisees = array('.png', '.gif', '.jpg', '.jpeg');
        return in_array($extension, $extensions_autorisees);
    }
    
    private function verifTailleIMG($taille){
        $taille_maxi = 2000000; // 100 Ko
        return $taille <= $taille_maxi;
    }
    
    private function sanitizeFilename($fichier){
        $fichier = strtr($fichier,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
        return $fichier;
    }
}

?>