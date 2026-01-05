-- Database Section
-- ________________ 

create database if not exists unibostu;
use unibostu;


-- Tables Section
-- _____________ 

create table amministratori (
     idamministratore varchar(60) not null,
     password varchar(255) not null,
     constraint IDamministratori primary key (idamministratore)
);

create table categorie (
     idcategoria int not null auto_increment,
     nome_categoria varchar(60) not null,
     constraint IDcategorie primary key (idcategoria),
     unique (nome_categoria)
);

create table commenti (
     idcommento int not null auto_increment,
     idpost int not null,
     testo text not null,
     data_creazione date not null,
     cancellato boolean not null default false,
     idutente varchar(60) not null,
     idcommento_genitore int,
     constraint IDcommenti primary key (idcommento)
);

create table corsi (
     idcorso int not null auto_increment,
     nome_corso varchar(60) not null,
     idfacolta int not null,
     constraint IDcorso primary key (idcorso));

create table facolta (
     idfacolta int not null auto_increment,
     nome_facolta varchar(60) not null,
     constraint IDfacolta primary key (idfacolta));

create table post_tags (
     idpost int not null,
     idcorso int not null,
     idtag int not null,
     constraint IDpost_tags primary key (idpost, idcorso, idtag));

create table posts (
     idpost int not null auto_increment,
     titolo varchar(100) not null,
     descrizione text not null,
     percorso_allegato varchar(255) default null,
     data_creazione timestamp not null,
     idutente varchar(60) not null,
     idcorso int not null,
     idcategoria int,
     constraint IDposts primary key (idpost)
);

create table tags (
     idtag int not null auto_increment,
     tipo varchar(30) not null,
     idcorso int not null,
     constraint IDtags primary key (idtag, idcorso),
     constraint UDX_tipo_corso unique (tipo, idcorso)
);

create table utenti (
     idutente varchar(60) not null,
     password varchar(255) not null,
     nome varchar(30) not null,
     cognome varchar(30) not null,
     idfacolta int,
     utente_sospeso boolean not null default false,
     constraint IDutenti primary key (idutente)
);

create table likes (
     idpost int not null,
     idutente varchar(60) not null,
     is_like boolean not null,
     constraint IDlikes primary key (idpost, idutente)
);

create table utenti_corsi (
     idutente varchar(60) not null,
     idcorso int not null,
     constraint IDutenti_corsi primary key (idutente, idcorso)
);

-- Constraints Section
-- ___________________ 

alter table utenti_corsi add constraint FKutente_corso_corso
     foreign key (idcorso)
     references corsi (idcorso)
     on delete cascade;

alter table utenti_corsi add constraint FKutente_corso_utente
     foreign key (idutente)
     references utenti (idutente)
     on delete cascade;

alter table commenti add constraint FKscrittura
     foreign key (idutente)
     references utenti (idutente)
     on delete cascade;

alter table commenti add constraint FKcommenti_posts
     foreign key (idpost)
     references posts (idpost)
     on delete cascade;

alter table commenti add constraint FKrisposte
     foreign key (idcommento_genitore)
     references commenti (idcommento)
     on delete cascade;

alter table corsi add constraint FKcomposizione
     foreign key (idfacolta)
     references facolta (idfacolta)
     on delete cascade;

alter table post_tags add constraint FKpos_tag
     foreign key (idtag, idcorso)
     references tags (idtag, idcorso)
     on delete cascade;

alter table post_tags add constraint FKpos_pos
     foreign key (idpost)
     references posts (idpost)
     on delete cascade;

alter table posts add constraint FKcreazione
     foreign key (idutente)
     references utenti (idutente)
     on delete cascade;

alter table posts add constraint FKcorso_posts
     foreign key (idcorso)
     references corsi (idcorso)
     on delete cascade;

alter table tags add constraint FKcorsi_tags
     foreign key (idcorso)
     references corsi (idcorso)
     on delete cascade;

alter table utenti add constraint FKappartenenza
     foreign key (idfacolta)
     references facolta (idfacolta)
     on delete set null;

alter table likes add constraint FKlike_utente
     foreign key (idutente)
     references utenti (idutente)
     on delete cascade;

alter table likes add constraint FKlike_post
     foreign key (idpost)
     references posts (idpost)
     on delete cascade;

-- Index Section
-- _____________

create index IDX_likes_post on likes (idpost);
create index IDX_posts_corso on posts (idcorso);
create index IDX_commenti_post on commenti (idpost);
create index IDX_post_utente on posts (idutente);
