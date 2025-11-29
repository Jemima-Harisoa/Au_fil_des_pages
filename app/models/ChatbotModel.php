<?php
namespace app\models;

use Flight;
use ReflectionClass;
use ReflectionMethod;

class ChatbotModel
{
    private $db;
    private $models = [];
    private $modelSchemas = [];

    public function __construct()
    {
        $this->db = Flight::db();
        
        // Initialiser les modèles existants
        $this->registerModel('employe', new EmployeModel());
        $this->registerModel('pointage', new PointageModel($this->db));
        $this->registerModel('departement', new DepartementModel());
        $this->registerModel('conge', new \app\models\conge\CongeModel($this->db));
        
        // Analyser automatiquement les méthodes disponibles
        $this->buildModelSchemas();
    }

    /**
     * Enregistre un nouveau modèle
     */
    private function registerModel(string $name, object $model): void
    {
        $this->models[$name] = $model;
    }

    /**
     * Construit un schéma des méthodes disponibles pour chaque modèle
     */
    private function buildModelSchemas(): void
    {
        foreach ($this->models as $modelName => $modelInstance) {
            $reflection = new ReflectionClass($modelInstance);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            $this->modelSchemas[$modelName] = [
                'class' => get_class($modelInstance),
                'methods' => []
            ];
            
            foreach ($methods as $method) {
                // Ignorer les constructeurs et méthodes magiques
                if ($method->isConstructor() || strpos($method->getName(), '__') === 0) {
                    continue;
                }
                
                $params = [];
                foreach ($method->getParameters() as $param) {
                    $params[] = [
                        'name' => $param->getName(),
                        'optional' => $param->isOptional(),
                        'type' => $param->hasType() ? $param->getType()->getName() : 'mixed'
                    ];
                }
                
                $this->modelSchemas[$modelName]['methods'][$method->getName()] = [
                    'params' => $params,
                    'static' => $method->isStatic()
                ];
            }
        }
    }

    /**
     * Génère un descriptif des fonctions disponibles pour l'IA
     */
    private function generateFunctionCatalog(): string
    {
        $catalog = "FONCTIONS_DISPONIBLES:\n\n";
        
        foreach ($this->modelSchemas as $modelName => $schema) {
            $catalog .= strtoupper($modelName) . " MODEL:\n";
            
            foreach ($schema['methods'] as $methodName => $details) {
                $paramsList = [];
                foreach ($details['params'] as $param) {
                    $optional = $param['optional'] ? '?' : '';
                    $paramsList[] = "{$param['type']} \${$param['name']}{$optional}";
                }
                $paramsStr = implode(', ', $paramsList);
                
                $catalog .= "  - {$methodName}({$paramsStr})\n";
            }
            $catalog .= "\n";
        }
        
        return $catalog;
    }

    /**
     * Analyse la question et détermine les fonctions à appeler
     */
    public function analyzeQuestion(string $question): array
    {
        $q = strtolower($question);
        $analysis = [
            'type' => 'general',
            'keywords' => [],
            'entities' => []
        ];

        // Détection des types de questions
        $patterns = [
            'conge' => '/cong[ée]|vacances|absence|solde\s+de\s+cong/i',
            'pointage' => '/pointage|pr[ée]sence|heures|horaire|retard/i',
            'employe' => '/employ[ée]|coll[èè]gue|personne|liste\s+des/i',
            'departement' => '/d[ée]partement|service|[ée]quipe/i',
            'paie' => '/salaire|paie|r[ée]mun[ée]ration/i',
            'statistiques' => '/combien|nombre|total|statistique/i',
            'historique' => '/historique|mouvement|mobilit[ée]/i',
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $q)) {
                $analysis['type'] = $type;
                $analysis['keywords'][] = $type;
            }
        }

        return $analysis;
    }

    /**
     * Exécute une fonction d'un modèle de manière dynamique
     */
    private function executeModelFunction(string $modelName, string $methodName, array $params = []): mixed
    {
        if (!isset($this->models[$modelName])) {
            throw new \Exception("Modèle '{$modelName}' non trouvé");
        }

        if (!isset($this->modelSchemas[$modelName]['methods'][$methodName])) {
            throw new \Exception("Méthode '{$methodName}' non trouvée dans le modèle '{$modelName}'");
        }

        $model = $this->models[$modelName];
        $methodInfo = $this->modelSchemas[$modelName]['methods'][$methodName];

        try {
            if ($methodInfo['static']) {
                $class = $this->modelSchemas[$modelName]['class'];
                return call_user_func_array([$class, $methodName], $params);
            } else {
                return call_user_func_array([$model, $methodName], $params);
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de l'exécution de {$modelName}::{$methodName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupère les données contextuelles de manière intelligente
     */
    public function getContextData(array $analysis, $idEmploye = null): array
    {
        $ctx = [];
        $type = $analysis['type'];

        try {
            switch ($type) {
                case 'conge':
                    if ($idEmploye) {
                        $ctx['solde_conge'] = $this->executeModelFunction('conge', 'getNombreConge', [$idEmploye]);
                        $ctx['demandes_conge'] = $this->executeModelFunction('conge', 'getDonneesConges', [$idEmploye]);
                    }
                    break;
                    
                case 'pointage':
                    if ($idEmploye) {
                        $ctx['dernier_pointage'] = $this->executeModelFunction('pointage', 'getDernierPointage', [$idEmploye]);
                    } else {
                        $ctx['presences'] = $this->executeModelFunction('pointage', 'getAllPresence', []);
                    }
                    break;
                    
                case 'employe':
                    $ctx['employes'] = $this->executeModelFunction('employe', 'listWithDetails', []);
                    if ($idEmploye) {
                        $ctx['employe_details'] = $this->executeModelFunction('employe', 'getEmployesWithDetails', [$idEmploye]);
                    }
                    break;
                    
                case 'departement':
                    $ctx['departements'] = $this->executeModelFunction('departement', 'list', []);
                    break;

                case 'historique':
                    if ($idEmploye) {
                        $ctx['historique_mobilite'] = $this->executeModelFunction('employe', 'getEmployerHistoriqueMouvement', [$idEmploye]);
                    }
                    break;
                    
                case 'statistiques':
                case 'general':
                    $ctx['departements'] = $this->executeModelFunction('departement', 'list', []);
                    $ctx['employes'] = $this->executeModelFunction('employe', 'listWithDetails', []);
                    $ctx['employes_count'] = count($ctx['employes'] ?? []);
                    break;
            }
        } catch (\Exception $e) {
            error_log("ChatbotModel::getContextData error: " . $e->getMessage());
        }

        return $ctx;
    }

    /**
     * Formate le contexte pour l'API de manière structurée
     */
    public function formatContextForAPI(array $context): string
    {
        $out = "DONNEES_RH:\n\n";
        
        foreach ($context as $section => $data) {
            $out .= "=== " . strtoupper(str_replace('_', ' ', $section)) . " ===\n";
            
            if (is_array($data) && empty($data)) {
                $out .= "Aucune donnée disponible.\n\n";
                continue;
            }
            
            if (is_array($data)) {
                foreach ($data as $idx => $item) {
                    if (is_array($item)) {
                        $out .= "\nEnregistrement " . ($idx + 1) . ":\n";
                        foreach ($item as $k => $v) {
                            $out .= "  - {$k}: " . (is_null($v) ? 'N/A' : $v) . "\n";
                        }
                    } else {
                        $out .= "  - {$item}\n";
                    }
                }
            } else {
                $out .= "  {$data}\n";
            }
            
            $out .= "\n";
        }
        
        return $out;
    }

    /**
     * Génère la réponse avec composition intelligente de fonctions
     */
    public function generateReply(string $question): string
    {
        // Analyser la question
        $analysis = $this->analyzeQuestion($question);
        
        // Récupérer l'ID employé
        $idEmp = $_SESSION['employe']['id_employe'] ?? ($_SESSION['infoAdmin']['id'] ?? null);
        
        // Récupérer le contexte
        $context = $this->getContextData($analysis, $idEmp);
        $contextText = $this->formatContextForAPI($context);
        
        // Générer le catalogue de fonctions
        $functionCatalog = $this->generateFunctionCatalog();

        // Construire le prompt système enrichi
        $systemPrompt = <<<PROMPT
Tu es un assistant RH professionnel et intelligent pour l'entreprise 'Au fil des pages'.

INSTRUCTIONS IMPORTANTES:
1. Utilise UNIQUEMENT les données fournies ci-dessous pour répondre
2. Si les données nécessaires ne sont pas disponibles, dis-le clairement
3. Sois précis, concis et professionnel
4. Fournis des réponses structurées et faciles à comprendre
5. Si tu détectes qu'une fonction spécifique serait utile mais n'est pas dans les données, suggère-la

{$functionCatalog}

{$contextText}

QUESTION DE L'UTILISATEUR: {$question}

Réponds de manière claire et professionnelle.
PROMPT;

        // Appel API Gemini
        $apiKey = "AIzaSyAbXblX7AEQ_h6RnSjb4C7uhqqazJr6UvA";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent";

        $payload = [
            'contents' => [
                ['parts' => [['text' => $systemPrompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 800,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POST => true
        ]);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            curl_setopt($ch, CURLOPT_CAINFO, false);
            curl_setopt($ch, CURLOPT_CAPATH, false);
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        error_log('ChatbotModel::generateReply HTTP ' . $httpCode . ' curlErr:' . $curlErr);

        if ($httpCode === 0) {
            throw new \Exception('Erreur de connexion: ' . $curlErr);
        }

        if ($httpCode !== 200) {
            $err = json_decode($result, true);
            $msg = $err['error']['message'] ?? ('HTTP ' . $httpCode);
            throw new \Exception('Erreur API: ' . $msg);
        }

        $decoded = json_decode($result, true);
        if (!$decoded) {
            throw new \Exception('Réponse JSON invalide');
        }

        // Extraction de la réponse
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($decoded['candidates'][0]['content']['parts'][0]['text']);
        }

        if (isset($decoded['candidates'][0]['parts'][0]['text'])) {
            return trim($decoded['candidates'][0]['parts'][0]['text']);
        }

        if (isset($decoded['candidates'][0]['finishReason']) && 
            $decoded['candidates'][0]['finishReason'] === 'MAX_TOKENS') {
            return "Réponse partielle (limite de tokens atteinte). Reformulez votre question de manière plus concise.";
        }

        error_log('ChatbotModel::generateReply structure inattendue: ' . print_r($decoded, true));
        throw new \Exception('Format de réponse API inattendu');
    }

    /**
     * Permet d'ajouter un nouveau modèle dynamiquement
     */
    public function addModel(string $name, object $model): void
    {
        $this->registerModel($name, $model);
        $this->buildModelSchemas(); // Reconstruire le schéma
    }

    /**
     * Retourne la liste des modèles disponibles
     */
    public function getAvailableModels(): array
    {
        return array_keys($this->models);
    }

    /**
     * Retourne les méthodes disponibles pour un modèle
     */
    public function getModelMethods(string $modelName): array
    {
        return $this->modelSchemas[$modelName]['methods'] ?? [];
    }
}