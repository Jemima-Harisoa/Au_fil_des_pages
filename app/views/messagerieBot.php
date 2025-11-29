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
.bot-header img { width:56px; height:56px; object-fit:cover; border-radius:50%; }
</style>

<div class="container my-4">
    <div class="bot-header">
<img src="https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExdmphMGhpdnNwczE1NDI1cTRxMWhhMTl5bWM1MWZqOXQwbHFvazd0ZCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/uWvnp79Pi5fE4/giphy.gif" alt="Bot animé">

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
    // insert at top since container is flex-column-reverse
    messagerieScroll.prepend(wrapper);
}

form.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;
    form.querySelector('button').disabled = true;

    // Append locally
    appendMessage(message, true, new Date().toLocaleString());
    input.value = '';

    // Send to backend bot endpoint (create /messagerieBot/send server-side)
    fetch('/messagerieBot/send', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ message: message })
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.success) {
            // backend may return botReply
            const reply = data.botReply || 'Réponse du bot';
            appendMessage(reply, false, data.date || new Date().toLocaleString());
        } else {
            appendMessage('Erreur: impossible de joindre le bot.', false, new Date().toLocaleString());
        }
    })
    .catch(() => {
        appendMessage('Erreur réseau.', false, new Date().toLocaleString());
    })
    .finally(() => {
        form.querySelector('button').disabled = false;
    });
});

window.addEventListener('load', function() {
    // Scroll to bottom: container is reversed so top is newest; keep focus on input
    input.focus();
});
</script>