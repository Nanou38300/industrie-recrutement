# ✅ Checklist Sécurité - Actions Immédiates

## 🔴 URGENT - À faire MAINTENANT

- [ ] **Ajouter `Security::getCSRFInput()` dans TOUS les formulaires**
  - [ ] `UtilisateurView.php` (login, création, édition)
  - [ ] `CandidatView.php` (profil, candidature)
  - [ ] `AnnonceView.php` (création, édition)
  - [ ] `CandidatureView.php` (soumission, statut)
  - [ ] `AdministrateurView.php` (profil, annonces, entretiens)
  - [ ] `EntretienView.php` (création, édition)
  - [ ] `CalendrierView.php` (rendez-vous)
  - [ ] `NewsView.php` (si existe)

- [ ] **Créer le dossier logs/**
  ```powershell
  mkdir logs
  ```

- [ ] **Vérifier que .env n'est PAS dans Git**
  ```powershell
  git rm --cached .env
  echo ".env" >> .gitignore
  ```

- [ ] **Créer .env.example**
  ```powershell
  Copy-Item .env .env.example
  # Remplacer les valeurs sensibles par des exemples
  ```

## 🟡 IMPORTANT - Cette semaine

- [ ] **Tester tous les formulaires avec CSRF**
  - [ ] Login fonctionne
  - [ ] Création utilisateur fonctionne
  - [ ] Modification profil fonctionne
  - [ ] Soumission candidature fonctionne
  - [ ] Création annonce fonctionne

- [ ] **Vérifier les logs de sécurité**
  - [ ] Fichier `logs/security.log` est créé
  - [ ] Les événements sont enregistrés
  - [ ] Format lisible

- [ ] **Tester le rate limiting**
  - [ ] 5 tentatives de login échouent → bloqué
  - [ ] Après 5 minutes → déblocage automatique

- [ ] **Tester le timeout de session**
  - [ ] Inactivité 30 min → déconnexion auto
  - [ ] Redirection vers login

## 🟢 RECOMMANDÉ - Ce mois-ci

- [ ] **Configurer HTTPS**
  - [ ] Certificat SSL installé
  - [ ] Redirection HTTP → HTTPS
  - [ ] HSTS activé

- [ ] **Backups automatiques**
  - [ ] Script de backup quotidien
  - [ ] Stockage externe sécurisé
  - [ ] Test de restauration

- [ ] **Mettre à jour les dépendances**
  ```powershell
  composer update
  composer audit
  ```

- [ ] **Tests automatisés**
  - [ ] PHPUnit configuré
  - [ ] Tests de sécurité écrits
  - [ ] CI/CD configuré

## 📋 Validation Finale

- [ ] **Audit complet**
  - [ ] Aucun `$_POST` sans validation
  - [ ] Aucun `$_GET` sans sanitization
  - [ ] Tous les formulaires ont CSRF
  - [ ] Toutes les pages admin vérifiées
  - [ ] Upload de fichiers sécurisé

- [ ] **Documentation**
  - [ ] `SECURITY_AUDIT.md` lu et compris
  - [ ] `IMPLEMENTATION_GUIDE.md` suivi
  - [ ] Équipe formée aux bonnes pratiques

- [ ] **Monitoring**
  - [ ] Logs vérifiés quotidiennement
  - [ ] Alertes configurées
  - [ ] Dashboard de sécurité

---

## 🎯 Score de Sécurité Actuel

### Avant Audit: ⚠️ 3/10

### Après Implémentation: ✅ 8/10

### Objectif Final: 🎯 9.5/10

---

**Date de dernière mise à jour:** 20 novembre 2025
