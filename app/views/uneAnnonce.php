<?php foreach ($Annonces as $a): ?>
  <div class="annonce">
      <h3><?= htmlspecialchars($a['titre']) ?></h3>
      <p><?= htmlspecialchars($a['contenu'] ?? '') ?></p>
      <small>📅 <?= htmlspecialchars($a['date_publication']) ?></small>
  </div>
<?php endforeach; ?>
