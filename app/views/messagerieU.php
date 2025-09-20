<?php include "headerU.php" ?>
<style>
.messagerie-container {
    background: #f4f8fb;
    border-radius: 12px;
    box-shadow: 0 2px 8px #619ffc22;
    padding: 24px;
    max-height: 500px;
    min-height: 350px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}
.message-bubble {
    border-radius: 18px;
    padding: 12px 18px;
    margin-bottom: 8px;
    display: inline-block;
    position: relative;
    font-size: 1rem;
    max-width: 48%;
    word-break: break-word;

}
.message-admin {
    background: #e3eafc;
    color: #2c3e50;
    align-self: flex-start;
}
.message-user {
    background: #619ffc;
    color: #fff;
    align-self: flex-end;
}
.message-meta {
    font-size: 0.8rem;
    color: #888;
    margin-bottom: 2px;
}
.send-box {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 18px;
    margin-bottom: 10px;
}
.send-input {
    flex: 1;
    border-radius: 20px;
    border: 1px solid #619ffc88;
    padding: 10px 16px;
    font-size: 1rem;
    background: #f8fbff;
}
.send-btn {
    background: #619ffc;
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.3rem;
    cursor: pointer;
    transition: background 0.2s;
}
.send-btn:hover {
    background: #4176c2;
}
</style>
<div class="container my-4">
    <h4 class="mb-3">
        <img src="/img/undraw_profile_1.svg" width="32" style="margin-bottom:6px;">
        <?= $titre ?>
    </h4>
    <div class="messagerie-container d-flex flex-column-reverse" id="messagerieScroll">
        <?php foreach (array_reverse($messages) as $msg): ?>
            <?php if (empty(trim($msg['message']))) continue; // Ne pas afficher les messages vides ?>
            <?php if ($msg['auteur'] === 'Admin'): ?>
                <div class="d-flex mb-2">
                    <div class="message-bubble message-admin">
                        <div class="message-meta">
                            <img src="/img/undraw_profile_1.svg" width="22" style="margin-right:4px;">
                             <?= htmlspecialchars($msg['date']) ?>
                        </div>
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-end mb-2">
                    <div class="message-bubble message-user">
                        <div class="message-meta text-end">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="22" style="margin-right:4px;">
                             <?= htmlspecialchars($msg['date']) ?>
                        </div>
                        <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div style="height:30px;"></div>
    <form id="sendMessageForm" class="send-box mt-2" autocomplete="off">
        <input type="text" name="message" id="messageInput" class="send-input" placeholder="Votre message..." required>
        <button type="submit" class="send-btn" title="Envoyer">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>
<script src="https://kit.fontawesome.com/2c36e9b7b1.js" crossorigin="anonymous"></script>
<script>
const form = document.getElementById('sendMessageForm');
const input = document.getElementById('messageInput');
const messagerieScroll = document.getElementById('messagerieScroll');

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    form.querySelector('button').disabled = true;

    fetch('/messagerieU/send', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            id_candidat: <?= json_encode($id_candidat) ?>,
            id_annonce: <?= json_encode($id_annonce) ?>,
            message: message
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            window.location.reload();
        } else {
            alert("Erreur lors de l'envoi du message.");
        }
    })
    .catch(() => alert("Erreur réseau."))
    .finally(() => form.querySelector('button').disabled = false);
});

window.onload = function() {
    messagerieScroll.scrollTop = messagerieScroll.scrollHeight;
};
</script>
<?php include "footerU.php" ?>