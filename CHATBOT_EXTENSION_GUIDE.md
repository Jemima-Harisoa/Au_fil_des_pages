# Guide d'Extension du Chatbot 🤖

Ce guide explique comment étendre les capacités du chatbot pour qu'il puisse répondre à **n'importe quelle question** en exploitant les fonctions existantes du projet.

## 🎯 Philosophie de l'Architecture

Le chatbot doit pouvoir **composer** les fonctions existantes pour répondre à toutes les questions possibles. Il ne doit **jamais dupliquer** de logique métier.

### Principe de base
```
Question utilisateur → Analyse → Sélection des modèles → Appel des méthodes → Formatage → Réponse IA
```

## 📚 Inventaire des Modèles Disponibles

Voici les modèles existants que le chatbot peut utiliser :

### 1. **EmployeModel** (`app/models/EmployeModel.php`)
```php
// Méthodes disponibles :
- listWithDetails()                      // Liste tous les employés avec détails
- getEmployesWithDetails($idEmploye)     // Détails d'un employé spécifique
- listeEmployerV2()                      // Liste alternative
- getEmployerHistoriqueMouvement($id)    // Historique des mouvements
- verifierEmploye($prenom, $mdp)         // Authentification
- getInfosEmploye($idEmploye)            // Infos basiques
- getEmployeAutre($idEmploye)            // Autres données
- getFicheEmploye($idEmploye)            // Fiche complète
```

### 2. **PointageModel** (`app/models/PointageModel.php`)
```php
// Méthodes disponibles :
- getDernierPointage($idEmploye)                        // Dernier pointage
- ajouterPointage($idEmploye)                          // Nouveau pointage
- cloturerPointage($idPointage)                        // Clôture
- getAllPresence()                                     // Tous présents
- creerReleverPresenceIndividuelle($id, $debut, $fin) // Relevé individuel
- creerReleverPresenceGroupe($idDept, $debut, $fin)   // Relevé groupe
```

### 3. **CongeModel** (`app/models/conge/CongeModel.php`)
```php
// Méthodes disponibles :
- absences($idEmploye, $estAutorise)  // Liste des absences
- getNombreConge($idEmploye)          // Solde congés
- getDonneesConges($idEmploye)        // Détails des demandes
```

### 4. **DepartementModel** (`app/models/DepartementModel.php`)
```php
// Méthodes disponibles :
- list()                    // Liste tous
- save($data)              // Créer
- updateById($id, $data)   // Modifier
- deleteById($id)          // Supprimer
- findById($id)            // Trouver par ID
- search($field, $value)   // Rechercher
- getBy($field, $value)    // Récupérer par champ
```

### 5. **FichePaieModel** (`app/models/FichePaieModel.php`)
```php
// Méthodes supposées (à vérifier) :
- getFichesEmploye($idEmploye)       // Fiches de paie
- getFicheById($id)                  // Fiche spécifique
- genererFiche($idEmploye, $periode) // Génération
```

### 6. **MessagerieModel** (`app/models/MessagerieModel.php`)
```php
// Méthodes supposées (à vérifier) :
- getConversations($idEmploye)  // Liste des conversations
- envoyerMessage($from, $to, $message)
```

### 7. **PlanningEntretienModel** (`app/models/PlanningEntretienModel.php`)
```php
// Méthodes supposées (à vérifier) :
- getEntretiensEmploye($idEmploye)
- getEntretiensAVenir()
```

## 🛠️ Comment ajouter une nouvelle capacité

### Étape 1 : Identifier les modèles nécessaires

**Exemple** : Répondre à "Combien j'ai de fiches de paie ?"

1. Vérifier si `FichePaieModel` existe
2. Lire la documentation ou le code pour trouver la méthode appropriée
3. Si la méthode n'existe pas, la créer dans le modèle (pas dans ChatbotModel !)

### Étape 2 : Ajouter le pattern de reconnaissance

Dans `ChatbotModel::analyzeQuestion()` :

```php
$patterns = [
    // ... patterns existants ...
    'fiche_paie' => '/fiche\s+de\s+paie|bulletin|salaire|r[ée]mun[ée]ration/i',
];
```

### Étape 3 : Ajouter l'initialisation du modèle

Dans `ChatbotModel::__construct()` :

```php
public function __construct()
{
    $this->db = Flight::db();
    $this->employeModel = new EmployeModel();
    // ... autres modèles ...
    $this->fichePaieModel = new FichePaieModel($this->db);
}
```

### Étape 4 : Créer la méthode de récupération

Dans `ChatbotModel`, ajouter :

```php
/**
 * Récupère les fiches de paie via FichePaieModel
 */
private function getFichePaieInfo($idEmploye = null)
{
    try {
        if ($idEmploye) {
            return $this->fichePaieModel->getFichesEmploye($idEmploye);
        } else {
            // Pour admin : toutes les fiches récentes
            return $this->fichePaieModel->getFichesRecentes(30); // 30 derniers jours
        }
    } catch (\Exception $e) {
        error_log('ChatbotModel::getFichePaieInfo: ' . $e->getMessage());
        return [];
    }
}
```

### Étape 5 : Ajouter le case dans getContextData()

```php
public function getContextData(string $type, $idEmploye = null): array
{
    $ctx = [];
    try {
        switch ($type) {
            // ... cases existants ...
            case 'fiche_paie':
                $ctx['fiches_paie'] = $this->getFichePaieInfo($idEmploye);
                break;
        }
    }
    // ...
}
```

## 🚀 Vers un Chatbot Universel

### Approche actuelle (manuelle)
```
Question → Pattern regex → Switch case → Méthode spécifique → Modèle
```

**Limitations** :
- Il faut ajouter manuellement chaque nouveau cas
- Pattern matching rigide
- Pas de composition automatique

### Approche avancée (future)

#### 1. **Découverte dynamique des modèles**

```php
// Scanner tous les modèles disponibles
private function discoverModels(): array
{
    $modelPath = __DIR__ . '/';
    $models = [];
    
    foreach (glob($modelPath . '*Model.php') as $file) {
        $className = basename($file, '.php');
        $reflection = new \ReflectionClass("app\\models\\$className");
        
        $models[$className] = [
            'methods' => $this->getPublicMethods($reflection),
            'description' => $this->extractDocBlock($reflection)
        ];
    }
    
    return $models;
}

// Obtenir toutes les méthodes publiques
private function getPublicMethods(\ReflectionClass $class): array
{
    $methods = [];
    foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
        if (!$method->isConstructor() && !$method->isStatic()) {
            $methods[] = [
                'name' => $method->getName(),
                'params' => $method->getParameters(),
                'doc' => $method->getDocComment()
            ];
        }
    }
    return $methods;
}
```

#### 2. **Invocation dynamique**

```php
/**
 * Appelle dynamiquement une méthode sur un modèle
 */
private function callModelMethod(string $modelName, string $method, array $params = [])
{
    // Vérifier si le modèle est déjà instancié
    $propertyName = lcfirst($modelName);
    
    if (!isset($this->$propertyName)) {
        // Instancier dynamiquement
        $fullClassName = "app\\models\\$modelName";
        $reflection = new \ReflectionClass($fullClassName);
        
        // Détecter si le constructeur nécessite $db
        $constructor = $reflection->getConstructor();
        if ($constructor && count($constructor->getParameters()) > 0) {
            $this->$propertyName = new $fullClassName($this->db);
        } else {
            $this->$propertyName = new $fullClassName();
        }
    }
    
    // Appeler la méthode
    return call_user_func_array([$this->$propertyName, $method], $params);
}
```

#### 3. **Intelligence avec Gemini pour choisir les appels**

Au lieu de patterns regex, envoyer à Gemini une première requête :

```php
/**
 * Demander à Gemini quelle(s) fonction(s) appeler
 */
private function planifyActions(string $question): array
{
    $availableFunctions = $this->discoverModels();
    
    $prompt = "Tu es un planificateur d'actions. Voici une question et les fonctions disponibles.\n\n";
    $prompt .= "QUESTION: $question\n\n";
    $prompt .= "FONCTIONS DISPONIBLES:\n";
    $prompt .= json_encode($availableFunctions, JSON_PRETTY_PRINT);
    $prompt .= "\n\nRéponds au format JSON avec la liste des fonctions à appeler et leurs paramètres:\n";
    $prompt .= '{"actions": [{"model": "EmployeModel", "method": "listWithDetails", "params": []}]}';
    
    // Appel API Gemini
    $response = $this->callGemini($prompt);
    return json_decode($response, true)['actions'] ?? [];
}

/**
 * Exécuter les actions planifiées
 */
private function executeActions(array $actions): array
{
    $results = [];
    foreach ($actions as $action) {
        $results[$action['method']] = $this->callModelMethod(
            $action['model'],
            $action['method'],
            $action['params']
        );
    }
    return $results;
}
```

#### 4. **Utilisation finale**

```php
public function generateReply(string $question): string
{
    // 1. Planifier les actions à exécuter
    $actions = $this->planifyActions($question);
    
    // 2. Exécuter les actions
    $data = $this->executeActions($actions);
    
    // 3. Formater et demander la réponse finale à Gemini
    $contextText = $this->formatContextForAPI($data);
    return $this->callGemini($contextText . "\nQUESTION: $question");
}
```

## 📊 Comparaison des approches

| Aspect              | Approche Actuelle ✅       | Approche Avancée 🚀              |
|---------------------|---------------------------|----------------------------------|
| **Ajout de fonctions** | Manuel (code PHP)     | Automatique (réflexion)          |
| **Flexibilité**     | Limitée (patterns fixes) | Très flexible (IA décide)        |
| **Maintenance**     | Moyenne                  | Faible                           |
| **Complexité**      | Simple                   | Complexe                         |
| **Coût API**        | 1 appel Gemini           | 2 appels Gemini (plan + réponse) |
| **Fiabilité**       | Haute                    | Moyenne (dépend de l'IA)         |

## 🎓 Recommandations

### Pour l'immédiat (Solution actuelle améliorée)
1. ✅ **Inventorier** tous les modèles et leurs méthodes publiques
2. ✅ **Documenter** chaque modèle avec PHPDoc clair
3. ✅ **Créer une matrice** : Question type → Modèles → Méthodes
4. ✅ **Ajouter progressivement** les patterns et méthodes dans `ChatbotModel`

### Pour le futur (Approche dynamique)
1. 🔮 **Implémenter** la découverte automatique des modèles
2. 🔮 **Tester** l'invocation dynamique avec des cas simples
3. 🔮 **Créer** un système de cache pour les métadonnées de modèles
4. 🔮 **Utiliser** Gemini pour planifier les actions (fonction calling)
5. 🔮 **Monitorer** les coûts API (2 appels au lieu de 1)

## 📝 Template de documentation pour nouveaux modèles

Chaque modèle devrait avoir un bloc de commentaire :

```php
/**
 * FichePaieModel - Gestion des fiches de paie
 * 
 * @chatbot-capable
 * 
 * Méthodes utilisables par le chatbot :
 * - getFichesEmploye($idEmploye): Récupère toutes les fiches d'un employé
 * - getFicheById($idFiche): Récupère une fiche spécifique
 * - getFichesRecentes($jours): Récupère les fiches créées dans les N derniers jours
 * 
 * Questions supportées :
 * - "Combien j'ai de fiches de paie ?"
 * - "Ma dernière fiche de paie ?"
 * - "Fiches de paie du mois dernier ?"
 */
class FichePaieModel { ... }
```

## 🔗 Ressources

- **PHPDoc** : https://docs.phpdoc.org/
- **ReflectionClass** : https://www.php.net/manual/en/class.reflectionclass.php
- **Gemini Function Calling** : https://ai.google.dev/gemini-api/docs/function-calling

---

**Prochain objectif** : Permettre au chatbot de composer **n'importe quelle combinaison** de fonctions pour répondre à des questions complexes.

Exemple :
> "Combien d'employés du département RH ont des congés en attente et sont absents cette semaine ?"

Nécessite :
1. `DepartementModel::getBy('nom', 'RH')` → id département
2. `EmployeModel::getEmployesByDepartement($idDept)` → liste employés
3. Pour chaque employé :
   - `CongeModel::getDonneesConges($id)` → filtrer `est_valide = null`
   - `PointageModel::creerReleverPresenceIndividuelle($id, $debut, $fin)` → vérifier absences
4. Croiser les résultats → réponse finale

Cette composition est possible avec l'approche avancée !
