<?php include "headerU.php" ?>
<<<<<<< HEAD
<!-- Page Heading -->

    <h1 class="h3 mb-4 text-gray-800">Blank Page</h1>

<?php include "footer.php" ?>
=======

<div class="min-h-screen bg-gradient-to-b from-blue-100 to-gray-200 flex flex-col items-center justify-center p-6">
    <div class="bg-white shadow-2xl rounded-3xl p-10 max-w-lg w-full text-center">
        <!-- Rouage animé -->
        <div class="mb-6">
            <svg class="w-32 h-32 mx-auto text-blue-500 animate-spin-slow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="30%">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M6.05 17.95l-1.414 1.414M17.95 17.95l-1.414-1.414M6.05 6.05L4.636 7.464M12 8a4 4 0 100 8 4 4 0 000-8z"/>
            </svg>
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold text-blue-600 mb-4">Section en maintenance</h1>
        <p class="text-gray-600 mb-6">
            Cette section est actuellement en maintenance. Les statistiques et données seront bientôt disponibles. Merci pour votre patience !
        </p>

        <a href="/dashboard" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-full font-semibold shadow hover:bg-blue-700 transition">
            Retour au tableau de bord
        </a>
    </div>

    <div class="mt-10 text-gray-500 text-sm">
        &copy; <?= date('Y') ?> MonEntreprise. Tous droits réservés.
    </div>
</div>

<!-- Animation personnalisée -->
<style>
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin-slow {
  animation: spin-slow 4s linear infinite;
}
</style>

<?php include "footer.php" ?>
>>>>>>> origin/testCopie2
