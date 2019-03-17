DROP SCHEMA IF EXISTS `venn_2`;
CREATE SCHEMA IF NOT EXISTS `venn_2` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;


CREATE USER IF NOT EXISTS 'venn_adm_2'@'localhost' IDENTIFIED BY 'Venn_2_adm';
GRANT ALTER, DELETE, DROP, INDEX, INSERT, SELECT, UPDATE ON venn_2.* TO 'venn_adm_2'@'localhost';

CREATE USER IF NOT EXISTS 'venn_user_2'@'localhost' IDENTIFIED BY 'Uservenn@2';
GRANT SELECT, INSERT, UPDATE, DELETE ON venn_2.* TO 'venn_user_2'@'localhost';


USE `venn_2`;

DROP TABLE IF EXISTS `bruker`;

CREATE TABLE IF NOT EXISTS `bruker` (
id_bruker INT AUTO_INCREMENT,
brukernavn VARCHAR(45) NOT NULL UNIQUE,
passord VARCHAR(40) NOT NULL,
epost VARCHAR(45) NOT NULL,
profil_aktiv VARCHAR(1) DEFAULT 1,
profil_synlig VARCHAR(1) DEFAULT 0,
profil_admin VARCHAR(1) DEFAULT 0,
PRIMARY KEY (`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `loginn_feil`;

CREATE TABLE IF NOT EXISTS `loginn_feil` (
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL,
feil_logginn_teller INT,
feil_logginn_siste DATETIME,
PRIMARY KEY (id),
FOREIGN KEY (`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `alle_interesser`;

CREATE TABLE IF NOT EXISTS `alle_interesser` (
id INT AUTO_INCREMENT,
interesse VARCHAR(50) UNIQUE,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bruker_interesser`;

CREATE TABLE IF NOT EXISTS `bruker_interesser` (
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL,
interesse VARCHAR(50) NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `alle_studium`;

CREATE TABLE IF NOT EXISTS `alle_studium` (
id INT AUTO_INCREMENT,
studium VARCHAR(50) UNIQUE,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `bruker_studium`;

CREATE TABLE IF NOT EXISTS `bruker_studium` (
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL UNIQUE,
studium VARCHAR(50) NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bruker_beskrivelse`;

CREATE TABLE IF NOT EXISTS `bruker_beskrivelse` (
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL,
beskrivelse TEXT,
PRIMARY KEY(`id`),
FOREIGN KEY(`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `bruker_bilde`;

CREATE TABLE IF NOT EXISTS `bruker_bilde`(
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL,
bilde VARCHAR(50) NOT NULL,
PRIMARY KEY(`id`),
FOREIGN KEY(`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `chat`;
CREATE TABLE IF NOT EXISTS `chat` (
chat_id INT AUTO_INCREMENT,
brukernavn_fra VARCHAR(50),
brukernavn_til VARCHAR(50),
PRIMARY KEY(`chat_id`),
UNIQUE KEY(`brukernavn_fra`, `brukernavn_til`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
CREATE TRIGGER chat_trigger BEFORE INSERT ON chat FOR EACH ROW 
BEGIN 
    DECLARE funnet,brukernavn_fra2,brukernavn_til2,dummy VARCHAR(50);
    SET brukernavn_fra2 = NEW.brukernavn_fra;
    SET brukernavn_til2 = NEW.brukernavn_til;
    SELECT COUNT(1) INTO funnet FROM chat
    WHERE brukernavn_fra = brukernavn_til2 AND brukernavn_til = brukernavn_fra2;
    IF funnet = 1 THEN
        SELECT 1 INTO dummy FROM information_schema.tables;
    END IF;
END; $$ 
DELIMITER ;


DROP TABLE IF EXISTS `chat_meldinger`;
CREATE TABLE IF NOT EXISTS `chat_meldinger` (
meldings_id INT AUTO_INCREMENT,
chat_id INT NOT NULL,
melding TEXT,
brukernavn VARCHAR(45) NOT NULL,
tidspunkt_sendt TIMESTAMP,
PRIMARY KEY(`meldings_id`),
FOREIGN KEY(`chat_id`) REFERENCES chat(`chat_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS arrangement;

CREATE TABLE IF NOT EXISTS arrangement(
id INT AUTO_INCREMENT,
id_bruker INT NOT NULL,
arrangement_navn VARCHAR(50),
arrangement_beskrivelse TEXT,
fra_dato DATETIME,
til_dato DATETIME,
aktiv BOOLEAN default true,
PRIMARY KEY(id),
FOREIGN KEY(`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS arrangement_paameldte;

CREATE TABLE IF NOT EXISTS arrangement_paameldte(
id INT NOT NULL,
id_bruker INT NOT NULL,
PRIMARY KEY(id, id_bruker),
FOREIGN KEY(`id`) REFERENCES arrangement(`id`),
FOREIGN KEY(`id_bruker`) REFERENCES bruker(`id_bruker`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS rapport;

CREATE TABLE IF NOT EXISTS rapport(
id INT AUTO_INCREMENT,
brukernavn VARCHAR(45) NOT NULL,
begrunnelse TEXT,
dato DATETIME,
PRIMARY KEY(id),
FOREIGN KEY(`brukernavn`) REFERENCES bruker(`brukernavn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS regler;

CREATE TABLE IF NOT EXISTS regler (
id INT AUTO_INCREMENT,
regel TEXT NOT NULL,
PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS bruker_advarsel;

CREATE TABLE IF NOT EXISTS bruker_advarsel(
id INT AUTO_INCREMENT,
brukernavn VARCHAR(45) NOT NULL,
`admin` VARCHAR(45) NOT NULL,
begrunnelse TEXT,
dato DATETIME,
til_dato DATETIME,
PRIMARY KEY(id),
FOREIGN KEY(`brukernavn`) REFERENCES bruker(`brukernavn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS bruker_karantene;

CREATE TABLE IF NOT EXISTS bruker_karantene(
id INT AUTO_INCREMENT,
brukernavn VARCHAR(45) NOT NULL,
`admin` VARCHAR(45) NOT NULL,
begrunnelse TEXT,
fra_dato_ban DATETIME,
til_dato_ban DATETIME,
PRIMARY KEY(id),
FOREIGN KEY(`brukernavn`) REFERENCES bruker(`brukernavn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_SAFE_UPDATES = 0;