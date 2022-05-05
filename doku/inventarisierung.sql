-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 05. Mai 2022 um 13:44
-- Server-Version: 8.0.27-0ubuntu0.20.04.1
-- PHP-Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `inventarisierung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Abteilungen`
--

CREATE TABLE `Abteilungen` (
  `abteilung` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Filialen`
--

CREATE TABLE `Filialen` (
  `filiale` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Inventar`
--

CREATE TABLE `Inventar` (
  `i_id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `typ` varchar(255) DEFAULT NULL,
  `buy_date` date DEFAULT NULL,
  `buy_price` double DEFAULT NULL,
  `dauer` int DEFAULT NULL,
  `filiale` varchar(255) DEFAULT NULL,
  `abteilung` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Inventartypen`
--

CREATE TABLE `Inventartypen` (
  `typ_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Users`
--

CREATE TABLE `Users` (
  `u_id` int NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `passwort` varchar(255) DEFAULT NULL,
  `login_attepms` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `Users`
--

INSERT INTO `Users` (`u_id`, `username`, `passwort`, `login_attepms`) VALUES
(1, 'admin', '$2y$10$Or0XzXWJtOTgTO19TTF1NOd4TTFx5W4p8mMmvW3/t6K67jiIj35cu', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Abteilungen`
--
ALTER TABLE `Abteilungen`
  ADD PRIMARY KEY (`abteilung`);

--
-- Indizes für die Tabelle `Filialen`
--
ALTER TABLE `Filialen`
  ADD PRIMARY KEY (`filiale`);

--
-- Indizes für die Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  ADD PRIMARY KEY (`i_id`),
  ADD KEY `typ` (`typ`),
  ADD KEY `abteilung` (`abteilung`),
  ADD KEY `filiale` (`filiale`);

--
-- Indizes für die Tabelle `Inventartypen`
--
ALTER TABLE `Inventartypen`
  ADD PRIMARY KEY (`typ_name`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`u_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  MODIFY `i_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  ADD CONSTRAINT `Inventar_ibfk_1` FOREIGN KEY (`typ`) REFERENCES `Inventartypen` (`typ_name`),
  ADD CONSTRAINT `Inventar_ibfk_2` FOREIGN KEY (`abteilung`) REFERENCES `Abteilungen` (`abteilung`),
  ADD CONSTRAINT `Inventar_ibfk_3` FOREIGN KEY (`filiale`) REFERENCES `Filialen` (`filiale`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
