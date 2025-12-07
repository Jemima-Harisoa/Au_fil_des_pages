<?php 
namespace app\models;

use PDO;
use PDOException;

class EvaluationModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

   public function getAllEvaluations() {
    try {
        $sql = "SELECT e.*, 
                       per_emp.nom AS employe, 
                       per_emp.prenom AS prenom_employe,
                       per_mgr.nom AS manager, 
                       per_mgr.prenom AS prenom_manager,
                       p.nom AS periode
                FROM employe_evaluations e
                JOIN employes emp ON emp.id_employe = e.employe_id
                JOIN personnes per_emp ON per_emp.id_personne = emp.id_personne
                LEFT JOIN employes mgr ON mgr.id_employe = e.manager_id
                LEFT JOIN personnes per_mgr ON per_mgr.id_personne = mgr.id_personne
                JOIN employe_evaluation_periodes p ON p.id_periode = e.periode_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getAllEvaluations: " . $e->getMessage());
        return [];
    }
}

public function getEvaluationById($id) {
    try {
        $stmt = $this->db->prepare("
            SELECT e.*,
                   per_emp.nom AS employe_nom,
                   per_emp.prenom AS employe_prenom,
                   per_mgr.nom AS manager_nom,
                   per_mgr.prenom AS manager_prenom,
                   p.nom AS periode_nom,
                   p.description AS periode_description,
                   emp.poste AS employe_poste,
                   emp.date_embauche AS employe_date_embauche,
                   d.nom AS departement_nom
            FROM employe_evaluations e
            JOIN employes emp ON emp.id_employe = e.employe_id
            JOIN personnes per_emp ON per_emp.id_personne = emp.id_personne
            LEFT JOIN employes mgr ON mgr.id_employe = e.manager_id
            LEFT JOIN personnes per_mgr ON per_mgr.id_personne = mgr.id_personne
            JOIN employe_evaluation_periodes p ON p.id_periode = e.periode_id
            LEFT JOIN departements d ON d.id_departement = emp.id_departement
            WHERE e.id_evaluation = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getEvaluationById: " . $e->getMessage());
        return null;
    }
}

   public function getCriteriaForEvaluation($evaluationId) {
    try {
        $stmt = $this->db->prepare("
            SELECT c.id_critere, c.nom, c.poids,
                   ed.note, ed.id_detail,ed.evaluation_id
            FROM employe_criteres_evaluation c
            LEFT JOIN employe_evaluations_details ed
                   ON ed.critere_id = c.id_critere
                  AND ed.evaluation_id = ?
        ");
        $stmt->execute([$evaluationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getCriteriaForEvaluation: " . $e->getMessage());
        return [];
    }
}
public function updateNoteByCriteria($evaluationId, $critereId, $noteValue) {
    try {
        // Validation de la note
        if (!is_numeric($noteValue) || $noteValue < 0 || $noteValue > 10) {
            throw new InvalidArgumentException("Note invalide pour le critère $critereId: $noteValue");
        }

        $stmt = $this->db->prepare("
            UPDATE employe_evaluations_details 
            SET note = ?, updated_at = CURRENT_TIMESTAMP
            WHERE evaluation_id = ? AND critere_id = ?
        ");
        
        $success = $stmt->execute([$noteValue, $evaluationId, $critereId]);
        
        if ($success && $stmt->rowCount() > 0) {
            // Mettre à jour le score total
            $this->updateEvaluationScoreFromEvaluation($evaluationId);
            return true;
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Erreur dans updateNoteByCriteria: " . $e->getMessage());
        return false;
    }
}

private function updateEvaluationScoreFromEvaluation($evaluationId) {
    try {
        $stmt = $this->db->prepare("
            UPDATE employe_evaluations 
            SET score_total = (
                SELECT SUM(ed.note * c.poids / 10)
                FROM employe_evaluations_details ed
                JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
                WHERE ed.evaluation_id = ?
            ),
            updated_at = CURRENT_TIMESTAMP
            WHERE id_evaluation = ?
        ");
        
        $stmt->execute([$evaluationId, $evaluationId]);
        
    } catch (PDOException $e) {
        error_log("Erreur dans updateEvaluationScoreFromEvaluation: " . $e->getMessage());
    }
}
public function evaluationHasNotes($evaluationId) {
    try {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as nb_notes
            FROM employe_evaluations_details
            WHERE evaluation_id = ? AND note IS NOT NULL
        ");
        
        $stmt->execute([$evaluationId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['nb_notes'] > 0;
        
    } catch (PDOException $e) {
        error_log("Erreur dans evaluationHasNotes: " . $e->getMessage());
        return false;
    }
}

public function getNotesStatus($evaluationId) {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_criteres,
                COUNT(ed.note) as notes_remplies,
                COUNT(*) - COUNT(ed.note) as notes_manquantes,
                CASE 
                    WHEN COUNT(ed.note) = COUNT(*) THEN 'COMPLETE'
                    WHEN COUNT(ed.note) > 0 THEN 'PARTIELLE'
                    ELSE 'VIDE'
                END as statut_notes
            FROM employe_criteres_evaluation c
            LEFT JOIN employe_evaluations_details ed 
                ON ed.critere_id = c.id_critere AND ed.evaluation_id = ?
        ");
        
        $stmt->execute([$evaluationId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur dans getNotesStatus: " . $e->getMessage());
        return [
            'total_criteres' => 0,
            'notes_remplies' => 0,
            'notes_manquantes' => 0,
            'statut_notes' => 'ERREUR'
        ];
    }
}
public function updateEvaluationStatus($evaluationId) {
    try {
        // Vérifier si toutes les notes sont remplies
        $status = $this->getNotesStatus($evaluationId);
        
        $newStatus = 'EN_COURS';
        if ($status['statut_notes'] === 'COMPLETE') {
            $newStatus = 'TERMINEE';
        }
        
        $stmt = $this->db->prepare("
            UPDATE employe_evaluations 
            SET statut = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id_evaluation = ?
        ");
        
        $stmt->execute([$newStatus, $evaluationId]);
        return true;
        
    } catch (PDOException $e) {
        error_log("Erreur dans updateEvaluationStatus: " . $e->getMessage());
        return false;
    }
}
    public function saveNotesForEvaluation($evaluationId, $notes) {
    try {
        $this->db->beginTransaction();

        foreach ($notes as $critereId => $note) {
            // Ignorer les notes vides (mais pas zéro)
            if ($note === '' || $note === null) {
                continue;
            }

            // Validation de la note
            if (!is_numeric($note) || $note < 0 || $note > 10) {
                throw new InvalidArgumentException("Note invalide pour le critère $critereId: $note");
            }

            // Vérifier d'abord si une note existe déjà
            $checkStmt = $this->db->prepare("
                SELECT id_detail 
                FROM employe_evaluations_details 
                WHERE evaluation_id = ? AND critere_id = ?
            ");
            $checkStmt->execute([$evaluationId, $critereId]);
            $existingNote = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingNote) {
                // UPDATE si la note existe
                $updateStmt = $this->db->prepare("
                    UPDATE employe_evaluations_details 
                    SET note = ? WHERE id_detail = ?
                ");
                $updateStmt->execute([$note, $existingNote['id_detail']]);
                
                error_log("Note mise à jour - Evaluation: $evaluationId, Critère: $critereId, Note: $note");
            } else {
                // INSERT si la note n'existe pas
                $insertStmt = $this->db->prepare("
                    INSERT INTO employe_evaluations_details (evaluation_id, critere_id, note)
                    VALUES (?, ?, ?)
                ");
                $insertStmt->execute([$evaluationId, $critereId, $note]);
                
                error_log("Note insérée - Evaluation: $evaluationId, Critère: $critereId, Note: $note");
            }
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Erreur saveNotesForEvaluation: " . $e->getMessage());
        return false;
    }
}

public function calculateScore($evaluationId) {
    try {
        // 1️⃣ Récupérer toutes les notes avec leurs poids
        $stmt = $this->db->prepare("
            SELECT ed.note, c.poids
            FROM employe_evaluations_details ed
            JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
            WHERE ed.evaluation_id = ?
        ");
        $stmt->execute([$evaluationId]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($details as $d) {
            $total += $d['note'] * $d['poids'] / 10;
        }

        $stmt2 = $this->db->prepare("
            UPDATE employe_evaluations
            SET score_total = ?
            WHERE id_evaluation = ?
        ");
        $stmt2->execute([$total, $evaluationId]);

        return $total;

    } catch (PDOException $e) {
        error_log("Erreur dans calculateScore: " . $e->getMessage());
        return null;
    }
}

    public function getEvaluationScore($evaluationId) {
        try {
            $stmt = $this->db->prepare("SELECT score_total FROM employe_evaluations WHERE id_evaluation = ?");
            $stmt->execute([$evaluationId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans getEvaluationScore: " . $e->getMessage());
            return null;
        }
    }

public function getEvaluationDetails($evaluationId) {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                ed.id_detail,
                ed.critere_id, 
                ed.note,
                c.nom as critere, 
                c.description,
                c.poids,
                e.employe_id,
                e.statut
            FROM employe_evaluations_details ed
            JOIN employe_criteres_evaluation c ON ed.critere_id = c.id_critere
            JOIN employe_evaluations e ON ed.evaluation_id = e.id_evaluation
            WHERE ed.evaluation_id = ?
            ORDER BY c.id_critere
        ");
        $stmt->execute([$evaluationId]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $details;
    } catch (PDOException $e) {
        error_log("Erreur getEvaluationDetails: " . $e->getMessage());
        return [];
    }
}
    public function getEmployeeEvaluations($employeeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT id_evaluation, date_evaluation, score_total
                FROM employe_evaluations
                WHERE employe_id = ?
                  AND statut = 'TERMINEE'
                ORDER BY date_evaluation DESC
            ");
            $stmt->execute([$employeeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans getEmployeeEvaluations: " . $e->getMessage());
            return [];
        }
    }

    public function getTeamEvaluationsSummary($managerId) {
    try {
        $stmt = $this->db->prepare("
            SELECT statut, COUNT(*) AS total
            FROM employe_evaluations WHERE manager_id = ?
            GROUP BY statut
        ");
        $stmt->execute([$managerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = [
            'PREVUE' => 0,
            'EN_COURS' => 0,
            'TERMINEE' => 0,
            'moyenne' => 0
        ];

        foreach ($rows as $r) {
            $summary[$r['statut']] = (int)$r['total'];
        }

        $stmt2 = $this->db->prepare("
            SELECT AVG(score_total) 
            FROM employe_evaluations 
            WHERE manager_id = ? AND statut = 'TERMINEE'
        ");
        $stmt2->execute([$managerId]);

        $summary['moyenne'] = (float)($stmt2->fetchColumn() ?? 0);

        return $summary;

    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

public function getScoreTrends($managerId, $months = 6) {
    $stmt = $this->db->prepare("
        SELECT 
            p.nom || ' ' || p.prenom AS employe,
            e.score_total,
            AVG(e.score_total) OVER (PARTITION BY e.employe_id) AS score_moyen,
            EXTRACT(MONTH FROM e.date_evaluation)::INTEGER AS mois,
            EXTRACT(YEAR FROM e.date_evaluation)::INTEGER AS annee
        FROM employe_evaluations e
        JOIN employes emp ON emp.id_employe = e.employe_id
        JOIN personnes p ON p.id_personne = emp.id_personne
        WHERE e.manager_id = ?
          AND e.statut = 'TERMINEE'
          AND e.date_evaluation >= CURRENT_DATE - INTERVAL '$months months'
        ORDER BY annee, mois, employe
    ");
    
    $stmt->execute([$managerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getTeamUrgentEvaluations($managerId) {
    $stmt = $this->db->prepare("
        SELECT 
            e.*,
            pe.nom || ' ' || pe.prenom AS employe,
            p.nom AS periode
        FROM employe_evaluations e
        JOIN employes emp ON emp.id_employe = e.employe_id
        JOIN personnes pe ON pe.id_personne = emp.id_personne
        JOIN employe_evaluation_periodes p ON p.id_periode = e.periode_id
        WHERE e.manager_id = $1
          AND e.statut IN ('PREVUE', 'EN_COURS')
          AND e.date_evaluation < CURRENT_DATE
    ");

    $stmt->execute([$managerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function generatePerformance($annee, $employeId = null) {

    $where = "WHERE e.statut = 'TERMINEE' AND EXTRACT(YEAR FROM e.date_evaluation) = ?";
    $params = [$annee];

    if ($employeId !== null) {
        $where .= " AND employe_id = ?";
        $params[] = $employeId;
    }

    $stmt = $this->db->prepare("
        SELECT 
            e.score_total,
            p.nom AS periode,
            e.date_evaluation
        FROM employe_evaluations e
        JOIN employe_evaluation_periodes p ON p.id_periode = e.periode_id
        $where
        ORDER BY e.date_evaluation ASC
    ");
    $stmt->execute($params);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $scores = array_column($rows, 'score_total');

    return [
        'evaluations' => $rows,
        'score_annuel' => array_sum($scores),
        'score_moyen' => count($scores) ? array_sum($scores)/count($scores) : 0,
        'par_periode' => $rows
    ];
}
// Récupère les performances par critère pour radar chart
    public function getPerformanceGraphData(int $employeId, int $annee) {
        $stmt = $this->db->prepare("
            SELECT critere, score_pondere
            FROM vue_employe_performances_par_critere
            WHERE employe_id=? AND annee=?
        ");
        $stmt->execute([$employeId, $annee]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   // Compare deux périodes pour un employé
public function comparePeriods($employeId, $periodeA, $periodeB) {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                e.periode_id,
                p.nom AS periode,
                AVG(ed.note * c.poids / 10) AS score_pondere
            FROM employe_evaluations e
            JOIN employe_evaluations_details ed ON ed.evaluation_id = e.id_evaluation
            JOIN employe_criteres_evaluation c ON c.id_critere = ed.critere_id
            JOIN employe_evaluation_periodes p ON p.id_periode = e.periode_id
            WHERE e.employe_id = ? AND e.periode_id IN (?, ?) AND e.statut = 'TERMINEE'
            GROUP BY e.periode_id, p.nom
        ");
        $stmt->execute([$employeId, $periodeA, $periodeB]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans comparePeriods: " . $e->getMessage());
        return [];
    }
}

}