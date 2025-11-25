# 🎤 Soutenance - Partie 2 : Architecture Technique (8 min)

## 📋 Où êtes-vous dans la soutenance ?

✅ Partie 1 : Introduction (5 min) - TERMINÉ
🔵 **Partie 2 : Architecture Technique (8 min) - EN COURS**
⬜ Partie 3 : Sécurité (7 min)
⬜ Partie 4 : Fonctionnalités Clés (8 min)
⬜ Partie 5 : Améliorations & Roadmap (5 min)
⬜ Partie 6 : Questions/Réponses (2 min)

---

## 🏗️ Partie 2 : Architecture Technique (8 minutes)

### Slide 5 : Le Modèle MVC Simplifié (2 min)

**Ce que vous affichez :**
```
🏗️ ARCHITECTURE MVC
(Model-View-Controller)

┌─────────────────────────────────────┐
│      UTILISATEUR (Navigateur)       │
└──────────────┬──────────────────────┘
               │ Requête HTTP
               ↓
┌──────────────────────────────────────┐
│         CONTROLLER                   │
│  (Contrôleur - Le Chef)             │
│  • Reçoit la demande                │
│  • Décide quoi faire                │
│  • Coordonne Model et View          │
└─────┬────────────────────┬───────────┘
      │                    │
      ↓                    ↓
┌─────────────┐      ┌──────────────┐
│   MODEL     │      │     VIEW     │
│ (Le Coffre) │      │ (L'Afficheur)│
│ • Base de   │      │ • Génère le  │
│   données   │      │   HTML       │
│ • Requêtes  │      │ • Affiche    │
│   SQL       │      │   les infos  │
└─────────────┘      └──────────────┘
```

**Ce que vous dites (vulgarisé) :**
> "Pour organiser mon code, j'ai utilisé l'architecture MVC. Imaginez un restaurant :
> 
> - Le **Controller**, c'est le serveur qui prend votre commande. Il reçoit votre demande, décide quoi faire, et coordonne tout.
> 
> - Le **Model**, c'est la cuisine et le garde-manger. C'est là qu'on stocke et qu'on récupère les données dans la base de données.
> 
> - La **View**, c'est l'assiette dressée qu'on vous présente. Elle prend les données et les affiche joliment en HTML.
> 
> Cette séparation est importante parce que si je veux changer l'apparence du site (la View), je ne touche pas à la logique ou à la base de données. Tout est bien séparé, c'est plus facile à maintenir."

**Astuce : Dessinez au tableau si possible, ou animez les flèches**

---

### Slide 6 : Exemple Concret - Voir une Annonce (2 min 30)

**Ce que vous affichez :**
```
🔍 EXEMPLE : Un candidat clique sur une annonce

1️⃣ REQUÊTE
   URL : www.tcs.com/annonce?id=42
   ↓

2️⃣ CONTROLLER (AnnonceController.php)
   • Lit l'ID dans l'URL (42)
   • Vérifie que c'est bien un nombre
   • Demande l'annonce au Model
   ↓

3️⃣ MODEL (AnnonceModel.php)
   • Se connecte à la base de données
   • Exécute : SELECT * FROM annonce WHERE id = 42
   • Retourne les données
   ↓

4️⃣ CONTROLLER (suite)
   • Reçoit les données
   • Les envoie à la View
   ↓

5️⃣ VIEW (AnnonceView.php)
   • Génère le HTML
   • Affiche : titre, description, salaire...
   ↓

6️⃣ RÉPONSE
   Page HTML affichée dans le navigateur
```

**Ce que vous dites :**
> "Prenons un exemple concret. Un candidat clique sur une annonce avec l'ID 42.
> 
> 1. Le **Controller** reçoit la requête et lit l'ID dans l'URL
> 2. Il vérifie que c'est bien un nombre (sécurité)
> 3. Il demande au **Model** : 'Donne-moi l'annonce numéro 42'
> 4. Le **Model** va chercher dans la base de données avec une requête SQL
> 5. Il renvoie les données au **Controller**
> 6. Le **Controller** les transmet à la **View**
> 7. La **View** génère le joli HTML avec le titre, la description, le salaire
> 8. Le navigateur affiche la page
> 
> Tout ça se passe en quelques millisecondes !"

**Astuce : Montrez le code d'AnnonceController.php rapidement**

---

### Slide 7 : Structure des Fichiers (1 min 30)

**Ce que vous affichez :**
```
📁 ORGANISATION DU CODE

app/
├── controller/          ← 8 contrôleurs
│   ├── AnnonceController.php
│   ├── CandidatureController.php
│   ├── UtilisateurController.php
│   └── ...
│
├── model/              ← 8 modèles
│   ├── AnnonceModel.php
│   ├── CandidatureModel.php
│   ├── UtilisateurModel.php
│   └── ...
│
├── view/               ← 11 vues
│   ├── AnnonceView.php
│   ├── CandidatureView.php
│   ├── UtilisateurView.php
│   └── ...
│
├── Security.php        ← Classe de sécurité
├── Router.php          ← Gestion des URLs
└── Database.php        ← Connexion BDD

index.php               ← Point d'entrée
```

**Ce que vous dites :**
> "Mon code est organisé de façon très claire. Dans le dossier 'app', j'ai trois sous-dossiers principaux : 'controller' avec mes 8 contrôleurs, 'model' avec mes 8 modèles qui gèrent la base de données, et 'view' avec mes 11 vues pour l'affichage.
> 
> J'ai aussi créé des classes utilitaires comme Security.php pour toute la sécurité, Router.php pour gérer les URLs, et Database.php pour la connexion à la base de données.
> 
> Le fichier index.php est le point d'entrée : toutes les requêtes passent par lui, comme une porte d'entrée unique."

---

### Slide 8 : Base de Données (2 min)

**Ce que vous affichez :**
```
🗄️ BASE DE DONNÉES (8 tables)

┌─────────────────┐
│   utilisateur   │  ← Comptes (candidats, admins)
│  • id           │
│  • email        │
│  • password     │
│  • role         │
└────────┬────────┘
         │
         ↓
┌─────────────────┐
│    candidat     │  ← Profils candidats
│  • id           │
│  • nom          │
│  • cv_path      │
└────────┬────────┘
         │
         ↓
┌─────────────────┐        ┌─────────────────┐
│  candidature    │───────→│     annonce     │
│  • id           │        │  • id           │
│  • statut       │        │  • titre        │
│  • date_envoi   │        │  • description  │
└────────┬────────┘        │  • salaire      │
         │                 └─────────────────┘
         ↓
┌─────────────────┐
│    entretien    │  ← Rendez-vous
│  • id           │
│  • date         │
│  • heure        │
└─────────────────┘

+ 3 autres tables : news, calendrier, administrateur
```

**Ce que vous dites :**
> "La base de données est le cœur du site. J'ai 8 tables principales qui sont toutes reliées entre elles.
> 
> - La table **utilisateur** stocke tous les comptes avec email, mot de passe crypté, et le rôle (candidat ou admin)
> 
> - La table **candidat** contient les profils détaillés : nom, prénom, téléphone, et le chemin vers le CV uploadé
> 
> - La table **annonce** stocke les offres d'emploi : titre, description, salaire, type de contrat...
> 
> - La table **candidature** fait le lien entre un candidat et une annonce. C'est là qu'on stocke le statut : 'envoyée', 'consultée', 'retenue', 'refusée'...
> 
> - La table **entretien** gère les rendez-vous avec date, heure et lieu
> 
> Toutes ces tables sont reliées par des clés étrangères. Par exemple, une candidature pointe vers un candidat ET vers une annonce. C'est ce qu'on appelle des relations."

**Astuce : Montrez un schéma de base de données si vous en avez un**

---

## 💡 Analogies pour Vulgariser

### MVC = Restaurant
- **Model** = La cuisine (préparation)
- **View** = L'assiette (présentation)
- **Controller** = Le serveur (coordination)

### Base de Données = Bibliothèque
- **Tables** = Étagères différentes (livres, magazines, DVD...)
- **Colonnes** = Informations sur chaque livre (titre, auteur, année...)
- **Lignes** = Chaque livre individuel
- **Relations** = Index qui dit "ce livre est écrit par cet auteur"

### Requête HTTP = Lettre à La Poste
1. Vous écrivez (navigateur envoie requête)
2. Le facteur livre (serveur web reçoit)
3. Le destinataire lit et répond (PHP traite)
4. Vous recevez la réponse (HTML s'affiche)

---

## 🎭 Script Complet (Exemple)

> **[SLIDE 5 - 2 min]**
> "Maintenant, parlons de l'architecture technique. J'ai utilisé le modèle MVC, qui signifie Model-View-Controller. Pour vulgariser, imaginez un restaurant. Le Controller, c'est le serveur qui prend votre commande et coordonne tout. Le Model, c'est la cuisine qui prépare les plats et stocke les ingrédients, dans notre cas c'est la base de données. Et la View, c'est l'assiette joliment dressée qu'on vous présente. Cette séparation est cruciale : si je veux changer le design du site, je ne touche que la View. Si je veux changer la structure de la base de données, je ne touche que le Model. Tout est indépendant."
>
> **[SLIDE 6 - 2 min 30]**
> "Prenons un exemple concret pour bien comprendre. Imaginez qu'un candidat clique sur une annonce. Voici ce qui se passe en quelques millisecondes : D'abord, le Controller reçoit la requête avec l'ID de l'annonce, par exemple 42. Il vérifie que c'est bien un nombre pour la sécurité. Ensuite, il demande au Model : 'Donne-moi l'annonce numéro 42'. Le Model se connecte à la base de données et exécute une requête SQL pour récupérer les informations. Il renvoie les données au Controller. Le Controller les transmet à la View qui génère le HTML avec le titre, la description, le salaire, tout bien formaté. Enfin, le navigateur affiche la page. Et voilà, tout ça en une fraction de seconde !"
>
> **[SLIDE 7 - 1 min 30]**
> "Mon code est organisé de manière très structurée. Dans le dossier 'app', j'ai séparé les contrôleurs, les modèles, et les vues dans trois dossiers différents. J'ai 8 contrôleurs qui gèrent la logique, 8 modèles qui communiquent avec la base de données, et 11 vues qui affichent les pages. J'ai aussi créé des classes utilitaires : Security.php gère toute la sécurité, Router.php gère les URLs et le routage, et Database.php gère la connexion à la base de données. Toutes les requêtes passent par index.php, c'est la porte d'entrée unique du site."
>
> **[SLIDE 8 - 2 min]**
> "Côté base de données, j'ai 8 tables principales. La table 'utilisateur' stocke tous les comptes avec email et mot de passe crypté. La table 'candidat' contient les profils détaillés avec CV. La table 'annonce' stocke les offres d'emploi. La table 'candidature' fait le lien entre un candidat et une annonce, avec le statut de la candidature. Et la table 'entretien' gère les rendez-vous. Toutes ces tables sont reliées entre elles par ce qu'on appelle des clés étrangères. Par exemple, une candidature pointe vers un candidat précis ET vers une annonce précise. C'est comme ça qu'on sait qui a postulé à quoi."

---

## ❓ Questions Possibles (Partie 2)

### Q1 : "Pourquoi avoir choisi MVC ?"
**Réponse :**
> "MVC est un standard dans le développement web parce qu'il sépare clairement les responsabilités. C'est plus facile à maintenir, plusieurs développeurs peuvent travailler en même temps sur des parties différentes, et si je veux changer quelque chose, je sais exactement où aller. C'est comme avoir des tiroirs bien rangés au lieu d'un grand carton en vrac."

### Q2 : "Est-ce que vous avez utilisé un framework ?"
**Réponse :**
> "Non, j'ai développé en PHP natif sans framework comme Symfony ou Laravel. C'était un choix pédagogique pour bien comprendre comment tout fonctionne sous le capot. Utiliser un framework aurait été plus rapide, mais là j'ai vraiment appris les fondamentaux. Par contre, j'ai utilisé Composer pour gérer quelques dépendances comme Dotenv pour les variables d'environnement."

### Q3 : "Comment gérez-vous les erreurs SQL ?"
**Réponse :**
> "J'utilise des requêtes préparées avec PDO, ce qui empêche les injections SQL. Si une erreur se produit, j'ai mis en place un système de gestion d'erreurs qui log l'erreur dans un fichier et affiche un message générique à l'utilisateur sans révéler d'informations sensibles. En développement, j'affiche les détails, mais en production, je les cache."

### Q4 : "Quelle est la différence entre utilisateur et candidat ?"
**Réponse :**
> "Bonne question ! La table 'utilisateur' contient les informations de connexion (email, mot de passe, rôle). C'est pour s'authentifier. La table 'candidat' contient le profil détaillé (nom, prénom, téléphone, CV...). Un utilisateur peut être un candidat OU un administrateur. C'est une séparation logique : l'authentification d'un côté, les données métier de l'autre."

### Q5 : "Comment les tables sont-elles reliées ?"
**Réponse :**
> "J'utilise des clés étrangères. Par exemple, la table 'candidature' a une colonne 'id_candidat' qui pointe vers l'ID dans la table 'candidat', et une colonne 'id_annonce' qui pointe vers l'ID dans la table 'annonce'. C'est comme des liens hypertexte dans la base de données. Ça permet de faire des jointures SQL pour récupérer toutes les infos en une seule requête."

---

## 💡 Conseils pour cette Partie

### ✅ À FAIRE
- Utiliser des analogies simples (restaurant, bibliothèque...)
- Montrer du code rapidement (pas lire ligne par ligne)
- Dessiner au tableau si possible
- Rester enthousiaste même sur la partie technique

### ❌ À ÉVITER
- Rentrer dans trop de détails techniques
- Dire des mots comme "polymorphisme", "encapsulation" sans les expliquer
- Lire le code ligne par ligne
- Assumer que tout le monde connaît SQL

### 🎯 Astuce Pro
Si quelqu'un semble perdu, revenez à une analogie :
> "C'est comme une bibliothèque : chaque table est une étagère différente, et les relations sont l'index qui dit où trouver quoi."

---

## ⏱️ Timing Checkpoint

Après cette partie, vous devez être à : **13 minutes** (5 + 8)

**Prêt pour la Partie 3 ? Demandez-moi quand vous êtes prêt !**

---

**🎯 Partie suivante : Sécurité (7 min)**
