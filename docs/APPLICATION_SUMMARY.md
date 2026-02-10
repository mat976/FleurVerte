# FleurVerte - Documentation Complète de l'Application

## 🌸 Vue d'ensemble

**FleurVerte** est une application e-commerce/marketplace moderne spécialisée dans la vente de fleurs et la mise en relation entre clients et fleuristes. Développée avec **Symfony 7**, elle offre une plateforme complète pour la gestion de catalogues, la communication client-fleuriste et les transactions commerciales.

---

## 📋 Table des matières

1. [Concept et Vision](#concept-et-vision)
2. [Architecture Technique](#architecture-technique)
3. [Modèle de Données](#modèle-de-données)
4. [Cas d'Utilisation](#cas-dutilisation)
5. [User Stories](#user-stories)
6. [Fonctionnalités Principales](#fonctionnalités-principales)
7. [Pages et Interfaces](#pages-et-interfaces)
8. [Flux Utilisateurs](#flux-utilisateurs)
9. [Technologies et Stack](#technologies-et-stack)
10. [Sécurité](#sécurité)
11. [Performance et Scalabilité](#performance-et-scalabilité)

---

## 🎯 Concept et Vision

### Mission
Fournir une marketplace intuitive et moderne connectant les passionnés de fleurs avec des fleuristes locaux, en offrant une expérience d'achat exceptionnelle et des outils de communication efficaces.

### Valeurs Clés
- **Qualité** : Sélection rigoureuse des fleuristes partenaires
- **Proximité** : Mise en relation avec des fleuristes locaux
- **Communication** : Dialogue direct entre clients et fleuristes
- **Simplicité** : Interface utilisateur moderne et intuitive

---

## 🏗️ Architecture Technique

### Architecture Globale
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Base de       │
│   (Twig +      │◄──►│   (Symfony 7)   │◄──►│   Données       │
│   Stimulus)     │    │                 │    │   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                       │                       │
        ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Assets        │    │   Services      │    │   Fichiers      │
│   (Webpack)     │    │   Métier        │    │   (Uploads)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Pattern Architectural
- **MVC (Model-View-Controller)** : Séparation claire des responsabilités
- **Domain-Driven Design** : Services métier organisés par domaine
- **Repository Pattern** : Abstraction de l'accès aux données
- **Dependency Injection** : Gestion des dépendances via le conteneur Symfony

---

## 🗄️ Modèle de Données

### Entités Principales

#### User (Utilisateur)
- **Rôle** : Entité centrale gérant l'authentification
- **Relations** : One-to-One avec Fleuriste et Client
- **Fonctionnalités** : Avatars, messages, adresses

#### Fleur (Produit)
- **Rôle** : Catalogue des produits disponibles
- **Attributs** : Nom, description, prix, stock, tags
- **Relations** : Many-to-One avec Fleuriste, Many-to-Many avec Tag

#### Fleuriste (Vendeur)
- **Rôle** : Profils des vendeurs/partenaires
- **Attributs** : Informations boutique, galerie, localisation
- **Relations** : One-to-One avec User, One-to-Many avec Fleur

#### CartItem (Panier)
- **Rôle** : Articles dans le panier des clients
- **Attributs** : Quantité, lien User-Fleur
- **Logique** : Gestion des stocks et calculs

#### Message & Conversation
- **Rôle** : Communication client-fleuriste
- **Attributs** : Contenu, timestamp, statut de lecture
- **Relations** : Expéditeur et destinataire

### Schéma Relationnel Simplifié
```
User (1) ──── (1) Fleuriste
User (1) ──── (1) Client
User (1) ──── (N) CartItem ──── (1) Fleur
User (1) ──── (N) Message
Fleuriste (1) ──── (N) Fleur
Fleur (N) ──── (N) Tag
```

---

## 🎭 Cas d'Utilisation

### Acteurs Principaux
1. **Client** : Acheteur de fleurs
2. **Fleuriste** : Vendeur partenaire
3. **Administrateur** : Gestion de la plateforme

### Cas d'Utilisation Clients

#### CU-001: Navigation et Recherche
- **Acteur** : Client
- **Objectif** : Trouver des fleurs correspondant à ses besoins
- **Préconditions** : Aucune
- **Scénario principal** :
  1. Le client accède au catalogue
  2. Il utilise la barre de recherche
  3. Il applique des filtres (tags, prix, tri)
  4. Il consulte les résultats paginés
  5. Il clique sur un produit pour voir les détails

#### CU-002: Gestion du Panier
- **Acteur** : Client
- **Objectif** : Gérer ses articles avant achat
- **Préconditions** : Client connecté
- **Scénario principal** :
  1. Ajout d'articles au panier
  2. Modification des quantités
  3. Suppression d'articles
  4. Consultation du total
  5. Validation de la commande

#### CU-003: Communication avec Fleuriste
- **Acteur** : Client
- **Objectif** : Poser des questions sur un produit
- **Préconditions** : Client connecté
- **Scénario principal** :
  1. Accès à la fiche d'un fleuriste
  2. Envoi d'un message
  3. Consultation de l'historique
  4. Réception des réponses

### Cas d'Utilisation Fleuristes

#### CU-004: Gestion du Catalogue
- **Acteur** : Fleuriste
- **Objectif** : Administrer ses produits
- **Préconditions** : Fleuriste connecté et actif
- **Scénario principal** :
  1. Création de nouvelles fleurs
  2. Mise à jour des informations
  3. Gestion des stocks
  4. Ajout de photos
  5. Association de tags

#### CU-005: Communication Client
- **Acteur** : Fleuriste
- **Objectif** : Répondre aux demandes clients
- **Préconditions** : Fleuriste connecté
- **Scénario principal** :
  1. Consultation des messages reçus
  2. Rédaction des réponses
  3. Gestion de l'historique
  4. Notifications des nouveaux messages

---

## 📖 User Stories

### Client Stories

#### Epic: Découverte Produits
- **En tant que** client visitant,
  **Je veux** parcourir le catalogue de fleurs,
  **Afin de** découvrir les produits disponibles.

- **En tant que** client cherchant un type spécifique,
  **Je veux** utiliser la recherche par mots-clés et tags,
  **Afin de** trouver rapidement ce qui m'intéresse.

- **En tant que** client comparant des options,
  **Je veux** trier les résultats par prix ou nom,
  **Afin de** prendre une décision éclairée.

#### Epic: Achat
- **En tant que** client intéressé,
  **Je veux** ajouter des articles à mon panier,
  **Afin de** préparer ma commande.

- **En tant que** client organisé,
  **Je veux** modifier les quantités dans mon panier,
  **Afin de** ajuster ma commande selon mon budget.

- **En tant que** client prudent,
  **Je veux** consulter les détails complets d'un produit,
  **Afin de** valider mon choix avant achat.

#### Epic: Communication
- **En tant que** client curieux,
  **Je veux** poser des questions directement au fleuriste,
  **Afin de** obtenir des informations précises sur les produits.

- **En tant que** client suivi,
  **Je veux** consulter l'historique de mes conversations,
  **Afin de** retrouver facilement les informations échangées.

### Fleuriste Stories

#### Epic: Gestion Boutique
- **En tant que** fleuriste,
  **Je veux** créer et modifier mes fiches produits,
  **Afin de** maintenir mon catalogue à jour.

- **En tant que** vendeur professionnel,
  **Je veux** gérer mes niveaux de stock en temps réel,
  **Afin de** éviter les ruptures et les surventes.

- **En tant que** fleuriste soucieux de son image,
  **Je veux** personnaliser ma boutique avec mes photos,
  **Afin de** présenter mon savoir-faire.

#### Epic: Relation Client
- **En tant que** fleuriste à l'écoute,
  **Je veux** recevoir et répondre aux messages des clients,
  **Afin de** construire une relation de confiance.

- **En tant que** professionnel organisé,
  **Je veux** suivre toutes mes conversations,
  **Afin de** garantir un service client de qualité.

---

## 🚀 Fonctionnalités Principales

### 🌺 Catalogue et Recherche
- **Navigation intuitive** : Interface moderne avec pagination fluide
- **Recherche avancée** : Mots-clés, filtres par tags, tri multiple
- **Fiches produits détaillées** : Photos, descriptions, prix, disponibilité
- **Système de tags** : Catégorisation flexible (couleur, type, occasion)
- **Mise en avant** : Produits épinglés pour la visibilité

### 👥 Gestion Utilisateurs
- **Profils polyvalents** : Un utilisateur peut être client ET fleuriste
- **Authentification sécurisée** : Login/register avec validation
- **Avatars personnalisables** : Upload ou sélection dans une galerie
- **Gestion d'adresses** : Plusieurs adresses de livraison par client
- **Rôles dynamiques** : Attribution automatique selon les profils actifs

### 🛒 Panier Intelligent
- **Gestion en temps réel** : Ajout, modification, suppression d'articles
- **Calculs automatiques** : Totaux avec gestion des promotions futures
- **Persistance session** : Sauvegarde du panier entre connexions
- **Gestion des stocks** : Vérification automatique de disponibilité
- **Interface responsive** : Expérience optimale sur tous appareils

### 💬 Messagerie Temps-Réel
- **Conversations privées** : Communication directe client-fleuriste
- **Historique complet** : Conservation de tous les échanges
- **Notifications** : Alertes pour nouveaux messages
- **Interface moderne** : Chat intuitif avec statuts de lecture
- **Organisation** : Regroupement par conversations

### 📸 Gestion Média
- **Upload optimisé** : VichUploader avec redimensionnement automatique
- **Galeries multiples** : Images pour produits et profils fleuristes
- **Avatars prédéfinis** : Sélection d'icônes personnalisées
- **Stockage structuré** : Organisation par type de contenu

---

## 📄 Pages et Interfaces

### 🏠 Pages Publiques
1. **Accueil (`/`)**
   - Hero section avec appel à l'action
   - Produits mis en avant
   - Présentation des fleuristes partenaires
   - Témoignages clients

2. **Catalogue (`/products`)**
   - Grille de produits responsive
   - Barre de recherche avec autocomplétion
   - Filtres latéraux par tags
   - Options de tri (nom, prix)
   - Pagination infinie ou classique

3. **Détail Produit (`/products/{id}`)**
   - Carrousel d'images
   - Informations complètes
   - Système de commentaires/notations
   - Bouton d'ajout au panier
   - Informations fleuriste

4. **Fleuristes (`/fleuristes`)**
   - Annuaire des fleuristes
   - Fiches détaillées avec galerie
   - Système de notation
   - Options de contact

### 🔐 Pages Authentifiées
5. **Panier (`/cart`)**
   - Liste des articles avec quantités
   - Résumé des totaux
   - Actions de modification
   - Bouton de validation commande

6. **Profil (`/profil`)**
   - Informations personnelles
   - Adresses de livraison
   - Historique commandes
   - Préférences utilisateur

7. **Messagerie (`/messages`)**
   - Liste des conversations
   - Interface de chat
   - Historique des échanges
   - Notifications en temps réel

### 🌸 Pages Fleuristes
8. **Dashboard Fleuriste (`/fleuriste/dashboard`)**
   - Statistiques ventes
   - Gestion produits
   - Messages clients
   - Informations boutique

9. **Gestion Produits (`/fleuriste/products`)**
   - CRUD complet des fleurs
   - Gestion des stocks
   - Upload d'images
   - Association tags

### 🛠️ Pages Administratives
10. **Administration**
    - Gestion utilisateurs
    - Modération contenus
    - Statistiques plateforme
    - Configuration système

---

## 🔄 Flux Utilisateurs

### Flux Client Standard
```
1. Accès au site
2. Navigation dans le catalogue
3. Recherche/filtrage de produits
4. Consultation fiches produits
5. Ajout au panier
6. Connexion/Création compte
7. Validation panier
8. Communication avec fleuriste (optionnel)
9. Finalisation commande
```

### Flux Fleuriste
```
1. Connexion à l'espace pro
2. Accès dashboard
3. Gestion catalogue produits
4. Mise à jour stocks
5. Réponse messages clients
6. Suivi commandes
7. Analyse performances
```

### Flux Inscription
```
1. Formulaire d'inscription
2. Validation email
3. Choix du profil (client/fleuriste/les deux)
4. Configuration avatar
5. Ajout informations complémentaires
6. Accès à l'espace personnel
```

---

## 💻 Technologies et Stack

### Backend Symfony 7
- **PHP 8.2+** : Dernières fonctionnalités du langage
- **Symfony Framework Bundle** : Cœur du framework
- **Symfony Security Bundle** : Authentification et autorisation
- **Doctrine ORM 3** : Mapping objet-relationnel
- **Doctrine Migrations** : Versioning base de données

### Frontend Moderne
- **Twig 3** : Moteur de templates
- **Webpack Encore 2.2** : Bundling et optimisation
- **Tailwind CSS** : Framework CSS utility-first
- **Stimulus 3** : Framework JavaScript léger
- **Turbo** : Navigation SPA-like
- **Alpine.js 2** : Réactivité côté client

### Base de Données
- **MySQL 5.7** : Base de données relationnelle
- **Doctrine DBAL 3** : Couche d'abstraction
- **Fixtures Bundle** : Données de test

### Gestion Fichiers
- **VichUploaderBundle 2.4** : Upload et gestion d'images
- **Stockage local** : `public/uploads/` organisé par type

### Outils Développement
- **Symfony CLI** : Serveur de développement
- **Maker Bundle** : Génération de code
- **PHPUnit** : Tests unitaires et fonctionnels
- **Web Profiler** : Débogage et profilage

### Docker et Déploiement
- **Docker** : Conteneurisation
- **Docker Compose** : Orchestration multi-services
- **Nginx/Apache** : Serveurs web

---

## 🔒 Sécurité

### Authentification et Autorisation
- **Symfony Security Component** : Système robuste
- **Hashage bcrypt** : Protection des mots de passe
- **Rôles hiérarchiques** : `ROLE_USER`, `ROLE_FLEURISTE`, `ROLE_ADMIN`
- **Tokens CSRF** : Protection sur formulaires sensibles

### Validation et Sanitisation
- **Symfony Validator** : Validation côté serveur
- **Assertions personnalisées** : Règles métier spécifiques
- **Échappement automatique** : Protection XSS dans Twig

### Sécurité Infrastructure
- **HTTPS obligatoire** : Chiffrement des communications
- **Variables d'environnement** : Protection des secrets
- **CORS configuré** : Contrôle des requêtes cross-origin
- **Rate limiting** : Protection contre les abus

---

## 📈 Performance et Scalabilité

### Optimisations Backend
- **Lazy Loading** : Chargement optimisé des relations
- **Query Builder** : Requêtes SQL optimisées
- **Cache Symfony** : Mise en cache intelligente
- **Pagination** : Gestion des grands volumes de données

### Optimisations Frontend
- **Webpack Encore** : Minification et bundling
- **Lazy Loading Images** : Chargement progressif des images
- **CSS/JS minifiés** : Réduction de la taille des assets
- **CDN-ready** : Préparation pour distribution de contenu

### Monitoring et Logs
- **Monolog** : Logging structuré
- **Web Profiler** : Analyse des performances
- **Symfony Debug Toolbar** : Débogage en développement

### Scalabilité
- **Architecture stateless** : Prête pour le load balancing
- **Base de données optimisée** : Indexation appropriée
- **Cache Redis** : Préparation pour mise en cache distribuée
- **API-ready** : Structure préparée pour exposation API

---

## ⚙️ CI/CD, Docker & Workflows de développement

### GitHub Actions CI globale (`.github/workflows/ci.yml`)
- **Objet** : pipeline complète qui teste **Symfony** et **Flutter**, puis construit les artefacts Flutter et déclenche le déploiement backend.
- **Jobs principaux** :
  - **`symfony-tests`** :
    - Lance un MySQL 8.0 éphémère pour les tests (`fleur_verte_test`).
    - Travaille dans le dossier `Symfony/`.
    - Installe PHP 8.2 + extensions (mbstring, xml, intl, pdo_mysql...).
    - Met en cache les dépendances Composer.
    - Installe les dépendances PHP (`composer install`) et JS (`npm ci`).
    - Construit les assets (`npm run build`).
    - Vérifie la validité de `composer.json` et lance `composer audit` (sécurité).
    - Lint YAML, Twig et le container Symfony.
    - Crée la base de test et le schéma Doctrine, puis exécute `phpunit` avec couverture (`--testdox --coverage-text`).
  - **`flutter-tests`** :
    - Travaille dans le dossier `Flutter/`.
    - Installe Flutter 3.24.0 (channel stable).
    - Récupère les dépendances (`flutter pub get`).
    - Vérifie le formatage (`dart format`) et analyse le code (`flutter analyze`).
    - Exécute les tests unitaires (`flutter test --coverage`).
  - **`flutter-build-web`** :
    - Dépend de `flutter-tests`.
    - Reconstruit le projet Flutter et génère un build Web (`flutter build web --release`).
    - Publie l'artefact `flutter-web-build`.
  - **`flutter-build-android`** :
    - Dépend de `flutter-tests`.
    - Installe Java 17 + Flutter et construit un **APK debug** (`flutter build apk --debug`).
    - Publie l'APK comme artefact.
  - **`ci-success`** :
    - Attend tous les jobs précédents.
    - Échoue explicitement si les tests Symfony ou Flutter ne sont pas passés.
  - **`deploy-render`** :
    - Ne s'exécute que sur `push` vers `main`.
    - Pousse le contenu du dossier `Symfony/` vers la branche `prod` du repo backend dédié (`mat976/FleurVerte`) en utilisant `SUBMODULE_DEPLOY_TOKEN`.
    - Appelle le webhook `RENDER_DEPLOY_HOOK` pour déclencher un déploiement sur **Render**.

### Workflows ciblés supplémentaires

- **`flutter.yml` – Flutter CI dédiée**
  - Déclenchée uniquement lorsque des fichiers sous `Flutter/` changent.
  - Pipeline allégée qui :
    - Formate le code (`dart format`) et analyse (`flutter analyze`) avec tolérance aux warnings.
    - Exécute les tests (`flutter test`), sans bloquer la CI si absents.
    - Construit un **APK debug** et vérifie sa présence.
    - Uploade l'APK comme artefact (`debug-apk`).

- **`symfony.yml` – Symfony CI dédiée**
  - Déclenchée quand des fichiers `Symfony/**` changent.
  - Similaire à `symfony-tests` dans `ci.yml` :
    - MySQL 8.0 de test.
    - Installation des dépendances Composer.
    - Commandes de vérification (`about`, `composer validate`, `composer audit`).
    - Lint YAML/Twig/container.
    - Exécution de `phpunit` sur une base `fleur_verte_test`.

- **`symfony-ci.yml` – CI orientée sous-module Symfony**
  - Spécifique au cas où Symfony est géré comme **sous-module** / repo séparé.
  - Sur `push` dans `Symfony/**` (branche `main`) :
    - Installe les dépendances Symfony.
    - Exécute les tests `phpunit`.
    - Si succès, crée une branche `auto-update-<timestamp>` dans le repo `mat976/FleurVerte` et y pousse les changements, via `PAT_TOKEN`.

En résumé, la CI/CD couvre :
- **Qualité**, **tests** et **lint** pour Symfony et Flutter.
- **Builds d’artefacts** (web, APK) pour consommation interne ou QA.
- **Déploiement automatique** du backend Symfony vers Render après succès des tests.

### Docker & Docker Compose

#### `Symfony/Dockerfile` (prod Render)
- **Base** : `php:8.2-cli` + étape Composer séparée.
- Installe les dépendances système nécessaires (zip, intl, PostgreSQL via `pdo_pgsql`).
- Installe **Composer** et **Symfony CLI** dans l’image.
- Copie tout le projet Symfony dans `/app`.
- Installe Node.js 20 + dépendances JS, puis exécute `npm run build` pour les assets.
- Installe les dépendances PHP en mode `APP_ENV=prod` (sans dev bundles) + scripts post-install.
- Ajoute explicitement `doctrine/doctrine-fixtures-bundle` pour permettre le chargement de fixtures en prod.
- **CMD** au démarrage (Render) :
  - Met à jour le schéma Doctrine.
  - Vérifie s’il existe des utilisateurs dans la table `"user"` (requête SQL brute).
  - Si aucun utilisateur : charge les **fixtures**.
  - Démarre le serveur Symfony via `symfony serve` sur le port `PORT` (ou 10000 par défaut).

#### `Symfony/docker-compose.yml` (dev local)
- **Service `database`** :
  - Image `mysql:5.7` avec base `fleurverte`.
  - Port mappé sur `3306:3306`.
  - Volume `db_data` pour la persistance.
  - Healthcheck pour ne démarrer l’app qu’une fois MySQL prêt.
- **Service `app`** :
  - Build local à partir du `Dockerfile` Symfony.
  - Monte le projet courant dans `/app` (pour rechargement du code).
  - Expose le serveur Symfony sur `8080` (interne 8000).
  - Configure `DATABASE_URL` pour pointer vers le service `database` (MySQL 5.7, charset utf8mb4).

En pratique :
- `docker-compose up -d` : lance **Symfony + MySQL** en dev.
- `docker-compose up -d database` : ne démarre que la base, utile si tu utilises PHP/Node en local.

### WARP.md : guide Dev local & structure du monorepo

Le fichier `WARP.md` documente **comment travailler efficacement dans le monorepo** :

- Rappelle qu’il y a deux applications principales :
  - **`Flutter/`** : app mobile `fleur_verte_app` consommant l’API Symfony.
  - **`Symfony/`** : backend Symfony 7 servant à la fois des pages HTML et des APIs JSON (`/api/login`, `/api/fleurs`, etc.).
- Répertorie les commandes locales clés :
  - Pour Flutter : `flutter pub get`, `flutter run`, `flutter test`, `dart format`, `flutter analyze`, `flutter build apk/web`.
  - Pour Symfony : `composer install`, `npm install`, `docker-compose up`, `php bin/console doctrine:*`, `php bin/phpunit`, `npm run dev/watch/build`.
- Confirme que **chaque sous-projet** est autonome :
  - Pas de build racine.
  - Chaque CI se place dans le bon dossier (`working-directory: Symfony` ou `Flutter`).
- Souligne le rôle de `Flutter/lib/config/api_config.dart` comme **source de vérité** pour l’URL de base de l’API Symfony, ce qui doit être cohérent avec l’environnement (local Docker, Render, etc.).

Cette couche CI/CD + Docker + documentation WARP garantit une **boucle de développement complète** :
- Dev local simple (Docker ou environnement natif).
- Exécution des mêmes commandes en local et en CI.
- Builds reproductibles et déploiement automatisé vers l’environnement de prod (Render).

---

## 🎯 Conclusion

**FleurVerte** représente une solution e-commerce complète et moderne pour l'univers floral, combinant :

- **Expérience utilisateur exceptionnelle** : Interface intuitive et responsive
- **Fonctionnalités riches** : Catalogue, panier, messagerie, profils
- **Architecture robuste** : Symfony 7 avec bonnes pratiques
- **Scalabilité** : Prête pour la croissance et l'évolution
- **Sécurité** : Protection complète des données utilisateurs

L'application est conçue pour évoluer vers des fonctionnalités avancées comme les paiements en ligne, la géolocalisation, les recommandations personnalisées, et l'intégration avec des systèmes externes.

---

*Document généré le 9 décembre 2025 - Version 1.0*
