<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Fleuriste;
use App\Entity\Tag;
use App\Entity\Fleur;
use App\Entity\Commentaire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    /** Liste des avatars disponibles (1.png à 10.png) */
    private const AVATARS = ['1.png', '2.png', '3.png', '4.png', '5.png', '6.png', '7.png', '8.png', '9.png', '10.png'];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Retourne un avatar aléatoire
     */
    private function getRandomAvatar(): string
    {
        return self::AVATARS[array_rand(self::AVATARS)];
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        $clientUsers = [];

        // Admin (id=1)
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setAvatarName('1.png');
        $admin->setUpdatedAt(new \DateTime());
        $manager->persist($admin);
        $users[] = $admin;

        // 20 Clients (id=2..21)
        $clientsData = [
            ['client1@test.com', 'Marie_Dupont', '2.png'],
            ['client2@test.com', 'Jean_Martin', '3.png'],
            ['client3@test.com', 'Sophie_Bernard', '4.png'],
            ['client4@test.com', 'Lucas_Petit', '5.png'],
            ['client5@test.com', 'Emma_Dubois', '6.png'],
            ['client6@test.com', 'Thomas_Moreau', '7.png'],
            ['client7@test.com', 'Lea_Laurent', '8.png'],
            ['client8@test.com', 'Hugo_Simon', '9.png'],
            ['client9@test.com', 'Chloe_Michel', '10.png'],
            ['client10@test.com', 'Nathan_Garcia', '1.png'],
            ['client11@test.com', 'Camille_Roux', '2.png'],
            ['client12@test.com', 'Maxime_Leroy', '3.png'],
            ['client13@test.com', 'Manon_David', '4.png'],
            ['client14@test.com', 'Antoine_Bertrand', '5.png'],
            ['client15@test.com', 'Julie_Robert', '6.png'],
            ['client16@test.com', 'Romain_Richard', '7.png'],
            ['client17@test.com', 'Laura_Durand', '8.png'],
            ['client18@test.com', 'Quentin_Morel', '9.png'],
            ['client19@test.com', 'Sarah_Fournier', '10.png'],
            ['client20@test.com', 'Theo_Girard', '1.png'],
        ];

        foreach ($clientsData as $data) {
            $user = new User();
            $user->setEmail($data[0]);
            $user->setUsername($data[1]);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setAvatarName($data[2]);
            $user->setUpdatedAt(new \DateTime());
            $manager->persist($user);
            $users[] = $user;
            $clientUsers[] = $user;
        }

        // Fleuristes (id=22..26)
        $fleuristesData = [
            [
                'email' => 'fleuriste1@test.com',
                'username' => 'Flora_Shop',
                'avatar' => '2.png',
                'nom' => 'Green Paradise Shop',
                'description' => 'Spécialiste des plantes tropicales et exotiques depuis 15 ans. Nous proposons une large gamme de fleurs rares et colorées.',
                'adresse' => '12 Rue des Fleurs, 75001 Paris',
                'telephone' => '01 23 45 67 89',
                'shopEmail' => 'contact@greenparadise.fr'
            ],
            [
                'email' => 'fleuriste2@test.com',
                'username' => 'Rose_Garden',
                'avatar' => '3.png',
                'nom' => 'Royal Bloom Boutique',
                'description' => 'Boutique haut de gamme spécialisée dans les arrangements floraux pour mariages et événements prestigieux.',
                'adresse' => '45 Avenue des Champs-Élysées, 75008 Paris',
                'telephone' => '01 98 76 54 32',
                'shopEmail' => 'info@royalbloom.fr'
            ],
            [
                'email' => 'fleuriste3@test.com',
                'username' => 'Nature_Bloom',
                'avatar' => '4.png',
                'nom' => 'Nature\'s Best Garden',
                'description' => 'Jardinerie familiale proposant des fleurs locales et de saison, cultivées avec amour dans notre pépinière.',
                'adresse' => '8 Chemin du Jardin, 69001 Lyon',
                'telephone' => '04 56 78 90 12',
                'shopEmail' => 'hello@naturesbest.fr'
            ],
            [
                'email' => 'fleuriste4@test.com',
                'username' => 'Premium_Flora',
                'avatar' => '5.png',
                'nom' => 'Premium Flora Store',
                'description' => 'Le meilleur de la fleur premium ! Roses éternelles, orchidées rares et bouquets sur mesure.',
                'adresse' => '23 Rue de la Paix, 33000 Bordeaux',
                'telephone' => '05 34 56 78 90',
                'shopEmail' => 'premium@florastore.fr'
            ],
            [
                'email' => 'fleuriste5@test.com',
                'username' => 'Exotic_Plants',
                'avatar' => '6.png',
                'nom' => 'Exotic Plants Market',
                'description' => 'Importateur de fleurs exotiques du monde entier. Découvrez des variétés uniques venues d\'Asie et d\'Amérique du Sud.',
                'adresse' => '67 Boulevard du Commerce, 13001 Marseille',
                'telephone' => '04 91 23 45 67',
                'shopEmail' => 'exotic@plantsmarket.fr'
            ],
        ];

        $fleuristes = [];
        foreach ($fleuristesData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $user->setRoles(['ROLE_FLEURISTE']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setAvatarName($data['avatar']);
            $user->setUpdatedAt(new \DateTime());
            $manager->persist($user);
            $users[] = $user;

            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $fleuriste->setNom($data['nom']);
            $fleuriste->setDescription($data['description']);
            $fleuriste->setAdresse($data['adresse']);
            $fleuriste->setTelephone($data['telephone']);
            $fleuriste->setEmail($data['shopEmail']);
            $fleuriste->setActif(true);
            $manager->persist($fleuriste);
            $fleuristes[] = $fleuriste;
        }

        // 20 Clients
        foreach ($clientUsers as $clientUser) {
            $client = new Client();
            $client->setUser($clientUser);
            $manager->persist($client);
        }

        // Tags
        $tagNames = [
            'Rose' => '#E11D48',
            'Tulipe' => '#EC4899',
            'Lys' => '#6B7280',
            'Orchidée' => '#7C3AED',
            'Jasmin' => '#D97706',
            'Lavande' => '#8B5CF6',
            'Pivoine' => '#DB2777',
            'Marguerite' => '#059669',
            'Tournesol' => '#CA8A04',
            'Iris' => '#2563EB',
            'Lilas' => '#9333EA',
            'Oeillet' => '#F472B6',
            'Gerbera' => '#EA580C',
            'Freesia' => '#EAB308',
            'Dahlia' => '#DC2626',
            'Chrysanthème' => '#B45309',
            'Camélia' => '#BE185D',
            'Hortensia' => '#3B82F6',
            'Magnolia' => '#4B5563',
            'Bruyère' => '#A855F7'
        ];

        $tags = [];
        foreach ($tagNames as $name => $couleur) {
            $tag = new Tag();
            $tag->setNom($name);
            $tag->setCouleur($couleur);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Fleurs - 30 variétés avec stock varié
        // Format: [nom, description, prix, stock, promoPercent, promoActive]
        $fleursData = [
            // Shop 1 - Green Paradise (Tropicales)
            ['Rose Rouge Passion', 'Magnifique rose rouge veloutée, symbole éternel de l\'amour. Cultivée en serre avec soin.', 15.99, 120, 20, true],
            ['Orchidée Phalaenopsis Blanche', 'Orchidée papillon d\'une élégance rare. Fleur exotique qui dure plusieurs semaines.', 35.00, 45, 0, false],
            ['Anthurium Rouge', 'Fleur tropicale en forme de cœur, parfaite pour décorer un intérieur moderne.', 22.50, 30, 15, true],
            ['Hibiscus Rose', 'Fleur hawaïenne colorée, symbole de beauté éphémère et de délicatesse.', 18.75, 25, 0, false],
            ['Bird of Paradise', 'Strelitzia reginae, fleur spectaculaire ressemblant à un oiseau exotique.', 28.00, 15, 0, false],
            ['Plumeria Jaune', 'Frangipanier au parfum envoûtant, évoque les îles tropicales.', 24.50, 20, 10, false],
            
            // Shop 2 - Royal Bloom (Mariages)
            ['Rose Blanche Avalanche', 'Rose premium pour mariages, pétales parfaits et longue tenue.', 19.99, 200, 30, true],
            ['Pivoine Sarah Bernhardt', 'Pivoine rose pâle, star des bouquets de mariée depuis 100 ans.', 32.00, 60, 0, false],
            ['Lys Casa Blanca', 'Lys oriental blanc pur au parfum capiteux, majestueux et élégant.', 25.00, 80, 25, true],
            ['Renoncule Blanche', 'Petite fleur délicate aux multiples pétales, romantique à souhait.', 14.50, 95, 0, false],
            ['Gypsophile', 'Nuage de petites fleurs blanches, indispensable pour les bouquets.', 8.99, 150, 0, false],
            ['Eucalyptus Silver Dollar', 'Feuillage argenté parfait pour accompagner les fleurs de mariage.', 12.00, 100, 0, false],
            
            // Shop 3 - Nature\'s Best (Locales)
            ['Tulipe Française', 'Tulipe cultivée en Provence, couleurs vives et naturelles.', 8.50, 180, 40, true],
            ['Marguerite des Champs', 'Fleur champêtre par excellence, fraîcheur et simplicité.', 5.99, 250, 0, false],
            ['Tournesol Géant', 'Tournesol local XXL, apporte la joie du soleil dans votre maison.', 12.50, 90, 15, true],
            ['Lavande de Provence', 'Botte de lavande séchée, parfum relaxant garanti.', 9.50, 120, 0, false],
            ['Bleuet Sauvage', 'Fleur des champs bleue intense, cueillie avec respect de la nature.', 6.75, 85, 0, false],
            ['Coquelicot Rouge', 'Symbole du souvenir, fleur fragile et poétique.', 7.25, 0, 50, true],
            
            // Shop 4 - Premium Flora (Luxe)
            ['Rose Éternelle Rouge', 'Rose naturelle stabilisée, conserve sa beauté pendant 3 ans.', 45.00, 35, 20, true],
            ['Orchidée Vanda Bleue', 'Orchidée rare teinte naturellement, pièce de collection.', 65.00, 12, 0, false],
            ['Bouquet Signature Premium', 'Composition exclusive de fleurs premium, création unique.', 89.00, 8, 10, true],
            ['Amaryllis Royal', 'Grande fleur majestueuse rouge vif, parfaite pour les fêtes.', 28.50, 40, 0, false],
            ['Camélia Japonais', 'Fleur délicate cultivée selon les traditions japonaises.', 38.00, 18, 0, false],
            ['Gardénia Parfumé', 'Fleur au parfum envoûtant, symbole d\'élégance raffinée.', 32.00, 22, 35, true],
            
            // Shop 5 - Exotic Plants (Importées)
            ['Protea King', 'Fleur nationale d\'Afrique du Sud, spectaculaire et durable.', 35.00, 25, 25, true],
            ['Lotus Sacré', 'Fleur spirituelle d\'Asie, symbole de pureté et d\'éveil.', 42.00, 10, 0, false],
            ['Heliconia Tropicana', 'Fleur pince de homard, couleurs flamboyantes d\'Amérique du Sud.', 28.00, 30, 0, false],
            ['Ginger Torch', 'Fleur de gingembre spectaculaire, rouge intense et forme unique.', 24.00, 20, 15, true],
            ['Monstera Deliciosa', 'Feuillage tropical tendance, incontournable en décoration.', 18.00, 55, 0, false],
            ['Ananas Ornemental', 'Mini ananas décoratif, touche exotique originale.', 15.00, 35, 0, false],
        ];

        $fleurs = [];
        $promoStart = new \DateTime('-7 days');
        $promoEnd = new \DateTime('+30 days');

        foreach ($fleursData as $index => $data) {
            $fleur = new Fleur();
            $fleur->setNom($data[0]);
            $fleur->setDescription($data[1]);
            $fleur->setPrix($data[2]);
            $fleur->setStock($data[3]);
            $fleur->setPromoPercent($data[4]);
            $fleur->setPromoActive($data[5]);
            if ($data[4] > 0) {
                $fleur->setPromoStart($promoStart);
                $fleur->setPromoEnd($promoEnd);
            }
            $fleur->setFleuriste($fleuristes[intdiv($index, 6)]);
            $manager->persist($fleur);
            $fleurs[] = $fleur;
        }

        // Associations Fleurs-Tags
        for ($i = 0; $i < count($fleurs); $i++) {
            $fleur = $fleurs[$i];
            // Ajouter 4 tags aléatoires par fleur
            $tagIds = [($i % 20), (($i + 5) % 20), (($i + 10) % 20), (($i + 15) % 20)];
            foreach ($tagIds as $tagId) {
                $fleur->addTag($tags[$tagId]);
            }
        }

        // Commentaires de test - Plus de variété
        $commentairesTextes = [
            ['Magnifique fleur ! Très satisfait de mon achat. Je recommande ce vendeur les yeux fermés.', 5],
            ['Bonne qualité, livraison rapide. Emballage soigné, fleurs fraîches à réception.', 4],
            ['Couleurs vives et éclatantes, correspond parfaitement à la description. Bravo !', 5],
            ['Correct mais un peu cher pour ce que c\'est. Je m\'attendais à mieux pour ce prix.', 3],
            ['Très belle fleur, ma femme a adoré ! Parfait pour notre anniversaire de mariage.', 5],
            ['Parfum agréable et bonne tenue en vase. Cela fait 10 jours et elles sont toujours belles.', 4],
            ['Un peu déçu, quelques pétales étaient abîmés à la livraison. Service client réactif cependant.', 2],
            ['Superbe ! Je recommande vivement ce fleuriste. Qualité professionnelle.', 5],
            ['Rapport qualité/prix excellent. J\'ai comparé avec d\'autres boutiques, c\'est ici le meilleur.', 4],
            ['Belle présentation et emballage très soigné. On sent le professionnalisme.', 4],
            ['Fleurs ultra fraîches et de grande qualité. On voit que c\'est cultivé avec passion.', 5],
            ['Service client réactif et fleurs absolument magnifiques. Client fidèle désormais !', 5],
            ['Pas mal mais j\'ai déjà vu mieux ailleurs. Correct sans plus.', 3],
            ['Excellente surprise pour un anniversaire ! Ma mère était aux anges.', 5],
            ['Livraison un peu lente mais le produit est vraiment top qualité.', 4],
            ['Waouh ! Ces fleurs sont incroyables. Toute la famille est fan.', 5],
            ['Très bon achat, je suis ravie. Les photos ne rendent pas justice à la beauté réelle.', 5],
            ['Qualité au rendez-vous. Je reviendrai sans hésiter pour mes prochains achats.', 4],
            ['Fleurs conformes à mes attentes. Bonne fraîcheur et jolies couleurs.', 4],
            ['Un vrai coup de cœur ! Ces fleurs illuminent mon salon depuis une semaine.', 5],
            ['Moyen. Les fleurs ont fané plus vite que prévu. Dommage car elles étaient jolies.', 2],
            ['Commande parfaite du début à la fin. Communication excellente avec le fleuriste.', 5],
            ['Je suis bluffée par la qualité ! On m\'a demandé où je les avais achetées.', 5],
            ['Bon rapport qualité-prix pour des fleurs de cette qualité. Satisfait.', 4],
            ['Fleurs magnifiques pour décorer ma table de Noël. Effet garanti !', 5],
        ];

        // Ajouter des commentaires sur toutes les fleurs (2-5 par fleur)
        foreach ($fleurs as $index => $fleur) {
            // Ajouter 2-5 commentaires par fleur
            $nbCommentaires = rand(2, 5);
            for ($c = 0; $c < $nbCommentaires; $c++) {
                $commentaireData = $commentairesTextes[($index + $c) % count($commentairesTextes)];
                $userIndex = rand(1, 20); // Users 1-20
                
                $commentaire = new Commentaire();
                $commentaire->setUser($users[$userIndex]);
                $commentaire->setFleur($fleur);
                $commentaire->setContenu($commentaireData[0]);
                $commentaire->setNote($commentaireData[1]);
                $commentaire->setDateCreation(new \DateTime('-' . rand(1, 60) . ' days'));
                $manager->persist($commentaire);
            }
        }

        $manager->flush();
    }
}