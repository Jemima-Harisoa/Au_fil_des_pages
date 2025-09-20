
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
                    <a class="btn btn-primary" href="/deconnexionA">Logout</a>
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
        .then(data => {
    if (data.success) {
        input.value = '';
        window.location.reload(); // <-- recharge la page pour afficher la session à jour
    } else {
        alert("Erreur lors de l'envoi du message.");
    }
})
    </script>
    <script>
document.getElementById('toggleSearch').addEventListener('click', function() {
    const container = document.getElementById('searchContainer');
    container.style.display = (container.style.display === 'none' || container.style.display === '') ? 'block' : 'none';
    document.getElementById('searchMessages').focus();
});

document.getElementById('searchMessages').addEventListener('keyup', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('.message-item').forEach(item => {
        const text = item.innerText.toLowerCase();
        item.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>
<script>
// Empêcher la fermeture du dropdown sur l'icône et le champ de recherche
document.getElementById('toggleMessageSearch').addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    const searchInput = document.getElementById('messageCenterSearch');
    const titleSpan = document.getElementById('messageCenterTitle');
    
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
});

document.getElementById('messageCenterSearch').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Fonction de filtrage des messages
function filterMessages(searchText) {
    const dropdownItems = document.querySelectorAll('.dropdown-item:not(.text-center)');
    searchText = searchText.toLowerCase().trim();
    
    let hasResults = false;
    
    dropdownItems.forEach(item => {
        const title = item.querySelector('.text-truncate').textContent.toLowerCase();
        const nameInfo = item.querySelector('.small.text-gray-500').textContent.toLowerCase();
        
        if (searchText === '' || title.includes(searchText) || nameInfo.includes(searchText)) {
            item.style.display = '';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
        // On cache l'élément si aucun critère ne correspond
        if (searchText === '' || title.includes(searchText) || nameInfo.includes(searchText)) {
            item.style.display = 'flex'; // Garde l'alignement original
            hasResults = true;
        } else {
            item.style.display = 'none'; // Cache complètement l'élément
        }
    });
    
    // Gestion du message "Aucun résultat"
    const noResults = document.querySelector('.dropdown-item.text-center.text-muted');
    if (noResults) {
        if (searchText !== '' && !hasResults) {
            noResults.style.display = 'block';
            noResults.textContent = 'Aucun résultat';
        } else {
            noResults.style.display = 'none';
        }
    }
}

// Recherche en temps réel avec debounce
let searchTimeout;
document.getElementById('messageCenterSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => filterMessages(this.value), 300);
});

// Empêcher la fermeture du dropdown lors de la recherche
document.querySelector('.dropdown-list').addEventListener('click', function(e) {
    e.stopPropagation();
});
</script>
</body>

</html>