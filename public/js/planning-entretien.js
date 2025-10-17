let button = document.getElementById("bouton-planification");
// initialisation (à faire au chargement de la page)
const table = $('#dataTable').DataTable({
  destroy: true,
  paging: true,
  processing: true,
  serverSide: false, // important : client-side
  columns: [
    {
      data: null,
      render: row => (row.nom_candidat ?? '') + ' ' + (row.prenom_candidat ?? '')
    },
    {
      data: "date_naissance",
      render: function(d) {
        if (!d) return "";
        // calcul d'âge plus précis
        const dob = new Date(d);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        return age;
      }
    },
    {
      data: null,
      render: row => (row.nom_responsable ?? '') + ' ' + (row.prenom_responsable ?? '')
    },
    { data: "profil", defaultContent: "" },
    { data: "score_test", defaultContent: "" },
    { data: "date_test", defaultContent: "" },
    { data: "date_heure_entretien", defaultContent: "" },
    {
      data: null,
      orderable: false,
      searchable: false,
      render: function(row) {
        // ajoute des classes/data-id pour la gestion des événements
        return `
          <button class="btn btn-sm btn-primary btn-reporter" data-id="${row.id ?? ''}">Reporter</button>
          <button class="btn btn-secondary ms-2 btn-rejeter" data-id="${row.id ?? ''}">Rejeter</button>
          <button class="btn btn-sm btn-success ms-2 btn-accepter" data-id="${row.id ?? ''}">Accepter</button>
        `;
      }
    }
  ]
});


button.addEventListener('click', function(e) {
  // fetch pour récupérer les enregistrements planifiés
  fetch("/api/planifier-entretien", {
      method: "GET"
      // pas besoin de Content-Type pour GET
  })
  .then(response => {
      if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
      return response.json();
  })
  .then(payload => {
      // payload.resultat doit être un tableau d'objets
      let rows = Array.isArray(payload.resultat) ? payload.resultat : [];

      // normaliser les objets (au cas où certaines clés manquent)
      rows = rows.map(r => ({
        id: r.id ?? null,
        nom_candidat: r.nom_candidat ?? '',
        prenom_candidat: r.prenom_candidat ?? '',
        date_naissance: r.date_naissance ?? null,
        nom_responsable: r.nom_responsable ?? '',
        prenom_responsable: r.prenom_responsable ?? '',
        profil: r.profil ?? '',
        score_test: r.score_test ?? '',
        date_test: r.date_test ?? '',
        date_heure_entretien: r.date_heure_entretien ?? ''
      }));

      table.clear();

      if (rows.length > 0) {
          // ajouter toutes les lignes en une seule fois — rapide et propre
          table.rows.add(rows).draw(); // draw() remet la pagination au début
      } else {
          // afficher une ligne "Aucun résultat trouvé"
          table.rows.add([{
            nom_candidat: "Aucun résultat trouvé",
            prenom_candidat: "",
            date_naissance: null,
            nom_responsable: "",
            prenom_responsable: "",
            profil: "",
            score_test: "",
            date_test: "",
            date_heure_entretien: ""
          }]).draw();
      }
  })
  .catch(error => {
      console.error("Erreur fetch :", error);
      // afficher un message utilisateur si besoin
      // $('#message-container').html(`<div class="alert alert-danger">Erreur : ${error.message}</div>`);
  });
});
