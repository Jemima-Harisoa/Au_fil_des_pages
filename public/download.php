<?php
// Sécurité : empêcher l'accès aux fichiers en dehors du dossier Documents
$basePath = $_SERVER['DOCUMENT_ROOT'] . '/Documents';
$requestedFile = isset($_GET['file']) ? $_GET['file'] : '';

// Nettoyer le chemin pour éviter les attaques de type directory traversal
$requestedFile = str_replace(['../', '..\\'], '', $requestedFile);
$filePath = $basePath . '/' . $requestedFile;

// Vérifier que le fichier existe et est dans le bon répertoire
if (!file_exists($filePath) || !is_file($filePath) || strpos(realpath($filePath), realpath($basePath)) !== 0) {
    http_response_code(404);
    die('Fichier non trouvé');
}

// Récupérer les informations du fichier
$fileName = basename($filePath);
$fileSize = filesize($filePath);
$mimeType = mime_content_type($filePath);

// Forcer le téléchargement avec les bons en-têtes
header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . $fileSize);

// Nettoyer le buffer de sortie
ob_clean();
flush();

// Lire et envoyer le fichier
readfile($filePath);
exit;
