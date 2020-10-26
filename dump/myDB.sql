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
	`Parola` CHAR(64) NOT NULL, #-- SHA256SUM (SALT="TeleShop") 
	PRIMARY KEY (`IDUtilizator`)
);

#--Tablea Cumparatori (1:1 Utilizator, 1:1 Card)
CREATE TABLE `Persoana` (
	`IDUtilizator` INT unsigned NOT NULL,
	`IDCard` INT unsigned,
	`Nume` VARCHAR(50) NOT NULL,
	`Prenume` VARCHAR(50) NOT NULL,
	`CNP` CHAR(13) NOT NULL,
	`Sex` CHAR(1) DEFAULT 'M',
	`Adresa` VARCHAR(50) NOT NULL,
	UNIQUE(CNP),
	FOREIGN KEY (IDUtilizator) REFERENCES Utilizator(IDUtilizator),
	FOREIGN KEY (IDCard) REFERENCES Card(IDCard),
	CHECK (Sex='M' or Sex='F')
);

#--Tabela Card cumparator
CREATE TABLE `Card` (
	`IDCard` INT unsigned NOT NULL AUTO_INCREMENT,
	`Tip` VARCHAR(20) NOT NULL,
	`Exp` DATE NOT NULL,
	`Data` VARCHAR(50) NOT NULL,
	`CCV` VARCHAR(50)
);

#-- Tabela Vanzator (1:1 Utilizator)
CREATE TABLE `Vanzator` (
	`IDUtilizator` INT unsigned NOT NULL,
	`NumeFirma` VARCHAR(50) NOT NULL,
	`Adresa` VARCHAR(50) NOT NULL,
	`IBAN` VARCHAR(50),
	`CUI` VARCHAR(50),
	`NrTelefon` VARCHAR(12) NOT NULL, #--Contact pentru firma vanzatoare

	FOREIGN KEY (IDUtilizator) REFERENCES Utilizator(IDUtilizator)
);

#-- Tabela Telefoane puse la licitatie ( Utilizator/Vanzator 1:N Telefon )
CREATE TABLE `Telefon` (
	`IDTelefon` INT unsigned NOT NULL AUTO_INCREMENT,
	`IDUtilizator` INT unsigned NOT NULL, #-- Vanzator
	`Nume` VARCHAR(50) NOT NULL,
	`PretInitial` DECIMAL(10,2) NOT NULL, 
	`AnAparitie` CHAR(4) NOT NULL,
	`Specificatii` VARCHAR(500),
	`DataLicitiatie` DATETIME DEFAULT NOW(),
	`Vandut` BOOL DEFAULT False,
	
	PRIMARY KEY (IDTelefon),
	FOREIGN KEY (IDUtilizator) REFERENCES Utilizator(IDUtilizator)
);

#-- Tabela Licitatii ( Utilizator N:N Telefon)
CREATE TABLE `Licitatie` (
	`IDLicitatie` INT unsigned NOT NULL AUTO_INCREMENT,
	`IDUtilizator` INT unsigned NOT NULL, #-- Cumparator
	`IDTelefon` INT unsigned NOT NULL,
	`PretLicitat` DECIMAL(10,2) NOT NULL,
	`DataLicitatie` DATETIME DEFAULT NOW(), 
	`Finalizata` BOOL DEFAULT False,
	
	PRIMARY KEY (IDLicitatie), #-- Un utilizator poate avea mai multe licitatii
	FOREIGN KEY (IDUtilizator) REFERENCES Utilizator(IDUtilizator),
	FOREIGN KEY (IDTelefon) REFERENCES Telefon(IDTelefon)
);

