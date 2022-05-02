-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Erstellungszeit: 02. Mai 2022 um 09:50
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
  `a_id` int NOT NULL,
  `abteilung` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Filialen`
--

CREATE TABLE `Filialen` (
  `f_id` int NOT NULL,
  `filiale` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Inventar`
--

CREATE TABLE `Inventar` (
  `inv_id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `typ` int DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `anschaffungspreis` double DEFAULT NULL,
  `nutzungsdauer` int DEFAULT NULL,
  `filiale` int DEFAULT NULL,
  `abteilung` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Inventartypen`
--

CREATE TABLE `Inventartypen` (
  `it_id` int NOT NULL,
  `typ_name` varchar(255) DEFAULT NULL
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
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Abteilungen`
--
ALTER TABLE `Abteilungen`
  ADD PRIMARY KEY (`a_id`);

--
-- Indizes für die Tabelle `Filialen`
--
ALTER TABLE `Filialen`
  ADD PRIMARY KEY (`f_id`);

--
-- Indizes für die Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  ADD PRIMARY KEY (`inv_id`),
  ADD KEY `typ` (`typ`),
  ADD KEY `filiale` (`filiale`),
  ADD KEY `abteilung` (`abteilung`);

--
-- Indizes für die Tabelle `Inventartypen`
--
ALTER TABLE `Inventartypen`
  ADD PRIMARY KEY (`it_id`);

--
-- Indizes für die Tabelle `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`u_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Abteilungen`
--
ALTER TABLE `Abteilungen`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Filialen`
--
ALTER TABLE `Filialen`
  MODIFY `f_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  MODIFY `inv_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Inventartypen`
--
ALTER TABLE `Inventartypen`
  MODIFY `it_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Users`
--
ALTER TABLE `Users`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Inventar`
--
ALTER TABLE `Inventar`
  ADD CONSTRAINT `Inventar_ibfk_1` FOREIGN KEY (`typ`) REFERENCES `Inventartypen` (`it_id`),
  ADD CONSTRAINT `Inventar_ibfk_2` FOREIGN KEY (`filiale`) REFERENCES `Filialen` (`f_id`),
  ADD CONSTRAINT `Inventar_ibfk_3` FOREIGN KEY (`abteilung`) REFERENCES `Abteilungen` (`a_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
