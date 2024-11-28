-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 25 nov. 2024 à 10:29
-- Version du serveur : 8.3.0
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fleurverte`
--

-- --------------------------------------------------------

--
-- Structure de la table `fleur`
--

DROP TABLE IF EXISTS `fleur`;
CREATE TABLE IF NOT EXISTS `fleur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thc` double NOT NULL,
  `prix` double NOT NULL,
  `stock` int DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fleur`
--

INSERT INTO `fleur` (`id`, `nom`, `thc`, `prix`, `stock`, `description`) VALUES
(1, 'OG Kush', 20.5, 12.99, 100, 'Une variété robuste originaire de la côte ouest, connue pour sa résistance et son arôme terreux distinctif.'),
(2, 'Blue Dream', 18, 11.5, 150, 'Cette plante au nom évocateur offre une floraison généreuse et un parfum doux et fruité.'),
(3, 'Girl Scout Cookies', 22, 14.99, 80, 'Une variété hybride populaire, réputée pour ses couleurs vives et son goût sucré rappelant les biscuits.'),
(4, 'Sour Diesel', 19.5, 13.5, 120, 'Reconnaissable à son odeur piquante, cette plante énergisante est appréciée pour sa croissance rapide.'),
(5, 'Purple Haze', 17, 10.99, 200, 'Célèbre pour ses teintes violettes uniques, cette variété offre une expérience visuelle et olfactive inoubliable.'),
(6, 'White Widow', 21, 13.99, 90, 'Cette plante robuste produit des fleurs recouvertes de cristaux, lui donnant une apparence givrée distinctive.'),
(7, 'Northern Lights', 18.5, 12.5, 110, 'Originaire du nord, cette variété est réputée pour sa floraison rapide et son parfum épicé.'),
(8, 'Pineapple Express', 20, 13.75, 130, 'Un hybride tropical au goût sucré d\'ananas, idéal pour les amateurs de saveurs exotiques.'),
(9, 'AK-47', 19, 12.25, 140, 'Cette variété vigoureuse, nommée d\'après un célèbre fusil, est connue pour sa croissance rapide et son rendement élevé.'),
(10, 'Bubba Kush', 17.5, 11.75, 160, 'Une plante compacte aux notes terreuses, appréciée pour sa floraison dense et son arôme prononcé.'),
(11, 'Durban Poison', 21.5, 14.5, 70, 'Originaire d\'Afrique du Sud, cette sativa pure est réputée pour ses effets énergisants et son goût épicé.'),
(12, 'Green Crack', 18, 11.99, 180, 'Une variété à la croissance rapide, nommée ainsi pour son énergie vibrante et sa productivité élevée.'),
(13, 'Jack Herer', 20, 13.25, 95, 'Nommée en l\'honneur d\'un célèbre militant, cette plante est connue pour ses effets créatifs et son parfum de pin.'),
(14, 'Granddaddy Purple', 17, 11.25, 170, 'Reconnaissable à ses teintes violettes profondes, cette variété offre une expérience relaxante unique.'),
(15, 'Strawberry Cough', 19, 12.75, 85, 'Cette plante au goût de fraise prononcé est appréciée pour ses effets euphorisants et sa saveur fruitée.'),
(16, 'Gorilla Glue', 23, 15.5, 75, 'Réputée pour sa puissance, cette variété produit des fleurs denses couvertes de résine collante.'),
(17, 'Lemon Haze', 18.5, 12, 125, 'Un hybride énergisant au parfum citronné rafraîchissant, idéal pour stimuler la créativité.'),
(18, 'Wedding Cake', 22.5, 14.75, 60, 'Cette variété gourmande offre un bouquet aromatique complexe rappelant un gâteau de mariage.'),
(19, 'Amnesia Haze', 20.5, 13.5, 105, 'Connue pour ses effets cérébraux puissants, cette sativa est populaire parmi les créatifs.'),
(20, 'Skywalker OG', 21, 14.25, 95, 'Un hybride puissant qui combine la force de ses lignées parentales pour une expérience équilibrée.'),
(21, 'despacito', 28, 202020, NULL, 'azzeaezzeazee');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
