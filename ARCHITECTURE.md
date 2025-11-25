# 🏗️ Architecture du Projet - Vision Complète

## 📂 Structure des Dossiers

```
industrie-recrutement/
│
├── 📁 app/                                    # Coeur de l'application
│   ├── 📁 Config/                            # ✨ NOUVEAU
│   │   ├── AppConstants.php                  # ✅ Constantes centralisées
│   │   └── SeoConfig.php                     # ✅ Configuration SEO
│   │
│   ├── 📁 controller/                        # Contrôleurs (logique)
│   │   ├── AdministrateurController.php      # 🔒 Sécurisé
│   │   ├── AnnonceController.php             # 
│   │   ├── CandidatController.php            #
│   │   ├── CandidatureController.php         # 🔒 Sécurisé
│   │   ├── EntretienController.php           #
│   │   ├── CalendrierController.php          #
│   │   ├── UtilisateurController.php         # 🔒 Sécurisé
│   │   └── NewsController.php                #
│   │
│   ├── 📁 model/                             # Modèles (données)
│   │   ├── AdministrateurModel.php           #
│   │   ├── AnnonceModel.php                  #
│   │   ├── CandidatModel.php                 #
│   │   ├── CandidatureModel.php              #
│   │   ├── EntretienModel.php                #
│   │   ├── CalendrierModel.php               #
│   │   ├── UtilisateurModel.php              #
│   │   └── NewsModel.php                     #
│   │
│   ├── 📁 view/                              # Vues (affichage)
│   │   ├── AdministrateurView.php            #
│   │   ├── AnnonceView.php                   #
│   │   ├── CandidatView.php                  #
│   │   ├── CandidatureView.php               #
│   │   ├── EntretienView.php                 #
│   │   ├── CalendrierView.php                #
│   │   ├── UtilisateurView.php               #
│   │   ├── NewsView.php                      #
│   │   └── SharedView.php                    #
│   │
│   ├── 📁 Entity/                            # ✨ NOUVEAU (vide - à venir)
│   │   # Value Objects pour typage fort
│   │   # Ex: Annonce.php, Utilisateur.php, Candidature.php
│   │
│   ├── 📁 Validator/                         # ✨ NOUVEAU (vide - à venir)
│   │   # Classes de validation
│   │   # Ex: AnnonceValidator.php, UtilisateurValidator.php
│   │
│   ├── 📁 Service/                           # ✨ NOUVEAU (vide - à venir)
│   │   # Logique métier complexe
│   │   # Ex: AnnonceService.php, EmailService.php
│   │
│   ├── Router.php                            # ✅ Gestion du routing
│   ├── Security.php                          # ✅ Sécurité centralisée
│   ├── Database.php                          # Connexion DB (existant)
│   └── DatabaseSecure.php                    # ✅ Version sécurisée
│
├── 📁 assets/                                # Ressources front-end
│   ├── 📁 css/                               # Styles
│   ├── 📁 images/                            # Images
│   ├── 📁 js/                                # JavaScript
│   └── 📁 templates/                         # Templates HTML partagés
│       ├── head.php
│       ├── menu-public.php
│       ├── menu-connecte.php
│       ├── footer.php
│       └── bulle-flottante.php
│
├── 📁 Pages/                                 # Pages publiques
│   ├── accueil.php
│   ├── bureauEtude.php
│   ├── domaineExpertise.php
│   ├── recrutement.php
│   ├── contact.php
│   └── page404.php
│
├── 📁 vendor/                                # Dépendances Composer
│   └── autoload.php
│
├── 📁 uploads/                               # Fichiers uploadés
│   # CV, photos de profil, etc.
│
├── 📁 logs/                                  # ✨ À CRÉER
│   └── security.log                          # Logs de sécurité
│
├── 📁 test/                                  # Tests
│   └── AnnonceControllerTest.php
│
├── 📄 index.php                              # Point d'entrée (à simplifier)
├── 📄 composer.json                          # Dépendances PHP
├── 📄 Dockerfile                             # Docker
├── 📄 Docker-compose.yml                     # Docker Compose
├── 📄 .env                                   # Variables d'environnement
├── 📄 .env.example                           # Template .env
├── 📄 .gitignore                             # Git ignore
│
└── 📚 DOCUMENTATION/                         # ✨ NOUVEAUX DOCUMENTS
    ├── SECURITY_AUDIT.md                     # ✅ Audit sécurité
    ├── SECURITY_CHECKLIST.md                 # ✅ Checklist sécurité
    ├── IMPLEMENTATION_GUIDE.md               # ✅ Guide implémentation
    ├── CODE_QUALITY_AUDIT.md                 # ✅ Audit qualité
    ├── REFACTORING_GUIDE.md                  # ✅ Guide refactoring
    └── README_COMPLETE.md                    # ✅ Documentation complète
```

---

## 🔄 Flux de l'Application

### 1️⃣ Point d'Entrée (index.php)

```
Requête HTTP
    ↓
index.php
    ├── Chargement autoload (Composer)
    ├── Chargement .env (Dotenv)
    ├── Configuration sécurité (Security::configureSecureSession())
    ├── Headers HTTP (X-Frame-Options, CSP, etc.)
    ├── Parsing URL (Router)
    ├── Configuration SEO (SeoConfig)
    ├── Inclusion layout (head, menus)
    ├── Routage vers Controller
    └── Inclusion footer
```

### 2️⃣ Routage Simplifié (avec Router)

```
Router::__construct()
    ├── Parse $_SERVER['REQUEST_URI']
    ├── Extrait action, step, id
    ├── Détecte contexte (public/auth/api)
    └── Retourne informations structurées

match($action)
    ├── Pages publiques → include Pages/*.php
    ├── 'administrateur' → AdministrateurController
    ├── 'candidat' → CandidatController
    ├── 'annonce' → AnnonceController
    └── default → accueil
```

### 3️⃣ Controller → Model → View

```
Controller (AnnonceController)
    ├── Validation CSRF (Security)
    ├── Validation données (Validator - à venir)
    ├── Appel Model
    │   └── Model (AnnonceModel)
    │       ├── Requête BDD (PDO)
    │       ├── Normalisation données
    │       └── Retour entité/array
    ├── Traitement logique
    └── Appel View
        └── View (AnnonceView)
            ├── Génération HTML
            ├── Échappement (htmlspecialchars)
            └── Affichage
```

---

## 🎯 Architecture MVC en Détail

### Model (Couche Données)

**Responsabilités:**
- Connexion base de données
- Requêtes SQL (SELECT, INSERT, UPDATE, DELETE)
- Validation au niveau DB
- Normalisation des données

**Exemple:**
```php
class AnnonceModel {
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM annonce WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
```

### Controller (Couche Logique)

**Responsabilités:**
- Réception requêtes HTTP
- Validation CSRF
- Validation métier
- Orchestration Model/View
- Gestion sessions/cookies
- Redirections

**Exemple:**
```php
class AnnonceController {
    public function viewAnnonce(int $id): void {
        // 1. Récupération données
        $annonce = $this->model->getById($id);
        
        // 2. Vérification
        if (!$annonce) {
            $this->showError("Annonce introuvable");
            return;
        }
        
        // 3. Affichage
        $this->view->renderDetails($annonce);
    }
}
```

### View (Couche Présentation)

**Responsabilités:**
- Génération HTML
- Échappement données (XSS protection)
- Templates réutilisables
- Aucune logique métier

**Exemple:**
```php
class AnnonceView {
    public function renderDetails(array $annonce): void {
        echo "<h2>" . htmlspecialchars($annonce['titre']) . "</h2>";
        echo "<p>" . htmlspecialchars($annonce['description']) . "</p>";
    }
}
```

---

## 🔐 Couches de Sécurité

```
┌─────────────────────────────────────────┐
│         Requête HTTP                    │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  1. Headers HTTP (index.php)            │
│     • X-Frame-Options: DENY             │
│     • Content-Security-Policy           │
│     • X-XSS-Protection                  │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  2. Session Sécurisée (Security)        │
│     • HttpOnly cookies                  │
│     • SameSite=Strict                   │
│     • Timeout 30 min                    │
│     • Regenerate ID après login         │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  3. Authentification (Security)         │
│     • Vérification session              │
│     • Contrôle rôles                    │
│     • Rate limiting                     │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  4. Validation CSRF (Controller)        │
│     • Token généré (Security)           │
│     • Validation hash_equals()          │
│     • Expiration 1h                     │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  5. Validation Données (Controller)     │
│     • Sanitization (filter_var)         │
│     • Validation typage                 │
│     • Validation métier                 │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  6. Requêtes Préparées (Model)          │
│     • PDO prepared statements           │
│     • Protection SQL Injection          │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  7. Échappement HTML (View)             │
│     • htmlspecialchars()                │
│     • Protection XSS                    │
└─────────────┬───────────────────────────┘
              │
              ↓
┌─────────────────────────────────────────┐
│  8. Logging (Security)                  │
│     • Événements de sécurité            │
│     • logs/security.log                 │
└─────────────────────────────────────────┘
```

---

## 📦 Classes Utilitaires

### Security.php - Hub de Sécurité

```
Security
├── CSRF Protection
│   ├── generateCSRFToken()
│   ├── validateCSRFToken()
│   └── getCSRFInput()
│
├── Session Management
│   ├── configureSecureSession()
│   ├── regenerateSession()
│   └── checkSessionTimeout()
│
├── Input Validation
│   ├── sanitizeInput()
│   ├── validateEmail()
│   ├── validateInt()
│   └── escape()
│
├── Access Control
│   ├── isAuthenticated()
│   ├── hasRole()
│   ├── requireAuth()
│   └── requireRole()
│
├── File Upload
│   ├── validateFileUpload()
│   └── generateSecureFilename()
│
├── Rate Limiting
│   └── rateLimitCheck()
│
└── Logging
    └── logSecurityEvent()
```

### Router.php - Gestion Routing

```
Router
├── URL Parsing
│   ├── getAction()
│   ├── getStep()
│   ├── getId()
│   └── getIdAsInt()
│
├── Context Detection
│   ├── isPublicPage()
│   ├── isAuthPage()
│   ├── isApiCall()
│   └── hasValidId()
│
├── Layout Control
│   ├── shouldShowPublicMenu()
│   ├── shouldShowAuthMenu()
│   └── shouldShowFooter()
│
└── URL Generation
    ├── generateUrl()
    ├── redirect()
    └── redirectToLogin()
```

### AppConstants.php - Constantes

```
AppConstants
├── Statuts Annonce
│   ├── ANNONCE_ACTIVE
│   ├── ANNONCE_BROUILLON
│   └── ANNONCE_ARCHIVEE
│
├── Rôles
│   ├── ROLE_ADMIN
│   └── ROLE_CANDIDAT
│
├── Types Contrat
│   ├── CONTRAT_CDI
│   ├── CONTRAT_CDD
│   └── CONTRAT_INTERIM
│
├── Statuts Candidature
│   ├── CANDIDATURE_ENVOYEE
│   ├── CANDIDATURE_CONSULTEE
│   └── CANDIDATURE_RECRUTE
│
├── Limites
│   ├── MAX_FILE_SIZE
│   ├── SESSION_TIMEOUT
│   └── LOGIN_MAX_ATTEMPTS
│
└── Helpers
    ├── isValidAnnonceStatut()
    ├── getAnnonceStatutLabel()
    └── getAnnonceStatutClass()
```

---

## 🎨 Patterns Utilisés

### 1. MVC (Model-View-Controller)
- **Séparation des responsabilités**
- Model = Données
- View = Affichage
- Controller = Logique

### 2. Singleton (Database)
- **Une seule instance de connexion DB**
```php
Database::getInstance()->getConnection()
```

### 3. Factory (à venir - Entity)
- **Création d'objets depuis arrays**
```php
Annonce::fromArray($data)
```

### 4. Repository (Models)
- **Abstraction accès données**
```php
AnnonceModel->getById(), getAll(), create()
```

### 5. Service Layer (à venir)
- **Logique métier complexe**
```php
AnnonceService->creerAnnonce(), modifierAnnonce()
```

---

## 🔄 Améliorations Futures

### Phase 1: Refactoring (En cours)
- [x] Router
- [x] SeoConfig
- [x] AppConstants
- [ ] Simplification index.php
- [ ] Templates PHP séparés

### Phase 2: Validation (À venir)
- [ ] AnnonceValidator
- [ ] UtilisateurValidator
- [ ] CandidatureValidator

### Phase 3: Entités (À venir)
- [ ] Annonce Entity
- [ ] Utilisateur Entity
- [ ] Candidature Entity

### Phase 4: Services (À venir)
- [ ] AnnonceService
- [ ] EmailService
- [ ] AuthService

### Phase 5: Tests (À venir)
- [ ] Tests unitaires (PHPUnit)
- [ ] Tests d'intégration
- [ ] CI/CD

---

## 📊 Métriques

### Complexité
- **Avant:** index.php ~400 lignes
- **Objectif:** index.php ~150 lignes

### Sécurité
- **Avant:** 3/10
- **Après:** 8/10

### Maintenabilité
- **Avant:** 6/10
- **Objectif:** 9/10

---

**Architecture évolutive et scalable ! 🚀**
