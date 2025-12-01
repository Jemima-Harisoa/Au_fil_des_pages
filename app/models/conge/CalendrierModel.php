<?php
namespace app\models\conge;

class CalendrierModel
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getConges()
    {
        $sql = "
            SELECT 
                cd.id_demande,
                cd.id_employe,
                cd.date_debut,
                cd.date_fin,
                p.nom,
                p.prenom,
                ct.nom as type_conge
            FROM conge_demande cd
            INNER JOIN employes e ON cd.id_employe = e.id_employe
            INNER JOIN personnes p ON e.id_personne = p.id_personne
            LEFT JOIN conge_type ct ON cd.id_type_conge = ct.id_type
            WHERE cd.niveau_validation = 0
            ORDER BY cd.date_debut
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getAbsences()
    {
        $sql = "
            SELECT 
                a.id_abscence,
                a.id_employe,
                a.debut,
                a.fin,
                a.est_autorise,
                p.nom,
                p.prenom
            FROM abscence a
            INNER JOIN employes e ON a.id_employe = e.id_employe
            INNER JOIN personnes p ON e.id_personne = p.id_personne
            ORDER BY a.debut
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getJoursFeries()
    {
        $sql = "
            SELECT 
                id_jour_ferie,
                date
            FROM jour_ferie
            ORDER BY date
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}