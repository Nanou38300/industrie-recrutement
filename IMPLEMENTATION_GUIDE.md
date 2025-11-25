# 📝 Guide d'Implémentation Rapide - Sécurité

## 🚀 Actions Immédiates à Réaliser

### 1. Ajouter les Tokens CSRF dans Tous les Formulaires

Dans **TOUS** vos fichiers de vue (View), ajoutez cette ligne dans chaque `<form>`:

```php
<?php 
use App\Security;
echo Security::getCSRFInput(); 
?>
```

#### Exemple dans UtilisateurView.php:

```php
public function displayLoginForm(): void
{
    echo "<form method='POST' action='/utilisateur/login'>";
    echo Security::getCSRFInput(); // ← AJOUTER CETTE LIGNE
    echo "<input type='email' name='email' required>";
    echo "<input type='password' name='mot_de_passe' required>";
    echo "<button type='submit'>Connexion</button>";
    echo "</form>";
}
```

### 2. Mettre à Jour les Contrôleurs Restants

#### AnnonceController.php

Ajoutez en haut du fichier:
```php
use App\Security;
```

Dans le constructeur:
```php
public function __construct() {
    Security::configureSecureSession();
    Security::checkSessionTimeout();
    // ... reste du code
}
```

Dans chaque méthode POST:
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::validateRequest()) {
        die('Erreur de sécurité');
    }
    // ... reste du code
}
```

#### CandidatController.php

Même procédure que AnnonceController.php

#### EntretienController.php, CalendrierController.php, NewsController.php

Même procédure pour tous les contrôleurs.

### 3. Créer le Dossier logs/

```powershell
mkdir c:\Users\jelmo\industrie-recrutement\logs
```

Ajouter dans `.gitignore`:
```
logs/
*.log
```

### 4. Fichier .env.example

Créer un template pour les autres développeurs:

```env
DB_HOST_LOCAL=localhost
DB_NAME_LOCAL=nom_base_de_donnees
DB_USER_LOCAL=utilisateur
DB_PASSWORD_LOCAL=mot_de_passe
```

### 5. Vérifier .gitignore

Assurez-vous que `.env` n'est PAS dans Git:

```gitignore
.env
.env.local
logs/
*.log
vendor/
```

---

## 🔍 Tests à Effectuer

### Test 1: Protection CSRF
1. Ouvrir un formulaire
2. Inspecter le HTML
3. Vérifier la présence de `<input type="hidden" name="csrf_token">`

### Test 2: Validation des Sessions
1. Se connecter
2. Attendre 30 minutes d'inactivité
3. Essayer d'accéder à une page protégée
4. Devrait être redirigé vers login

### Test 3: Rate Limiting
1. Essayer de se connecter 6 fois avec un mauvais mot de passe
2. Devrait être bloqué après 5 tentatives

### Test 4: Logs de Sécurité
1. Effectuer une action (login, création...)
2. Vérifier `logs/security.log`
3. Devrait contenir les événements

---

## ⚡ Optimisations de Performance

### 1. Mise en Cache de la Connexion DB

Déjà implémentée via le pattern Singleton dans `Database.php`

### 2. Compression des Réponses

Dans `.htaccess` (Apache) ou `nginx.conf`:

```nginx
gzip on;
gzip_types text/css application/javascript application/json;
```

### 3. Optimisation des Requêtes

Éviter les `SELECT *`, spécifier uniquement les colonnes nécessaires:

```php
// ❌ Mauvais
SELECT * FROM utilisateur

// ✅ Bon
SELECT id, nom, prenom, email FROM utilisateur
```

---

## 📊 Organisation du Code - Bonnes Pratiques

### Structure MVC Respectée ✅

```
app/
├── controller/     (Logique métier)
├── model/          (Accès données)
├── view/           (Affichage)
├── Database.php    (Connexion DB)
└── Security.php    (Sécurité centralisée)
```

### Nomenclature ✅

- Classes: `PascalCase` (UtilisateurController)
- Méthodes: `camelCase` (createUtilisateur)
- Constantes: `SNAKE_CASE` (TOKEN_NAME)
- Variables: `camelCase` ($utilisateurModel)

### Commentaires

```php
// ✅ Bon commentaire explicatif
// Validation du token CSRF pour prévenir les attaques CSRF
if (!Security::validateRequest()) {
    // ...
}

// ❌ Commentaire inutile
// Incrémenter i
$i++;
```

---

## 🎯 Prochaines Étapes Recommandées

### Court Terme (1-2 semaines)
1. ✅ Ajouter CSRF dans toutes les vues
2. ✅ Tester tous les formulaires
3. ✅ Configurer HTTPS
4. ✅ Backups automatiques

### Moyen Terme (1 mois)
1. Tests automatisés (PHPUnit)
2. CI/CD (GitHub Actions)
3. Monitoring (Sentry, New Relic)
4. Documentation API

### Long Terme (3-6 mois)
1. Authentification 2FA
2. Tests de pénétration
3. Audit de performance
4. Refactoring (PSR-12, PHPStan niveau 8)

---

## 💡 Astuces de Développement

### Debug en Développement

Dans `.env`:
```env
APP_ENV=development
APP_DEBUG=true
```

Dans `index.php`:
```php
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
```

### Logs Structurés

```php
Security::logSecurityEvent('USER_ACTION', [
    'user_id' => $_SESSION['utilisateur']['id'],
    'action' => 'update_profile',
    'ip' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT']
]);
```

### Validation Personnalisée

```php
// Validation téléphone français
function validatePhoneFR(string $phone): bool {
    return preg_match('/^(?:(?:\+|00)33|0)[1-9](?:[0-9]{8})$/', $phone);
}

// Validation code postal
function validateCodePostal(string $cp): bool {
    return preg_match('/^[0-9]{5}$/', $cp);
}
```

---

## 🔧 Commandes Utiles

```powershell
# Vérifier les dépendances obsolètes
composer outdated

# Mettre à jour les dépendances
composer update

# Vérifier la syntaxe PHP
php -l app/Security.php

# Lancer les tests
./vendor/bin/phpunit

# Analyser le code (si PHPStan installé)
./vendor/bin/phpstan analyse app/
```

---

## 📞 Support

Questions ? Contactez l'équipe technique ou consultez:
- Documentation: `/readme.md`
- Audit sécurité: `/SECURITY_AUDIT.md`
- Issues GitHub: [Votre repo]

---

**Bon développement ! 🚀**
