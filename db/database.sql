SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- docker compose down -v 
-- comando per eliminare i volumi e eseguire sta roba al prox compose up
--
-- documentazione mysql per blob/text (mi parevano piu adatti di varchar)
-- https://dev.mysql.com/doc/refman/9.5/en/blob.html

-- Tables Section
-- _____________ 

create table facolta (
     idfacolta int not null auto_increment,
     nome_facolta varchar(60) not null,
     constraint IDcorsi_di_laurea primary key (idfacolta));

create table utenti (
     username varchar(60) not null,
     password varchar(255) not null,
     nome varchar(30) not null,
     cognome varchar(30) not null,
     idfacolta int not null,
     constraint IDutenti primary key (username));

create table amministratori (
     username varchar(30) not null,
     password varchar(255) not null,
     constraint IDamministratori primary key (username));

create table corsi (
     idcorso int not null auto_increment,
     nome_corso varchar(60) not null,
     idfacolta int,
     constraint IDcorso primary key (idcorso));

insert into corsi (idcorso, nome_corso) values (0, "tag globali");

create table posts (
     idpost int not null auto_increment,
     titolo varchar(100) not null,
     descrizione text not null,
     allegato blob null,
     likes int not null default 0,
     dislikes int not null default 0,
     idutente varchar(60) not null,
     constraint IDposts primary key (idpost));

create table tags (
     tipo varchar(30) not null,     -- Tipo del tag (prima parte della PK)
     idcorso int not null,          -- ID del corso associato (seconda parte della PK)
     constraint IDtags primary key (tipo, idcorso));

create table tags_post (
     tipo varchar(30) not null,     -- Parte della FK a tags.tipo
     idcorso int not null,          -- Parte della FK a tags.idcorso  
     idpost int not null,           -- FK a posts.idpost
     constraint IDtags_post primary key (tipo, idcorso, idpost));

create table commenti (
     idcommento int not null auto_increment,
     idpost int not null,
     testo text not null,
     idutente varchar(60) not null,
     commento_genitore int null,
     constraint IDcommenti primary key (idcommento));


-- Constraints Section
-- ___________________ 

alter table commenti add constraint FKscrittura
     foreign key (idutente)
     references utenti (username);

alter table commenti add constraint FKcommenti_posts
     foreign key (idpost)
     references posts (idpost);

alter table commenti add constraint FKcommenti_genitore
     foreign key (commento_genitore)
     references commenti (idcommento);

alter table corsi add constraint FKcomposizione
     foreign key (idfacolta)
     references facolta (idfacolta);

alter table posts add constraint FKcreazione
     foreign key (idutente)
     references utenti (username);

alter table tags add constraint FKcorsi_tags
     foreign key (idcorso)
     references corsi (idcorso);

alter table tags_post add constraint FKtag_pos_1
     foreign key (idpost)
     references posts (idpost);

alter table tags_post add constraint FKtag_tag
     foreign key (tipo, idcorso)
     references tags (tipo, idcorso);

alter table utenti add constraint FKappartenenza
     foreign key (idfacolta)
     references facolta (idfacolta);

COMMIT;