-- Database Section
-- ________________ 

create database if not exists unibostu;
use unibostu;


-- Tables Section
-- _____________ 

create table amministratori (
     idamministratore varchar(60) not null,
     identita int not null,
     password varchar(255) not null,
     constraint IDamministratori primary key (idamministratore),
     constraint FKR_ID unique (identita));

create table castegorie_posts (
     idpost int not null,
     idcategoria varchar(30) not null,
     constraint IDcastegorie_post primary key (idcategoria, idpost));

create table categorie (
     idcategoria varchar(30) not null,
     riservata char not null,
     constraint IDcategorie primary key (idcategoria));

create table commenti (
     idpost int not null,
     idcommento int not null,
     testo text not null,
     data_creazione date not null,
     cancellato boolean not null default false,
     identita int not null,
     idpost_genitore int not null,
     idcommento_genitore int not null,
     constraint IDcommenti primary key (idpost, idcommento));

create table corsi (
     idcorso int not null auto_increment,
     nome_corso varchar(60) not null,
     idfacolta int not null,
     constraint IDcorso primary key (idcorso));

create table entita (
     identita int not null auto_increment,
     constraint IDcf primary key (identita));

create table facolta (
     idfacolta int not null auto_increment,
     nome_facolta varchar(60) not null,
     constraint IDcorsi_di_laurea primary key (idfacolta));

create table post_tags (
     idpost int not null,
     idcorso int not null,
     tipo varchar(30) not null,
     constraint IDpost_tags primary key (idpost, idcorso, tipo));

create table posts (
     idpost int not null auto_increment,
     titolo varchar(100) not null,
     descrizione text not null,
     percorso_allegato varchar(255) null,
     likes int not null default 0,
     dislikes int not null default 0,
     data_creazione date not null,
     identita int not null,
     idcorso int,
     constraint IDposts primary key (idpost));

create table tags (
     tipo varchar(30) not null,
     idcorso int not null,
     constraint IDtags primary key (idcorso, tipo));

create table utenti (
     idutente varchar(60) not null,
     identita int not null,
     password varchar(255) not null,
     nome varchar(30) not null,
     cognome varchar(30) not null,
     idfacolta int,
     utente_sospeso boolean not null default false,
     constraint IDutenti primary key (idutente),
     constraint FKR_1_ID unique (identita));

create table visione_posts (
     idpost int not null,
     idfacolta int not null,
     constraint IDvisione_post primary key (idfacolta, idpost));


-- Constraints Section
-- ___________________ 

alter table amministratori add constraint FKR_FK
     foreign key (identita)
     references entita (identita);

alter table castegorie_posts add constraint FKcas_cat
     foreign key (idcategoria)
     references categorie (idcategoria);

alter table castegorie_posts add constraint FKcas_pos
     foreign key (idpost)
     references posts (idpost);

alter table commenti add constraint FKscrittura
     foreign key (identita)
     references entita (identita);

alter table commenti add constraint FKcommenti_posts
     foreign key (idpost)
     references posts (idpost);

alter table commenti add constraint FKrisposte
     foreign key (idpost_genitore, idcommento_genitore)
     references commenti (idpost, idcommento);

alter table corsi add constraint FKcomposizione
     foreign key (idfacolta)
     references facolta (idfacolta);

alter table post_tags add constraint FKpos_tag
     foreign key (idcorso, tipo)
     references tags (idcorso, tipo);

alter table post_tags add constraint FKpos_pos
     foreign key (idpost)
     references posts (idpost);

alter table posts add constraint FKcreazione
     foreign key (identita)
     references entita (identita);

alter table posts add constraint FKcorso_posts
     foreign key (idcorso)
     references corsi (idcorso);

alter table tags add constraint FKcorsi_tags
     foreign key (idcorso)
     references corsi (idcorso);

alter table utenti add constraint FKappartenenza
     foreign key (idfacolta)
     references facolta (idfacolta);

alter table utenti add constraint FKR_1_FK
     foreign key (identita)
     references entita (identita);

alter table visione_posts add constraint FKvis_fac
     foreign key (idfacolta)
     references facolta (idfacolta);

alter table visione_posts add constraint FKvis_pos
     foreign key (idpost)
     references posts (idpost);


-- Index Section
-- _____________