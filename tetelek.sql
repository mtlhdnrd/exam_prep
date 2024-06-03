CREATE DATABASE IF NOT EXISTS tetelek CHARACTER SET utf8 COLLATE utf8_hungarian_ci;
USE tetelek;
CREATE TABLE IF NOT EXISTS tantargyak(
    id int PRIMARY KEY AUTO_INCREMENT,
    tantargy varchar(225)
);
USE tetelek;
CREATE TABLE IF NOT EXISTS tetelcimek (
    id int AUTO_INCREMENT,
    cim varchar(225),
    vazlat varchar(2550),
    kidolgozas varchar(4000),
    modosit DATE,
    tantargyid int,
    PRIMARY KEY (id),
    FOREIGN KEY (tantargyid) REFERENCES tantargyak(id)
);
INSERT INTO tantargyak (tantargy) VALUES ("Történelem"),("Irodalom"),("Nyelvtan");
INSERT INTO tetelcimek (cim, vazlat, kidolgozas, modosit, tantargyid) VALUES ("Felis Catus","masneven kozonseges hazimacska","avagy az elet egyik ertelme",'2000-02-02',2);
