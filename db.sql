-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 07:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `g27`
--

-- --------------------------------------------------------

--
-- Table structure for table `achat`
--

CREATE TABLE `achat` (
  `id` int(11) NOT NULL,
  `id_visiteur` int(11) DEFAULT NULL,
  `date_achat` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 0,
  `id_achat` int(11) DEFAULT NULL,
  `image_article` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `id_visiteur` int(11) NOT NULL,
  `id_oeuvres` int(11) DEFAULT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `id_article` int(11) DEFAULT NULL,
  `notes` float DEFAULT NULL CHECK (`notes` between 0 and 5),
  `commentaire` varchar(1000) DEFAULT NULL,
  `date_avis` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping dahhhta for table `avis`
--


-- --------------------------------------------------------

--
-- Table structure for table `billets`
--

CREATE TABLE `billets` (
  `id` int(11) NOT NULL,
  `tarif` decimal(10,2) NOT NULL,
  `reduction` decimal(5,2) DEFAULT 0.00,
  `type_billet` varchar(100) NOT NULL,
  `id_evenement` int(11) DEFAULT NULL,
  `id_visiteur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employe`
--

CREATE TABLE `employe` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(500) NOT NULL,
  `role` varchar(100) NOT NULL,
  `date_embauche` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employe`
--

-------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `lieu` varchar(100) NOT NULL,
  `capacite` int(11) NOT NULL,
  `image_evenement` varchar(255) DEFAULT NULL,
  `id_employe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oeuvres`
--

CREATE TABLE `oeuvres` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `artiste` varchar(100) NOT NULL,
  `date_creation` date DEFAULT NULL,
  `type_oeuvre` varchar(100) NOT NULL,
  `materiaux` varchar(100) DEFAULT NULL,
  `informations` text DEFAULT NULL,
  `image_oeuvre` varchar(255) DEFAULT NULL,
  `id_employe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `type_utilisateur` varchar(20) NOT NULL,
  `password` varchar(500) NOT NULL,
  `role` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visiteur`
--

CREATE TABLE `visiteur` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(500) NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `age` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `type_visiteur` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visiteur`
--


--
-- Indexes for dumped tables
--

-
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
