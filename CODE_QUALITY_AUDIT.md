# 🎓 Audit de Code - Vision Mentor Bienveillant

**Date:** 20 novembre 2025  
**Projet:** TCS Chaudronnerie - Plateforme de Recrutement  
**Auditeur:** Expert Senior PHP/Architecture

---

## 👋 Introduction

Félicitations pour votre travail ! Votre application fonctionne et respecte globalement l'architecture MVC. C'est une excellente base. Maintenant, prenons le temps d'améliorer la **clarté**, la **maintenabilité** et la **simplicité** de votre code.

> 💡 **Philosophie:** Un bon code se lit comme un livre. Si quelqu'un d'autre (ou vous dans 6 mois) peut comprendre rapidement ce qui se passe, c'est gagné !

---

## 📊 Vue d'Ensemble - Points Forts

### ✅ Ce qui est TRÈS bien

1. **Architecture MVC respectée**
   - Séparation claire Controller / Model / View
   - Chaque couche a son rôle défini

2. **Namespaces utilisés**
   - Organisation logique avec `App\Controller`, `App\Model`, `App\View`

3. **PDO et requêtes préparées**
   - Protection SQL Injection en place

4. **Type declarations**
   - `declare(strict_types=1)` activé ✅
   - Types sur les propriétés et méthodes

5. **Gestion d'erreurs**
   - Try/catch présents
   - Messages utilisateur clairs

---

## 🔍 Audit Détaillé par Domaine

### 1. 🎯 ROUTING (index.php)

#### ❌ Problèmes Actuels

```php
// ❌ Trop complexe, difficile à lire
$action = $_GET['action'] ?? ($segments[0] ?? '');
$step   = $_GET['step']   ?? ($segments[1] ?? '');
$id     = $_GET['id']     ?? ($segments[2] ?? '');
```

#### ✅ Solution Proposée: Classe Router

**Créer:** `app/Router.php`

```php
<?php
namespace App;

class Router
{
    private string $action;
    private string $step;
    private string $id;
    private array $queryParams;

    public function __construct()
    {
        $this->parseRequest();
    }

    private function parseRequest(): void
    {
        // Parse URL segments
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = array_values(array_filter(explode('/', $uri)));

        // Assign with clear priority
        $this->action = $_GET['action'] ?? ($segments[0] ?? 'accueil');
        $this->step = $_GET['step'] ?? ($segments[1] ?? '');
        $this->id = $_GET['id'] ?? ($segments[2] ?? '');
        $this->queryParams = $_GET;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getStep(): string
    {
        return $this->step;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdAsInt(): int
    {
        return (int) $this->id;
    }

    public function isPublicPage(): bool
    {
        $publicPages = ['accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact'];
        return in_array($this->action, $publicPages, true);
    }

    public function isApiCall(): bool
    {
        return $this->action === 'administrateur' && $this->step === 'api-rdv';
    }
}
```

**Usage dans index.php:**

```php
$router = new Router();
$action = $router->getAction();
$step = $router->getStep();
$id = $router->getIdAsInt();

// Plus lisible !
if ($router->isApiCall()) {
    // Pas de layout
}
```

---

### 2. 📝 SEO Configuration

#### ❌ Problème: Trop dans index.php

Le tableau `$metaByAction` avec 300+ caractères par description rend index.php illisible.

#### ✅ Solution: Classe SeoConfig

**Créer:** `app/Config/SeoConfig.php`

```php
<?php
namespace App\Config;

class SeoConfig
{
    private const PUBLIC_PAGES = ['accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact'];

    private const META_DATA = [
        'accueil' => [
            'title' => "Page d'accueil",
            'description' => "Spécialistes en chaudronnerie, tuyauterie et soudure...",
        ],
        'bureauEtude' => [
            'title' => "Bureau d'études — TCS Chaudronnerie",
            'description' => "Conception, ingénierie, dossiers techniques...",
        ],
        // ... autres pages
    ];

    private const DEFAULT_META = [
        'title' => "TCS Chaudronnerie",
        'description' => "Solutions de chaudronnerie, tuyauterie et soudure pour l'industrie.",
        'robots' => 'noindex, nofollow',
    ];

    public static function getMetaForAction(string $action): array
    {
        $meta = self::META_DATA[$action] ?? self::DEFAULT_META;
        
        // Add canonical
        $meta['canonical'] = self::generateCanonicalUrl();
        
        // Set robots
        $meta['robots'] = self::isPublicPage($action) ? 'index, follow' : 'noindex, nofollow';
        
        return $meta;
    }

    public static function getMetaForAnnonce(int $id): array
    {
        return [
            'title' => "Offre #$id — TCS Chaudronnerie",
            'description' => "Découvrez l'offre d'emploi #$id chez TCS Chaudronnerie. Postulez dès maintenant.",
            'canonical' => self::generateCanonicalUrl(),
            'robots' => 'index, follow',
        ];
    }

    private static function isPublicPage(string $action): bool
    {
        return in_array($action, self::PUBLIC_PAGES, true);
    }

    private static function generateCanonicalUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        
        return rtrim("$protocol://$host", '/') . $path;
    }
}
```

**Usage:**

```php
// Dans index.php - BEAUCOUP plus simple !
$SEO = SeoConfig::getMetaForAction($action);

// Pour une annonce spécifique
if ($action === 'annonce' && $step === 'view' && $id) {
    $SEO = SeoConfig::getMetaForAnnonce((int)$id);
}
```

---

### 3. 🎮 CONTROLLERS - Améliorer la Clarté

#### ❌ Problème: Logique métier dans les contrôleurs

**Exemple dans AnnonceController.php:**

```php
// ❌ Validation dans le controller
if (empty($_POST[$field])) {
    throw new Exception("Le champ '$field' est requis.");
}
```

#### ✅ Solution: Classe Validator + Variables explicites

**Créer:** `app/Validator/AnnonceValidator.php`

```php
<?php
namespace App\Validator;

class AnnonceValidator
{
    private array $errors = [];
    private const REQUIRED_FIELDS = [
        'titre', 'description', 'mission', 'localisation', 
        'code_postale', 'secteur_activite', 'type_contrat'
    ];

    public function validate(array $data): bool
    {
        $this->errors = [];

        // Vérification des champs requis
        $this->validateRequiredFields($data);
        
        // Validations spécifiques
        $this->validateTitre($data['titre'] ?? '');
        $this->validateCodePostal($data['code_postale'] ?? '');
        $this->validateSalaire($data['salaire'] ?? '');

        return empty($this->errors);
    }

    private function validateRequiredFields(array $data): void
    {
        foreach (self::REQUIRED_FIELDS as $field) {
            if (empty($data[$field])) {
                $this->errors[] = "Le champ '$field' est requis.";
            }
        }
    }

    private function validateTitre(string $titre): void
    {
        if (strlen($titre) < 5) {
            $this->errors[] = "Le titre doit contenir au moins 5 caractères.";
        }
        
        if (strlen($titre) > 200) {
            $this->errors[] = "Le titre ne peut pas dépasser 200 caractères.";
        }
    }

    private function validateCodePostal(string $code): void
    {
        if (!preg_match('/^[0-9]{5}$/', $code)) {
            $this->errors[] = "Le code postal doit contenir 5 chiffres.";
        }
    }

    private function validateSalaire(string $salaire): void
    {
        // Format attendu: "30K - 40K" ou "30000€"
        if (empty($salaire)) {
            $this->errors[] = "Le salaire est requis.";
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsAsString(): string
    {
        return implode('<br>', $this->errors);
    }
}
```

**Usage dans AnnonceController:**

```php
use App\Validator\AnnonceValidator;
use App\Security;

public function createAnnonce(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->view->renderForm('create');
        return;
    }

    // 1. Validation CSRF
    if (!Security::validateRequest()) {
        $this->showError("Erreur de sécurité. Veuillez réessayer.");
        return;
    }

    // 2. Validation des données
    $validator = new AnnonceValidator();
    if (!$validator->validate($_POST)) {
        $this->showErrors($validator->getErrors());
        $this->view->renderForm('create', $_POST); // Garde les données saisies
        return;
    }

    // 3. Création
    try {
        $annonceCreee = $this->model->create($_POST);
        $this->redirectWithSuccess('Annonce créée avec succès !', '/administrateur/annonces');
    } catch (\Exception $e) {
        $this->showError("Erreur lors de la création: " . $e->getMessage());
    }
}

// Méthodes helper pour clarté
private function showError(string $message): void
{
    echo "<div class='alert alert-danger'>⚠️ $message</div>";
}

private function showErrors(array $errors): void
{
    foreach ($errors as $error) {
        $this->showError($error);
    }
}

private function redirectWithSuccess(string $message, string $url): void
{
    $_SESSION['flash_success'] = $message;
    header("Location: $url");
    exit;
}
```

---

### 4. 🗄️ MODELS - Variables Explicites

#### ❌ Problème: Arrays avec clés magiques

```php
// ❌ On ne sait pas quelles clés sont disponibles
$annonce = $this->model->getById($id);
echo $annonce['titre']; // Et si la clé n'existe pas ?
```

#### ✅ Solution: Value Objects (DTO - Data Transfer Objects)

**Créer:** `app/Entity/Annonce.php`

```php
<?php
namespace App\Entity;

class Annonce
{
    public function __construct(
        public readonly int $id,
        public readonly string $titre,
        public readonly string $description,
        public readonly string $mission,
        public readonly string $localisation,
        public readonly string $codePostal,
        public readonly string $salaire,
        public readonly string $typeContrat,
        public readonly string $statut,
        public readonly string $datePublication,
        public readonly ?string $avantages = null,
        public readonly ?string $profilRecherche = null,
        public readonly ?string $secteurActivite = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            titre: $data['titre'] ?? '',
            description: $data['description'] ?? '',
            mission: $data['mission'] ?? '',
            localisation: $data['localisation'] ?? '',
            codePostal: $data['code_postale'] ?? '',
            salaire: $data['salaire'] ?? '',
            typeContrat: $data['type_contrat'] ?? '',
            statut: $data['statut'] ?? 'brouillon',
            datePublication: $data['date_publication'] ?? date('Y-m-d'),
            avantages: $data['avantages'] ?? null,
            profilRecherche: $data['profil_recherche'] ?? null,
            secteurActivite: $data['secteur_activite'] ?? null,
        );
    }

    public function isActive(): bool
    {
        return $this->statut === 'activée';
    }

    public function isArchived(): bool
    {
        return $this->statut === 'archivée';
    }

    public function getFormattedSalary(): string
    {
        return number_format((float)$this->salaire, 0, ',', ' ') . ' €';
    }
}
```

**Modification dans AnnonceModel:**

```php
use App\Entity\Annonce;

public function getById(int $id): ?Annonce
{
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    return $data ? Annonce::fromArray($data) : null;
}

/** @return Annonce[] */
public function getAll(): array
{
    $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY date_publication DESC");
    $results = $stmt->fetchAll();

    return array_map(fn($row) => Annonce::fromArray($row), $results);
}
```

**Usage dans Controller:**

```php
public function viewAnnonce(int $id): void
{
    $annonce = $this->model->getById($id);
    
    if (!$annonce) {
        $this->showError("Annonce introuvable");
        return;
    }

    // ✅ Autocomplétion dans l'IDE !
    echo $annonce->titre;
    echo $annonce->isActive() ? 'Active' : 'Inactive';
    echo $annonce->getFormattedSalary();
}
```

---

### 5. 🎨 VIEWS - Éviter le HTML dans les Controllers

#### ❌ Problème: echo HTML partout

```php
// ❌ Dans AnnonceView.php
echo "<div class='annonce'>";
echo "<h3>" . htmlspecialchars($annonce['titre']) . "</h3>";
echo "</div>";
```

#### ✅ Solution: Templates PHP séparés

**Créer:** `app/view/templates/annonce_liste.php`

```php
<section class="annonces-list">
    <h2>Annonces Disponibles</h2>
    
    <?php foreach ($annonces as $annonce): ?>
        <article class="annonce-card">
            <div class="annonce-header">
                <h3><?= htmlspecialchars($annonce->titre) ?></h3>
                <span class="badge badge-<?= $annonce->statut ?>">
                    <?= htmlspecialchars($annonce->statut) ?>
                </span>
            </div>
            
            <div class="annonce-body">
                <p class="localisation">
                    <i class="icon-location"></i>
                    <?= htmlspecialchars($annonce->localisation) ?>
                </p>
                
                <p class="salaire">
                    <i class="icon-money"></i>
                    <?= $annonce->getFormattedSalary() ?>
                </p>
                
                <p class="contrat">
                    <i class="icon-contract"></i>
                    <?= htmlspecialchars($annonce->typeContrat) ?>
                </p>
            </div>
            
            <div class="annonce-footer">
                <a href="/annonce/view/<?= $annonce->id ?>" class="btn btn-primary">
                    Voir détails
                </a>
                
                <?php if (isset($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] === 'candidat'): ?>
                    <form method="POST" action="/candidat/postuler?id=<?= $annonce->id ?>">
                        <?= Security::getCSRFInput() ?>
                        <button type="submit" class="btn btn-success">Postuler</button>
                    </form>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>
```

**Dans AnnonceView.php:**

```php
<?php
namespace App\View;

class AnnonceView
{
    private string $templatePath = __DIR__ . '/templates/';

    public function renderListe(array $annonces): void
    {
        require $this->templatePath . 'annonce_liste.php';
    }

    public function renderDetails(Annonce $annonce): void
    {
        require $this->templatePath . 'annonce_details.php';
    }

    public function renderForm(string $mode, ?Annonce $annonce = null): void
    {
        $isEdit = $mode === 'edit';
        require $this->templatePath . 'annonce_form.php';
    }
}
```

---

### 6. 🔧 CONSTANTS et Configuration

#### ❌ Problème: Valeurs magiques dispersées

```php
// ❌ Dans le code
if ($statut === 'activée') { ... }
if ($role === 'administrateur') { ... }
```

#### ✅ Solution: Classe de constantes

**Créer:** `app/Config/AppConstants.php`

```php
<?php
namespace App\Config;

class AppConstants
{
    // Statuts d'annonce
    public const ANNONCE_ACTIVE = 'activée';
    public const ANNONCE_BROUILLON = 'brouillon';
    public const ANNONCE_ARCHIVEE = 'archivée';

    public const ANNONCE_STATUTS = [
        self::ANNONCE_ACTIVE,
        self::ANNONCE_BROUILLON,
        self::ANNONCE_ARCHIVEE,
    ];

    // Rôles utilisateur
    public const ROLE_ADMIN = 'administrateur';
    public const ROLE_CANDIDAT = 'candidat';

    // Types de contrat
    public const CONTRAT_CDI = 'CDI';
    public const CONTRAT_CDD = 'CDD';
    public const CONTRAT_INTERIM = 'Intérim';
    public const CONTRAT_ALTERNANCE = 'Alternance';

    public const CONTRATS_DISPONIBLES = [
        self::CONTRAT_CDI,
        self::CONTRAT_CDD,
        self::CONTRAT_INTERIM,
        self::CONTRAT_ALTERNANCE,
    ];

    // Statuts candidature
    public const CANDIDATURE_ENVOYEE = 'envoyée';
    public const CANDIDATURE_CONSULTEE = 'consultée';
    public const CANDIDATURE_ENTRETIEN = 'entretien';
    public const CANDIDATURE_RECRUTE = 'recruté';
    public const CANDIDATURE_REFUSE = 'refusé';

    // Limites
    public const MAX_FILE_SIZE = 5242880; // 5 MB
    public const SESSION_TIMEOUT = 1800; // 30 minutes
    public const LOGIN_MAX_ATTEMPTS = 5;
}
```

**Usage:**

```php
use App\Config\AppConstants;

// ✅ Plus clair et sûr
if ($annonce->statut === AppConstants::ANNONCE_ACTIVE) {
    // ...
}

if (Security::hasRole(AppConstants::ROLE_ADMIN)) {
    // ...
}
```

---

### 7. 📦 Services Layer - Extraction de la Logique

#### ❌ Problème: Controllers trop chargés

Les controllers font trop de choses : validation, transformation, logique métier.

#### ✅ Solution: Service classes

**Créer:** `app/Service/AnnonceService.php`

```php
<?php
namespace App\Service;

use App\Model\AnnonceModel;
use App\Entity\Annonce;
use App\Validator\AnnonceValidator;
use App\Security;

class AnnonceService
{
    public function __construct(
        private AnnonceModel $model,
        private AnnonceValidator $validator
    ) {}

    public function creerAnnonce(array $data, int $adminId): array
    {
        // 1. Validation
        if (!$this->validator->validate($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        // 2. Enrichissement des données
        $data['id_administrateur'] = $adminId;
        $data['date_publication'] = date('Y-m-d');
        $data['statut'] = $data['statut'] ?? AppConstants::ANNONCE_BROUILLON;

        // 3. Sanitization
        $data = $this->sanitizeAnnonceData($data);

        // 4. Création
        try {
            $success = $this->model->create($data);
            
            if ($success) {
                Security::logSecurityEvent('ANNONCE_CREATED', [
                    'admin_id' => $adminId,
                    'titre' => $data['titre']
                ]);
            }

            return [
                'success' => $success,
                'errors' => []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['Erreur lors de la création: ' . $e->getMessage()]
            ];
        }
    }

    public function modifierAnnonce(int $id, array $data, int $adminId): array
    {
        // Vérifier que l'annonce existe
        $annonce = $this->model->getById($id);
        if (!$annonce) {
            return [
                'success' => false,
                'errors' => ['Annonce introuvable']
            ];
        }

        // Validation
        if (!$this->validator->validate($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        // Sanitization
        $data = $this->sanitizeAnnonceData($data);

        // Mise à jour
        try {
            $success = $this->model->update($id, $data);
            
            if ($success) {
                Security::logSecurityEvent('ANNONCE_UPDATED', [
                    'admin_id' => $adminId,
                    'annonce_id' => $id
                ]);
            }

            return [
                'success' => $success,
                'errors' => []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['Erreur lors de la modification: ' . $e->getMessage()]
            ];
        }
    }

    private function sanitizeAnnonceData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = Security::sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    public function obtenirAnnoncesActives(): array
    {
        return $this->model->getByStatus(AppConstants::ANNONCE_ACTIVE);
    }

    public function archiverAnnonce(int $id): bool
    {
        return $this->model->archive($id);
    }
}
```

**Controller simplifié:**

```php
use App\Service\AnnonceService;

class AnnonceController
{
    private AnnonceService $service;

    public function __construct()
    {
        $this->service = new AnnonceService(
            new AnnonceModel(),
            new AnnonceValidator()
        );
    }

    public function createAnnonce(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->view->renderForm('create');
            return;
        }

        if (!Security::validateRequest()) {
            $this->showError("Erreur de sécurité");
            return;
        }

        $adminId = $_SESSION['utilisateur']['id'];
        $result = $this->service->creerAnnonce($_POST, $adminId);

        if ($result['success']) {
            $this->redirectWithSuccess('Annonce créée !', '/administrateur/annonces');
        } else {
            $this->showErrors($result['errors']);
            $this->view->renderForm('create', $_POST);
        }
    }
}
```

---

## 🎯 Recommandations Prioritaires

### 🔴 URGENT (Cette semaine)

1. **Extraire la configuration SEO** → Classe `SeoConfig`
2. **Créer classe Router** → Simplifie index.php
3. **Ajouter constantes** → Classe `AppConstants`

### 🟡 IMPORTANT (Ce mois)

4. **Value Objects (DTO)** → Classe `Annonce`, `Candidature`, `Utilisateur`
5. **Validators séparés** → Une classe par entité
6. **Templates PHP** → Séparer HTML des Views

### 🟢 AMÉLIORATION CONTINUE

7. **Service Layer** → Extraction logique métier
8. **Tests unitaires** → PHPUnit
9. **Documentation** → PHPDoc sur toutes les méthodes

---

## 📚 Exemples de Refactoring Progressif

### Étape 1: Simplifier index.php (1-2 heures)

**Avant (200+ lignes):**
```php
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $requestUri)));
$action = $_GET['action'] ?? ($segments[0] ?? '');
// ... 50 lignes de routing
// ... 100 lignes de SEO
```

**Après (30 lignes):**
```php
$router = new Router();
$seo = SeoConfig::getMetaForAction($router->getAction());

// Routing clair
match($router->getAction()) {
    'accueil' => include 'Pages/accueil.php',
    'administrateur' => (new AdministrateurController)->route($router),
    'annonce' => (new AnnonceController)->route($router),
    // ...
};
```

### Étape 2: Améliorer un Controller (2-3 heures)

**Choisir AnnonceController** (le plus complexe)

1. Créer `AnnonceValidator`
2. Créer `Annonce` entity
3. Créer templates dans `view/templates/`
4. Nettoyer le controller

### Étape 3: Ajouter les constantes (30 min)

1. Créer `AppConstants`
2. Remplacer toutes les chaînes magiques
3. Profit !

---

## 💡 Bonnes Pratiques à Appliquer

### 1. Nommage Explicite

```php
// ❌ Mauvais
$data = $this->model->get($id);
$ok = $this->do($x);

// ✅ Bon
$annonce = $this->model->getAnnonceById($id);
$isCreated = $this->annonceService->createAnnonce($formData);
```

### 2. Méthodes Courtes (Max 20 lignes)

```php
// ❌ Méthode trop longue
public function create() {
    // Validation
    // Sanitization  
    // Création
    // Email
    // Log
    // Redirect
    // 80 lignes...
}

// ✅ Méthodes courtes et claires
public function create() {
    $this->validateRequest();
    $data = $this->prepareData($_POST);
    $annonce = $this->service->create($data);
    $this->sendNotifications($annonce);
    $this->redirectToList();
}
```

### 3. Commentaires Utiles

```php
// ❌ Commentaires inutiles
// Récupère l'annonce
$annonce = $this->getAnnonce();

// ✅ Commentaires qui expliquent le POURQUOI
// On normalise en minuscules car la BDD est sensible à la casse
// et les anciennes données peuvent avoir des majuscules
$statut = mb_strtolower($statut);
```

### 4. Gestion d'Erreurs Cohérente

```php
// ✅ Créer une classe de réponse standardisée
class ServiceResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly array $errors = [],
        public readonly mixed $data = null
    ) {}

    public static function success(mixed $data = null): self
    {
        return new self(true, [], $data);
    }

    public static function error(array|string $errors): self
    {
        $errorsArray = is_string($errors) ? [$errors] : $errors;
        return new self(false, $errorsArray);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
```

### 5. Dependency Injection

```php
// ❌ Couplage fort
class AnnonceController {
    public function __construct() {
        $this->model = new AnnonceModel(); // Difficile à tester
    }
}

// ✅ Injection de dépendances
class AnnonceController {
    public function __construct(
        private AnnonceModel $model,
        private AnnonceView $view
    ) {}
}

// Création (peut être dans un Container)
$controller = new AnnonceController(
    new AnnonceModel(),
    new AnnonceView()
);
```

---

## 🎓 Formation Continue

### Ressources Recommandées

1. **PHP: The Right Way** - https://phptherightway.com/
2. **PSR Standards** - https://www.php-fig.org/psr/
3. **Clean Code PHP** - https://github.com/jupeter/clean-code-php
4. **SOLID Principles** - Uncle Bob Martin

### Outils à Installer

```bash
# Analyse statique
composer require --dev phpstan/phpstan

# Style de code
composer require --dev squizlabs/php_codesniffer

# Tests
composer require --dev phpunit/phpunit
```

---

## ✅ Checklist de Qualité

### Code Review Personnel

- [ ] **Noms explicites** : Variables, méthodes, classes compréhensibles
- [ ] **Méthodes courtes** : Max 20-30 lignes
- [ ] **Single Responsibility** : Une classe = une responsabilité
- [ ] **DRY (Don't Repeat Yourself)** : Pas de duplication
- [ ] **Commentaires utiles** : Expliquer le POURQUOI, pas le QUOI
- [ ] **Constantes** : Pas de valeurs magiques
- [ ] **Gestion d'erreurs** : Try/catch cohérents
- [ ] **Logging** : Événements importants tracés
- [ ] **Tests** : Au moins les fonctions critiques

---

## 🎯 Plan d'Action 30 Jours

### Semaine 1: Fondations
- Jour 1-2: Créer Router + SeoConfig
- Jour 3-4: Créer AppConstants
- Jour 5: Nettoyer index.php

### Semaine 2: Entités
- Jour 1-2: Créer Annonce entity
- Jour 3-4: Créer Utilisateur entity
- Jour 5: Créer Candidature entity

### Semaine 3: Validators
- Jour 1-2: AnnonceValidator
- Jour 3-4: UtilisateurValidator
- Jour 5: Templates PHP

### Semaine 4: Services
- Jour 1-3: AnnonceService
- Jour 4-5: Documentation + Tests

---

## 💬 Conclusion

Votre code est **fonctionnel et sécurisé** (après les correctifs de sécurité). Maintenant, l'objectif est de le rendre **maintenable et évolutif**.

> 💡 **Conseil d'ami:** Ne refactorisez pas tout d'un coup ! Prenez une fonctionnalité (ex: Annonces), améliorez-la complètement, puis passez à la suivante.

**Questions ? Bloqué sur un refactoring ?**  
→ Revenez vers moi avec un exemple précis, je vous guide ! 🚀

---

**Bon courage et excellent travail ! 👏**
