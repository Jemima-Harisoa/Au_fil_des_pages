<?php include "headerA.php" ?>
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
    max-width: 70%;
    word-break: break-word;
}

.message-bot {
    background: #e9ecef;
    color: #212529;
    align-self: flex-start;
}

.message-me {
    background: #619ffc;
    color: #fff;
    align-self: flex-end;
}

.message-meta {
    font-size: 0.8rem;
    color: #888;
    margin-bottom: 6px;
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
    background: #fff;
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
    font-size: 1.2rem;
    cursor: pointer;
    transition: background 0.2s;
}

.send-btn:hover { background: #4176c2; }

.bot-header {
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:16px;
}
.bot-header img { width:60px; height:60px; object-fit:cover; border-radius:50%; }
</style>

<div class="container my-4">
    <div class="bot-header">
        <img src="/img/Chatbot.gif" alt="Bot">
        <div>
            <h4 class="mb-0">Chatbot</h4>
            <small class="text-muted">Assistance automatique</small>
        </div>
    </div>

    <div class="messagerie-container d-flex flex-column-reverse" id="messagerieScroll">
        <?php
        // $messages attendu : array de ['auteur'=>'Me'|'Bot', 'message'=>string, 'date'=>string]
        if (empty($messages)) {
            echo '<div class="text-center text-muted">Aucun message pour le moment. Démarrez la conversation.</div>';
        } else {
            foreach (array_reverse($messages) as $msg) {
                if (empty(trim($msg['message']))) continue;
                $isMe = ($msg['auteur'] === 'Me' || $msg['auteur'] === ($_SESSION['employe']['id_employe'] ?? 'Me'));
                $cls = $isMe ? 'message-me' : 'message-bot';
                echo '<div class="d-flex '.($isMe ? 'justify-content-end' : '').' mb-2">';
                echo '<div class="message-bubble '.$cls.'">';
                echo '<div class="message-meta">'.htmlspecialchars($msg['date'] ?? '').'</div>';
                echo nl2br(htmlspecialchars($msg['message']));
                echo '</div></div>';
            }
        }
        ?>
    </div>

    <div style="height:18px;"></div>

    <form id="sendMessageForm" class="send-box mt-2" autocomplete="off">
        <input type="text" name="message" id="messageInput" class="send-input" placeholder="Ecrire au bot..." required>
        <button type="submit" class="send-btn" title="Envoyer">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div>

<?php include "footer.php" ?>

<script>
const form = document.getElementById('sendMessageForm');
const input = document.getElementById('messageInput');
const messagerieScroll = document.getElementById('messagerieScroll');

function appendMessage(text, isMe, dateText) {
    const wrapper = document.createElement('div');
    wrapper.className = 'd-flex mb-2' + (isMe ? ' justify-content-end' : '');
    const bubble = document.createElement('div');
    bubble.className = 'message-bubble ' + (isMe ? 'message-me' : 'message-bot');
    const meta = document.createElement('div');
    meta.className = 'message-meta';
    meta.textContent = dateText || new Date().toLocaleString();
    const content = document.createElement('div');
    content.innerHTML = text.replace(/\n/g, '<br>');
    bubble.appendChild(meta);
    bubble.appendChild(content);
    wrapper.appendChild(bubble);
    messagerieScroll.prepend(wrapper);
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;
    
    const submitBtn = form.querySelector('button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    // Afficher le message de l'utilisateur immédiatement
    appendMessage(message, true, new Date().toLocaleString());
    input.value = '';

    console.log('Envoi message vers /messagerieBot/send:', message);

    // Envoyer au backend avec gestion d'erreur détaillée
    fetch('/messagerieBot/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => {
        console.log('Réponse status:', response.status);
        console.log('Réponse headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Données reçues:', data);
        
        if (data && data.success && data.botReply) {
            appendMessage(data.botReply, false, data.date || new Date().toLocaleString());
        } else {
            const errorMsg = data?.error || 'Erreur inconnue';
            appendMessage(`❌ ${errorMsg}`, false, new Date().toLocaleString());
        }
    })
    .catch(error => {
        console.error('Erreur complète:', error);
        appendMessage(`❌ Erreur réseau: ${error.message}`, false, new Date().toLocaleString());
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        input.focus();
    });
});

// Message de bienvenue au chargement
window.addEventListener('load', function() {
    input.focus();
    appendMessage('👋 Bonjour ! Je suis votre assistant RH. Posez-moi vos questions sur les congés, la paie, les horaires, les procédures...', false, new Date().toLocaleString());
});

// Envoyer avec Entrée
input.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit'));
    }
});
</script>