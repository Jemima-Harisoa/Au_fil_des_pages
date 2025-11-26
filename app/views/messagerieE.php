<?php
$idEmploye = $_SESSION['employe']['id_employe'] ?? null;
$partenaireId = $partenaire_id ?? null;
$partenaireNom = $partenaire_nom ?? 'Employé';
$partenairePrenom = $partenaire_prenom ?? '';
$messages = $messages ?? [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Conversation - <?= htmlspecialchars($partenairePrenom . ' ' . $partenaireNom) ?></title>
    <link href="/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php require_once __DIR__ . '/headerE.php'; ?>
    
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    Conversation avec <?= htmlspecialchars($partenairePrenom . ' ' . $partenaireNom) ?>
                </h6>
                <a href="/accueilE" class="btn btn-secondary btn-sm">Retour</a>
            </div>
            
            <div class="card-body" style="height: 500px; overflow-y: auto;" id="messageContainer">
                <?php if (empty($messages)): ?>
                    <p class="text-center text-muted">Aucun message pour le moment. Commencez la conversation !</p>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <?php 
                        $isMe = (strpos($msg['auteur'], 'Employe' . $idEmploye) !== false);
                        $alignClass = $isMe ? 'text-right' : 'text-left';
                        $bgClass = $isMe ? 'bg-primary text-white' : 'bg-light';
                        ?>
                        <div class="mb-3 <?= $alignClass ?>">
                            <div class="d-inline-block p-3 rounded <?= $bgClass ?>" style="max-width: 70%;">
                                <div><?= $msg['message'] ?></div>
                                <small class="<?= $isMe ? 'text-white-50' : 'text-muted' ?>">
                                    <?= htmlspecialchars($msg['date']) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="card-footer">
                <form id="sendMessageForm" class="d-flex">
                    <input type="hidden" name="id_employe" value="<?= $idEmploye ?>">
                    <input type="hidden" name="partenaire_id" value="<?= $partenaireId ?>">
                    <textarea name="message" 
                              class="form-control mr-2" 
                              rows="2" 
                              placeholder="Votre message..." 
                              required></textarea>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // Scroller vers le bas au chargement
        const container = $('#messageContainer');
        container.scrollTop(container[0].scrollHeight);
        
        // Envoi du message
        $('#sendMessageForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = $(this).serialize();
            
            $.post('/messagerieE/send', formData, function(response) {
                if (response.success) {
                    // Recharger la page pour afficher le nouveau message
                    location.reload();
                } else {
                    alert('Erreur lors de l\'envoi du message');
                }
            }, 'json');
        });
    });
    </script>
</body>
</html>