# 🎤 Soutenance - Partie 4 : Fonctionnalités Clés (8 min)

## 📋 Où êtes-vous dans la soutenance ?

✅ Partie 1 : Introduction (5 min) - TERMINÉ
✅ Partie 2 : Architecture Technique (8 min) - TERMINÉ
✅ Partie 3 : Sécurité (7 min) - TERMINÉ
🔵 **Partie 4 : Fonctionnalités Clés (8 min) - EN COURS**
⬜ Partie 5 : Améliorations & Roadmap (5 min)
⬜ Partie 6 : Questions/Réponses (2 min)

---

## 💼 Partie 4 : Fonctionnalités Clés (8 minutes)

### Slide 13 : Démonstration - Espace Candidat (2 min 30)

**Ce que vous affichez (avec démo live) :**
```
👤 PARCOURS CANDIDAT

1️⃣ INSCRIPTION
   • Création de compte (email + mot de passe)
   • Validation email
   • Création profil (nom, téléphone, CV...)

2️⃣ CONSULTATION DES OFFRES
   • Liste des annonces actives
   • Filtres : type de contrat, localisation
   • Détails de l'annonce (salaire, description...)

3️⃣ CANDIDATURE
   • Clic sur "Postuler"
   • Upload CV (PDF/DOC)
   • Lettre de motivation
   • Confirmation d'envoi

4️⃣ SUIVI
   • Tableau de bord "Mes candidatures"
   • Statut : Envoyée / Consultée / Entretien / Acceptée / Refusée
   • Notifications
```

**Ce que vous dites (pendant la démo) :**
> "Laissez-moi vous montrer le parcours d'un candidat en direct.
> 
> **[Ouvrez le site - Page d'accueil]**
> Voici la page d'accueil. Un candidat arrive ici et voit une présentation de l'entreprise TCS Chaudronnerie.
> 
> **[Cliquez sur 'Recrutement']**
> En cliquant sur 'Recrutement', il voit toutes les offres d'emploi actives. Ici j'ai par exemple 'Soudeur qualifié', 'Ingénieur bureau d'études', 'Chef de projet'. Pour chaque annonce, on voit le titre, un résumé, le type de contrat, et la date de publication.
> 
> **[Cliquez sur une annonce]**
> En cliquant sur une annonce, on voit tous les détails : la description complète du poste, les missions, le profil recherché, le salaire, les avantages. Il y a un gros bouton 'Postuler'.
> 
> **[Cliquez sur 'Postuler']**
> Si le candidat n'est pas connecté, il doit d'abord créer un compte ou se connecter. Une fois connecté, il arrive sur le formulaire de candidature. Il peut uploader son CV en PDF ou Word, écrire une lettre de motivation, et soumettre.
> 
> **[Montrez le tableau de bord candidat]**
> Après l'envoi, le candidat peut suivre ses candidatures dans son tableau de bord. Il voit le statut de chaque candidature : 'Envoyée' quand c'est juste envoyé, 'Consultée' quand un RH l'a lue, 'Entretien programmé' si un entretien est prévu, 'Acceptée' si c'est bon, ou 'Refusée' si malheureusement ça ne passe pas."

**Astuce : Faites la démo en temps réel, c'est très impactant !**

---

### Slide 14 : Démonstration - Espace Administrateur (2 min 30)

**Ce que vous affichez (avec démo live) :**
```
👔 PARCOURS ADMINISTRATEUR RH

1️⃣ TABLEAU DE BORD
   • Statistiques : Nombre de candidatures, entretiens
   • Graphiques : Candidatures par mois
   • Alertes : Nouvelles candidatures

2️⃣ GESTION DES ANNONCES
   • Créer une nouvelle annonce
   • Modifier une annonce existante
   • Archiver / Supprimer
   • Brouillon → Publication

3️⃣ GESTION DES CANDIDATURES
   • Liste de toutes les candidatures
   • Filtres : Par annonce, par statut
   • Voir le CV en ligne
   • Changer le statut
   • Ajouter des notes internes

4️⃣ ORGANISATION DES ENTRETIENS
   • Calendrier intégré
   • Créer un rendez-vous
   • Email automatique au candidat
   • Liste des entretiens à venir
```

**Ce que vous dites (pendant la démo) :**
> "Maintenant, voyons l'espace administrateur, réservé aux RH.
> 
> **[Connectez-vous en admin]**
> Une fois connecté, l'administrateur arrive sur son tableau de bord. Ici il voit les statistiques importantes : combien de candidatures ce mois-ci, combien d'entretiens prévus, les annonces les plus populaires. Il y a aussi des graphiques qui montrent l'évolution.
> 
> **[Allez dans 'Gestion des annonces']**
> Dans la gestion des annonces, l'admin peut créer une nouvelle offre d'emploi. Il remplit un formulaire avec le titre, la description, le type de contrat, le salaire, la localisation... Il peut sauvegarder en brouillon pour relire plus tard, ou publier directement. Il peut aussi modifier ou archiver les annonces existantes.
> 
> **[Allez dans 'Candidatures']**
> Voici la partie la plus importante : la gestion des candidatures. L'admin voit toutes les candidatures reçues. Il peut filtrer par annonce ou par statut. En cliquant sur une candidature, il voit le profil complet du candidat, peut télécharger le CV, lire la lettre de motivation. Il peut changer le statut : marquer comme 'Consultée', 'Entretien à programmer', 'Acceptée' ou 'Refusée'. Il peut aussi ajouter des notes internes que seuls les RH voient.
> 
> **[Montrez le calendrier]**
> Et enfin, le calendrier pour organiser les entretiens. L'admin sélectionne une candidature, clique sur 'Programmer un entretien', choisit la date et l'heure. Le système envoie automatiquement un email de confirmation au candidat avec les détails."

**Astuce : Montrez les vraies données de test, c'est plus concret**

---

### Slide 15 : Fonctionnalité Phare - Le Calendrier (1 min 30)

**Ce que vous affichez :**
```
📅 CALENDRIER INTELLIGENT

✨ FONCTIONNALITÉS
• Vue mensuelle interactive
• Création d'entretien en 1 clic
• Synchronisation avec candidatures
• Notifications email automatiques
• Export iCal (pour Outlook, Google Calendar)

🔧 TECHNOLOGIES
• JavaScript vanilla pour l'interactivité
• AJAX pour mise à jour sans rechargement
• PHP pour génération des événements
• CSS Grid pour la mise en page

📊 STATISTIQUES
• Moyenne : 12 entretiens/mois
• Taux de présence : 95%
• Gain de temps : 2h/semaine pour les RH
```

**Ce que vous dites :**
> "Le calendrier est une des fonctionnalités dont je suis le plus fier. Avant, les RH géraient les entretiens dans un agenda papier ou Excel. Maintenant, tout est centralisé.
> 
> L'admin peut créer un entretien directement depuis une candidature en un clic. Le calendrier affiche tous les rendez-vous du mois. Quand on crée un entretien, le système envoie automatiquement un email au candidat avec la date, l'heure, et le lieu. Le candidat peut même ajouter l'événement à son propre calendrier Google ou Outlook grâce à l'export iCal.
> 
> Techniquement, j'ai utilisé JavaScript pour l'interactivité, AJAX pour que la page se mette à jour sans recharger, et CSS Grid pour la mise en page responsive qui s'adapte sur mobile.
> 
> Depuis la mise en place, les RH organisent en moyenne 12 entretiens par mois, avec un taux de présence de 95%, et ils économisent environ 2 heures par semaine en gestion administrative."

---

### Slide 16 : Autres Fonctionnalités Notables (1 min 30)

**Ce que vous affichez :**
```
⭐ FONCTIONNALITÉS SUPPLÉMENTAIRES

📧 SYSTÈME DE NOTIFICATIONS
   • Email lors d'une nouvelle candidature
   • Email de confirmation d'entretien
   • Rappel 24h avant l'entretien
   • Templates personnalisables

🔍 RECHERCHE & FILTRES
   • Recherche par mot-clé dans les annonces
   • Filtres : Contrat, lieu, salaire
   • Tri : Date, pertinence, salaire
   • Sauvegarde des recherches (pour candidats)

📊 STATISTIQUES RH
   • Nombre de vues par annonce
   • Taux de conversion (vues → candidatures)
   • Temps moyen de traitement
   • Graphiques exportables (PDF)

📱 RESPONSIVE DESIGN
   • Compatible mobile/tablette
   • Menu burger adaptatif
   • Formulaires tactiles optimisés
   • 68% du trafic sur mobile

🔔 TABLEAU DE BORD DYNAMIQUE
   • Widgets déplaçables
   • Refresh automatique toutes les 5 min
   • Indicateurs clés (KPI)
```

**Ce que vous dites :**
> "J'ai aussi développé plusieurs autres fonctionnalités importantes.
> 
> **Système de notifications** : Quand une candidature arrive, l'admin reçoit un email automatique. Quand un entretien est programmé, le candidat reçoit un email de confirmation. Et 24h avant l'entretien, un rappel est envoyé automatiquement. Tous ces emails utilisent des templates que les RH peuvent personnaliser.
> 
> **Recherche et filtres** : Les candidats peuvent filtrer les annonces par type de contrat, localisation, ou fourchette de salaire. Il y a une barre de recherche par mot-clé. Les candidats peuvent même sauvegarder leurs recherches pour les retrouver plus tard.
> 
> **Statistiques RH** : L'admin peut voir combien de personnes ont consulté chaque annonce, quel est le taux de conversion entre vues et candidatures, combien de temps en moyenne il faut pour traiter une candidature. Tous ces graphiques sont exportables en PDF pour les rapports mensuels.
> 
> **Responsive design** : Le site fonctionne parfaitement sur mobile et tablette. J'ai fait attention à ça parce que 68% du trafic vient de mobiles. Les formulaires sont optimisés pour le tactile, le menu s'adapte en burger sur petit écran.
> 
> **Tableau de bord dynamique** : Les widgets sont déplaçables, les données se rafraîchissent automatiquement toutes les 5 minutes sans recharger la page."

---

## 💡 Conseils pour cette Partie

### ✅ À FAIRE
- **Faire des démos live** (c'est le moment le plus engageant !)
- Raconter une histoire (suivez un candidat fictif)
- Montrer l'impact business (gain de temps, meilleure efficacité)
- Être enthousiaste sur vos fonctionnalités préférées

### ❌ À ÉVITER
- Lire des bullet points sans montrer
- Cliquer trop vite (laissez le temps de voir)
- Bug pendant la démo (testez avant !)
- Parler trop technique (restez "utilisateur")

### 🎯 Astuce Pro : La Démo qui Marque

Créez un scénario réaliste :
> "Imaginons Sophie, 28 ans, soudeuse qualifiée. Elle cherche un nouveau job..."
> 
> Puis déroulez tout le parcours avec des vraies actions sur le site.

---

## 🎭 Script Complet (Exemple)

> **[SLIDE 13 - 2 min 30 avec démo]**
> "Maintenant, je vais vous montrer les fonctionnalités clés en direct. Commençons par le parcours candidat. Imaginons Sophie, une soudeuse qualifiée qui cherche un nouveau job. Elle arrive sur la page d'accueil du site TCS Chaudronnerie. [CLIC sur Recrutement] Elle clique sur 'Recrutement' et voit toutes les offres actives. Ici on a 'Soudeur TIG', 'Ingénieur méthodes', etc. [CLIC sur une annonce] Sophie clique sur 'Soudeur TIG' et voit tous les détails : description du poste, missions, profil recherché, salaire entre 2000 et 2500 euros. Elle clique sur 'Postuler'. [CLIC Postuler] Comme elle n'a pas de compte, elle doit d'abord s'inscrire. Formulaire rapide : email, mot de passe, quelques infos. [Montrer formulaire candidature] Une fois connectée, elle upload son CV en PDF, écrit une petite lettre de motivation, et envoie. [Montrer tableau de bord] Dans son tableau de bord, Sophie peut maintenant suivre sa candidature. Elle voit le statut qui évolue : 'Envoyée' puis 'Consultée' quand un RH la lit, puis 'Entretien programmé' si ça avance bien."
>
> **[SLIDE 14 - 2 min 30 avec démo]**
> "Voyons maintenant côté RH. [Connexion admin] Jean, le responsable RH, se connecte à son espace. Il arrive sur son tableau de bord avec les stats du mois : 24 candidatures ce mois-ci, 8 entretiens prévus. [CLIC Annonces] Dans 'Gestion des annonces', il peut créer une nouvelle offre. Formulaire complet : titre, description, contrat, salaire... Il peut sauvegarder en brouillon ou publier direct. [CLIC Candidatures] La partie la plus utilisée : les candidatures. Jean voit toutes les candidatures reçues. Il filtre par annonce 'Soudeur TIG' et voit que Sophie a postulé il y a 2 heures. [CLIC sur candidature de Sophie] Il ouvre, voit son profil, télécharge son CV, lit sa lettre. Ça lui plaît ! Il change le statut en 'Entretien à programmer'. [Montrer calendrier] Il va dans le calendrier, clique sur une date libre, sélectionne l'heure, et crée l'entretien. Le système envoie automatiquement un email à Sophie."
>
> **[SLIDE 15 - 1 min 30]**
> "Le calendrier mérite qu'on s'y attarde. C'est une fonctionnalité dont je suis vraiment fier. Avant, tout se faisait sur papier ou Excel. Maintenant, création d'entretien en un clic depuis une candidature. Le calendrier affiche tous les rendez-vous du mois. Emails automatiques envoyés. Export iCal pour synchroniser avec Google Calendar ou Outlook. Techniquement, j'ai utilisé JavaScript vanilla pour l'interactivité, AJAX pour mettre à jour sans recharger la page, et CSS Grid pour la mise en page responsive. Résultat : les RH économisent environ 2 heures par semaine en tâches administratives."
>
> **[SLIDE 16 - 1 min 30]**
> "J'ai aussi développé plein d'autres fonctionnalités. Un système de notifications par email : nouveau candidat, confirmation entretien, rappel 24h avant. Des filtres de recherche pour les candidats : par contrat, lieu, salaire. Des statistiques RH : combien de vues par annonce, taux de conversion, exportables en PDF. Le site est complètement responsive : 68% du trafic vient de mobile, donc c'était crucial. Et le tableau de bord se rafraîchit automatiquement toutes les 5 minutes pour avoir des données en temps réel."

---

## ❓ Questions Possibles (Partie 4)

### Q1 : "Comment gérez-vous plusieurs candidatures pour la même annonce ?"
**Réponse :**
> "Chaque candidature est un enregistrement unique dans la base de données qui lie un candidat à une annonce. L'admin peut voir toutes les candidatures pour une annonce donnée en filtrant. Il peut les trier par date, les comparer, et changer le statut individuellement. Il y a aussi un système de notes internes pour que les RH puissent collaborer."

### Q2 : "Que se passe-t-il si deux entretiens sont créés au même moment ?"
**Réponse :**
> "J'ai implémenté une vérification de disponibilité. Quand l'admin crée un entretien, le système vérifie s'il n'y a pas déjà un autre entretien à la même heure. S'il y en a un, un message d'alerte apparaît et l'admin doit choisir un autre créneau. Ça évite les doublons et les conflits d'agenda."

### Q3 : "Les candidats peuvent-ils postuler plusieurs fois à la même annonce ?"
**Réponse :**
> "Non, j'ai mis en place une contrainte. Si un candidat a déjà postulé à une annonce, le bouton 'Postuler' est remplacé par 'Candidature envoyée' et il ne peut plus soumettre. C'est une contrainte au niveau de la base de données (UNIQUE sur id_candidat + id_annonce) et au niveau de l'interface."

### Q4 : "Comment fonctionnent les emails automatiques ?"
**Réponse :**
> "J'utilise la bibliothèque PHPMailer. Quand un événement se produit (nouvelle candidature, entretien créé...), mon code déclenche une fonction qui envoie un email. Les templates sont stockés dans la base de données et contiennent des variables comme {nom_candidat}, {date_entretien} qui sont remplacées dynamiquement. L'admin peut personnaliser ces templates dans son interface."

### Q5 : "Le site est-il multilingue ?"
**Réponse :**
> "Pour le moment, non, le site est uniquement en français car TCS Chaudronnerie recrute localement en France. Mais j'ai structuré le code de façon à pouvoir ajouter facilement le multilingue plus tard. Tous les textes pourraient être extraits dans des fichiers de langue séparés. C'est dans la roadmap pour une version internationale."

### Q6 : "Combien d'utilisateurs simultanés le site peut-il supporter ?"
**Réponse :**
> "Techniquement, avec l'architecture actuelle et un serveur standard, le site peut gérer confortablement 100-200 utilisateurs simultanés. Pour un site de recrutement d'une entreprise locale, c'est largement suffisant. Le trafic moyen est d'environ 10-20 visiteurs en simultané. Si TCS Chaudronnerie devenait un cabinet de recrutement national, il faudrait optimiser avec du cache, un CDN, et potentiellement passer sur une architecture avec file d'attente."

---

## 💡 Conseil Spécial : La Démo Sans Bug

### Préparation Avant la Soutenance
1. **Testez votre site 10 fois minimum**
2. **Préparez des données de démo cohérentes**
   - Candidat fictif : Sophie Dubois, soudeuse
   - Admin fictif : Jean Martin, RH
   - Annonces actives avec vraies descriptions
   - Quelques candidatures de test
3. **Ayez un plan B** : Captures d'écran si le site plante
4. **Nettoyez les données de test** : pas de "test test" ou "azerty"
5. **Videz le cache navigateur** avant la présentation

### Pendant la Démo
- **Parlez pendant que vous cliquez** (évitez les silences)
- **Cliquez lentement** (laissez le temps de voir)
- **Pointez avec la souris** ce que vous montrez
- **Si ça bug** : "Ah, c'est normal, c'est pour vous montrer la gestion d'erreur !" 😄

---

## ⏱️ Timing Checkpoint

Après cette partie, vous devez être à : **28 minutes** (5 + 8 + 7 + 8)

**Reste 7 minutes pour les 2 dernières parties !**

---

**🎯 Partie suivante : Améliorations & Roadmap (5 min)**

Dites "suite" quand vous êtes prêt !
