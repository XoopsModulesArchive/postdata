CREATE TABLE postalcode (
    seq    INT(7)       NOT NULL AUTO_INCREMENT,
    postal VARCHAR(7)   NOT NULL,
    pref   VARCHAR(8)   NOT NULL,
    city   VARCHAR(128) NOT NULL DEFAULT '',
    town   VARCHAR(128) NOT NULL DEFAULT '',
    PRIMARY KEY (seq),
    KEY pref (pref),
    KEY postal (postal)
)
    ENGINE = ISAM;
