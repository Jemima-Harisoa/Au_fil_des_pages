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
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALISATION RECHERCHE MESSAGE CENTER ===');
    
    const toggleButton = document.getElementById('toggleMessageSearch');
    const searchInput = document.getElementById('messageCenterSearch');
    const titleSpan = document.getElementById('messageCenterTitle');
    const messagesDropdown = document.getElementById('messagesDropdown');

    function filterMessages(searchText) {
        const dropdownItems = document.querySelectorAll('.dropdown-list .dropdown-item:not(.text-center)');
        searchText = searchText.toLowerCase().trim();
        
        let hasResults = false;
        
        dropdownItems.forEach(item => {
            const title = item.querySelector('.text-truncate').textContent.toLowerCase();
            const nameInfo = item.querySelector('.small.text-gray-500').textContent.toLowerCase();
            
            if (searchText === '' || title.includes(searchText) || nameInfo.includes(searchText)) {
                item.style.display = 'flex';
                hasResults = true;
            } else {
                item.style.display = 'none';
            }
        });

        // Handle "No results" message
        handleNoResultsMessage(searchText, hasResults);
    }

    function handleNoResultsMessage(searchText, hasResults) {
        const dropdownList = document.querySelector('.dropdown-list');
        let noResultsElement = dropdownList.querySelector('.no-results-message');
        
        if (!noResultsElement) {
            noResultsElement = document.createElement('div');
            noResultsElement.className = 'dropdown-item text-center text-muted no-results-message';
            noResultsElement.textContent = 'Aucun résultat trouvé';
            dropdownList.insertBefore(noResultsElement, dropdownList.querySelector('.dropdown-item.text-center.small'));
        }
        
        noResultsElement.style.display = (searchText !== '' && !hasResults) ? 'block' : 'none';
    }

    // Toggle search input
    toggleButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
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

    // Prevent dropdown from closing when clicking search
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Real-time search with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => filterMessages(this.value), 300);
    });

    // Prevent dropdown from closing when clicking inside
    document.querySelector('.dropdown-list').addEventListener('click', function(e) {
        if (!e.target.closest('a[href*="/messagerieU/"]')) {
            e.stopPropagation();
        }
    });

    // Reset search on dropdown close
    const dropdownMenu = document.querySelector('.dropdown-list').parentElement;
    dropdownMenu.addEventListener('hidden.bs.dropdown', function () {
        titleSpan.classList.remove('d-none');
        searchInput.classList.add('d-none');
        searchInput.value = '';
        filterMessages('');
    });

    // Handle Escape key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            titleSpan.classList.remove('d-none');
            this.classList.add('d-none');
            this.value = '';
            filterMessages('');
        }
    });
});
</script>

</body>

</html>