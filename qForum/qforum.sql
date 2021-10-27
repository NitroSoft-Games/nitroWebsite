-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 30 Lis 2020, 00:52
-- Wersja serwera: 10.4.11-MariaDB
-- Wersja PHP: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `qforum`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `IDcomment` int(11) NOT NULL,
  `PostID` int(11) NOT NULL,
  `AuthorID` int(11) NOT NULL,
  `content` longtext COLLATE utf8_polish_ci NOT NULL,
  `comment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `comments`
--

INSERT INTO `comments` (`IDcomment`, `PostID`, `AuthorID`, `content`, `comment_date`) VALUES
(1, 1, 1, 'komentarz do postu w celu testowania.', '2020-11-25 00:05:03'),
(2, 1, 2, 'Komentarz dla zasięgów. Pozdrawiam, user.', '2020-11-30 00:37:08');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `posts`
--

CREATE TABLE `posts` (
  `IDpost` int(11) NOT NULL,
  `AuthorID` int(11) NOT NULL,
  `topic` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `content` longtext COLLATE utf8_polish_ci NOT NULL,
  `SectionID` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `comments` int(11) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `posts`
--

INSERT INTO `posts` (`IDpost`, `AuthorID`, `topic`, `content`, `SectionID`, `views`, `comments`, `post_date`) VALUES
(1, 1, 'Testowy post', 'post stworzony w celach testowych.', 1, 1411, 7, '2020-11-25 00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sections`
--

CREATE TABLE `sections` (
  `IDsection` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `sections`
--

INSERT INTO `sections` (`IDsection`, `name`) VALUES
(1, 'HydePark');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `IDuser` int(11) NOT NULL,
  `login` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `register_date` date NOT NULL,
  `last_seen_date` datetime NOT NULL,
  `photo_link` longtext COLLATE utf8_polish_ci NOT NULL,
  `rank` varchar(25) COLLATE utf8_polish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`IDuser`, `login`, `password`, `register_date`, `last_seen_date`, `photo_link`, `rank`) VALUES
(1, 'kpalson121', '*B50BDC8BCB1A1D85E86B55D06B90D4B713B9A770', '2020-11-26', '2020-11-29 19:33:34', './resources/images/avatars/konon.jpg', 'admin'),
(2, 'user', '*7EA5752FE7D3A5986C2BEC7043963CF2FB7702B1', '2020-11-28', '2020-11-29 21:18:11', './resources/images/avatars/major.jpg', 'moderator');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`IDcomment`),
  ADD KEY `PostID` (`PostID`),
  ADD KEY `AuthorID` (`AuthorID`);

--
-- Indeksy dla tabeli `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`IDpost`),
  ADD KEY `AuthorID` (`AuthorID`);

--
-- Indeksy dla tabeli `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`IDsection`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`IDuser`);

--
-- AUTO_INCREMENT dla tabel zrzutów
--

--
-- AUTO_INCREMENT dla tabeli `comments`
--
ALTER TABLE `comments`
  MODIFY `IDcomment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT dla tabeli `posts`
--
ALTER TABLE `posts`
  MODIFY `IDpost` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `sections`
--
ALTER TABLE `sections`
  MODIFY `IDsection` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `IDuser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`AuthorID`) REFERENCES `users` (`IDuser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`PostID`) REFERENCES `posts` (`IDpost`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`AuthorID`) REFERENCES `users` (`IDuser`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
