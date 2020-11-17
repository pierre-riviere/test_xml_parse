    CREATE TABLE source (
        id int NOT NULL auto_increment,
        name varchar(255),
        PRIMARY KEY(id)
    );
    CREATE TABLE article (
        id int NOT NULL auto_increment,
        source_id int NOT NULL,
        name varchar(255),
        content BLOB,
        PRIMARY KEY(id)
    );
    INSERT INTO source VALUES (1, 'src-1');
    INSERT INTO source VALUES (2, 'src-2');
    INSERT INTO article VALUES (1, 1, 'Article 1', 'Lorem ipsum dolor sit amet 1');
    INSERT INTO article VALUES (2, 2, 'Article 2', 'Lorem ipsum dolor sit amet 2');
    INSERT INTO article VALUES (3, 2, 'Article 3', 'Lorem ipsum dolor sit amet 3');
    INSERT INTO article VALUES (4, 1, 'Article 4', 'Lorem ipsum dolor sit amet 4');