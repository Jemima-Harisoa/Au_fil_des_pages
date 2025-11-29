# 🚀 Exemple : Ajouter le support des Fiches de Paie

Ce guide montre **pas à pas** comment ajouter une nouvelle capacité au chatbot.

## Objectif

Permettre au chatbot de répondre à :
- "Combien j'ai de fiches de paie ?"
- "Ma dernière fiche de paie ?"
- "Fiches de paie du mois dernier ?"

## Étape 1 : Vérifier le modèle existant

```bash
# Vérifier si FichePaieModel existe
ls app/models/FichePaieModel.php
```

Si le modèle existe, lire les méthodes disponibles :
```php
// Exemple de méthodes dans FichePaieModel
- getFichesEmploye($idEmploye)
- getDerniereFiche($idEmploye)
- getFichesMois($idEmploye, $mois, $annee)
```

## Étape 2 : Ajouter le pattern de reconnaissance

Dans `app/models/ChatbotModel.php`, méthode `analyzeQuestion()` :

```php
public function analyzeQuestion(string $question): string
{
    $q = strtolower($question);
    $patterns = [
        'conge' => '/cong[ée]|vacances|absence|solde\s+de\s+cong/i',
        'pointage' => '/pointage|pr[ée]sence|heures|horaire|retard/i',
        'employe' => '/employ[ée]|coll[èe]gue|personne|liste\s+des/i',
        'departement' => '/d[ée]partement|service|[ée]quipe/i',
        'statistiques' => '/combien|nombre|total|statistique/i',
        // ✨ NOUVEAU : Ajouter le pattern pour les fiches de paie
        'fiche_paie' => '/fiche\s+de\s+paie|bulletin|salaire|paie/i',
    ];
    // ...
}
```

## Étape 3 : Ajouter le modèle dans le constructeur

```php
public function __construct()
{
    $this->db = Flight::db();
    $this->employeModel = new EmployeModel();
    $this->pointageModel = new PointageModel($this->db);
    $this->departementModel = new DepartementModel();
    $this->congeModel = new \app\models\conge\CongeModel($this->db);
    
    // ✨ NOUVEAU : Initialiser FichePaieModel
    $this->fichePaieModel = new FichePaieModel($this->db);
}
```

N'oubliez pas de déclarer la propriété :
```php
private $db;
private $employeModel;
private $pointageModel;
private $departementModel;
private $congeModel;
private $fichePaieModel;  // ✨ NOUVEAU
```

## Étape 4 : Créer la méthode de récupération

```php
/**
 * Récupère les fiches de paie via FichePaieModel
 */
private function getFichePaieInfo($idEmploye = null)
{
    try {
        if ($idEmploye) {
            // Fiches d'un employé spécifique
            return $this->fichePaieModel->getFichesEmploye($idEmploye);
        } else {
            // Pour admin : toutes les fiches récentes (30 derniers jours)
            $sql = "SELECT fp.*, e.id_employe, p.nom, p.prenom 
                    FROM fiche_paie fp 
                    JOIN employes e ON fp.id_employe = e.id_employe
                    JOIN personnes p ON e.id_personne = p.id_personne
                    WHERE fp.date_creation >= CURRENT_DATE - INTERVAL '30 days'
                    ORDER BY fp.date_creation DESC LIMIT 20";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    } catch (\Exception $e) {
        error_log('ChatbotModel::getFichePaieInfo: ' . $e->getMessage());
        return [];
    }
}
```

## Étape 5 : Ajouter le case dans getContextData()

```php
public function getContextData(string $type, $idEmploye = null): array
{
    $ctx = [];
    try {
        switch ($type) {
            case 'conge':
                $ctx['conges'] = $this->getCongesInfo($idEmploye);
                break;
            case 'pointage':
                $ctx['pointage'] = $this->getPointageInfo($idEmploye);
                break;
            case 'employe':
                $ctx['employes'] = $this->getEmployesInfo();
                break;
            case 'departement':
                $ctx['departements'] = $this->getDepartementsInfo();
                break;
            case 'statistiques':
                $ctx['stats'] = $this->getStatistiques();
                break;
            // ✨ NOUVEAU : Ajouter le case pour les fiches de paie
            case 'fiche_paie':
                $ctx['fiches_paie'] = $this->getFichePaieInfo($idEmploye);
                break;
            default:
                $ctx = $this->getGeneralInfo();
        }
    } catch (\Exception $e) {
        error_log("ChatbotModel::getContextData error: " . $e->getMessage());
    }
    return $ctx;
}
```

## Étape 6 : Tester

1. Ouvrir `public/test-chatbot.html`
2. Poser la question : **"Combien j'ai de fiches de paie ?"**
3. Vérifier la réponse du chatbot

### Debugging
Si ça ne marche pas, vérifier les logs :
```php
// Dans error_log :
// ChatbotModel::analyzeQuestion → Vérifier le type détecté
// ChatbotModel::getContextData → Vérifier les données récupérées
// ChatbotModel::getFichePaieInfo → Vérifier les erreurs SQL
```

## C'est tout ! 🎉

Vous venez d'ajouter une nouvelle capacité au chatbot en **5 étapes simples** :

1. ✅ Vérifier le modèle existant
2. ✅ Ajouter le pattern de reconnaissance
3. ✅ Initialiser le modèle dans le constructeur
4. ✅ Créer la méthode de récupération (qui appelle le modèle)
5. ✅ Ajouter le case dans getContextData()

---

## Autres exemples

### Planning Entretien

```php
// Pattern
'entretien' => '/entretien|interview|rendez[-\s]vous\s+RH/i'

// Méthode
private function getEntretiensInfo($idEmploye = null) {
    return $this->planningEntretienModel->getEntretiensEmploye($idEmploye);
}
```

### Heures Supplémentaires

```php
// Pattern
'heures_sup' => '/heure[s]?\s+sup|overtime|heures?\s+suppl[ée]mentaire/i'

// Méthode
private function getHeuresSupInfo($idEmploye = null) {
    return $this->heureSupplementaireModel->getHistorique($idEmploye);
}
```

### Primes

```php
// Pattern
'prime' => '/prime|bonus|r[ée]compense/i'

// Méthode
private function getPrimesInfo($idEmploye = null) {
    return $this->primeModel->getPrimesEmploye($idEmploye);
}
```

---

## Récapitulatif du code complet

```php
<?php
namespace app\models;

use Flight;

class ChatbotModel
{
    // 1. Déclarer les propriétés
    private $db;
    private $employeModel;
    private $pointageModel;
    private $departementModel;
    private $congeModel;
    private $fichePaieModel;  // ✨ NOUVEAU

    // 2. Initialiser dans le constructeur
    public function __construct()
    {
        $this->db = Flight::db();
        $this->employeModel = new EmployeModel();
        $this->pointageModel = new PointageModel($this->db);
        $this->departementModel = new DepartementModel();
        $this->congeModel = new \app\models\conge\CongeModel($this->db);
        $this->fichePaieModel = new FichePaieModel($this->db);  // ✨ NOUVEAU
    }

    // 3. Ajouter le pattern
    public function analyzeQuestion(string $question): string
    {
        $patterns = [
            'conge' => '/cong[ée]|vacances/i',
            'fiche_paie' => '/fiche\s+de\s+paie|bulletin/i',  // ✨ NOUVEAU
            // ...
        ];
        // ...
    }

    // 4. Ajouter le case
    public function getContextData(string $type, $idEmploye = null): array
    {
        switch ($type) {
            case 'fiche_paie':  // ✨ NOUVEAU
                $ctx['fiches_paie'] = $this->getFichePaieInfo($idEmploye);
                break;
            // ...
        }
    }

    // 5. Créer la méthode
    private function getFichePaieInfo($idEmploye = null)  // ✨ NOUVEAU
    {
        try {
            return $this->fichePaieModel->getFichesEmploye($idEmploye);
        } catch (\Exception $e) {
            error_log('ChatbotModel::getFichePaieInfo: ' . $e->getMessage());
            return [];
        }
    }
}
```

---

**Le chatbot peut maintenant répondre aux questions sur les fiches de paie !** 🎉

Répétez ce processus pour **n'importe quelle autre fonctionnalité** du projet.
