#-- Baza de date gestionare licitatii online de telefoane
#---------------------------------
#--Se creaza un cont in tabela utilizatori care poate fi de tipul Persoana sau Vanzator. O persoana trebuie sa completeze informatiile legate de cardul de credit pentru a putea licita
#--Licitatiile au o data limita, la care persoana care licita cel mai mult primeste nr de telefoane cerute ( in limita stocului ). Daca stocul nu s-a epuizat, urmatoarea persoana care a licitat cel mai mult primeste telefonale, si asa mai departe pana stocul se epuizeaza sau nu mai sunt persoane care au licitat.
#--Licitatiile trebuie sa fie neaparat mai mari decat pretul initial propus de vanzator, pentru a putea fi luate in consideratie.
#---------------------------------

#-- Technologi: MySQL 5.6.40, PHP, HTML, JS, Docker.

CREATE DATABASE myDb; 

USE myDb;


#--Tabela informatii referitoare la contul de login.
CREATE TABLE `Utilizator` (
	`IDUtilizator` INT unsigned NOT NULL AUTO_INCREMENT,
	`Email` VARCHAR(50) NOT NULL,
	`Parola` CHAR(64) NOT NULL,
	PRIMARY KEY (`IDUtilizator`)
);

#--Tabela Card cumparator
CREATE TABLE `Card` (
	`IDCard` INT unsigned NOT NULL AUTO_INCREMENT,
	`Tip` VARCHAR(20) NOT NULL,
	`Exp` DATE NOT NULL,
	`Data` VARCHAR(50) NOT NULL,
	`CCV` VARCHAR(50),
	PRIMARY KEY(`IDCard`)
);



CREATE TABLE `Persoana` (
	`IDUtilizator` INT unsigned NOT NULL,
	`IDCard` INT unsigned,
	`Nume` VARCHAR(50) NOT NULL,
	`Prenume` VARCHAR(50) NOT NULL,
	`CNP` CHAR(13) NOT NULL,
	`Adresa` VARCHAR(80) NOT NULL,
	PRIMARY KEY(`IDUtilizator`),
	UNIQUE(CNP),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`),
	FOREIGN KEY (`IDCard`) REFERENCES Card(`IDCard`)
);

#-- Tabela Vanzator (1:1 Utilizator)
CREATE TABLE `Vanzator` (
	`IDUtilizator` INT unsigned NOT NULL,
	`NumeFirma` VARCHAR(50) NOT NULL,
	`Adresa` VARCHAR(50) NOT NULL,
	`IBAN` VARCHAR(50),
	`CUI` VARCHAR(50),
	`NrTelefon` VARCHAR(12) NOT NULL, 

	PRIMARY KEY(`IDUtilizator`),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`)
);

#-- Tabela Telefoane puse la licitatie ( Utilizator/Vanzator 1:N Telefon )
CREATE TABLE `Telefon` (
	`IDTelefon` INT unsigned NOT NULL AUTO_INCREMENT,
	`IDUtilizator` INT unsigned NOT NULL, #-- Vanzator
	`LocImagine` VARCHAR(50) NOT NULL,
	`Nume` VARCHAR(50) NOT NULL,
	`PretInitial` DECIMAL(10,2) NOT NULL, 
	`AnAparitie` CHAR(4) NOT NULL,
	`Specificatii` VARCHAR(500),
	`DataLicitiatie` DATETIME DEFAULT NOW(),
	`Vandut` BOOL DEFAULT False,
	
	PRIMARY KEY (`IDTelefon`),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`)
);

#-- Tabela Licitatii ( Utilizator N:N Telefon)
CREATE TABLE `Licitatie` (
	`IDUtilizator` INT unsigned NOT NULL, 
	`IDTelefon` INT unsigned NOT NULL,
	`PretLicitat` DECIMAL(10,2) NOT NULL,
	`DataLicitatie` DATETIME DEFAULT NOW(), 
	`Finalizata` BOOL DEFAULT False,
	
	PRIMARY KEY (`IDUtilizator`, `IDTelefon`),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`),
	FOREIGN KEY (`IDTelefon`) REFERENCES Telefon(`IDTelefon`)
);
#-- IDUtilizator = Cumparator




#------------------------------------------------

INSERT INTO `Utilizator` (`Email`, `Parola`) VALUES
('aP@a.c', 'ed2260eb29a229809ab6a4dabc66a3059bc3a5a8147ffa18cbc0f2867bc06843'),
#-- Admin
('aV@a.c', 'ed2260eb29a229809ab6a4dabc66a3059bc3a5a8147ffa18cbc0f2867bc06843'),
#-- Admin
('licitatorInstarit@example.com', '778af318efb63cb1f7dee5848310ad819b92627e070e1959b2db421c009a5197');
#-- BDasf2131dmin

