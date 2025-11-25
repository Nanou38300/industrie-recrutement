# 🚀 Guide de Refactoring Rapide

## 📋 Checklist d'Implémentation

### Phase 1: Fondations (2-3 heures)

#### ✅ Étape 1: Créer Router.php
- [x] Fichier créé: `app/Router.php`
- [ ] Tester dans `index.php`:
```php
$router = new App\Router();
$action = $router->getAction();
$step = $router->getStep();
$id = $router->getIdAsInt();
```

#### ✅ Étape 2: Créer SeoConfig.php
- [x] Fichier créé: `app/Config/SeoConfig.php`
- [ ] Tester dans `index.php`:
```php
$SEO = App\Config\SeoConfig::getMetaForAction($action);
```

#### ✅ Étape 3: Créer AppConstants.php
- [x] Fichier créé: `app/Config/AppConstants.php`
- [ ] Remplacer les strings magiques:
```php
// ❌ Avant
if ($statut === 'activée') { ... }

// ✅ Après
use App\Config\AppConstants;
if ($statut === AppConstants::ANNONCE_ACTIVE) { ... }
```

### Phase 2: Simplifier index.php (1 heure)

#### Avant (Actuel - ~400 lignes)
```php
// Routing complexe
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $requestUri)));
$action = $_GET['action'] ?? ($segments[0] ?? '');
// ... 50+ lignes

// SEO complexe
$metaByAction = [
  'accueil' => [...],
  // ... 80+ lignes
];
```

#### Après (Objectif - ~150 lignes)
```php
<?php
declare(strict_types=1);
ob_start();

require_once __DIR__ . '/vendor/autoload.php';

use App\Router;
use App\Config\SeoConfig;
use App\Security;
use App\Controller\*;

// Environnement
Dotenv\Dotenv::createImmutable(__DIR__)->load();

// Sécurité
Security::configureSecureSession();
Security::checkSessionTimeout();

// Headers
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline';...");

// Routing
$router = new Router();
$action = $router->getAction();
$step = $router->getStep();
$id = $router->getIdAsInt();

// SEO
$SEO = SeoConfig::getMetaForAction($action);
if ($action === 'annonce' && $step === 'view' && $router->hasValidId()) {
    $SEO = SeoConfig::getMetaForAnnonce($id);
}

// Layout
if (!$router->isApiCall()) {
    require_once 'assets/templates/head.php';
    
    if ($router->shouldShowAuthMenu()) {
        require_once 'assets/templates/menu-connecte.php';
    }
    
    if ($router->shouldShowPublicMenu()) {
        require_once 'assets/templates/menu-public.php';
    }
}

// Routes
try {
    match($action) {
        // Pages publiques
        'accueil', 'bureauEtude', 'domaineExpertise', 'recrutement', 'contact' 
            => include "Pages/{$action}.php",
        
        // Contrôleurs
        'administrateur' => (new AdministrateurController)->handleRequest($step, $id),
        'candidat' => (new CandidatController)->handleRequest($step, $id),
        'annonce' => (new AnnonceController)->handleRequest($step, $id),
        'candidature' => (new CandidatureController)->handleRequest($step, $id),
        'utilisateur' => (new UtilisateurController)->handleRequest($step, $id),
        
        // Défaut
        default => include 'Pages/accueil.php'
    };
} catch (\Exception $e) {
    // Gestion d'erreurs...
}

// Footer
if ($router->shouldShowFooter()) {
    require_once 'assets/templates/footer.php';
}

ob_end_flush();
```

### Phase 3: Améliorer les Controllers (2-3 jours)

#### Créer une méthode `handleRequest()` dans chaque controller

**Exemple dans AdministrateurController.php:**

```php
public function handleRequest(string $step, int $id): void
{
    match($step) {
        'dashboard' => $this->dashboard(),
        'profil' => $this->profil(),
        'edit-profil' => $this->editProfil(),
        'annonces' => $this->viewAnnonces(),
        'create-annonce' => $this->createAnnonce(),
        'edit-annonce' => $this->editAnnonce($id),
        default => $this->dashboard()
    };
}
```

---

## 🎯 Exemples Concrets de Refactoring

### Exemple 1: Validation avec Constantes

**Avant:**
```php
// ❌ Dans AnnonceModel.php
if ($statut === 'activée' || $statut === 'active' || $statut === 'en_cours') {
    $statutNormalise = 'activée';
}
```

**Après:**
```php
// ✅ Avec AppConstants
use App\Config\AppConstants;

private function normalizeStatut(?string $statut): string
{
    $s = strtolower(trim((string)$statut));
    
    $map = [
        'activée' => AppConstants::ANNONCE_ACTIVE,
        'active' => AppConstants::ANNONCE_ACTIVE,
        'en_cours' => AppConstants::ANNONCE_ACTIVE,
        'brouillon' => AppConstants::ANNONCE_BROUILLON,
        'archivée' => AppConstants::ANNONCE_ARCHIVEE,
    ];
    
    return $map[$s] ?? AppConstants::ANNONCE_BROUILLON;
}
```

### Exemple 2: Router dans les Vues

**Avant:**
```php
// ❌ URL construites manuellement
<a href="?action=annonce&step=view&id=<?= $annonce['id'] ?>">Voir</a>
```

**Après:**
```php
// ✅ Avec Router
<?php 
$router = new App\Router();
$url = $router->generateUrl('annonce', 'view', (string)$annonce->id);
?>
<a href="<?= $url ?>">Voir</a>
```

### Exemple 3: Affichage Statut avec Constantes

**Avant:**
```php
// ❌ HTML codé en dur
if ($statut === 'activée') {
    echo '<span class="badge badge-success">Active</span>';
} elseif ($statut === 'archivée') {
    echo '<span class="badge badge-secondary">Archivée</span>';
}
```

**Après:**
```php
// ✅ Avec méthodes helper
use App\Config\AppConstants;

$label = AppConstants::getAnnonceStatutLabel($annonce->statut);
$class = AppConstants::getAnnonceStatutClass($annonce->statut);

echo "<span class='badge $class'>$label</span>";
```

---

## 📝 Templates à Utiliser

### Template pour nouveau Validator

```php
<?php
namespace App\Validator;

class MonValidator
{
    private array $errors = [];

    public function validate(array $data): bool
    {
        $this->errors = [];
        
        // Vos validations ici
        $this->validateChamp1($data['champ1'] ?? '');
        $this->validateChamp2($data['champ2'] ?? '');
        
        return empty($this->errors);
    }

    private function validateChamp1(string $value): void
    {
        if (empty($value)) {
            $this->errors[] = "Champ1 est requis.";
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
```

### Template pour nouveau Service

```php
<?php
namespace App\Service;

class MonService
{
    public function __construct(
        private MonModel $model,
        private MonValidator $validator
    ) {}

    public function create(array $data): array
    {
        if (!$this->validator->validate($data)) {
            return [
                'success' => false,
                'errors' => $this->validator->getErrors()
            ];
        }

        try {
            $result = $this->model->create($data);
            return [
                'success' => $result,
                'errors' => []
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [$e->getMessage()]
            ];
        }
    }
}
```

---

## ⚡ Commandes Rapides

```powershell
# Créer les dossiers nécessaires
mkdir app\Config
mkdir app\Validator
mkdir app\Service
mkdir app\Entity

# Vérifier la syntaxe
php -l app\Router.php
php -l app\Config\SeoConfig.php
php -l app\Config\AppConstants.php

# Analyser le code (si PHPStan installé)
vendor\bin\phpstan analyse app\
```

---

## 🎯 Priorités

### 🔴 CETTE SEMAINE (Essential)
1. ✅ Router.php créé
2. ✅ SeoConfig.php créé
3. ✅ AppConstants.php créé
4. [ ] Modifier index.php pour utiliser Router
5. [ ] Tester que tout fonctionne

### 🟡 CE MOIS (Important)
6. [ ] Créer méthodes handleRequest() dans controllers
7. [ ] Créer AnnonceValidator
8. [ ] Créer CandidatureValidator
9. [ ] Séparer templates dans view/templates/

### 🟢 AMÉLIORATION CONTINUE
10. [ ] Entity classes (Annonce, Utilisateur, Candidature)
11. [ ] Service layer
12. [ ] Tests unitaires

---

## 💡 Astuces

### Test Rapide Router
```php
// Dans un fichier test-router.php
require 'vendor/autoload.php';
$router = new App\Router();

echo "Action: " . $router->getAction() . "\n";
echo "Step: " . $router->getStep() . "\n";
echo "ID: " . $router->getId() . "\n";
echo "Public page? " . ($router->isPublicPage() ? 'Oui' : 'Non') . "\n";
```

### Test Rapide SeoConfig
```php
// Dans un fichier test-seo.php
require 'vendor/autoload.php';
$meta = App\Config\SeoConfig::getMetaForAction('accueil');

print_r($meta);
```

### Test Rapide AppConstants
```php
// Dans un fichier test-constants.php
require 'vendor/autoload.php';
use App\Config\AppConstants;

echo "Statut valide? " . (AppConstants::isValidAnnonceStatut('activée') ? 'Oui' : 'Non') . "\n";
echo "Label: " . AppConstants::getAnnonceStatutLabel('activée') . "\n";
```

---

**Suivez ce guide étape par étape et votre code sera beaucoup plus clair ! 🚀**
