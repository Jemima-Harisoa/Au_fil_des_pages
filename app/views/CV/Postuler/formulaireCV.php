<?php
  session_start();
  $idUser = $_SESSION['idUser'];
  $idAnnonce = $idAnnonce;
  echo "Utilisateur: " . $idUser . "Annonce: " . $idAnnonce;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h1>Formulaire CV</h1>
    <form action="/<?=$idUser?>/Annonce/<?=$idAnnonce?>/fillCV/postulationCV" method="post" enctype="multipart/form-data">

      <!-- Peronne - debut -->
        <input type="hidden" name="MAX_FILE_SIZE">            
        <input type="file" name="photo_identite" id="id_photo_identite">
        
        <input type="text" name="nom" id="" placeholder="Nom">
        <input type="text" name="prenoms" id="" placeholder="Prenoms">
        <input type="date" name="date" id="">
        <input type="text" name="contact" id="" placeholder="Contact">
      <!-- Personne - fin -->

      <!-- Profil - debut -->
      
      <!-- Profil - fin -->
      
      <input type="submit" value="Postuler">

    </form>
</body>
</html>

<!-- -- PAGE 1 
  CREATE TABLE `Personnes` (
  `id_personne` int PRIMARY KEY AUTO_INCREMENT,
  `nom` varchar(255),
  `prenom` varchar(255),
  `date_naissance` date,
  `contact` varchar(255) UNIQUE,
  `lien_image` varchar(255)
);

CREATE TABLE `Candidats` (
  `id_candidat` int PRIMARY KEY AUTO_INCREMENT,
  `id_personne` int,
  `id_annonce` int,
  `cv_url` varchar(255),
  `poste` varchar(255)
); -->