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
	`Propietar` VARCHAR(50) NOT NULL,
	`Exp` CHAR(5) NOT NULL,
	`Detalii` CHAR(16) NOT NULL,
	`CCV` VARCHAR(5),
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
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`) ON DELETE CASCADE,
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
	`DataLicitatie` DATETIME NOT NULL,
	`Vandut` BOOL DEFAULT False,
	
	PRIMARY KEY (`IDTelefon`),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`)
);

#-- Tabela Licitatii ( Utilizator N:N Telefon)
CREATE TABLE `Licitatie` (
	`IDLicitatie`  INT unsigned NOT NULL AUTO_INCREMENT,
	`IDUtilizator` INT unsigned, 
	`IDTelefon` INT unsigned NOT NULL,
	`PretLicitat` DECIMAL(10,2) NOT NULL,
	`DataLicitatie` DATETIME DEFAULT NOW(),
	
	PRIMARY KEY (`IDLicitatie`),
	FOREIGN KEY (`IDUtilizator`) REFERENCES Utilizator(`IDUtilizator`) ON DELETE SET NULL,
	FOREIGN KEY (`IDTelefon`) REFERENCES Telefon(`IDTelefon`)
);
#-- IDUtilizator = Cumparator




#------------------------------------------------

INSERT INTO `Utilizator` (`Email`, `Parola`) VALUES
('aP@auction.bb', 'ed2260eb29a229809ab6a4dabc66a3059bc3a5a8147ffa18cbc0f2867bc06843'),
#-- Admin
('aV@auction.bb', 'ed2260eb29a229809ab6a4dabc66a3059bc3a5a8147ffa18cbc0f2867bc06843'),
#-- Admin
('licitatorInstarit@example.com', '778af318efb63cb1f7dee5848310ad819b92627e070e1959b2db421c009a5197'),
#-- BDasf2131dmin
('market@nocia.com','a43bbd89d311bd62270cc61557618a2e3c59a9a3ae7c888988bd58387b600365'),
#-- Nocia
('market@provider.com','322320dda3c2fb943a3d0468195f070b98c67460f19d041dfc1f3216e187fe24');
#-- Provider

INSERT INTO `Vanzator` (`IDUtilizator`,`NumeFirma` ,`Adresa`,`IBAN` ,`CUI`,`NrTelefon`) VALUES 
(2,'Test Firma','127.0.0.1','1234567890','123456','0000000000'),
(4,'Nocia SRL.','Bucuresti, Sector7, Orhideea Towers','RO0RN4123052230001234','612345','0700000000'),
(5,'Provider SRL.','Bucuresti, Sector7, Orhideea Towers','RO0RN4123293333001444','192506','0700000000');


INSERT INTO `Card` (`Propietar`, `Exp`, `Detalii`, `CCV`) VALUES
('Admin', '99-12', '0000000000000000','00000'),
('Banu Iliescu', '20-06', '1231231231231231','13731');

INSERT INTO `Persoana` (`IDUtilizator`,`IDCard`, `Nume`, `Prenume`, `CNP`, `Adresa`) VALUES
(1,1, 'Admin', 'A.', '0000000000000','127.0.0.1'),
(3,2, 'Banu', 'Iliescu', '1238539192423','Bucuresti, Sector 3, Bl 99, Sc 1, Ap 44');

INSERT INTO `Telefon` (`IDUtilizator`,`LocImagine`,`Nume`,`PretInitial`,`AnAparitie`,`Specificatii`,`DataLicitatie`,`Vandut`) VALUES
(4,'img\\tel-1.jpg','Nocia 2000',400,'2000','Telefon super rapid, 1 GB RAM, 2GB Memorie, touch-screen din viitor!','2020-11-1',False),
(5,'img\\tel-2.jpg','Samseng G7',500,'2004','Telefon mega rapid, 2 GB RAM, 2GB Memorie, touch-screen din viitor!','2020-10-12',True),
(5,'img\\tel-3.jpg','GL 22',550,'2012','Telefon ultra rapid, 4 GB RAM, 4GB Memorie, touch-screen din viitor!','2020-12-5',False),
(5,'img\\tel-4.jpg','ISO 802',600,'2013','Telefon giga rapid, 8 GB RAM, 4GB Memorie.','2021-1-20',False),
(5,'img\\tel-5.jpg','Sonic 6',2000,'2015','Telefon penta rapid, 16 GB RAM, 8GB Memorie.','2020-1-15',False);


INSERT INTO `Licitatie` (`IDUtilizator`,`IDTelefon`, `PretLicitat`, `DataLicitatie`) VALUES
(3,2,600,'2020-10-10');