# Architecture du Chatbot - Au fil des pages

## 📋 Vue d'ensemble

Le chatbot a été refactorisé pour **réutiliser les modèles existants** au lieu de dupliquer les requêtes SQL. Cette architecture suit le principe **DRY (Don't Repeat Yourself)** et facilite la maintenance.

## 🏗️ Structure MVC

```
app/
├── controllers/
│   └── ChatBotController.php       # Gère les requêtes HTTP POST
├── models/
│   ├── ChatbotModel.php            # Orchestration et appels API Gemini
│   ├── EmployeModel.php            # Gestion des employés
│   ├── PointageModel.php           # Gestion des pointages
│   ├── DepartementModel.php        # Gestion des départements
│   └── conge/
│       └── CongeModel.php          # Gestion des congés
└── views/
    └── messagerieBot.php           # Interface utilisateur
```

## 🔄 Flux de données

```
Utilisateur → messagerieBot.php (Frontend)
                ↓ (POST /messagerieBot/send)
          ChatBotController::send()
                ↓
          ChatbotModel::generateReply()
                ↓
          ┌─────────────────────┐
          │ analyzeQuestion()   │ → Détecte le type (congé, pointage, etc.)
          ├─────────────────────┤
          │ getContextData()    │ → Appelle les modèles appropriés
          │  ├─ EmployeModel    │
          │  ├─ PointageModel   │
          │  ├─ CongeModel      │
          │  └─ DepartementModel│
          ├─────────────────────┤
          │ formatContextForAPI()│ → Formate les données pour Gemini
          ├─────────────────────┤
          │ Appel API Gemini    │ → Génère la réponse IA
          └─────────────────────┘
                ↓
          Réponse JSON → Frontend
```

## 🔧 Modèles utilisés et leurs méthodes

### 1. **CongeModel** (`app\models\conge\CongeModel`)
```php
// Utilisé dans getCongesInfo()
$this->congeModel->getNombreConge($idEmploye);    // Solde de congés
$this->congeModel->getDonneesConges($idEmploye);  // Détails des demandes
```

### 2. **PointageModel** (`app\models\PointageModel`)
```php
// Utilisé dans getPointageInfo()
$this->pointageModel->getDernierPointage($idEmploye);  // Dernier pointage
$this->pointageModel->getAllPresence();                // Tous les présents
```

### 3. **EmployeModel** (`app\models\EmployeModel`)
```php
// Utilisé dans getEmployesInfo()
$this->employeModel->listWithDetails();  // Liste complète avec détails (JOIN)
```

### 4. **DepartementModel** (`app\models\DepartementModel`)
```php
// Utilisé dans getDepartementsInfo()
$this->departementModel->list();  // Liste tous les départements
```

## 🎯 Avantages de l'architecture refactorisée

### ✅ Avant (Problèmes)
- ❌ Requêtes SQL dupliquées entre `ChatbotModel` et les autres modèles
- ❌ Maintenance difficile (modifications multiples)
- ❌ Risque d'incohérence entre les requêtes
- ❌ Violation du principe DRY

### ✅ Après (Solutions)
- ✅ **Réutilisation** : Appels aux modèles existants
- ✅ **Maintenance centralisée** : Modifications uniquement dans les modèles métier
- ✅ **Cohérence** : Même logique partout dans l'application
- ✅ **Évolutivité** : Ajouter une nouvelle fonctionnalité = ajouter un appel au modèle

## 📦 Initialisation des dépendances

```php
public function __construct()
{
    $this->db = Flight::db();
    $this->employeModel = new EmployeModel();
    $this->pointageModel = new PointageModel($this->db);
    $this->departementModel = new DepartementModel();
    $this->congeModel = new \app\models\conge\CongeModel($this->db);
}
```

**Note** : Certains modèles nécessitent `$db` en paramètre (`PointageModel`, `CongeModel`), d'autres utilisent `Flight::db()` directement dans leur constructeur.

## 🤖 Analyse des questions

Le chatbot utilise des **expressions régulières** pour classifier les questions :

```php
$patterns = [
    'conge'       => '/cong[ée]|vacances|absence|solde\s+de\s+cong/i',
    'pointage'    => '/pointage|pr[ée]sence|heures|horaire|retard/i',
    'employe'     => '/employ[ée]|coll[èe]gue|personne|liste\s+des/i',
    'departement' => '/d[ée]partement|service|[ée]quipe/i',
    'statistiques'=> '/combien|nombre|total|statistique/i',
];
```

## 🔮 Intégration Gemini API

- **Modèle** : `gemini-2.5-flash`
- **Endpoint** : `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent`
- **Configuration** :
  - `temperature`: 0.4 (réponses factuelles)
  - `maxOutputTokens`: 500
  - `topP`: 0.8

### Gestion des erreurs
- ✅ `finishReason: MAX_TOKENS` → Message explicite à l'utilisateur
- ✅ Parsing récursif de la structure JSON (plusieurs formats possibles)
- ✅ Logs détaillés pour débogage

## 📊 Cas d'usage supportés

| Question                              | Type détecté  | Modèle(s) utilisé(s)              |
|---------------------------------------|---------------|-----------------------------------|
| "Combien de congés me reste-t-il ?"  | `conge`       | `CongeModel`                      |
| "Qui est présent aujourd'hui ?"      | `pointage`    | `PointageModel`                   |
| "Liste des employés du département X"| `employe`     | `EmployeModel`, `DepartementModel`|
| "Combien de départements ?"          | `statistiques`| `DepartementModel` + SQL          |

## 🚀 Évolutions futures possibles

1. **Extraction de la clé API** vers `config.php`
2. **Découverte dynamique** de toutes les méthodes disponibles (réflexion PHP)
3. **Cache de réponses** pour questions fréquentes
4. **Mode local** sans API (résumé basique avec les données)
5. **Historique des conversations** (table `conversations_bot`)
6. **Support multilingue** (français/anglais)

## 🔐 Sécurité

- ✅ Validation de session (`$_SESSION['employe']` ou `$_SESSION['infoAdmin']`)
- ✅ Données limitées par utilisateur (filtre `$idEmploye`)
- ⚠️ **TODO** : Extraire la clé API du code source vers fichier de config
- ⚠️ **TODO** : Ajouter rate limiting pour prévenir abus API

## 📝 Maintenance

### Ajouter un nouveau type de question

1. **Ajouter un pattern** dans `analyzeQuestion()`
2. **Créer une méthode** `getXXXInfo()` qui appelle les modèles
3. **Ajouter un case** dans `getContextData()`
4. **Documenter** le nouveau cas d'usage

Exemple :
```php
// 1. Pattern
'fiche_paie' => '/fiche\s+de\s+paie|salaire|bulletin/i'

// 2. Méthode
private function getFichePaieInfo($idEmploye) {
    return $this->fichePaieModel->getFichesEmploye($idEmploye);
}

// 3. Switch case
case 'fiche_paie':
    $ctx['fiches'] = $this->getFichePaieInfo($idEmploye);
    break;
```

## 🧪 Tests

Utiliser `public/test-chatbot.html` pour tester :
- Questions sur les congés
- Questions sur les pointages
- Questions générales
- Cas limites (MAX_TOKENS, connexion API, etc.)

---

**Version** : 2.0 (Refactorisée avec composition de modèles)  
**Date** : 2025  
**Mainteneur** : Équipe Dev Au fil des pages
