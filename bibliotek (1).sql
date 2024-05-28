-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 28 maj 2024 kl 15:36
-- Serverversion: 10.4.32-MariaDB
-- PHP-version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `bibliotek`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `användare`
--

CREATE TABLE `användare` (
  `användar_id` int(11) NOT NULL,
  `namn` varchar(255) DEFAULT NULL,
  `telefon` varchar(15) DEFAULT NULL,
  `epost` varchar(255) DEFAULT NULL,
  `losen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `användare`
--

INSERT INTO `användare` (`användar_id`, `namn`, `telefon`, `epost`, `losen`) VALUES
(1, 'Admin', '070 123 45 67', 'Admin@gmail.com', '$2y$10$8ugIXuhFqE/5Uzj3ADtHcOvbxhT.gRmTMYdVx0BMLvpjRgC8YRdha'),
(2, 'albert', '070 123 45 67', 'Albert@brohede.se', '$2y$10$xh4FqGQ1hE1T8HjZo03qquwkonFn9m8y/myHfXSP1UFZLylMp.3m6'),
(3, 'albert2', '070 123 45 67', 'abbe.gamer@brohede.se', '$2y$10$HsbGtZg2awBWaEmY93YEtO1B0dnN7FXhvoruluc/16BQDWJM5Huze'),
(4, 'albert2', '070 123 45 67', 'abbe.gamer@brohede.se', '$2y$10$rPhTGZnktR3X628iILVJqOqrfdquagxnwOgTjMqw7OqACGd3vZJi.'),
(5, 'albert3', '070 123 45 67', 'Albert@brohede.se', '$2y$10$uT4Pv8fFzusNOgWyFIJwKeKvTGKB0LjEwnpP6QSgbHwEF0D3jhM7S'),
(6, 'albert4', '070 123 45 67', 'Albert@brohede.se', '$2y$10$1.PouTgIdyWdrVdYdpxqFuhZagJKXNmOWaxVTFej0HQNgfD1yv.DK'),
(7, 'albert5', '070 123 45 67', 'Albert@brohede.se', '$2y$10$XJmlcT39xV/fQ7d1Urue7uxbkQhrWNI0mW5cuj200OdVbmjjKIHWK');

-- --------------------------------------------------------

--
-- Tabellstruktur `böcker`
--

CREATE TABLE `böcker` (
  `bok_id` int(11) NOT NULL,
  `titel` varchar(255) DEFAULT NULL,
  `författare` varchar(255) DEFAULT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `utgivningsår` int(11) DEFAULT NULL,
  `tillgängliga_exemplar` int(11) DEFAULT NULL,
  `bild` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `böcker`
--

INSERT INTO `böcker` (`bok_id`, `titel`, `författare`, `ISBN`, `utgivningsår`, `tillgängliga_exemplar`, `bild`) VALUES
(1, 'To Kill a Mockingbird', 'Harper Lee', '9780061120084', 1960, 2, 'uploads/mocking.png'),
(2, '1984', 'George Orwell', '9780451524935', 1949, 6, 'uploads/bild2.png'),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 1925, 8, 'uploads/Great.png'),
(4, 'The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 1951, 5, 'uploads/rye.png'),
(5, 'Pride and Prejudice', 'Jane Austen', '9780486284736', 1813, 2, 'uploads/pride.png'),
(6, 'To the Lighthouse', 'Virginia Woolf', '9780156907392', 1927, 2, 'uploads/bild.png'),
(7, 'Moby-Dick', 'Herman Melville', '9780142000083', 1851, 2, 'uploads/moby.png'),
(8, 'Crime and Punishment', 'Fyodor Dostoevsky', '9780679734505', 1866, 1, 'uploads/crime.png'),
(9, 'Brave New World', 'Aldous Huxley', '9780060850524', 1932, 0, 'uploads/brave.png'),
(10, 'The Lord of the Rings', 'J.R.R. Tolkien', '9780544003415', 1954, 1, 'uploads/bild1.png'),
(11, 'Harry Potter and the Chamber of Secrets', 'J.K. Rowling', '9780439064873', 1998, 5, 'uploads/book.jpg'),
(15, 'Of Mice and Men', 'John Steinbeck', '9780140177398', 1937, 4, 'uploads/mam.jpg'),
(16, 'Mein Kampf', 'Adolf Hitler', '9783956591046', 1925, 8, 'uploads/bunnies.jpg'),
(17, 'Diamantmysteriet', 'Martin Widmark', '9780439064856', 2002, 2, 'uploads/9789163871931_383x_diamantmysteriet.jpg');

-- --------------------------------------------------------

--
-- Tabellstruktur `utlåning`
--

CREATE TABLE `utlåning` (
  `utlånings_id` int(11) NOT NULL,
  `bok_id` int(11) DEFAULT NULL,
  `användar_id` int(11) DEFAULT NULL,
  `utlåningsdatum` date DEFAULT NULL,
  `återlämningsdatum` date DEFAULT NULL,
  `status` enum('utlånad','återlämnad') DEFAULT NULL,
  `återlämnad` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumpning av Data i tabell `utlåning`
--

INSERT INTO `utlåning` (`utlånings_id`, `bok_id`, `användar_id`, `utlåningsdatum`, `återlämningsdatum`, `status`, `återlämnad`) VALUES
(47, 1, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(48, 2, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(49, 3, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(50, 4, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(51, 5, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(52, 6, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(53, 7, 2, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(54, 17, 5, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(55, 9, 6, '2024-05-27', '2024-06-10', 'utlånad', NULL),
(56, 5, 7, '2024-05-27', '2024-06-10', 'utlånad', NULL);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `användare`
--
ALTER TABLE `användare`
  ADD PRIMARY KEY (`användar_id`);

--
-- Index för tabell `böcker`
--
ALTER TABLE `böcker`
  ADD PRIMARY KEY (`bok_id`);

--
-- Index för tabell `utlåning`
--
ALTER TABLE `utlåning`
  ADD PRIMARY KEY (`utlånings_id`),
  ADD KEY `bok_id` (`bok_id`),
  ADD KEY `användar_id` (`användar_id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `användare`
--
ALTER TABLE `användare`
  MODIFY `användar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT för tabell `böcker`
--
ALTER TABLE `böcker`
  MODIFY `bok_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT för tabell `utlåning`
--
ALTER TABLE `utlåning`
  MODIFY `utlånings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `utlåning`
--
ALTER TABLE `utlåning`
  ADD CONSTRAINT `utlåning_ibfk_1` FOREIGN KEY (`bok_id`) REFERENCES `böcker` (`bok_id`),
  ADD CONSTRAINT `utlåning_ibfk_2` FOREIGN KEY (`användar_id`) REFERENCES `användare` (`användar_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
