
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; white-space: pre-wrap; }
        .files { margin-top: 20px; }
        .file-item { margin: 10px 0; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 3px; }
        .file-preview { 
            max-width: 100%; 
            margin: 10px 0; 
            border: 1px solid #ddd;
            background: #fff;
        }
        .file-preview iframe, 
        .file-preview embed {
            width: 100%;
            height: 500px;
            border: none;
        }
        .file-preview img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            cursor: zoom-in;
        }
        .preview-notice {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .actions { margin-top: 20px; }
        .btn { display: inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 3px; margin-right: 10px; }
        .btn:hover { background: #0056b3; }
        
        /* Modal pour l'agrandissement d'images */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>

<div class="container">
        <div class="header">
            <p><strong>Employé:</strong> <?= htmlspecialchars($absence['prenom'] . ' ' . $absence['nom']) ?></p>
            <p><strong>Poste:</strong> <?= htmlspecialchars($absence['poste']) ?></p>
            <p><strong>Période:</strong> <?= htmlspecialchars($absence['debut'] . ' au ' . $absence['fin']) ?></p>
            <p><strong>Statut:</strong> <?= $absence['est_autorise'] ? 'Autorisé' : 'Non autorisé' ?></p>
        </div>

        <?php if ($type === 'text' && isset($content)): ?>
            <div class="content">
                <h3>Justificatif texte:</h3>
                <?= nl2br(htmlspecialchars($content)) ?>
            </div>
        <?php endif; ?>

        <!-- Section dossier -->
        <?php if ($type === 'dossier'): ?>
            <?php if (isset($texte) && !empty($texte)): ?>
                <div class="content">
                    <h3>Description:</h3>
                    <?= nl2br(htmlspecialchars($texte)) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($files) && !empty($files)): ?>
                <div class="files">
                    <h3>Fichiers joints (<?= count($files) ?>):</h3>
                    <?php foreach ($files as $file): ?>
                        <div class="file-item">
                            <strong>Fichier:</strong> <?= htmlspecialchars($file['name']) ?>
                            <br><small>Type: <?= htmlspecialchars($file['mimeType']) ?></small>
                            <?= $file['displayHtml'] ?? '' ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Section fichier unique -->
        <?php if ($type === 'file' && isset($fileInfo)): ?>
            <div class="files">
                <h3>Fichier joint:</h3>
                <div class="file-item">
                    <strong>Fichier:</strong> <?= htmlspecialchars($fileInfo['fileName']) ?>
                    <br><small>Type: <?= htmlspecialchars($fileInfo['mimeType']) ?></small>
                    <?= $fileInfo['displayHtml'] ?? '' ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="/justificatif/download/<?= $absence['id_abscence'] ?>" class="btn">Télécharger le justificatif</a>
        </div>
    </div>

    <!-- Modal pour l'agrandissement d'images -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        // Gestion du modal pour l'agrandissement des images
        function openModal(src, alt) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'block';
            modalImg.src = src;
            modalImg.alt = alt;
        }

        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('imageModal').style.display = 'none';
        });

        // Fermer le modal en cliquant en dehors de l'image
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Ajuster la hauteur des iframes sur mobile
        document.addEventListener('DOMContentLoaded', function() {
            const iframes = document.querySelectorAll('iframe');
            iframes.forEach(iframe => {
                if (window.innerWidth < 768) {
                    iframe.style.height = '400px';
                }
            });
        });
    </script>
