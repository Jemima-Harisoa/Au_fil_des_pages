
$(document).ready(function() {
    console.log("Filtres envoyés :");
var table = $('#dataTable').DataTable({
    destroy: true, // permet de recréer la table
    processing: true,
    serverSide: true,
    ajax: {
        url: '/planning_entretien/filtre',
        type: 'POST',
        data: function (d) {
            return $.extend({}, d, {
                candidat: $('#candidat').val(),
                age_min: $('#age_min').val(),
                age_max: $('#age_max').val(),
                responsable: $('#responsable').val(),
                profil_candidat: $('#profil_candidat').val(),
                score_min: $('#score_min').val(),
                score_max: $('#score_max').val(),
                date_test: $('#date_test').val(),
                date_heure_entretien: $('#date_heure_entretien').val()
            });
        },
        dataSrc: function(json) {
        return json.data;
    }
    },
    columns: [
        { 
            data: null,
            render: function(row) {
                return row.nom_candidat + " " + (row.prenom_candidat ?? '');
            }
        },
        { 
            data: "date_naissance",
            render: function(d) {
                if (!d) return "";
                let birth = new Date(d);
                let ageDifMs = Date.now() - birth.getTime();
                let ageDate = new Date(ageDifMs);
                return Math.abs(ageDate.getUTCFullYear() - 1970);
            }
        },
        { 
            data: null,
            render: function(row) {
                return row.nom_responsable + " " + (row.prenom_responsable ?? '');
            }
        },
        { data: "profil" },
        { data: "score_test" },
        { data: "date_test" },
        { data: "date_heure_entretien" },
        {
            data: null,
            render: function(row) {
                return `<button class="btn btn-sm btn-primary">Voir</button>`;
            }
        }
    ]
});
    // Soumission du formulaire en AJAX
    $('#form-filtre').on('submit', function(e) {
        e.preventDefault(); // empêche le rechargement
        table.ajax.reload(); // recharge DataTable avec les filtres
    });

    // Bouton reset = réinitialiser les filtres et rafraîchir
    $('#form-filtre').on('reset', function() {
        setTimeout(function() { table.ajax.reload(); }, 100);
    });
});

