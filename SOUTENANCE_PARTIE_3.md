# 🎤 Soutenance - Partie 3 : Sécurité (7 min)

## 📋 Où êtes-vous dans la soutenance ?

✅ Partie 1 : Introduction (5 min) - TERMINÉ
✅ Partie 2 : Architecture Technique (8 min) - TERMINÉ
🔵 **Partie 3 : Sécurité (7 min) - EN COURS**
⬜ Partie 4 : Fonctionnalités Clés (8 min)
⬜ Partie 5 : Améliorations & Roadmap (5 min)
⬜ Partie 6 : Questions/Réponses (2 min)

---

## 🔐 Partie 3 : Sécurité (7 minutes)

### Slide 9 : Pourquoi la Sécurité est Cruciale (1 min 30)

**Ce que vous affichez :**
```
⚠️ RISQUES SANS SÉCURITÉ

🔴 Vol de données personnelles
   • Emails, mots de passe, CV...
   • 68% des sites sont attaqués chaque année

🔴 Modification frauduleuse
   • Pirate se fait passer pour un admin
   • Supprime des candidatures, modifie des annonces

🔴 Injection de code malveillant
   • Vol de sessions utilisateurs
   • Redirection vers sites malveillants

💰 Coût moyen d'une faille : 50 000€
⚖️ Risques légaux : RGPD
```

**Ce que vous dites (vulgarisé) :**
> "La sécurité, c'est comme les serrures de votre maison. Sans elles, n'importe qui peut entrer. Sur un site web, c'est pareil.
> 
> Sans sécurité, un pirate pourrait voler les données personnelles : emails, mots de passe, CV des candidats. Il pourrait se faire passer pour un administrateur et supprimer des candidatures ou modifier des annonces. Il pourrait même injecter du code malveillant qui vole les sessions des utilisateurs.
> 
> Les statistiques sont claires : 68% des sites web sont attaqués chaque année. Le coût moyen d'une faille de sécurité est de 50 000 euros, sans compter les risques légaux avec le RGPD qui protège les données personnelles.
> 
> C'est pour ça que j'ai passé beaucoup de temps sur la sécurité de ce site."

---

### Slide 10 : Les 5 Protections Principales (2 min)

**Ce que vous affichez :**
```
🛡️ MES 5 PROTECTIONS

1️⃣ CSRF (Cross-Site Request Forgery)
   ✓ Token unique sur chaque formulaire
   ✓ Expiration 1 heure
   
2️⃣ SESSIONS SÉCURISÉES
   ✓ Cookies HttpOnly (invisible au JavaScript)
   ✓ SameSite=Strict (pas d'envoi cross-site)
   ✓ Timeout 30 minutes d'inactivité
   
3️⃣ VALIDATION DES DONNÉES
   ✓ Tous les inputs sont nettoyés
   ✓ Vérification email, téléphone, nombres...
   
4️⃣ RATE LIMITING
   ✓ Max 5 tentatives de connexion / 5 min
   ✓ Protection contre force brute
   
5️⃣ HEADERS HTTP SÉCURISÉS
   ✓ X-Frame-Options (anti-iframe)
   ✓ Content-Security-Policy
   ✓ X-XSS-Protection

📊 Score de sécurité : 8/10
```

**Ce que vous dites (vulgarisé) :**
> "J'ai mis en place 5 grandes protections :
> 
> **1. Protection CSRF** - Imaginez que vous recevez un email disant 'cliquez ici pour gagner'. En cliquant, ça pourrait envoyer une action sur mon site à votre place. Pour éviter ça, chaque formulaire a un token unique qui expire au bout d'une heure. Comme un code de sécurité temporaire.
> 
> **2. Sessions sécurisées** - Quand vous vous connectez, le site vous reconnaît grâce à un cookie. J'ai configuré ce cookie pour qu'il soit invisible au JavaScript (HttpOnly), qu'il ne soit jamais envoyé vers d'autres sites (SameSite=Strict), et qu'il expire après 30 minutes d'inactivité.
> 
> **3. Validation des données** - Tous les formulaires sont vérifiés. Si vous entrez un email, je vérifie que c'est vraiment un email. Si c'est un nombre, je vérifie que c'est bien un nombre. Comme un videur à l'entrée d'une boîte de nuit.
> 
> **4. Rate Limiting** - Pour le login, vous avez maximum 5 tentatives en 5 minutes. Après, vous êtes bloqué temporairement. Ça empêche un robot de tester des milliers de mots de passe.
> 
> **5. Headers HTTP** - J'ai ajouté des en-têtes spéciaux qui disent au navigateur : 'N'affiche pas ce site dans une iframe', 'Bloque les scripts suspects', etc.
> 
> Après audit, j'ai obtenu un score de 8 sur 10, ce qui est très bon."

---

### Slide 11 : Exemple CSRF en Détail (2 min)

**Ce que vous affichez :**
```
🔍 EXEMPLE : Protection CSRF

❌ SANS PROTECTION (Danger !)
┌──────────────────────────────────────┐
│ <form action="delete-account">      │
│   <button>Supprimer</button>         │
│ </form>                              │
└──────────────────────────────────────┘
→ N'importe qui peut créer ce formulaire
  et vous faire supprimer votre compte !


✅ AVEC PROTECTION (Sécurisé)
┌──────────────────────────────────────┐
│ <form action="delete-account">      │
│   <input type="hidden"               │
│          name="csrf_token"           │
│          value="a3f9k2m...">        │
│   <button>Supprimer</button>         │
│ </form>                              │
└──────────────────────────────────────┘
→ Token unique généré par le serveur
→ Vérifié avant toute action sensible
→ Expire au bout d'1 heure


📝 CODE PHP
generateCSRFToken()  → Création
validateCSRFToken()  → Vérification
```

**Ce que vous dites (avec analogie) :**
> "Laissez-moi vous expliquer la protection CSRF avec une analogie simple.
> 
> Imaginez que vous êtes dans un magasin et que vous donnez votre carte bancaire au vendeur. Sans protection CSRF, c'est comme si le vendeur pouvait utiliser votre carte sans vous demander le code PIN. N'importe qui pourrait créer un faux formulaire et faire une action à votre place.
> 
> Avec la protection CSRF, c'est comme si chaque transaction nécessitait un code PIN unique qui change à chaque fois. Même si quelqu'un vole votre formulaire, il n'a pas le bon code.
> 
> Concrètement, quand vous affichez un formulaire, mon site génère un token unique, une longue chaîne de caractères aléatoires. Ce token est caché dans le formulaire. Quand vous soumettez le formulaire, je vérifie que le token est le bon. Si ce n'est pas le cas, j'arrête tout et je bloque l'action. Le token expire au bout d'une heure pour plus de sécurité.
> 
> J'ai implémenté ça sur tous les formulaires sensibles : connexion, modification de profil, suppression de compte, etc."

---

### Slide 12 : Autres Mesures de Sécurité (1 min 30)

**Ce que vous affichez :**
```
🔒 SÉCURITÉ SUPPLÉMENTAIRE

💾 BASE DE DONNÉES
✓ Requêtes préparées (PDO)
✓ Aucune concaténation SQL directe
✓ Protection contre SQL Injection

🔐 MOTS DE PASSE
✓ Hashage avec password_hash() (bcrypt)
✓ Coût : 12 rounds
✓ Impossible de retrouver le mot de passe original

📤 UPLOAD DE FICHIERS
✓ Vérification du type MIME
✓ Limite de taille (5 Mo)
✓ Renommage sécurisé (pas d'exécution)
✓ Extensions autorisées : .pdf, .doc, .docx

📊 LOGGING
✓ Tous les événements de sécurité sont logués
✓ Fichier : logs/security.log
✓ Date, IP, action, résultat

🚨 GESTION DES ERREURS
✓ Messages génériques pour l'utilisateur
✓ Détails techniques dans les logs
✓ Pas de révélation d'infos sensibles
```

**Ce que vous dites :**
> "En plus de ces 5 grandes protections, j'ai ajouté d'autres mesures :
> 
> Pour la **base de données**, j'utilise des requêtes préparées. C'est comme utiliser un formulaire officiel plutôt que d'écrire à la main. Ça empêche les injections SQL où un pirate pourrait modifier ma requête.
> 
> Pour les **mots de passe**, je ne les stocke JAMAIS en clair. J'utilise un algorithme de hashage qui transforme le mot de passe en une empreinte unique. Même moi, administrateur du site, je ne peux pas voir les mots de passe des utilisateurs.
> 
> Pour l'**upload de CV**, je vérifie le type de fichier, je limite la taille à 5 Mo, je renomme le fichier pour éviter qu'on puisse exécuter du code, et j'accepte uniquement les PDF et documents Word.
> 
> J'ai aussi un système de **logging** qui enregistre tous les événements de sécurité dans un fichier : qui s'est connecté, quand, quelle action a été faite. C'est comme une caméra de surveillance.
> 
> Enfin, la **gestion des erreurs** : si quelque chose plante, l'utilisateur voit un message générique, mais les détails techniques sont enregistrés dans les logs, pas affichés à l'écran."

---

## 💡 Analogies pour Vulgariser

### CSRF = Code PIN
- Sans CSRF = Carte bancaire sans code PIN
- Avec CSRF = Carte bancaire + code PIN unique à chaque transaction

### Session = Badge d'Entrée
- Cookie = Votre badge personnel
- HttpOnly = Badge avec puce RFID (pas copiable facilement)
- SameSite = Badge qui ne fonctionne que dans votre bâtiment
- Timeout = Badge qui expire après 30 min d'inactivité

### Validation = Videur de Boîte
- Formulaire = File d'attente
- Validation = Le videur vérifie votre âge, tenue, etc.
- Rejet = Pas le bon format, désolé

### Rate Limiting = Distributeur de Billets
- 3 mauvais codes PIN → Carte avalée
- 5 mauvais logins → Compte bloqué temporairement

### Hash de Mot de Passe = Hachoir à Viande
- Mot de passe = Viande
- Hash = Viande hachée
- Impossible de retrouver le steak d'origine à partir du haché

---

## 🎭 Script Complet (Exemple)

> **[SLIDE 9 - 1 min 30]**
> "Parlons maintenant de la sécurité, un aspect crucial de tout site web. La sécurité, c'est comme les serrures de votre maison. Sans elles, n'importe qui peut entrer. Sur ce site de recrutement, on manipule des données sensibles : emails, mots de passe, CV. Sans protection, un pirate pourrait voler ces données, se faire passer pour un administrateur et modifier ou supprimer des candidatures, ou même injecter du code malveillant. Les chiffres sont éloquents : 68% des sites web sont attaqués chaque année, et le coût moyen d'une faille de sécurité est de 50 000 euros, sans compter les conséquences légales avec le RGPD. C'est pour ça que j'ai consacré beaucoup de temps à sécuriser ce site."
>
> **[SLIDE 10 - 2 min]**
> "J'ai mis en place 5 grandes protections. Premièrement, la protection CSRF : imaginez que chaque formulaire a un code de sécurité temporaire unique. Même si quelqu'un essaie de créer un faux formulaire, il n'aura pas le bon code. Deuxièmement, les sessions sécurisées : quand vous vous connectez, le site vous donne un cookie pour vous reconnaître. Ce cookie est invisible au JavaScript, ne peut pas être envoyé vers d'autres sites, et expire après 30 minutes d'inactivité. Troisièmement, la validation des données : tous les formulaires sont vérifiés, comme un videur à l'entrée d'une boîte. Quatrièmement, le rate limiting : vous avez maximum 5 tentatives de connexion en 5 minutes, ça empêche un robot de tester des milliers de mots de passe. Cinquièmement, les headers HTTP qui disent au navigateur comment se protéger. Après audit complet, j'ai obtenu un score de 8 sur 10."
>
> **[SLIDE 11 - 2 min]**
> "Laissez-moi détailler la protection CSRF avec une analogie. Sans protection, c'est comme donner votre carte bancaire sans demander le code PIN. Avec protection, chaque transaction nécessite un code unique qui change à chaque fois. Concrètement, quand vous affichez un formulaire, mon serveur génère un token aléatoire unique. Ce token est caché dans le formulaire. Quand vous soumettez, je vérifie que c'est le bon token. Si quelqu'un essaie de créer un faux formulaire, il n'aura pas le bon token, et l'action sera bloquée. Ce token expire au bout d'une heure. J'ai implémenté ça partout : connexion, modification de profil, suppression, toutes les actions sensibles."
>
> **[SLIDE 12 - 1 min 30]**
> "J'ai aussi d'autres mesures. Pour la base de données, j'utilise des requêtes préparées qui empêchent les injections SQL. Les mots de passe sont hashés avec un algorithme puissant : même moi je ne peux pas voir les mots de passe des utilisateurs. Pour l'upload de CV, je vérifie le type de fichier, limite la taille à 5 Mo, et renomme les fichiers pour éviter l'exécution de code. J'ai un système de logging qui enregistre tous les événements de sécurité, comme une caméra de surveillance. Et enfin, la gestion des erreurs : si quelque chose plante, l'utilisateur voit un message simple, mais les détails sont dans les logs, jamais affichés à l'écran."

---

## ❓ Questions Possibles (Partie 3)

### Q1 : "C'est quoi concrètement une attaque CSRF ?"
**Réponse avec exemple :**
> "Imaginez que vous êtes connecté sur mon site dans un onglet. Dans un autre onglet, vous visitez un site malveillant qui contient un formulaire caché qui dit 'Supprime le compte de cet utilisateur'. Si je n'ai pas de protection CSRF, quand ce formulaire est soumis, il utilise votre session active et supprime votre compte. Avec le token CSRF, le site malveillant n'a pas le bon token, donc l'action est bloquée."

### Q2 : "Pourquoi ne pas stocker les mots de passe en clair ?"
**Réponse :**
> "Même moi, en tant que développeur, je ne dois pas pouvoir voir les mots de passe. Premièrement parce que c'est contraire à la vie privée. Deuxièmement, si ma base de données est piratée, les mots de passe sont inutilisables car ils sont hashés. C'est comme transformer de la viande en viande hachée : on ne peut pas retrouver le steak d'origine. Et comme beaucoup de gens utilisent le même mot de passe partout, je protège aussi leurs autres comptes."

### Q3 : "Comment fonctionne le rate limiting techniquement ?"
**Réponse :**
> "Je stocke en session le nombre de tentatives de connexion et l'heure de la première tentative. À chaque tentative ratée, j'incrémente le compteur. Si on atteint 5 tentatives en moins de 5 minutes, je bloque temporairement. Après 5 minutes, le compteur se réinitialise. C'est simple mais efficace contre les attaques par force brute."

### Q4 : "Qu'est-ce qu'une injection SQL ?"
**Réponse avec exemple :**
> "C'est quand un pirate essaie d'insérer du code SQL dans un formulaire. Par exemple, dans le champ 'nom', au lieu d'écrire 'Dupont', il écrit : ' OR 1=1 --. Si ma requête SQL n'est pas protégée, ça pourrait modifier la requête et renvoyer tous les utilisateurs au lieu d'un seul. Avec les requêtes préparées, ce texte est traité comme une simple chaîne de caractères, pas comme du code SQL."

### Q5 : "Pourquoi un score de 8/10 et pas 10/10 ?"
**Réponse honnête :**
> "Bonne question ! J'ai atteint 8/10 parce qu'il reste deux améliorations à faire : implémenter l'authentification à deux facteurs (2FA) avec un code SMS ou email, et ajouter une politique de mots de passe encore plus stricte avec expiration tous les 90 jours. Ce sont des fonctionnalités avancées que je prévois d'ajouter dans la prochaine version. Mais 8/10 est déjà un très bon niveau de sécurité."

### Q6 : "Est-ce que le site est conforme au RGPD ?"
**Réponse :**
> "Oui, j'ai pris plusieurs mesures : je ne collecte que les données nécessaires, je les stocke de façon sécurisée, je permets aux utilisateurs de consulter et supprimer leurs données, j'ai mis en place une politique de confidentialité claire, et je logue tous les accès aux données personnelles. Il faudrait un audit juridique complet pour une certification RGPD officielle, mais j'ai respecté tous les principes."

---

## 💡 Conseils pour cette Partie

### ✅ À FAIRE
- Utiliser des analogies du quotidien (carte bancaire, serrure...)
- Montrer l'impact réel (coûts, risques)
- Expliquer le "pourquoi" avant le "comment"
- Montrer que vous avez conscience des enjeux

### ❌ À ÉVITER
- Utiliser trop de termes techniques sans les expliquer
- Dire "c'est sécurisé, faites-moi confiance" sans détails
- Minimiser l'importance de la sécurité
- Oublier de parler du RGPD

### 🎯 Message Clé
> "La sécurité n'est pas une option, c'est une responsabilité. J'ai pris ce sujet très au sérieux et j'ai obtenu un score de 8/10."

---

## ⏱️ Timing Checkpoint

Après cette partie, vous devez être à : **20 minutes** (5 + 8 + 7)

**Prêt pour la Partie 4 ? Demandez-moi quand vous êtes prêt !**

---

**🎯 Partie suivante : Fonctionnalités Clés (8 min)**
