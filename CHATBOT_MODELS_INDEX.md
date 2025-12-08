# Index des Modèles - Méthodes disponibles pour le Chatbot

Ce fichier liste toutes les méthodes des modèles qui peuvent être utilisées par le chatbot.
Cherchez le tag `[CHATBOT]` dans les fichiers sources pour plus de détails.

## 🎯 CongeModel (`app/models/conge/CongeModel.php`)

### Méthodes utilisables

```php
// [CHATBOT] Questions: "Mes congés?", "Combien de jours pris?", "Types de congés?"
$this->congeModel->getDonneesConges($idEmploye);
// Retourne: array avec type_conge, jours_pris, jours_totaux, jours_restants

// [CHATBOT] Questions: "Mon solde de congés?", "Combien de congés me reste?"
$this->congeModel->getNombreConge($idEmploye);
// Retourne: array formaté avec pourcentages et détails par type
```

### Autres méthodes disponibles
- `getDemandeConge($idDemande)` - Détails d'une demande
- `getDonneesCongesParType($idEmploye)` - Congés groupés par type
- `absences($idEmploye, $estAutorise)` - Liste des absences

---

## 📊 PointageModel (`app/models/PointageModel.php`)

### Méthodes utilisables

```php
// [CHATBOT] Questions: "Mon dernier pointage?", "Suis-je connecté?"
$this->pointageModel->getDernierPointage($idEmploye);
// Retourne: array avec connexion, deconnexion, duree_session

// [CHATBOT] Questions: "Qui est présent?", "Liste des présences"
$this->pointageModel->getAllPresence();
// Retourne: array avec tous les employés et leurs pointages
```

### Autres méthodes disponibles
- `ajouterPointage($idEmploye)` - Créer nouveau pointage
- `cloturerPointage($idPointage)` - Fermer un pointage
- `creerReleverPresenceIndividuelle($id, $debut, $fin)` - Relevé période
- `creerReleverPresenceGroupe($idDept, $debut, $fin)` - Relevé groupe

---

## 👥 EmployeModel (`app/models/EmployeModel.php`)

### Méthodes utilisables

```php
// [CHATBOT] Questions: "Liste des employés?", "Qui travaille où?", "Informations employés?"
$this->employeModel->listWithDetails();
// Retourne: array avec id, nom, prenom, poste, departement, date_embauche, contact, etc.
```

### Autres méthodes disponibles
- `getEmployesWithDetails($idEmploye)` - Détails d'un employé spécifique
- `listeEmployerV2()` - Liste alternative avec contrat
- `getEmployerHistoriqueMouvement($id)` - Historique mouvements
- `verifierEmploye($prenom, $mdp)` - Authentification
- `getInfosEmploye($idEmploye)` - Infos basiques
- `getFicheEmploye($idEmploye)` - Fiche complète

---

## 🏢 DepartementModel (`app/models/DepartementModel.php`)

### Méthodes utilisables

```php
// [CHATBOT] Questions: "Quels départements?", "Liste des services?"
$this->departementModel->list();
// Retourne: array avec id_departement, nom
```

### Autres méthodes disponibles
- `findById($id)` - Trouver par ID
- `search($field, $value)` - Rechercher
- `getBy($field, $value)` - Récupérer par champ
- `save($data)` - Créer
- `updateById($id, $data)` - Modifier
- `deleteById($id)` - Supprimer

---

## 💰 FichePaieModel (`app/models/FichePaieModel.php`)

### Méthodes potentielles (à vérifier dans le fichier)

```php
// À documenter avec [CHATBOT] si disponibles
$this->fichePaieModel->getFichesEmploye($idEmploye);
$this->fichePaieModel->getDerniereFiche($idEmploye);
$this->fichePaieModel->getFicheById($id);
```

---

## 📅 PlanningEntretienModel (`app/models/PlanningEntretienModel.php`)

### Méthodes potentielles (à vérifier dans le fichier)

```php
// À documenter avec [CHATBOT] si disponibles
$this->planningEntretienModel->getEntretiensEmploye($idEmploye);
$this->planningEntretienModel->getEntretiensAVenir();
```

---

## 💵 PrimeModel (`app/models/PrimeModel.php`)

### Méthodes potentielles (à vérifier dans le fichier)

```php
// À documenter avec [CHATBOT] si disponibles
$this->primeModel->getPrimesEmploye($idEmploye);
$this->primeModel->getPrimesParPeriode($debut, $fin);
```

---

## 💼 HeureSupplementaireModel (`app/models/HeureSupplementaireModel.php`)

### Méthodes potentielles (à vérifier dans le fichier)

```php
// À documenter avec [CHATBOT] si disponibles
$this->heureSupplementaireModel->getHeuresEmploye($idEmploye);
$this->heureSupplementaireModel->getHistorique($idEmploye);
```

---

## 📨 MessagerieModel (`app/models/MessagerieModel.php`)

### Méthodes potentielles (à vérifier dans le fichier)

```php
// À documenter avec [CHATBOT] si disponibles
$this->messagerieModel->getConversations($idEmploye);
$this->messagerieModel->getMessages($idConversation);
```

---

## 🔍 Comment trouver les méthodes ?

### 1. Recherche par tag `[CHATBOT]`
```bash
# Dans VS Code, chercher dans les fichiers :
[CHATBOT]
```

### 2. Lire la documentation dans le modèle
Ouvrez le fichier du modèle et cherchez les commentaires au-dessus des méthodes publiques.

### 3. Exemples de questions supportées
Les commentaires `[CHATBOT]` incluent des exemples de questions que l'utilisateur peut poser.

---

## 📝 Convention de documentation

Pour ajouter une nouvelle méthode utilisable par le chatbot :

```php
/**
 * [CHATBOT] Description courte de la méthode
 * Questions supportées: "Question 1?", "Question 2?", "Question 3?"
 * @param type $param Description du paramètre
 * @return type Description du retour (format exact des données)
 */
public function maMethode($param) {
    // ...
}
```

---

## 🚀 Utilisation dans ChatbotModel

Le ChatbotModel pointe simplement vers ces méthodes :

```php
case 'conge':
    // @see \app\models\conge\CongeModel::getNombreConge()
    // @see \app\models\conge\CongeModel::getDonneesConges()
    $ctx['solde'] = $this->congeModel->getNombreConge($idEmploye);
    $ctx['demandes'] = $this->congeModel->getDonneesConges($idEmploye);
    break;
```

**Pas de requêtes SQL dans ChatbotModel** - Tout passe par les modèles existants !

---

## ✅ Avantages de cette architecture

1. **DRY** : Pas de duplication de code
2. **Maintenance** : Modifications uniquement dans les modèles métier
3. **Documentation** : Tag `[CHATBOT]` facilite la recherche
4. **Évolutivité** : Ajouter un nouveau cas = pointer vers un modèle existant
5. **Cohérence** : Même logique partout dans l'application

---

**Dernière mise à jour** : 2025-11-29
