//////////////////////////////////////////////////
// Database: aufildespages
//////////////////////////////////////////////////

Table annonces {
  id_annonce int [pk, increment]
  id_profil int
  titre varchar
  date_publication date
  date_expiration date
  nombre_poste int
  lien text
}

Table diplomes {
  id_diplome int [pk, increment]
  nom varchar
  niveau int
}

Table treshold {
  id_treshold int [pk, increment]
  valeur decimal(5,2)
  date_treshold timestamp
}

Table departements {
  id_departement int [pk, increment]
  nom varchar
}

Table filieres {
  id_filiere int [pk, increment]
  nom varchar
}

Table type_contrats {
  id_type_contrat int [pk, increment]
  nom varchar
}

Table profils {
  id_profil int [pk, increment]
  titre varchar
  competences text
  skills text
  loisirs text
  id_diplome int
  id_filiere int
  experience_pro text
  certifications text
  langues text
  id_type_contrat int
  id_departement int
  est_minimum boolean
}

Table utilisateurs {
  id_utilisateur int [pk, increment]
  nom varchar
  mdp varchar
  date_inscription timestamp
  date_sortie timestamp
}

Table admins {
  id_admin int [pk, increment]
  id_employe int
  nom varchar
  mdp varchar
  date_affiliation timestamp
  date_fin_affiliation timestamp
}

Table personnes {
  id_personne int [pk, increment]
  nom varchar
  prenom varchar
  date_naissance date
  contact varchar
  lien_image varchar
}

Table api {
  id_api int [pk, increment]
  nom varchar
  cle_api varchar
}

Table questions {
  id_question int [pk, increment]
  question text
  id_profil int
  note decimal(5,2)
}

Table reponses_question {
  id_reponse int [pk, increment]
  id_question int
  reponse text
  est_correct boolean
}

Table candidats {
  id_candidat int [pk, increment]
  id_personne int
  id_annonce int
  id_profil int
  cv_url varchar
  poste varchar
  id_utilisateur int
}

Table tests {
  id_test int [pk, increment]
  id_candidat int
  id_annonce int
  score_test decimal(5,2)
  date_test date
}

Table etat {
  id_etat int [pk, increment]
  nom varchar
}

Table appreciation {
  id_appreciation int [pk, increment]
  type_appreciation text
  code int
}

Table planning_entretien {
  id_entretien int [pk, increment]
  id_candidat int
  id_responsable int
  date_heure_entretien timestamp
  score_entretien decimal(5,2)
  etat int
  id_appreciation int
}

Table message_automatique {
  id_message_automatique int [pk, increment]
  message text
}

Table notifications {
  id_notification int [pk, increment]
  id_personne int
  message text
  date_notification timestamp
}

Table contrats {
  id_contrat int [pk, increment]
  id_candidat int
  id_type_contrat int
  url_contrat varchar
}

Table essais {
  id_essai int [pk, increment]
  id_personne int
  id_contrat int
  id_etat int
  date_debut date
  date_fin date
}

Table employes {
  id_employe int [pk, increment]
  id_personne int
  id_contrat int
  id_departement int
  poste varchar
  date_embauche date
  nombre_conge int
  salaire_base double
}

Table disponibilite_employe {
  id_dispo int [pk, increment]
  id_employe int
  heure_debut time
  heure_fin time
}

Table historique_validation {
  id_historique_validation int [pk, increment]
  id_employe int
  id_candidat int
  date_heure_validation timestamp
  id_etat int
}

Table profilsCV {
  id_profil int [pk, increment]
  titre varchar
  competences text
  skills text
  loisirs text
  id_diplome int
  filiere text
  experience_pro text
  certifications text
  langues text
  id_type_contrat int
  est_minimum boolean
  id_departement int
}

Table cv_candidats {
  id_cv_candidats int [pk, increment]
  id_candidat int
  competences text
  skills text
  loisirs text
  id_diplome int
  filiere text
  experience_pro text
  certifications text
  langues text
  date_deposition timestamp
}

Table status_validation_cv {
  id_status_validation_cv int [pk, increment]
  statut varchar
}

Table validation_cv {
  id_validation_cv int [pk, increment]
  id_candidat int
  id_cv_candidat int
  id_status_validation_cv int
  similarite decimal(5,2)
}

Table responsable_entretien {
  id_responsable int [pk, increment]
  id_profil int
  id_employe int
  ordre_passage int
}

Table disponibilite_entretien {
  id_dispo int [pk, increment]
  id_responsable int
  heure_debut time
  heure_fin time
  jour int
  est_valide boolean
}

Table jour_ferie {
  id_jour_ferie int [pk, increment]
  date date
}

Table config_entretien {
  id_config_entretien int [pk, increment]
  id_departement int
  duree_entretien varchar
}

//////////////////////////////////////////////////
// Relations
//////////////////////////////////////////////////

Ref: profils.id_diplome > diplomes.id_diplome
Ref: profils.id_filiere > filieres.id_filiere
Ref: profils.id_type_contrat > type_contrats.id_type_contrat
Ref: profils.id_departement > departements.id_departement

Ref: candidats.id_personne > personnes.id_personne
Ref: candidats.id_annonce > annonces.id_annonce
Ref: candidats.id_profil > profils.id_profil

Ref: tests.id_candidat > candidats.id_candidat
Ref: tests.id_annonce > annonces.id_annonce

Ref: planning_entretien.id_candidat > candidats.id_candidat
Ref: planning_entretien.id_appreciation > appreciation.id_appreciation

Ref: contrats.id_candidat > candidats.id_candidat
Ref: contrats.id_type_contrat > type_contrats.id_type_contrat

Ref: employes.id_personne > personnes.id_personne
Ref: employes.id_contrat > contrats.id_contrat
Ref: employes.id_departement > departements.id_departement

Ref: historique_validation.id_employe > employes.id_employe
Ref: historique_validation.id_candidat > candidats.id_candidat
Ref: historique_validation.id_etat > etat.id_etat

Ref: disponibilite_employe.id_employe > employes.id_employe
Ref: disponibilite_entretien.id_responsable > responsable_entretien.id_responsable
Ref: responsable_entretien.id_employe > employes.id_employe
Ref: responsable_entretien.id_profil > profils.id_profil

Ref: essais.id_personne > personnes.id_personne
Ref: essais.id_contrat > contrats.id_contrat
Ref: essais.id_etat > etat.id_etat

Ref: notifications.id_personne > personnes.id_personne
Table evenements {
  id_evenement int [pk, increment]
  nom_evenement varchar
}

Table historique_mobilite {
  id_mobilite int [pk, increment]
  id_candidat int
  id_evenement int
  id_profil int
  id_departement int
  date_evenement date
  support text
}

Ref: historique_mobilite.id_candidat > candidats.id_candidat
Ref: historique_mobilite.id_evenement > evenements.id_evenement
Ref: historique_mobilite.id_profil > profils.id_profil
Ref: historique_mobilite.id_departement > departements.id_departement

Table conge_demande {
  id_demande int [pk, increment]
  description text
  id_employe int
  niveau_validation int [default: 2]
}

Table conge_historique_validation {
  id_historique_validation int [pk, increment]
  id_demande int
  id_employe int
  date_validation timestamp
}

Table conge_historique{
  id_conge_historique int [pk , increment]
  nombres_abscence_attribue double
  id_employe int 
}
Table abscence {
  id_abscence int [pk, increment]
  debut datetime
  fin datetime
  est_autorise boolean
  justificatif text
}
Table pointage {
  id_pointage int [pk, increment]
  id_employe int
  connexion datetime
  deconnexion datetime
  duree_session interval
}

Ref: pointage.id_employe > employes.id_employe

Ref: conge_demande.id_employe > employes.id_employe
Ref: conge_historique_validation.id_demande > conge_demande.id_demande
Ref: conge_historique_validation.id_employe > employes.id_employe


Ref: "type_contrats"."id_type_contrat" < "type_contrats"."nom"

Table seuil_tolerance {
  id_seuil int [pk, increment]
  valeur decimal(5,2) [default: 5]
  date_creation timestamp [default: `CURRENT_TIMESTAMP`]
}

Table salaire_historique{
  id_salaire_historique int [pk , increment]
  salaire double
  date_creation timestamp [default: `CURRENT_TIMESTAMP`]
  id_employe  int
}

Table parametre {
  id int [pk, increment]
  libelle varchar
  pourcentage decimal(5,2)
}

Table irsa {
  id int [pk, increment]
  min double
  max double
  pourcentage decimal(5,2)
  date_creation timestamp [default: `CURRENT_TIMESTAMP`]
}

Table type_prime {
  id int [pk, increment]
  libelle varchar
}

Table prime {
  id int [pk, increment]
  id_type_prime int
  pourcentage decimal(5,2)
  id_employe int
  date_creation timestamp [default: `CURRENT_TIMESTAMP`]
}

Ref: prime.id_type_prime > type_prime.id
Ref: prime.id_employe > employes.id_employe
