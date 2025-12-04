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

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer les utilisateurs
        $users = [];
        
        // Admin
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);
        $users[] = $admin;

        // Users normaux
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@test.com");
            $user->setUsername("user{$i}");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        // Fleuristes avec données complètes
        $fleuristesData = [
            [
                'nom' => 'Green Paradise Shop',
                'description' => 'Spécialiste des plantes tropicales et exotiques depuis 15 ans. Nous proposons une large gamme de fleurs rares et colorées.',
                'adresse' => '12 Rue des Fleurs, 75001 Paris',
                'telephone' => '01 23 45 67 89',
                'email' => 'contact@greenparadise.fr'
            ],
            [
                'nom' => 'Royal Bloom Boutique',
                'description' => 'Boutique haut de gamme spécialisée dans les arrangements floraux pour mariages et événements prestigieux.',
                'adresse' => '45 Avenue des Champs-Élysées, 75008 Paris',
                'telephone' => '01 98 76 54 32',
                'email' => 'info@royalbloom.fr'
            ],
            [
                'nom' => 'Nature\'s Best Garden',
                'description' => 'Jardinerie familiale proposant des fleurs locales et de saison, cultivées avec amour dans notre pépinière.',
                'adresse' => '8 Chemin du Jardin, 69001 Lyon',
                'telephone' => '04 56 78 90 12',
                'email' => 'hello@naturesbest.fr'
            ],
            [
                'nom' => 'Premium Flora Store',
                'description' => 'Le meilleur de la fleur premium ! Roses éternelles, orchidées rares et bouquets sur mesure.',
                'adresse' => '23 Rue de la Paix, 33000 Bordeaux',
                'telephone' => '05 34 56 78 90',
                'email' => 'premium@florastore.fr'
            ],
            [
                'nom' => 'Exotic Plants Market',
                'description' => 'Importateur de fleurs exotiques du monde entier. Découvrez des variétés uniques venues d\'Asie et d\'Amérique du Sud.',
                'adresse' => '67 Boulevard du Commerce, 13001 Marseille',
                'telephone' => '04 91 23 45 67',
                'email' => 'exotic@plantsmarket.fr'
            ],
        ];

        $fleuristes = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail("fleuriste{$i}@test.com");
            $user->setUsername("fleuriste{$i}");
            $user->setRoles(['ROLE_FLEURISTE']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;

            $data = $fleuristesData[$i-1];
            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $fleuriste->setNom($data['nom']);
            $fleuriste->setDescription($data['description']);
            $fleuriste->setAdresse($data['adresse']);
            $fleuriste->setTelephone($data['telephone']);
            $fleuriste->setEmail($data['email']);
            $fleuriste->setActif(true);
            $manager->persist($fleuriste);
            $fleuristes[] = $fleuriste;
        }

        // Clients (premiers 10 users)
        for ($i = 1; $i <= 10; $i++) {
            $client = new Client();
            $client->setUser($users[$i]);
            $manager->persist($client);
        }

        // Tags
        $tagNames = [
            'Rose' => 'red',
            'Tulipe' => 'pink',
            'Lys' => 'white',
            'Orchidée' => 'purple',
            'Jasmin' => 'yellow',
            'Lavande' => 'lavender',
            'Pivoine' => 'magenta',
            'Marguerite' => 'white',
            'Tournesol' => 'yellow',
            'Iris' => 'blue',
            'Lilas' => 'purple',
            'Oeillet' => 'pink',
            'Gerbera' => 'orange',
            'Freesia' => 'yellow',
            'Dahlia' => 'red',
            'Chrysanthème' => 'gold',
            'Camélia' => 'pink',
            'Hortensia' => 'blue',
            'Magnolia' => 'white',
            'Bruyère' => 'purple'
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
        $fleursData = [
            // Shop 1 - Green Paradise (Tropicales)
            ['Rose Rouge Passion', 'Magnifique rose rouge veloutée, symbole éternel de l\'amour. Cultivée en serre avec soin.', 15.99, 120, true],
            ['Orchidée Phalaenopsis Blanche', 'Orchidée papillon d\'une élégance rare. Fleur exotique qui dure plusieurs semaines.', 35.00, 45, true],
            ['Anthurium Rouge', 'Fleur tropicale en forme de cœur, parfaite pour décorer un intérieur moderne.', 22.50, 30, false],
            ['Hibiscus Rose', 'Fleur hawaïenne colorée, symbole de beauté éphémère et de délicatesse.', 18.75, 25, false],
            ['Bird of Paradise', 'Strelitzia reginae, fleur spectaculaire ressemblant à un oiseau exotique.', 28.00, 15, true],
            ['Plumeria Jaune', 'Frangipanier au parfum envoûtant, évoque les îles tropicales.', 24.50, 20, false],
            
            // Shop 2 - Royal Bloom (Mariages)
            ['Rose Blanche Avalanche', 'Rose premium pour mariages, pétales parfaits et longue tenue.', 19.99, 200, true],
            ['Pivoine Sarah Bernhardt', 'Pivoine rose pâle, star des bouquets de mariée depuis 100 ans.', 32.00, 60, true],
            ['Lys Casa Blanca', 'Lys oriental blanc pur au parfum capiteux, majestueux et élégant.', 25.00, 80, false],
            ['Renoncule Blanche', 'Petite fleur délicate aux multiples pétales, romantique à souhait.', 14.50, 95, false],
            ['Gypsophile', 'Nuage de petites fleurs blanches, indispensable pour les bouquets.', 8.99, 150, false],
            ['Eucalyptus Silver Dollar', 'Feuillage argenté parfait pour accompagner les fleurs de mariage.', 12.00, 100, false],
            
            // Shop 3 - Nature\'s Best (Locales)
            ['Tulipe Française', 'Tulipe cultivée en Provence, couleurs vives et naturelles.', 8.50, 180, true],
            ['Marguerite des Champs', 'Fleur champêtre par excellence, fraîcheur et simplicité.', 5.99, 250, false],
            ['Tournesol Géant', 'Tournesol local XXL, apporte la joie du soleil dans votre maison.', 12.50, 90, true],
            ['Lavande de Provence', 'Botte de lavande séchée, parfum relaxant garanti.', 9.50, 120, false],
            ['Bleuet Sauvage', 'Fleur des champs bleue intense, cueillie avec respect de la nature.', 6.75, 85, false],
            ['Coquelicot Rouge', 'Symbole du souvenir, fleur fragile et poétique.', 7.25, 0, false],
            
            // Shop 4 - Premium Flora (Luxe)
            ['Rose Éternelle Rouge', 'Rose naturelle stabilisée, conserve sa beauté pendant 3 ans.', 45.00, 35, true],
            ['Orchidée Vanda Bleue', 'Orchidée rare teinte naturellement, pièce de collection.', 65.00, 12, true],
            ['Bouquet Signature Premium', 'Composition exclusive de fleurs premium, création unique.', 89.00, 8, false],
            ['Amaryllis Royal', 'Grande fleur majestueuse rouge vif, parfaite pour les fêtes.', 28.50, 40, false],
            ['Camélia Japonais', 'Fleur délicate cultivée selon les traditions japonaises.', 38.00, 18, false],
            ['Gardénia Parfumé', 'Fleur au parfum envoûtant, symbole d\'élégance raffinée.', 32.00, 22, false],
            
            // Shop 5 - Exotic Plants (Importées)
            ['Protea King', 'Fleur nationale d\'Afrique du Sud, spectaculaire et durable.', 35.00, 25, true],
            ['Lotus Sacré', 'Fleur spirituelle d\'Asie, symbole de pureté et d\'éveil.', 42.00, 10, true],
            ['Heliconia Tropicana', 'Fleur pince de homard, couleurs flamboyantes d\'Amérique du Sud.', 28.00, 30, false],
            ['Ginger Torch', 'Fleur de gingembre spectaculaire, rouge intense et forme unique.', 24.00, 20, false],
            ['Monstera Deliciosa', 'Feuillage tropical tendance, incontournable en décoration.', 18.00, 55, false],
            ['Ananas Ornemental', 'Mini ananas décoratif, touche exotique originale.', 15.00, 35, false],
        ];

        $fleurs = [];
        foreach ($fleursData as $index => $data) {
            $fleur = new Fleur();
            $fleur->setNom($data[0]);
            $fleur->setDescription($data[1]);
            $fleur->setPrix($data[2]);
            $fleur->setStock($data[3]);
            $fleur->setIsPinned($data[4]);
            // 6 fleurs par shop (index 0-5 = shop 0, 6-11 = shop 1, etc.)
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