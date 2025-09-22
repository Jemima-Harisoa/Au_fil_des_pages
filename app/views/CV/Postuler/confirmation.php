<?php
// Garder les echo originaux
// echo "Note similarite: " . (isset($note_similarite) ? $note_similarite : 'N/A');
// echo " || Boolean validation: " . (isset($boolean_validation) ? ($boolean_validation ? 'true' : 'false') : 'N/A');
// echo " || Id annonce: " . (isset($idAnnonce) ? $idAnnonce : 'N/A');
// echo " || Id Candidat: " . (isset($idCandidat) ? $idCandidat : 'N/A');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmation CV</title>
  <!-- Inclure Tailwind CSS via CDN (pour développement, utilisez une build locale en production) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Animation légère pour les boutons */
    .btn {
      transition: transform 0.2s ease, background-color 0.3s ease;
    }
    .btn:hover {
      transform: scale(1.05);
    }
    /* Animation d'entrée pour le conteneur */
    .container {
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    /* Style pour la note de similarité en grand */
    .similarity-score {
      font-size: 3.5rem; /* Très grande taille pour mettre en évidence */
      font-weight: bold;
      color: #1f2937; /* Gris foncé pour contraste */
      animation: pulse 1.5s ease-in-out infinite; /* Animation subtile */
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full container">
    <!-- En-tête -->
    <h1 class="text-2xl font-bold text-green-600 mb-4 text-center">
      Confirmation de soumission
    </h1>

    <!-- Message principal -->
    <p class="text-gray-700 text-center mb-6">
      Merci d’avoir soumis votre CV.
    </p>

    <!-- Message conditionnel -->
    <?php if (isset($boolean_validation) && $boolean_validation) { ?>
      <form action="/testAccueil" method="GET" >
       <input type="hidden" name="idCdt" value="<?= htmlspecialchars($idCandidat) ?>">
      <input type="hidden" name="idAnn" value="<?= htmlspecialchars($idAnnonce) ?>">

      <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded" role="alert">
        <p class="text-green-700 font-semibold">
          Félicitations ! Votre CV correspond au profil recherché.
         
        </p>
        <p class="similarity-score text-center my-6">
          <?= isset($note_similarite) ? htmlspecialchars(number_format($note_similarite * 100, 2)) . '%' : 'N/A' ?>
        </p>
        <p class="text-green-600 text-center">
          Vous êtes un candidat idéal pour ce poste.
        </p>
        <div class="mt-6 flex justify-center space-x-4">
          <a href="/accueilU" class="btn bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600" aria-label="Reporter l'action">
            Plus tard
          </a>
          <input class="btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" aria-label="Passer le test" type="submit" value="Passer le test">

        </div>
      </div>
      </form>
    <?php } else { ?>
      <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded" role="alert">
        <p class="text-red-700 font-semibold">
          Malheureusement, votre CV ne correspond pas au profil recherché.
        </p>
        <div class="mt-6 flex justify-center">
          <a href="/accueilU" class="btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" aria-label="Revenir à l'accueil">
            Revenir à l'accueil
          </a>
        </div>
      </div>
    <?php } ?>
  </div>
</body>
</html>