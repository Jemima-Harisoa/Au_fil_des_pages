<?php

namespace app\models\conge;

class NotificationModel {
    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère les absences non justifiées d'un employé
     */
    public function getAbsencesNonJustifiees($idEmploye) {
        $sql = "
            SELECT 
                a.id_abscence,
                a.debut,
                a.fin,
                (EXTRACT(EPOCH FROM (a.fin - a.debut))/86400 + 1) as jours_pris,
                cd.description
            FROM abscence a
            LEFT JOIN abscence_conge_suivi acs ON a.id_abscence = acs.id_abscence
            LEFT JOIN conge_demande cd ON acs.id_demande = cd.id_demande
            WHERE a.id_employe = :id_employe 
            AND a.est_autorise = false
            AND (a.justificatif IS NULL OR a.justificatif = '')
            ORDER BY a.debut DESC
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les informations d'un employé
     */
    public function getInfosEmploye($idEmploye) {
        $sql = "
            SELECT 
                e.id_employe,
                p.nom,
                p.prenom,
                p.contact,
                e.poste,
                d.nom as departement
            FROM employes e
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN departements d ON e.id_departement = d.id_departement
            WHERE e.id_employe = :id_employe
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_employe' => $idEmploye]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Crée une notification pour un employé
     */
    public function creerNotification($idEmploye, $message, $type = 'absence') {
        $sql = "
            INSERT INTO notifications (id_personne, message, date_notification)
            SELECT p.id_personne, :message, NOW()
            FROM employes e
            LEFT JOIN personnes p ON e.id_personne = p.id_personne
            WHERE e.id_employe = :id_employe
        ";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'message' => $message,
            'id_employe' => $idEmploye
        ]);
    }


}