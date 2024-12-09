-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Dic 09, 2024 alle 11:47
-- Versione del server: 8.0.26
-- Versione PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_corsilg`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Commenti`
--

CREATE TABLE `filmDB_Commenti` (
  `ID` int NOT NULL,
  `Stelle` decimal(10,0) NOT NULL,
  `Commento` text COLLATE utf8mb4_general_ci NOT NULL,
  `ID_User` int NOT NULL,
  `ID_Film` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Commenti`
--

INSERT INTO `filmDB_Commenti` (`ID`, `Stelle`, `Commento`, `ID_User`, `ID_Film`) VALUES
(1, '5', '\"Un capolavoro visivo e mentale! La trama intricata e la regia impeccabile ti tengono incollato fino all\'ultimo secondo. Un\'esperienza unica nel suo genere!\"', 2, 1),
(2, '4', '\"Un film che ti sorprende ad ogni scena. Una critica sociale pungente e una trama che mescola tensione, dramma e satira. Un\'esperienza indimenticabile.\"', 3, 3),
(3, '4', '\"Un film che ti fa sognare ad occhi aperti! La chimica tra i protagonisti e le musiche ti coinvolgono in un\'emozionante montagna russa di emozioni. Un vero tributo alla magia di Hollywood.\"', 3, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Films`
--

CREATE TABLE `filmDB_Films` (
  `ID` int NOT NULL,
  `Titolo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Data_Uscita` date NOT NULL,
  `Descrizione` text COLLATE utf8mb4_general_ci NOT NULL,
  `Durata` time NOT NULL,
  `Copertina` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ID_Lingua` int NOT NULL,
  `ID_Genere` int NOT NULL,
  `ID_Seguito` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Films`
--

INSERT INTO `filmDB_Films` (`ID`, `Titolo`, `Data_Uscita`, `Descrizione`, `Durata`, `Copertina`, `ID_Lingua`, `ID_Genere`, `ID_Seguito`) VALUES
(1, 'Inception', '2014-12-12', 'Un ladro di sogni deve compiere l\'idea perfetta in un mondo onirico instabile.', '02:25:44', NULL, 2, 3, NULL),
(2, 'La La Land', '2016-12-09', 'Un\'aspirante attrice e un musicista si innamorano a Los Angeles.\r\n', '02:21:13', NULL, 2, 2, NULL),
(3, 'Parasite', '2019-11-05', 'Una famiglia povera si infiltra in una ricca, scatenando un dramma inaspettato.\r\n\r\n\r\n', '03:00:52', NULL, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Generi`
--

CREATE TABLE `filmDB_Generi` (
  `ID` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Generi`
--

INSERT INTO `filmDB_Generi` (`ID`, `Nome`) VALUES
(1, 'Dramma'),
(2, 'Musical'),
(3, 'Thriller'),
(4, 'Commedia');

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Lingue`
--

CREATE TABLE `filmDB_Lingue` (
  `ID` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Lingue`
--

INSERT INTO `filmDB_Lingue` (`ID`, `Nome`) VALUES
(1, 'Italiano'),
(2, 'Inglese'),
(3, 'Francese'),
(4, 'Cinese');

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Preferenze`
--

CREATE TABLE `filmDB_Preferenze` (
  `ID` int NOT NULL,
  `ID_User` int NOT NULL,
  `ID_Film` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Preferenze`
--

INSERT INTO `filmDB_Preferenze` (`ID`, `ID_User`, `ID_Film`) VALUES
(1, 2, 2),
(2, 2, 3),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `filmDB_Users`
--

CREATE TABLE `filmDB_Users` (
  `ID` int NOT NULL,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Cognome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `filmDB_Users`
--

INSERT INTO `filmDB_Users` (`ID`, `Nome`, `Cognome`, `Email`, `Password`, `Admin`) VALUES
(1, 'Lucas', 'Corsi', 'corsi@gmail.com', '1234', 1),
(2, 'Mario', 'Rossi', 'rossi@gmail.com', '1234', 0),
(3, 'Luigi', 'Verdi', 'verdi@gmail.com', '1234', 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `filmDB_Commenti`
--
ALTER TABLE `filmDB_Commenti`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Commento-User` (`ID_User`),
  ADD KEY `Commento-Film` (`ID_Film`);

--
-- Indici per le tabelle `filmDB_Films`
--
ALTER TABLE `filmDB_Films`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Film-Lingua` (`ID_Lingua`),
  ADD KEY `Film-Genere` (`ID_Genere`),
  ADD KEY `Film-Film` (`ID_Seguito`);

--
-- Indici per le tabelle `filmDB_Generi`
--
ALTER TABLE `filmDB_Generi`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `filmDB_Lingue`
--
ALTER TABLE `filmDB_Lingue`
  ADD PRIMARY KEY (`ID`);

--
-- Indici per le tabelle `filmDB_Preferenze`
--
ALTER TABLE `filmDB_Preferenze`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Preferenza-User` (`ID_User`),
  ADD KEY `Preferenza-Film` (`ID_Film`);

--
-- Indici per le tabelle `filmDB_Users`
--
ALTER TABLE `filmDB_Users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `filmDB_Commenti`
--
ALTER TABLE `filmDB_Commenti`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `filmDB_Films`
--
ALTER TABLE `filmDB_Films`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `filmDB_Generi`
--
ALTER TABLE `filmDB_Generi`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `filmDB_Lingue`
--
ALTER TABLE `filmDB_Lingue`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `filmDB_Preferenze`
--
ALTER TABLE `filmDB_Preferenze`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `filmDB_Users`
--
ALTER TABLE `filmDB_Users`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `filmDB_Commenti`
--
ALTER TABLE `filmDB_Commenti`
  ADD CONSTRAINT `Commento-Film` FOREIGN KEY (`ID_Film`) REFERENCES `filmDB_Films` (`ID`),
  ADD CONSTRAINT `Commento-User` FOREIGN KEY (`ID_User`) REFERENCES `filmDB_Users` (`ID`);

--
-- Limiti per la tabella `filmDB_Films`
--
ALTER TABLE `filmDB_Films`
  ADD CONSTRAINT `Film-Film` FOREIGN KEY (`ID_Seguito`) REFERENCES `filmDB_Films` (`ID`),
  ADD CONSTRAINT `Film-Genere` FOREIGN KEY (`ID_Genere`) REFERENCES `filmDB_Generi` (`ID`),
  ADD CONSTRAINT `Film-Lingua` FOREIGN KEY (`ID_Lingua`) REFERENCES `filmDB_Lingue` (`ID`);

--
-- Limiti per la tabella `filmDB_Preferenze`
--
ALTER TABLE `filmDB_Preferenze`
  ADD CONSTRAINT `Preferenza-Film` FOREIGN KEY (`ID_Film`) REFERENCES `filmDB_Films` (`ID`),
  ADD CONSTRAINT `Preferenza-User` FOREIGN KEY (`ID_User`) REFERENCES `filmDB_Users` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
