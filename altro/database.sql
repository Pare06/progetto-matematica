DROP DATABASE IF EXISTS piattaforma_matematica;
CREATE DATABASE piattaforma_matematica;
USE piattaforma_matematica;

CREATE TABLE professori (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cognome VARCHAR(50) NOT NULL,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE classi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(10) NOT NULL,
    professore INT NOT NULL,
    FOREIGN KEY (professore) REFERENCES professori(id)
);

CREATE TABLE studenti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    classe INT,
    FOREIGN KEY (classe) REFERENCES classi(id)
        ON DELETE SET NULL
);

CREATE TABLE compiti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descrizione VARCHAR(500)
);

CREATE TABLE esercizi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titolo VARCHAR(100) NOT NULL,
    testo VARCHAR(1000) NOT NULL,
    risposta_aperta VARCHAR(1000),
    risposte_multiple VARCHAR(20),
    commento_errore VARCHAR(1000),
    -- controlla se uno è null e uno non null
    CHECK(risposta_aperta IS NULL XOR risposte_multiple IS NULL)
);

CREATE TABLE embed_esercizi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    esercizio INT NOT NULL,
    embed_link VARCHAR(100),
    FOREIGN KEY (esercizio) REFERENCES esercizi(id)
        ON DELETE CASCADE
);

CREATE TABLE suggerimenti_esercizi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    esercizio INT NOT NULL,
    suggerimento VARCHAR(1000),
    FOREIGN KEY (esercizio) REFERENCES esercizi(id)
        ON DELETE CASCADE
);

CREATE TABLE istanze_compiti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compito INT NOT NULL,
    classe INT NOT NULL,
    data_creazione DATETIME NOT NULL,
    data_consegna DATETIME NOT NULL,
    ritardo_ammesso BOOLEAN NOT NULL,
    FOREIGN KEY (compito) REFERENCES compiti(id)
        ON DELETE CASCADE,
    FOREIGN KEY (classe) REFERENCES classi(id)
        ON DELETE CASCADE
);

CREATE TABLE compiti_esercizi (
    compito INT NOT NULL,
    esercizio INT NOT NULL,
    FOREIGN KEY (compito) REFERENCES compiti(id)
        ON DELETE CASCADE,
    FOREIGN KEY (esercizio) REFERENCES esercizi(id)
        ON DELETE CASCADE
);

CREATE TABLE feedback (
    studente INT NOT NULL,
    compiti_classi INT NOT NULL,
    testo VARCHAR(1000) NOT NULL,
    mittente ENUM('p', 's') NOT NULL, -- professore / studente
    data_invio DATETIME NOT NULL,
    CHECK(LENGTH(testo) > 0),
    FOREIGN KEY (studente) REFERENCES studenti(id)
        ON DELETE CASCADE,
    FOREIGN KEY (compiti_classi) REFERENCES istanze_compiti(id)
        ON DELETE CASCADE
);

CREATE TABLE consegne (
    id INT PRIMARY KEY AUTO_INCREMENT,
    studente INT NOT NULL,
    compito_ist INT NOT NULL,
    finito BOOLEAN NOT NULL,
    data_consegna DATETIME,
    UNIQUE(studente, compito_ist),
    FOREIGN KEY (studente) REFERENCES studenti(id)
        ON DELETE CASCADE,
    FOREIGN KEY (compito_ist) REFERENCES istanze_compiti(id)
        ON DELETE CASCADE
);

CREATE TABLE risposte (
    consegna INT NOT NULL,
    esercizio INT NOT NULL,
    PRIMARY KEY(consegna, esercizio),
    risposta_aperta VARCHAR(1000),
    risposte_multiple VARCHAR(20),
    CHECK(risposta_aperta IS NULL XOR risposte_multiple IS NULL),
    FOREIGN KEY (consegna) REFERENCES consegne(id)
        ON DELETE CASCADE,
    FOREIGN KEY (esercizio) REFERENCES esercizi(id)
        ON DELETE CASCADE 
);

-- funzioni: https://www.geeksforgeeks.org/mysql-creating-stored-function/
-- (100% facciamo delle query disumane sennò)

DELIMITER $$

-- TUTTE DA TESTARE

CREATE FUNCTION NUM_ESERCIZI(compito_id INT)
RETURNS INT DETERMINISTIC
BEGIN
    DECLARE num_rows INT;

    SELECT COUNT(*) INTO num_rows FROM compiti_esercizi
    WHERE compito = compito_id;

    RETURN num_rows;
END $$

CREATE FUNCTION DA_CONSEGNARE_OGGI(compito_istanza INT)
RETURNS BOOLEAN NOT DETERMINISTIC
BEGIN
    DECLARE consegna DATETIME;
    DECLARE adesso DATETIME;

    SET adesso = CURRENT_TIMESTAMP();
    SELECT data_consegna INTO consegna FROM istanze_compiti
    WHERE id = compito_istanza;

    IF consegna > adesso AND DATE(consegna) = DATE(adesso) THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END $$

CREATE FUNCTION SCADUTO(compito_istanza INT, studente_id INT)
RETURNS BOOLEAN NOT DETERMINISTIC
BEGIN
    DECLARE consegna DATETIME;
    DECLARE consegnato BOOLEAN;

    SELECT data_consegna, finito INTO consegna, consegnato
    FROM istanze_compiti
    JOIN consegne ON istanze_compiti.id = consegne.compito_ist
    WHERE istanze_compiti.id = compito_istanza AND consegne.studente = studente_id;

    RETURN consegnato = TRUE AND consegna < CURRENT_TIMESTAMP();
END $$

CREATE FUNCTION TUTTI_CONSEGNATI(compito_istanza INT)
RETURNS BOOLEAN NOT DETERMINISTIC
BEGIN
    DECLARE classeId INT;
    DECLARE classeCount INT;
    DECLARE consegnati INT;

    SELECT classe INTO classeId FROM istanze_compiti WHERE id = compito_istanza;
    SELECT COUNT(*) INTO classeCount FROM studenti WHERE classe = classeId;
    SELECT COUNT(*) INTO consegnati FROM consegne 
    WHERE finito = TRUE AND compito_ist = compito_istanza AND SCADUTO(compito_istanza, studente);

    RETURN classeCount = consegnati;
END $$

-- fine funzioni
DELIMITER ;