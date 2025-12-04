-- Données de test pour Fleur Verte
-- 1 admin, 20 clients, 5 fleuristes, 20 types de plantes
-- Mot de passe pour tous: 'password'

-- Nettoyage des tables (dans l'ordre des dépendances)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE commentaire;
TRUNCATE TABLE fleur_tag;
TRUNCATE TABLE cart_item;
TRUNCATE TABLE cart;
TRUNCATE TABLE message;
TRUNCATE TABLE conversation;
TRUNCATE TABLE fleur;
TRUNCATE TABLE tag;
TRUNCATE TABLE fleuriste;
TRUNCATE TABLE client;
TRUNCATE TABLE `user`;
SET FOREIGN_KEY_CHECKS = 1;

-- Utilisateurs (1 admin + 20 clients + 5 fleuristes = 26 users)
INSERT INTO `user` (id, email, roles, password, username, avatar_name, updated_at) VALUES
(1, 'admin@test.com', '["ROLE_ADMIN"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '1.png', NOW()),
(2, 'client1@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marie_Dupont', '2.png', NOW()),
(3, 'client2@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean_Martin', '3.png', NOW()),
(4, 'client3@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie_Bernard', '4.png', NOW()),
(5, 'client4@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucas_Petit', '5.png', NOW()),
(6, 'client5@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma_Dubois', '6.png', NOW()),
(7, 'client6@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thomas_Moreau', '7.png', NOW()),
(8, 'client7@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lea_Laurent', '8.png', NOW()),
(9, 'client8@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hugo_Simon', '9.png', NOW()),
(10, 'client9@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chloe_Michel', '10.png', NOW()),
(11, 'client10@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nathan_Garcia', '1.png', NOW()),
(12, 'client11@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Camille_Roux', '2.png', NOW()),
(13, 'client12@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maxime_Leroy', '3.png', NOW()),
(14, 'client13@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manon_David', '4.png', NOW()),
(15, 'client14@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Antoine_Bertrand', '5.png', NOW()),
(16, 'client15@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Julie_Robert', '6.png', NOW()),
(17, 'client16@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Romain_Richard', '7.png', NOW()),
(18, 'client17@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura_Durand', '8.png', NOW()),
(19, 'client18@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quentin_Morel', '9.png', NOW()),
(20, 'client19@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah_Fournier', '10.png', NOW()),
(21, 'client20@test.com', '["ROLE_USER"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Theo_Girard', '1.png', NOW()),
(22, 'fleuriste1@test.com', '["ROLE_FLEURISTE"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Flora_Shop', '2.png', NOW()),
(23, 'fleuriste2@test.com', '["ROLE_FLEURISTE"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rose_Garden', '3.png', NOW()),
(24, 'fleuriste3@test.com', '["ROLE_FLEURISTE"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nature_Bloom', '4.png', NOW()),
(25, 'fleuriste4@test.com', '["ROLE_FLEURISTE"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Premium_Flora', '5.png', NOW()),
(26, 'fleuriste5@test.com', '["ROLE_FLEURISTE"]', '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Exotic_Plants', '6.png', NOW());

-- Clients (users 2 à 21 = 20 clients)
INSERT INTO client (id, user_id) VALUES
(1, 2), (2, 3), (3, 4), (4, 5), (5, 6), (6, 7), (7, 8), (8, 9), (9, 10), (10, 11),
(11, 12), (12, 13), (13, 14), (14, 15), (15, 16), (16, 17), (17, 18), (18, 19), (19, 20), (20, 21);

-- Fleuristes (users 22 à 26 = 5 fleuristes avec boutiques)
INSERT INTO fleuriste (id, user_id, nom, actif) VALUES
(1, 22, 'Green Paradise Shop', 1),
(2, 23, 'Royal Bloom Boutique', 1),
(3, 24, 'Nature''s Best Garden', 1),
(4, 25, 'Premium Flora Store', 1),
(5, 26, 'Exotic Plants Market', 1);

-- Tags (20 types de plantes)
INSERT INTO tag (nom, couleur) VALUES
('Rose', 'red'),
('Tulipe', 'pink'),
('Lys', 'white'),
('Orchidée', 'purple'),
('Jasmin', 'yellow'),
('Lavande', 'lavender'),
('Pivoine', 'magenta'),
('Marguerite', 'white'),
('Tournesol', 'yellow'),
('Iris', 'blue'),
('Lilas', 'purple'),
('Oeillet', 'pink'),
('Gerbera', 'orange'),
('Freesia', 'yellow'),
('Dahlia', 'red'),
('Chrysanthème', 'gold'),
('Camélia', 'pink'),
('Hortensia', 'blue'),
('Magnolia', 'white'),
('Bruyère', 'purple');

-- Fleurs (20 produits variés)
INSERT INTO fleur (fleuriste_id, nom, description, prix, stock, is_pinned, image_name, updated_at) VALUES
(1, 'Rose Rouge', 'Magnifique rose rouge classique, symbole de l\'amour et de la passion', 15.99, 50, 1, 'rose_rouge.jpg', NOW()),
(1, 'Tulipe Jaune', 'Tulipe jaune vif, parfaite pour égayer votre jardin', 8.50, 75, 0, 'tulipe_jaune.jpg', NOW()),
(1, 'Lys Blanc', 'Lys blanc élégant, idéal pour les bouquets de mariage', 12.75, 30, 1, 'lys_blanc.jpg', NOW()),
(1, 'Orchidée Pourpre', 'Orchidée pourpre exotique, fleur délicate et raffinée', 25.00, 40, 0, 'orchidee_pourpre.jpg', NOW()),
(1, 'Jasmin Blanc', 'Jasmin blanc au parfum enivrant, fleur nocturne', 10.25, 60, 0, 'jasmin_blanc.jpg', NOW()),
(2, 'Lavande Bleue', 'Lavande bleue aromatique, parfaite pour la relaxation', 9.50, 25, 1, 'lavande_bleue.jpg', NOW()),
(2, 'Pivoine Rose', 'Pivoine rose luxuriante, fleur printanière spectaculaire', 18.75, 35, 0, 'pivoine_rose.jpg', NOW()),
(2, 'Marguerite Blanche', 'Marguerite blanche simple et charmante, classique des champs', 6.25, 45, 0, 'marguerite_blanche.jpg', NOW()),
(2, 'Tournesol Géant', 'Tournesol géant jaune d\'or, suit le soleil toute la journée', 14.50, 55, 0, 'tournesol_geant.jpg', NOW()),
(2, 'Iris Bleu', 'Iris bleu royal, fleur élégante et structurée', 16.75, 40, 0, 'iris_bleu.jpg', NOW()),
(3, 'Lilas Violet', 'Lilas violet parfumé, annonciateur du printemps', 11.25, 80, 1, 'lilas_violet.jpg', NOW()),
(3, 'Oeillet Rouge', 'Oeillet rouge vif, classique des bouquets romantiques', 7.00, 65, 0, 'oeillet_rouge.jpg', NOW()),
(3, 'Gerbera Orange', 'Gerbera orange éclatant, fleur joyeuse et colorée', 13.50, 20, 0, 'gerbera_orange.jpg', NOW()),
(3, 'Freesia Blanche', 'Freesia blanche au parfum délicat et sucré', 17.00, 30, 0, 'freesia_blanche.jpg', NOW()),
(3, 'Dahlia Rouge', 'Dahlia rouge spectaculaire, fleur d\'été imposante', 19.25, 35, 0, 'dahlia_rouge.jpg', NOW()),
(4, 'Chrysanthème Or', 'Chrysanthème doré, fleur d\'automne par excellence', 8.75, 15, 1, 'chrysantheme_or.jpg', NOW()),
(4, 'Camélia Rose', 'Camélia rose délicat, fleur d\'hiver élégante', 22.50, 10, 0, 'camelia_rose.jpg', NOW()),
(4, 'Hortensia Bleu', 'Hortensia bleu ciel, fleur d\'été abondante', 12.25, 50, 0, 'hortensia_bleu.jpg', NOW()),
(4, 'Magnolia Blanc', 'Magnolia blanc majestueux, fleur printanière ancestrale', 28.00, 40, 0, 'magnolia_blanc.jpg', NOW()),
(5, 'Bruyère Pourpre', 'Bruyère pourpre sauvage, fleur de montagne rustique', 9.75, 25, 0, 'bruyere_pourpre.jpg', NOW());

-- Associations Fleurs-Tags
INSERT INTO fleur_tag (fleur_id, tag_id) VALUES
(1, 1), (1, 10), (1, 18), (1, 19),
(2, 2), (2, 6), (2, 14), (2, 19),
(3, 3), (3, 10), (3, 18), (3, 19),
(4, 4), (4, 10), (4, 12), (4, 18),
(5, 5), (5, 6), (5, 9), (5, 18),
(6, 6), (6, 9), (6, 13), (6, 18),
(7, 7), (7, 10), (7, 14), (7, 18),
(8, 8), (8, 10), (8, 14), (8, 18),
(9, 9), (9, 10), (9, 14), (9, 18),
(10, 10), (10, 11), (10, 14), (10, 18),
(11, 11), (11, 12), (11, 13), (11, 18),
(12, 12), (12, 13), (12, 14), (12, 18),
(13, 13), (13, 14), (13, 15), (13, 18),
(14, 14), (14, 15), (14, 16), (14, 18),
(15, 15), (15, 16), (15, 17), (15, 18),
(16, 16), (16, 17), (16, 18), (16, 19),
(17, 17), (17, 18), (17, 19), (17, 20),
(18, 18), (18, 19), (18, 20), (18, 1),
(19, 19), (19, 20), (19, 1), (19, 2),
(20, 20), (20, 1), (20, 2), (20, 3);