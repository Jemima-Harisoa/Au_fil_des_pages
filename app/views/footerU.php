</div>
                <!-- /.container-fluid -->

            </div>

            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="/deconnexionU">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>
    
    <script>
        // Script existant pour autre fonctionnalité
        // (corrigé pour éviter l'erreur de syntaxe)
        /*
        .then(data => {
            if (data.success) {
                input.value = '';
                window.location.reload();
            } else {
                alert("Erreur lors de l'envoi du message.");
            }
        })
        */
    </script>

    <script>
        // Script pour la recherche dans le Message Center (Version Utilisateur)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INITIALISATION RECHERCHE MESSAGE CENTER (UTILISATEUR) ===');
            
            // Vérifier que tous les éléments nécessaires existent
            const toggleButton = document.getElementById('toggleMessageSearch');
            const searchInput = document.getElementById('messageCenterSearch');
            const titleSpan = document.getElementById('messageCenterTitle');
            const messagesDropdown = document.getElementById('messagesDropdown');

            console.log('Toggle button:', toggleButton);
            console.log('Search input:', searchInput);
            console.log('Title span:', titleSpan);
            console.log('Messages dropdown:', messagesDropdown);

            if (!toggleButton || !searchInput || !titleSpan) {
                console.error('Un ou plusieurs éléments manquent pour la recherche');
                return;
            }

            // Trouver le vrai conteneur du dropdown
            const dropdownContainer = document.querySelector('#messagesDropdown + .dropdown-menu') || 
                                    messagesDropdown.nextElementSibling ||
                                    messagesDropdown.parentElement.querySelector('.dropdown-menu');
            
            console.log('Dropdown container:', dropdownContainer);

            // Fonction pour trouver tous les messages
            function findMessageItems() {
                const container = dropdownContainer || messagesDropdown;
                console.log('Recherche des messages dans:', container);
                
                // Essayer différents sélecteurs pour les utilisateurs (/messagerieU/)
                const selectors = [
                    'a.dropdown-item[href*="/messagerieU/"]',
                    '.dropdown-item[href*="/messagerieU/"]',
                    'a[href*="/messagerieU/"]',
                    '.dropdown-item:not(.text-center):not(.no-results-message)'
                ];
                
                for (let selector of selectors) {
                    const items = container.querySelectorAll(selector);
                    console.log(`Sélecteur "${selector}" trouvé ${items.length} éléments`);
                    
                    // Log de debug pour voir tous les éléments trouvés
                    items.forEach((item, index) => {
                        console.log(`  Item ${index}:`, item.outerHTML.substring(0, 100) + '...');
                    });
                    
                    if (items.length > 0) {
                        // Filtrer pour ne garder que les vrais messages
                        const validItems = Array.from(items).filter(item => {
                            const href = item.getAttribute('href');
                            const hasValidHref = href && href.includes('/messagerieU/');
                            const hasContent = item.textContent && item.textContent.trim().length > 0;
                            const isNotSpecial = !item.classList.contains('text-center') && 
                                               !item.classList.contains('no-results-message');
                            
                            console.log(`    Validation - Href: ${hasValidHref}, Content: ${hasContent}, NotSpecial: ${isNotSpecial}`);
                            console.log(`    Texte: "${item.textContent?.trim().substring(0, 50)}..."`);
                            
                            return hasValidHref && hasContent && isNotSpecial;
                        });
                        
                        if (validItems.length > 0) {
                            console.log(`✅ Trouvé ${validItems.length} messages valides avec le sélecteur: ${selector}`);
                            return validItems;
                        }
                    }
                }
                
                // Si rien trouvé, essayer de lister TOUS les éléments dans le conteneur
                console.log('🔍 Debug: Listage de TOUS les éléments dans le conteneur:');
                const allElements = container.querySelectorAll('*');
                allElements.forEach((el, i) => {
                    if (el.tagName === 'A' || el.classList.contains('dropdown-item')) {
                        console.log(`  Élément ${i}: ${el.tagName} - Classes: ${el.className} - Href: ${el.href || 'N/A'}`);
                        console.log(`    HTML: ${el.outerHTML.substring(0, 100)}...`);
                    }
                });
                
                console.log('❌ Aucun message trouvé avec tous les sélecteurs');
                return [];
            }

            // Fonction de filtrage des messages
            function filterMessages(searchText) {
                console.log('=== FILTRAGE ===');
                console.log('Recherche pour:', searchText);
                
                const messageItems = findMessageItems();
                console.log('Messages trouvés:', messageItems.length);
                
                if (messageItems.length === 0) {
                    console.log('Aucun message à filtrer');
                    return;
                }
                
                searchText = searchText.toLowerCase().trim();
                let hasResults = false;
                
                messageItems.forEach((item, index) => {
                    try {
                        const fullText = item.textContent ? item.textContent.toLowerCase().trim() : '';
                        console.log(`Message ${index}: "${fullText}"`);
                        
                        const matches = searchText === '' || fullText.includes(searchText);
                        console.log(`Correspondance: ${matches}`);
                        
                        if (matches) {
                            if (item.style) {
                                item.style.setProperty('display', '', 'important');
                                item.style.setProperty('visibility', 'visible', 'important');
                            }
                            item.classList && item.classList.remove('hidden-by-search');
                            hasResults = true;
                        } else {
                            if (item.style) {
                                item.style.setProperty('display', 'none', 'important');
                                item.style.setProperty('visibility', 'hidden', 'important');
                            }
                            item.classList && item.classList.add('hidden-by-search');
                        }
                    } catch (error) {
                        console.error(`Erreur lors du filtrage du message ${index}:`, error);
                    }
                });
                
                console.log('Résultats trouvés:', hasResults);
                
                // Gérer le message "Aucun résultat"
                handleNoResultsMessage(searchText, hasResults);
            }

            // Fonction pour gérer le message "Aucun résultat"
            function handleNoResultsMessage(searchText, hasResults) {
                try {
                    const dropdownList = dropdownContainer || messagesDropdown.querySelector('.dropdown-list');
                    let noResultsElement = dropdownList.querySelector('.no-results-message');
                    
                    if (!noResultsElement) {
                        noResultsElement = document.createElement('div');
                        noResultsElement.className = 'dropdown-item text-center text-muted no-results-message';
                        noResultsElement.textContent = 'Aucun résultat trouvé';
                        dropdownList.appendChild(noResultsElement);
                    }
                    
                    if (searchText !== '' && !hasResults) {
                        noResultsElement.style.display = 'block';
                    } else {
                        noResultsElement.style.display = 'none';
                    }
                } catch (error) {
                    console.error('Erreur lors de la gestion du message "Aucun résultat":', error);
                }
            }

            // Event listeners
            // Déclencher la recherche quand on clique sur le toggle
            toggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                setTimeout(() => {
                    if (searchInput.classList.contains('d-none')) {
                        titleSpan.classList.add('d-none');
                        searchInput.classList.remove('d-none');
                        searchInput.focus();
                    } else {
                        titleSpan.classList.remove('d-none');
                        searchInput.classList.add('d-none');
                        searchInput.value = '';
                        filterMessages('');
                    }
                }, 100);
            });

            searchInput.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Recherche avec debounce
            let searchTimeout;
            searchInput.addEventListener('input', function(e) {
                console.log('Input changed:', this.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => filterMessages(this.value), 300);
            });

            // Empêcher fermeture du dropdown
            const dropdownList = dropdownContainer || messagesDropdown.querySelector('.dropdown-list');
            if (dropdownList) {
                dropdownList.addEventListener('click', function(e) {
                    if (!e.target.closest('a[href*="/messagerieU/"]')) {
                        e.stopPropagation();
                    }
                });
            }

            // Réinitialiser à la fermeture du dropdown
            const dropdownMenu = dropdownContainer || messagesDropdown.parentElement;
            if (dropdownMenu) {
                dropdownMenu.addEventListener('hidden.bs.dropdown', function () {
                    titleSpan.classList.remove('d-none');
                    searchInput.classList.add('d-none');
                    searchInput.value = '';
                    filterMessages('');
                });
            }

            // Support de la touche Échap
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    titleSpan.classList.remove('d-none');
                    this.classList.add('d-none');
                    this.value = '';
                    filterMessages('');
                }
            });

            console.log('=== SCRIPT INITIALISÉ ===');
        });
    </script>
<script>
        // === SCRIPT GLOBAL POUR ACTUALISATION NOTIFICATIONS MESSAGERIE ===
        
        // Variables globales pour la messagerie
        let messageNotificationInterval;
        let currentConversation = null;
        
        // Fonction pour mettre à jour le badge de notification
        function updateNotificationBadge(count) {
            console.log('Mise à jour badge, count:', count);
            
            // Chercher tous les badges possibles dans le header
            const badgeSelectors = [
                '#messagesDropdown .badge',
                '#messagesDropdown .badge-danger',
                '.messages-nav .badge',
                '.nav-link .badge',
                '.notification-badge',
                '[class*="badge"][class*="danger"]'
            ];
            
            let badgeFound = false;
            
            badgeSelectors.forEach(selector => {
                const badges = document.querySelectorAll(selector);
                badges.forEach(badge => {
                    // Vérifier que c'est bien le badge des messages
                    const parent = badge.closest('#messagesDropdown, .messages-nav, [id*="message"]');
                    if (parent) {
                        badgeFound = true;
                        if (count > 0) {
                            badge.textContent = count;
                            badge.style.display = 'inline-block';
                            badge.classList.remove('d-none');
                        } else {
                            badge.style.display = 'none';
                            badge.classList.add('d-none');
                        }
                    }
                });
            });
            
            // Si aucun badge trouvé, chercher dans le dropdown des messages
            if (!badgeFound) {
                const messagesDropdown = document.getElementById('messagesDropdown');
                if (messagesDropdown) {
                    let badge = messagesDropdown.querySelector('.badge');
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
            }
        }
        
        // Fonction pour vérifier les nouveaux messages
        function checkForNewMessages() {
            fetch('/messagerie/getCount', {
                method: 'GET',
                headers: {'Content-Type': 'application/json'}
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.count);
                }
            })
            .catch(err => {
                console.log('Erreur vérification messages:', err);
            });
        }
        
        // Fonction pour marquer une conversation comme lue
        function markConversationAsRead(id_candidat, id_annonce) {
            if (!id_candidat || !id_annonce) return;
            
            fetch(`/messagerie/markAsRead/${id_candidat}/${id_annonce}`, {
                method: 'GET',
                headers: {'Content-Type': 'application/json'}
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && typeof data.newCount !== 'undefined') {
                    updateNotificationBadge(data.newCount);
                }
            })
            .catch(err => {
                console.log('Erreur marquage lecture:', err);
            });
        }
        
        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INITIALISATION NOTIFICATIONS MESSAGERIE ===');
            
            // Vérifier immédiatement
            setTimeout(checkForNewMessages, 1000);
            
            // Puis toutes les 30 secondes
            messageNotificationInterval = setInterval(checkForNewMessages, 10000);
            
            // Détecter si on est sur une page de messagerie
            const currentUrl = window.location.pathname;
            const messagerieMatch = currentUrl.match(/\/messagerieU\/(\d+)\/(\d+)/);
            
            if (messagerieMatch) {
                currentConversation = {
                    id_candidat: messagerieMatch[1],
                    id_annonce: messagerieMatch[2]
                };
                
                console.log('Page messagerie détectée:', currentConversation);
                
                // Marquer comme lu dès l'ouverture
                setTimeout(() => {
                    markConversationAsRead(currentConversation.id_candidat, currentConversation.id_annonce);
                }, 500);
                
                // Vérifier plus fréquemment sur la page de messagerie
                clearInterval(messageNotificationInterval);
                messageNotificationInterval = setInterval(checkForNewMessages, 10000);
            }
            
            // Gérer les clics sur les liens de messagerie dans le dropdown
            const messageLinks = document.querySelectorAll('a[href*="/messagerieU/"]');
            messageLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    const match = href.match(/\/messagerieU\/(\d+)\/(\d+)/);
                    if (match) {
                        // Marquer comme lu avant de naviguer
                        setTimeout(() => {
                            markConversationAsRead(match[1], match[2]);
                        }, 100);
                    }
                });
            });
        });
        
        // Vérifier aussi quand la fenêtre retrouve le focus
        window.addEventListener('focus', function() {
            setTimeout(checkForNewMessages, 500);
        });
        
        // Nettoyer l'interval avant de quitter la page
        window.addEventListener('beforeunload', function() {
            if (messageNotificationInterval) {
                clearInterval(messageNotificationInterval);
            }
        });
        
        // Fonction pour actualiser les notifications depuis d'autres scripts (messagerie)
        window.refreshMessageNotifications = function() {
            checkForNewMessages();
        };
        
        // Fonction globale pour marquer comme lu (utilisable depuis d'autres pages)
        window.markMessageAsRead = function(id_candidat, id_annonce) {
            markConversationAsRead(id_candidat, id_annonce);
        };
        
        console.log('=== SCRIPT NOTIFICATIONS MESSAGERIE CHARGÉ ===');
    </script>
</body>

</html>