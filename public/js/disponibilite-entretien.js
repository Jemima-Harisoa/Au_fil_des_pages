$(document).ready(function () {
    const idAdmin = $('#id_admin').val(); // récupéré depuis une variable cachée ou session
    let table;

    // 🔹 1. INITIALISATION DATATABLE
    table = $('#tableDisponibilite').DataTable({
        ordering:true,
        processing:true,
        serverSide:true,
        ajax: {
            url: `/disponibilite-entretien/liste`,
            dataSrc: function (json) {
                // Ne garder que les lignes valides
                return json.data.filter(row => row.est_valide === true);
            }
        },
        columns: [
            { data: 'jour' },
            { data: 'heure_debut' },
            { data: 'heure_fin' },
            {
                data: 'id_dispo',
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-warning btn-sm btn-modifier" data-id="${data}">Modifier</button>
                        <button class="btn btn-danger btn-sm btn-supprimer" data-id="${data}">Supprimer</button>
                    `;
                }
            }
        ]
    });

    // 🔹 2. INSERTION (nouvelle disponibilité)
    $('#btn-ajouter').on('click', function () {
        $('#formDispo')[0].reset();
        $('#modalDispoLabel').text('Nouvelle disponibilité');
        $('#id_dispo').val('');
        $('#modalDispo').modal('show');
    });

    $('#formDispo').on('submit', function (e) {
        e.preventDefault();

        const id_dispo = $('#id_dispo').val();
        const jour = $('#jour').val();
        const heure_debut = $('#heure_debut').val();
        const heure_fin = $('#heure_fin').val();

        const url = id_dispo ? '/disponibilite-entretien/modification' : '/disponibilite-entretien/insertion';
        
        $.ajax({
            url: url,
            method: 'POST',
            dataType: 'json',
            contentType:'application/json',
            data:JSON.stringify({
                id_dispo: id_dispo,
                jour: jour,
                heure_debut: heure_debut,
                heure_fin: heure_fin,
                est_valide:true
            }),
            success: function (response) {
                if (response.message === "success") {
                    const msg = id_dispo ? 
                        `Disponibilité n°${id_dispo} mise à jour.` :
                        "Nouvelle disponibilité insérée.";
                    alert(msg);
                    $('#modalDispo').modal('hide');
                    table.ajax.reload();
                } else {
                    alert("Erreur lors de l’enregistrement : " + response.error);
                }
            },
            error: function (xhr, status, error) {
                alert("Erreur AJAX : " + error);
            }
        });
    });

    // 🔹 3. MODIFICATION
    $('#tableDisponibilite').on('click', '.btn-modifier', function () {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id_dispo == id);
        if (row) {
            $('#id_dispo').val(row.id_dispo);
            $('#jour').val(row.jour);
            $('#heure_debut').val(row.heure_debut);
            $('#heure_fin').val(row.heure_fin);
            $('#modalDispoLabel').text(`Modifier disponibilité #${id}`);
            $('#modalDispo').modal('show');
        }
    });

    // 🔹 4. SUPPRESSION
    $('#tableDisponibilite').on('click', '.btn-supprimer', function () {
        const id = $(this).data('id');
        if (confirm(`Voulez-vous vraiment supprimer la disponibilité n°${id} ?`)) {
            $.ajax({
                url: '/disponibilite-entretien/suppression',
                method: 'POST',
                dataType: 'json',
                contentType:'application/json',
                data: JSON.stringify({ id_dispo: id }),
                success: function (response) {
                    if (response.message === "success") {
                        alert(`Disponibilité n°${id} supprimée.`);
                        table.ajax.reload();
                    } else {
                        alert("Erreur lors de la suppression : " + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    alert("Erreur AJAX : " + error+status);
                }
            });
        }
    });

});


