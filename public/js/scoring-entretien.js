
// ======================
// 2️⃣ Gestion du popup
// ======================
let modelScoreEntretien = null; // variable globale pour stocker une seule instance

function openPopup(className) {
    const modalElement = document.getElementById("modalScoreEntretien");
    let id = $(className).data("id");
    // 🔹 Réutiliser la même instance si elle existe
    if (!modelScoreEntretien) {
        modelScoreEntretien = new bootstrap.Modal(modalElement, { backdrop: true });
    }


    modelScoreEntretien.show();

    // Nettoyage propre à la fermeture
    $(modalElement).off("hidden.bs.modal").on("hidden.bs.modal", function () {
        $("#form-historique")[0].reset();
        $("#score_entretien").removeClass("d-none");

        // 🔹 Sécurité : supprimer tout backdrop restant
        $(".modal-backdrop").remove();
        $("body").removeClass("modal-open").css("overflow", "");
    });

    // Boutons de fermeture
    $("#btn-annuler, .btn-close").off("click").on("click", function () {
        modelScoreEntretien.hide();
    });

    // Gestion de la soumission
    $("#form-historique").off("submit").on("submit", function (e) {
        e.preventDefault();

        const score = $("#score").val();

            if (!score) {
                alert("Veuillez remplir tous les champs.");
                return;
            }
        const data = {
            id_entretien: id,
            score_entretien:score
        };
        $.ajax({
            url: `/scoring-entretien/${id}`,
            type: "post",
            success: function () {
                alert("Planification créée avec succès");
                table.ajax.reload();
            },
            error: function () {
                alert("Erreur lors de la planification");
                $("#bouton-planification").prop("disabled", false);
            }
        });
        modelScoreEntretien.hide();
    });
}



// ======================
// 3️⃣ Initialisation DataTable
// ======================
var table = $("#dataTableScoreEntretien").DataTable({
    destroy: true,
    processing: true,
    serverSide: true,
    ordering:true,
    ajax: {
        url: "/scoring-entretien/liste",
        type: "POST",
        dataSrc:function(json){
            return json.data;
        }
    },
    columns: [
        { data: null, render: row => `${row.nom_candidat ?? ""} ${row.prenom_candidat ?? ""}` },
        { data: "profil"},
        { data: "score_test" },
        { data: "date_test" },
        { data: "date_heure_entretien" },
        {data:null,render:function(row){
            return `<button class="btn-note" data-id=${row.id_entretien}>Noter</button>`;
        }}
    ]
});

// ======================
// 4️⃣ Gestion des boutons
// ======================
// $("#btn-note").off("click").on("click", function () {
//     $(this).prop("disabled", true);
//     $data
//     $.ajax({
//         url: "",
//         type: "GET",
//         success: function () {
//             alert("Planification créée avec succès");
//             table.ajax.reload();
//         },
//         error: function () {
//             alert("Erreur lors de la planification");
//             $("#bouton-planification").prop("disabled", false);
//         }
//     });
// });

$(document).on("click", ".btn-note", function() {
    openPopup(".btn-note");
});