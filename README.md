# FleurVerte 🌸

Une application e‑commerce/marketplace moderne pour fleurs et fleuristes, développée avec **Symfony 7**, **Doctrine ORM 3**, **Twig** et un front modernisé (**Webpack Encore**, **Tailwind CSS**, **Stimulus/Turbo**). Le projet propose un catalogue complet, recherche avancée par tags, panier intelligent, messagerie temps-réel client↔fleuriste, profils utilisateurs et gestion d'images optimisée.

## 📋 Sommaire
- [🎯 Aperçu & fonctionnalités](#aperçu--fonctionnalités)
- [🛠️ Stack technique](#stack-technique)
- [🚀 Démarrage rapide](#démarrage-rapide)
- [⚙️ Configuration & variables d'environnement](#configuration--variables-denvironnement)
- [🐳 Configuration Docker](#configuration-docker)
- [🗄️ Base de données](#base-de-données)
- [🎨 Frontend (assets, Tailwind, Stimulus/Turbo)](#frontend-assets)
- [📁 Architecture & Structure détaillée](#architecture--structure-détaillée)
- [💻 Exemples de code](#exemples-de-code)
- [🔧 Commandes utiles](#commandes-utiles)
- [🔒 Sécurité & bonnes pratiques](#sécurité--bonnes-pratiques)
- [🧪 Tests & Qualité](#tests--qualité)

## 🎯 Aperçu & fonctionnalités

### 🌺 Catalogue & Recherche
- **Catalogue complet** : Affichage des fleurs avec images, descriptions, prix et stock
- **Recherche avancée** : Filtrage par tags avec autocomplétion intelligente
- **Système de tags** : Catégorisation flexible des produits (couleur, type, occasion, etc.)
- **Pagination optimisée** : Navigation fluide dans le catalogue

### 👥 Gestion des utilisateurs
- **Profils clients** : Informations personnelles, historique des commandes
- **Profils fleuristes** : Galerie d'images, informations de contact, localisation
- **Gestion d'adresses** : Adresses de livraison multiples par client
- **Authentification sécurisée** : Système de connexion/inscription avec validation

### 🛒 Panier & Commandes
- **Panier intelligent** : Ajout/suppression d'articles, mise à jour des quantités
- **Calcul automatique** : Total en temps réel avec gestion du stock
- **Persistance** : Sauvegarde du panier entre les sessions

### 💬 Messagerie temps-réel
- **Conversations privées** : Communication directe client ↔ fleuriste
- **Interface intuitive** : Chat moderne avec notifications
- **Historique complet** : Sauvegarde de tous les échanges

### 📸 Gestion d'images
- **Upload optimisé** : Via VichUploaderBundle avec redimensionnement automatique
- **Galeries** : Images multiples pour fleuristes et produits
- **Avatars personnalisés** : Sélection d'avatars prédéfinis ou upload personnalisé

## 🛠️ Stack technique

### 🔧 Backend
- **PHP 8.2+** : Dernières fonctionnalités du langage (types union, attributs, etc.)
- **Symfony 7.1.*** : Framework moderne avec :
  - `FrameworkBundle` : Cœur du framework
  - `SecurityBundle` : Authentification et autorisation
  - `FormBundle` : Génération et validation de formulaires
  - `ValidatorBundle` : Validation des données
  - `SerializerBundle` : Sérialisation JSON/XML
  - `MailerBundle` : Envoi d'emails
  - `MessengerBundle` : Gestion des messages asynchrones

### 🗄️ Persistence & Base de données
- **Doctrine ORM 3** : Mapping objet-relationnel moderne
- **Doctrine Migrations** : Versioning de la base de données
- **MySQL 5.7** : Base de données relationnelle (via Docker)
- **Doctrine DBAL 3** : Couche d'abstraction de base de données

### 🎨 Frontend & Assets
- **Twig 3** : Moteur de templates avec `twig/extra-bundle`
- **Webpack Encore 2.2** : Bundling et optimisation des assets
- **Tailwind CSS** : Framework CSS utility-first
- **Stimulus 3** (`@hotwired/stimulus`) : Framework JavaScript léger
- **Turbo** (`@hotwired/turbo`) : Navigation SPA sans JavaScript complexe
- **UX Autocomplete** : Composant d'autocomplétion Symfony UX
- **Alpine.js 2** : Framework JavaScript réactif pour les interactions
- **Font Awesome 6.5** : Icônes vectorielles

### 📁 Gestion des fichiers
- **VichUploaderBundle 2.4** : Upload et gestion d'images
- **Stockage** : `public/uploads/` (avatars, fleuristes, fleurs)

### 🛠️ Outils de développement
- **Symfony CLI** : Serveur de développement et outils
- **Web Profiler** : Débogage et profilage
- **Maker Bundle** : Génération de code
- **PHPUnit** : Tests unitaires et fonctionnels
- **Monolog** : Logging avancé

## Démarrage rapide

### Option 1: Avec Docker (recommandé)
1) Prérequis: Docker & Docker Compose
2) Lancer l'application complète: `make up` (ou `docker compose up -d --build`)
3) Accéder à l'application: http://localhost:3010

Tout est automatiquement configuré: PHP 8.2, PostgreSQL 16, Symfony serve, et les dépendances sont installées dans le conteneur.

### Option 2: Installation locale
1) Prérequis: PHP 8.2+, Composer, Node.js 18+, npm, Docker & Docker Compose, Symfony CLI.
2) Dépendances: `make install` (équivaut à `composer install` + `npm ci`)
3) Base de données (Docker): `docker compose up -d database` (uniquement le service PostgreSQL)
4) Config .env (voir section ci‑dessous). Par défaut: PostgreSQL 16 local.
5) Créer schéma & charger les fixtures: `make db-reset`
6) Lancer l'app en dev: `make dev` (serveur PHP sur http://localhost:3010)

Astuce: `make help` liste toutes les cibles disponibles (install, dev, test, lint, up, down…).

## Configuration & variables d’environnement
Extraits du .env (défaut dev):
- APP_ENV=dev
- APP_SECRET=… (changer en prod)
- DATABASE_URL="postgresql://root:@127.0.0.1:5432/fleurverte?serverVersion=16&charset=utf8"
- MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
- MAILER_DSN=null://null (à adapter en prod)
Ne jamais committer de secrets de prod. En production, utiliser `composer dump-env prod` et un vrai secret.

## Configuration Docker

### Prérequis
- Docker
- Docker Compose

### Services disponibles
- **app**: Serveur Symfony (PHP 8.2) accessible sur http://localhost:3010
- **database**: PostgreSQL 16 accessible sur localhost:5432

Les fichiers Docker sont regroupés ainsi: `Dockerfile` (image de prod multi‑stage), `docker-compose.yml` (stack de dev), `docker/entrypoint.sh` (script de démarrage du conteneur).

### Démarrage complet
```bash
make up            # docker compose up -d --build
```

### Démarrage de la base de données uniquement
```bash
docker compose up -d database
```

### Paramètres de connexion PostgreSQL
- **Host**: 127.0.0.1 (depuis l'hôte) ou database (depuis le conteneur app)
- **Port**: 5432
- **Database**: fleurverte
- **User**: root
- **Password**: (vide)
- **Version**: PostgreSQL 16

### Commandes utiles
```bash
make up                              # Démarrer les conteneurs
make down                            # Arrêter les conteneurs
make logs                            # Logs du conteneur app
docker compose exec database psql -U root fleurverte   # Shell PostgreSQL
docker compose exec app sh           # Shell du conteneur app
```

## Base de données
- Création/mise à jour: Doctrine Migrations + fixtures (`make db-reset`).
- Import de données d'exemple (optionnel): dump PostgreSQL disponible dans `database/fleur_postgres.sql`.

## Frontend (assets)
- Webpack Encore (config: config/packages/webpack_encore.yaml → manifest public/build/manifest.json).
- Tailwind CSS (tailwind.config.js) + PostCSS/Autoprefixer.
- Stimulus controllers: assets/controllers/*.js, fonctionnalités JS (ex: smooth-scroll, tag-selection).
- Turbo & UX: navigation accélérée et composants (autocomplete).
- Scripts npm: `npm run dev`, `npm run watch`, `npm run build`, `npm run dev-server`.

## Commandes utiles
- Lancer serveur Symfony: `symfony serve` (ou via `npm run start`).
- Assets:
  - Dev: `npm run watch`
  - Prod: `npm run build` (Encore production)
- Doctrine:
  - Migrations: `php bin/console doctrine:migrations:diff` / `migrate`
  - Fixtures: `php bin/console doctrine:fixtures:load` (ou `make db-reset`)
- Tests: `make test` (build assets + base de test + PHPUnit) ou `php bin/phpunit`
- Lint: `make lint` (composer validate + lint yaml/twig/container)

### Organisation des tests
```
tests/
├── Unit/         # Tests unitaires purs (ex: Entity)
├── Integration/  # Tests avec la base de données / le conteneur
└── Functional/   # Tests fonctionnels HTTP (WebTestCase)
```
Suites PHPUnit: `php bin/phpunit --testsuite Unit|Integration|Functional`.

## 📁 Architecture & Structure détaillée

### 🏗️ Architecture générale
FleurVerte suit l'architecture **MVC** de Symfony avec une séparation claire des responsabilités :

```
src/
├── Controller/     # Contrôleurs (logique de présentation)
├── Entity/         # Entités Doctrine (modèle de données)
├── Repository/     # Repositories (accès aux données)
├── Service/        # Services métier (logique applicative)
├── Form/          # Types de formulaires Symfony
├── Security/      # Authentification personnalisée
└── Autocomplete/  # Champs d'autocomplétion UX
```

### 🎮 Contrôleurs principaux
- **`HomeController`** : Page d'accueil et navigation principale
- **`ProductController`** : Catalogue des fleurs, détails produit
- **`CartController`** : Gestion du panier d'achat
- **`FleuristeController`** : Profils et fiches des fleuristes
- **`MessageController`** : Système de messagerie temps-réel
- **`SecurityController`** : Authentification (login/register)
- **`ProfilController`** : Gestion des profils utilisateurs
- **`SearchController`** : Recherche avancée avec filtres

### 🗃️ Modèle de données (Entités)

#### Entités principales
- **`User`** : Utilisateurs du système (clients et fleuristes)
- **`Fleur`** : Produits (fleurs) avec prix, stock, description
- **`Fleuriste`** : Profils des vendeurs avec galerie d'images
- **`CartItem`** : Articles dans le panier d'un client
- **`Tag`** : Système de catégorisation des produits
- **`Conversation`** & **`Message`** : Messagerie client-fleuriste
- **`Adresse`** : Adresses de livraison des clients

#### Relations entre entités
```
User (1) ←→ (N) CartItem ←→ (1) Fleur
User (1) ←→ (N) Conversation ←→ (N) Message
Fleuriste (1) ←→ (N) FleuristeImage
Fleur (N) ←→ (N) Tag (ManyToMany)
User (1) ←→ (N) Adresse
```

### 🔧 Services métier
- **`CartService`** : Logique complète du panier (ajout, suppression, calculs)
- **`MessageService`** : Gestion des conversations et notifications

### 🎨 Frontend & Assets
```
assets/
├── controllers/           # Contrôleurs Stimulus
│   ├── avatar_controller.js   # Sélection d'avatars
│   └── hello_controller.js    # Exemple de base
├── js/                   # JavaScript personnalisé
│   ├── components/       # Composants réutilisables
│   ├── pages/           # Scripts spécifiques aux pages
│   └── smooth-scroll.js # Navigation fluide
├── styles/              # Styles CSS
│   ├── app.css         # Styles principaux avec Tailwind
│   ├── search.css      # Styles de recherche
│   └── smooth-scroll.css # Animations de scroll
├── app.js              # Point d'entrée JavaScript
└── bootstrap.js        # Configuration Stimulus
```

### 📄 Templates Twig
```
templates/
├── base.html.twig          # Template de base avec navigation
├── home/                   # Page d'accueil
├── product/               # Catalogue et détails produits
├── fleuriste/            # Profils des fleuristes
├── cart/                 # Panier d'achat
├── message/              # Interface de messagerie
├── profil/               # Gestion des profils
├── security/             # Login/Register
└── components/           # Composants réutilisables
```

### 🗂️ Gestion des uploads
```
public/uploads/
├── avatars/              # Avatars des utilisateurs
├── fleuristes/          # Images des profils fleuristes
└── fleurs/              # Images des produits
```

## 💻 Exemples de code

### 🌸 Entité Fleur (Produit)
```php
<?php
// src/Entity/Fleur.php

/**
 * Entité représentant une fleur
 * 
 * Cette classe gère les informations relatives aux fleurs disponibles à la vente,
 * incluant leurs caractéristiques, prix, stock et relation avec le fleuriste.
 */
#[ORM\Entity(repositoryClass: FleurRepository::class)]
#[Vich\Uploadable]
class Fleur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column]
    private ?int $stock = null;

    // Relations
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'fleurs')]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'fleur', targetEntity: CartItem::class)]
    private Collection $cartItems;

    // Upload d'image avec VichUploader
    #[Vich\UploadableField(mapping: 'fleur_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->cartItems = new ArrayCollection();
    }

    // Getters et setters...
}
```

### 🛒 Service de Panier
```php
<?php
// src/Service/CartService.php

/**
 * Service de gestion du panier d'achat
 * 
 * Ce service gère toutes les opérations liées au panier d'achat :
 * - Ajout et suppression d'articles
 * - Mise à jour des quantités
 * - Calcul du total
 */
class CartService
{
    public function __construct(
        private CartItemRepository $cartItemRepository,
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    /**
     * Ajoute un produit au panier
     * 
     * Si le produit existe déjà, la quantité est augmentée.
     */
    public function addToCart(Fleur $fleur, int $quantity = 1): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new \RuntimeException('Utilisateur non connecté');
        }

        $existingItem = $this->cartItemRepository->findOneBy([
            'user' => $user,
            'fleur' => $fleur
        ]);

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setUser($user)
                     ->setFleur($fleur)
                     ->setQuantity($quantity);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();
    }

    /**
     * Calcule le total du panier
     */
    public function getCartTotal(User $user): float
    {
        $cartItems = $this->cartItemRepository->findBy(['user' => $user]);
        $total = 0;

        foreach ($cartItems as $item) {
            $total += $item->getFleur()->getPrix() * $item->getQuantity();
        }

        return $total;
    }
}
```

### 🎮 Contrôleur Home
```php
<?php
// src/Controller/HomeController.php

/**
 * Contrôleur gérant la page d'accueil du site
 */
class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page_title' => 'Accueil - FleurVerte',
            'meta_description' => 'Bienvenue sur FleurVerte, votre boutique en ligne de fleurs de qualité.',
        ]);
    }
}
```

### 📝 Formulaire de Produit
```php
<?php
// src/Form/FleurType.php

/**
 * Formulaire de gestion des produits (fleurs)
 */
class FleurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (€)',
                'currency' => 'EUR',
                'attr' => ['class' => 'form-control']
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Catégories'
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => false
            ]);
    }
}
```

### 🎨 Contrôleur Stimulus (JavaScript)
```javascript
// assets/controllers/avatar_controller.js

import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur de sélection d'avatar
 * 
 * Gère la sélection d'avatars prédéfinis et l'upload personnalisé
 */
export default class extends Controller {
    static targets = ['avatarRadio', 'fileInput', 'avatarImage'];

    connect() {
        console.log('Avatar controller connected');
        this.addEventListeners();
    }

    addEventListeners() {
        // Écoute les changements sur les boutons radio d'avatar
        if (this.hasAvatarRadioTarget) {
            this.avatarRadioTargets.forEach(radio => {
                radio.addEventListener('change', this.handleAvatarSelection.bind(this));
            });
        }

        // Écoute les changements sur l'input file
        if (this.hasFileInputTarget) {
            this.fileInputTarget.addEventListener('change', this.handleFileSelection.bind(this));
        }
    }

    handleAvatarSelection(event) {
        // Vide l'input file quand un avatar prédéfini est sélectionné
        if (this.hasFileInputTarget) {
            this.fileInputTarget.value = '';
        }

        // Met à jour l'indication visuelle
        this.updateAvatarHighlight(event.target);
    }

    updateAvatarHighlight(selectedRadio) {
        // Supprime la surbrillance de tous les avatars
        this.avatarImageTargets.forEach(img => {
            img.classList.remove('ring-2', 'ring-green-500');
        });

        // Ajoute la surbrillance à l'avatar sélectionné
        const selectedLabel = selectedRadio.closest('label');
        if (selectedLabel) {
            const selectedImg = selectedLabel.querySelector('img');
            if (selectedImg) {
                selectedImg.classList.add('ring-2', 'ring-green-500');
            }
        }
    }
}
```

### 🎨 Template Twig de base
```twig
{# templates/base.html.twig #}
<!DOCTYPE html>
<html class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <title>{% block title %}FleurVerte{% endblock %}</title>
    
    {% block stylesheets %}
        {{ encore_entry_link_tags('app') }}
    {% endblock %}
    
    {% block javascripts %}
        {{ encore_entry_script_tags('app') }}
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    {% endblock %}
</head>
<body>
    <!-- Navigation principale -->
    <nav class="bg-green-600 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ path('app_home') }}" class="flex-shrink-0">
                        <img src="{{ asset('img/logo/logo_last.png') }}" alt="FleurVerte Logo" class="h-16 w-auto">
                    </a>
                    
                    <!-- Menu principal -->
                    <div class="hidden md:flex ml-10 space-x-4">
                        <a href="{{ path('app_home') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-home mr-2"></i>Accueil
                        </a>
                        <a href="{{ path('app_product') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-seedling mr-2"></i>Nos Variétés
                        </a>
                        <a href="{{ path('app_fleuriste_index') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-store mr-2"></i>Nos Fleuristes
                        </a>
                    </div>
                </div>
                
                <!-- Actions utilisateur -->
                <div class="flex items-center space-x-4">
                    {% if is_granted('ROLE_USER') %}
                        <a href="{{ path('app_cart') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-shopping-cart mr-2"></i>Panier
                        </a>
                        <a href="{{ path('app_profil') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-user mr-2"></i>Profil
                        </a>
                    {% else %}
                        <a href="{{ path('app_login') }}" class="text-white hover:bg-green-700 px-3 py-2 rounded-md">
                            <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                        </a>
                    {% endif %}
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Contenu principal -->
    <main>
        {% block body %}{% endblock %}
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ 'now'|date('Y') }} FleurVerte. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
```

## 🔒 Sécurité & bonnes pratiques

### 🔐 Configuration sécurisée
- **APP_SECRET** : Générer un secret unique en production (32+ caractères aléatoires)
- **Variables sensibles** : Utiliser des variables d'environnement, jamais de hardcoding
- **Base de données** : Mots de passe forts, connexions chiffrées en production
- **HTTPS** : Obligatoire en production avec certificats SSL/TLS valides

### 🛡️ Authentification & Autorisation
- **Symfony Security** : Système d'authentification robuste avec hashage bcrypt
- **Validation des formulaires** : Validation côté serveur systématique
- **Protection CSRF** : Tokens CSRF sur tous les formulaires sensibles
- **Rôles utilisateurs** : Séparation claire des permissions (ROLE_USER, ROLE_ADMIN)

### 🔍 Validation & Sanitisation
```php
// Exemple de validation d'entité
class Fleur
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Positive(message: 'Le prix doit être positif')]
    private ?string $prix = null;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'Le stock ne peut être négatif')]
    private ?int $stock = null;
}
```

### 📊 Monitoring & Logs
- **Monolog** : Logging structuré avec niveaux appropriés
- **Erreurs** : Logs détaillés en développement, anonymisés en production
- **Performance** : Monitoring des requêtes lentes et de l'utilisation mémoire

### 🔄 Mises à jour
- **Dépendances** : Mise à jour régulière avec `composer update`
- **Sécurité** : Surveillance des vulnérabilités avec `composer audit`
- **Tests** : Validation complète avant déploiement

## 🧪 Tests & Qualité

### 🔬 Tests unitaires
```php
// tests/Service/CartServiceTest.php
class CartServiceTest extends KernelTestCase
{
    private CartService $cartService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->cartService = $kernel->getContainer()->get(CartService::class);
    }

    public function testAddToCart(): void
    {
        $user = new User();
        $fleur = new Fleur();
        $fleur->setNom('Rose Rouge')->setPrix('15.99');

        $this->cartService->addToCart($fleur, 2);
        
        $cartItems = $this->entityManager
            ->getRepository(CartItem::class)
            ->findBy(['user' => $user]);
            
        $this->assertCount(1, $cartItems);
        $this->assertEquals(2, $cartItems[0]->getQuantity());
    }
}
```

### 🌐 Tests fonctionnels
```php
// tests/Controller/HomeControllerTest.php
class HomeControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'FleurVerte');
        $this->assertSelectorExists('.navbar');
    }

    public function testProductCatalog(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.product-card');
    }
}
```

### 📏 Commandes de qualité
```bash
# Tests unitaires et fonctionnels
php bin/phpunit

# Tests avec couverture de code
php bin/phpunit --coverage-html coverage/

# Analyse statique (si configuré)
vendor/bin/phpstan analyse src/

# Vérification du style de code
vendor/bin/php-cs-fixer fix --dry-run

# Audit de sécurité
composer audit
```

### 🎯 Métriques de qualité
- **Couverture de tests** : Objectif > 80%
- **Complexité cyclomatique** : Maintenir < 10 par méthode
- **Standards PSR** : Respect des PSR-1, PSR-4, PSR-12
- **Documentation** : Commentaires PHPDoc sur toutes les méthodes publiques

## 📚 Ressources & Documentation

### 🔗 Liens utiles
- [Documentation Symfony 7.1](https://symfony.com/doc/7.1/index.html)
- [Doctrine ORM 3](https://www.doctrine-project.org/projects/orm.html)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Stimulus Handbook](https://stimulus.hotwired.dev/handbook/introduction)
- [Twig Documentation](https://twig.symfony.com/doc/3.x/)

### 🚀 Déploiement
```bash
# Production build
composer install --no-dev --optimize-autoloader
npm run build
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
```

### 🐛 Débogage
```bash
# Vérifier la configuration
php bin/console debug:config

# Lister les routes
php bin/console debug:router

# Vérifier les services
php bin/console debug:container

# Analyser les performances
php bin/console debug:profiler
```

## 📄 Licence
**Proprietary** - Voir `composer.json` pour plus de détails.

---

**FleurVerte** - Une marketplace moderne pour l'univers floral 🌸

*Développé avec ❤️ et Symfony 7*