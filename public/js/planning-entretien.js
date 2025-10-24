// ======================
// 1️⃣ Fonctions utilitaires
// ======================
function buildData(type_action, id_entretien) {
    return {
        type_action: type_action,
        id_entretien: id_entretien,
        date_heure_entretien: null,
        raison_modification: null
    };
}

function AjaxModificationPlanning(data, nouvel_etat) {
    fetch("/api/planning-entretien/modification", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.message === "success") {
            alert(`Planning entretien numéro ${data.id_entretien} est ${nouvel_etat}`);
            setTimeout(() => table.ajax.reload(null, false), 2000);
        } else {
            throw new Error(result.message);
        }
    })
    .catch(error => {
        alert(`Erreur lors de la modification du planning entretien numéro ${data.id_entretien} : ${error.message}`);
    });
}

// ======================
// 2️⃣ Gestion du popup
// ======================
function openPopup(type_action, nouvel_etat, isRefus) {
    const modal = new bootstrap.Modal(document.getElementById("modalEntretien"));

    // Masquer ou afficher la date
    if (isRefus) {
        $("#date_heure_modification").addClass("d-none").prop("required", false);
        $("#label-date").addClass("d-none");
    } else {
        $("#date_heure_modification").removeClass("d-none").prop("required", true);
        $("#label-date").removeClass("d-none");
    }

    modal.show();

    // Nettoyage à la fermeture
    $("#btn-annuler, .btn-close").off("click").on("click", function () {
        modal.hide();
        $("#form-historique")[0].reset();
        $("#date_heure_modification").removeClass("d-none");
    });

    // Gestion de la soumission
    $("#form-historique").off("submit").on("submit", function (e) {
        e.preventDefault();

        const id = $("#id_entretien").val();
        const raison = $("#raison_modification").val();
        let date = null;

        if (!isRefus) {
            date = $("#date_heure_modification").val();
            if (!date || !raison) {
                alert("Veuillez remplir tous les champs.");
                return;
            }
        } else if (!raison) {
            alert("Veuillez remplir la raison de modification.");
            return;
        }

        const data = {
            type_action: type_action,
            id_entretien: id,
            date_heure_entretien: date,
            raison_modification: raison
        };

        AjaxModificationPlanning(data, nouvel_etat);
        modal.hide();
        $("#form-historique")[0].reset();
    });
}

// ======================
// 3️⃣ Initialisation DataTable
// ======================
var table = $("#dataTable").DataTable({
    destroy: true,
    processing: true,
    serverSide: true,
    ajax: {
        url: "/planning_entretien/filtre",
        type: "POST",
        data: function (d) {
            return $.extend({}, d, {
                candidat: $("#candidat").val(),
                age_min: $("#age_min").val(),
                age_max: $("#age_max").val(),
                responsable: $("#responsable").val(),
                profil_candidat: $("#profil_candidat").val(),
                score_min: $("#score_min").val(),
                score_max: $("#score_max").val(),
                date_test: $("#date_test").val()
            });
        },
        dataSrc: function (json) {
            if (json.data.length === 0) {
                $("#bouton-planification").prop("disabled", false).show();
            } else {
                $("#bouton-planification").prop("disabled", true).show();
            }
            return json.data;
        }
    },
    columns: [
        { data: null, render: row => `${row.nom_candidat ?? ""} ${row.prenom_candidat ?? ""}` },
        {
            data: "date_naissance",
            render: d => {
                if (!d) return "";
                const birth = new Date(d);
                return new Date().getFullYear() - birth.getFullYear();
            }
        },
        { data: "profil" },
        { data: "score_test" },
        { data: "date_test" },
        {data: "etat"},
        { data: "date_heure_entretien" },
        {
            data: null,
            render: (row) => {
                const now = new Date();
                const entretienDate = new Date(row.date_heure_entretien);
                let html = "";

                if (entretienDate.toDateString() === now.toDateString()) {
                    if (row.etat === "accepte" || row.etat === "reporte") {
                        html = `
                            <button data-id="${row.id_entretien}" class="btn-commencer btn btn-primary btn-icon-split" type="button">
                                <span class="icon text-white-50"><i class="fa fa-play"></i></span>
                                <span class="text">Commencer</span>
                            </button>`;
                    } else if (row.etat === "en cours") {
                        html = `
                            <button data-id="${row.id_entretien}" class="btn-terminer btn btn-success btn-icon-split btn-fixed" type="button">
                                <i class="fa fa-stop"></i>
                                <span class="text">Terminer</span>
                            </button>`;
                    }
                } else if (entretienDate.getTime() > now.getTime()) {
                    if (row.etat === "planifie") {
                        html = `
                            <button data-id="${row.id_entretien}" class="btn-accepter btn btn-success btn-icon-split btn-fixed" type="button">
                                <span class="icon text-white-50"><i class="fas fa-check"></i></span>
                                <span class="text">Accepter</span>
                            </button>
                            <div class="my-2"></div>
                            <button data-id="${row.id_entretien}" class="btn-refuser btn btn-danger btn-icon-split btn-fixed" type="button">
                                <span class="icon text-white-50"><i class="fa fa-times-circle"></i></span>
                                <span class="text">Refuser</span>
                            </button>
                            <div class="my-2"></div>
                            <button data-id="${row.id_entretien}" class="btn-reporter btn btn-secondary btn-icon-split btn-fixed" type="button">
                                <span class="icon text-white-50"><i class="fas fa-calendar"></i></span>
                                <span class="text">Reporter</span>
                            </button>`;
                    } else if (row.etat !== "refuse") {
                        html = "accessible à la date heure d'entretien";
                    } else {
                        html = "indisponible pour un entretien refusé";
                    }
                }
                return html;
            }
        }
    ]
});

// ======================
// 4️⃣ Gestion des boutons
// ======================
$(document).on("click", ".btn-commencer", function () {
    AjaxModificationPlanning(buildData("commencer", $(this).data("id")), "en cours");
});

$(document).on("click", ".btn-terminer", function () {
    AjaxModificationPlanning(buildData("terminer", $(this).data("id")), "terminé");
});

$(document).on("click", ".btn-accepter", function () {
    AjaxModificationPlanning(buildData("accepter", $(this).data("id")), "accepté");
});

$(document).on("click", ".btn-refuser", function () {
    const id = $(this).data("id");
    $("#id_entretien").val(id);
    openPopup("refuser", "refusé", true);
});

$(document).on("click", ".btn-reporter", function () {
    const id = $(this).data("id");
    $("#id_entretien").val(id);
    openPopup("reporter", "reporté", false);
});

$("#bouton-planification").off("click").on("click", function () {
    $(this).prop("disabled", true);
    $.ajax({
        url: "/api/planifier-entretien/",
        type: "GET",
        success: function () {
            alert("Planification créée avec succès");
            table.ajax.reload();
        },
        error: function () {
            alert("Erreur lors de la planification");
            $("#bouton-planification").prop("disabled", false);
        }
    });
});
