-- Database Section
-- ________________ 

create database if not exists unibostu;
use unibostu;


-- Tables Section
-- _____________ 

create table administrators (
     admin_id varchar(60) not null,
     password varchar(255) not null,
     constraint ID_administrators primary key (admin_id)
);

create table categories (
     category_id int not null auto_increment,
     category_name varchar(60) not null,
     constraint ID_categories primary key (category_id),
     unique (category_name)
);

create table comments (
     comment_id int not null auto_increment,
     post_id int not null,
     text text not null,
     created_at date not null,
     deleted boolean not null default false,
     user_id varchar(60) not null,
     parent_comment_id int,
     constraint ID_comments primary key (comment_id)
);

create table courses (
     course_id int not null auto_increment,
     course_name varchar(60) not null,
     faculty_id int not null,
     constraint ID_courses primary key (course_id));

create table faculties (
     faculty_id int not null auto_increment,
     faculty_name varchar(60) not null,
     constraint ID_faculties primary key (faculty_id));

create table post_tags (
     post_id int not null,
     course_id int not null,
     tag_id int not null,
     constraint ID_post_tags primary key (post_id, course_id, tag_id));

create table posts (
     post_id int not null auto_increment,
     title varchar(100) not null,
     description text not null,
     attachment_path varchar(255) default null,
     created_at timestamp not null,
     user_id varchar(60) not null,
     course_id int not null,
     category_id int,
     constraint ID_posts primary key (post_id)
);

create table tags (
     tag_id int not null auto_increment,
     tag_name varchar(30) not null,
     course_id int not null,
     constraint ID_tags primary key (tag_id),
     constraint UDX_tag_name_course unique (tag_name, course_id)
);

create table users (
     user_id varchar(60) not null,
     password varchar(255) not null,
     first_name varchar(30) not null,
     last_name varchar(30) not null,
     faculty_id int,
     suspended boolean not null default false,
     constraint ID_users primary key (user_id)
);

create table likes (
     post_id int not null,
     user_id varchar(60) not null,
     is_like boolean not null,
     constraint ID_likes primary key (post_id, user_id)
);

create table user_courses (
     user_id varchar(60) not null,
     course_id int not null,
     constraint ID_user_courses primary key (user_id, course_id)
);

-- Constraints Section
-- ___________________ 

alter table user_courses add constraint FK_user_course_course
     foreign key (course_id)
     references courses (course_id)
     on delete cascade;

alter table user_courses add constraint FK_user_course_user
     foreign key (user_id)
     references users (user_id)
     on delete cascade;

alter table comments add constraint FK_comment_author
     foreign key (user_id)
     references users (user_id)
     on delete cascade;

alter table comments add constraint FK_comment_post
     foreign key (post_id)
     references posts (post_id)
     on delete cascade;

alter table comments add constraint FK_comment_parent
     foreign key (parent_comment_id)
     references comments (comment_id)
     on delete cascade;

alter table courses add constraint FK_course_faculty
     foreign key (faculty_id)
     references faculties (faculty_id)
     on delete cascade;

alter table post_tags add constraint FK_post_tag_tag
     foreign key (tag_id)
     references tags (tag_id)
     on delete cascade;

alter table post_tags add constraint FK_post_tag_post
     foreign key (post_id)
     references posts (post_id)
     on delete cascade;

alter table posts add constraint FK_post_author
     foreign key (user_id)
     references users (user_id)
     on delete cascade;

alter table posts add constraint FK_post_course
     foreign key (course_id)
     references courses (course_id)
     on delete cascade;

alter table posts add constraint FK_post_category
        foreign key (category_id)
        references categories (category_id)
        on delete set null;

alter table tags add constraint FK_tag_course
     foreign key (course_id)
     references courses (course_id)
     on delete cascade;

alter table users add constraint FK_user_faculty
     foreign key (faculty_id)
     references faculties (faculty_id)
     on delete set null;

alter table likes add constraint FK_like_user
     foreign key (user_id)
     references users (user_id)
     on delete cascade;

alter table likes add constraint FK_like_post
     foreign key (post_id)
     references posts (post_id)
     on delete cascade;

-- Index Section
-- _____________

create index IDX_likes_post on likes (post_id);
create index IDX_posts_course on posts (course_id);
create index IDX_comments_post on comments (post_id);
create index IDX_posts_user on posts (user_id);


-- Mock Data Section

INSERT INTO `faculties` (`faculty_id`, `faculty_name`) VALUES (1, 'Matematica'), (2, 'Informatica'), (3, 'Fisica');

INSERT INTO `courses` (`course_id`, `course_name`, `faculty_id`) VALUES 
(1, 'Algebra', 1), 
(2, 'Analisi 1', 1), 
(3, 'Programmazione', 2), 
(4, 'Algoritmi e Strutture Dati', 2), 
(5, 'Meccanica', 3), 
(6, 'Chimica', 3);

INSERT INTO `categories` (`category_id`, `category_name`) VALUES 
(1, 'Domande'),
(2, 'Appunti'),
(3, 'Notizie'),
(4, 'Informazioni Esami');

INSERT INTO `tags` (`tag_id`, `tag_name`, `course_id`) VALUES 
(1, 'Matrici', 1),
(2, 'Spazi vettoriali', 1),
(3, 'Applicazioni lineari', 1),
(4, "Esercizi d'esame", 1),
(5, 'Limiti', 2),
(6, 'Derivate', 2),
(7, 'Integrali', 2),
(8, "Esercizi d'esame", 2),
(9, 'Sintassi', 3),
(10, 'Grafi', 4),
(11, 'Algoritmi di ordinamento', 4),
(12, "Esercizi d'esame", 4),
(13, 'Cinematica', 5),
(14, 'Dinamica', 5),
(15, 'Reazioni', 6),
(16, 'Composti organici', 6),
(17, 'Laboratorio', 6);

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `faculty_id`, `password`) VALUES
('mrossi', 'Mario', 'Rossi', 1, ''),
('l.bianchi', 'Luca', 'Bianchi', 1, ''),
('giulia_verdi', 'Giulia', 'Verdi', 1, ''),
('andrea.ferrari', 'Andrea', 'Ferrari', 2, ''),
('francesca.gialli', 'Francesca', 'Gialli', 2, ''),
('simone.neri', 'Simone', 'Neri', 2, ''),
('alice.testa', 'Alice', 'Testa', 3, ''),
('matteo.longo', 'Matteo', 'Longo', 3, ''),
('laura.monti', 'Laura', 'Monti', 3, '');

INSERT INTO `user_courses` (`user_id`, `course_id`) VALUES 
('mrossi', 1),
('mrossi', 2),
('l.bianchi', 1),
('giulia_verdi', 2),
('andrea.ferrari', 3),
('andrea.ferrari', 4),
('francesca.gialli', 3),
('simone.neri', 4),
('alice.testa', 5),
('matteo.longo', 5),
('laura.monti', 6);

INSERT INTO `posts` (`post_id`, `title`, `description`, `created_at`, `user_id`, `course_id`, `category_id`) VALUES 
(1, 'Domanda su Matrici', 'Come si calcola il determinante di una matrice 3x3?', '2024-01-15 10:00:00', 'mrossi', 1, 1),
(2, 'Appunti Analisi 1', 'Ecco gli appunti del capitolo sui limiti.', '2024-01-16 12:30:00', 'l.bianchi', 2, 2),
(3, 'Notizie sul Corso di Programmazione', 'Il professore ha annunciato un esame a sorpresa.', '2024-01-17 09:15:00', 'andrea.ferrari', 3, 3),
(4, 'Informazioni Esami di Meccanica', "L'esame si terrà il 20 Febbraio.", '2024-01-18 14:45:00', 'alice.testa', 5, 4),
(5, 'Esercizi su Derivate', 'Qualcuno ha esercizi extra sulle derivate?', '2024-01-19 11:20:00', 'giulia_verdi', 2, 1),
(6, 'Appunti su Algoritmi di Ordinamento', 'Condivido i miei appunti sugli algoritmi di ordinamento.', '2024-01-20 16:00:00', 'simone.neri', 4, 2),
(7, 'Domanda su Reazioni Chimiche', 'Come bilanciare una reazione redox?', '2024-01-21 13:10:00', 'laura.monti', 6, 1),
(8, 'Notizie sul Laboratorio di Chimica', 'Il laboratorio di questa settimana è cancellato.', '2024-01-22 15:30:00', 'matteo.longo', 6, 3),
(9, 'Informazioni Esami di Analisi 1', "L'esame coprirà i primi tre capitoli.", '2024-01-23 10:50:00', 'mrossi', 2, 4),
(10, 'Appunti su Spazi Vettoriali', 'Ecco gli appunti sugli spazi vettoriali.', '2024-01-24 09:40:00', 'l.bianchi', 1, 2);

INSERT INTO `comments` (`comment_id`, `post_id`, `text`, `created_at`, `user_id`, `parent_comment_id`) VALUES 
(1, 1, 'Puoi usare la regola di Sarrus per calcolare il determinante.', '2024-01-15', 'giulia_verdi', NULL),
(2, 1, 'Grazie per il consiglio!', '2024-01-15', 'mrossi', 1),
(3, 2, 'Ottimi appunti, grazie per condividerli!', '2024-01-16', 'andrea.ferrari', NULL),
(4, 3, "Spero di essere preparato per l'esame a sorpresa.", '2024-01-17', 'francesca.gialli', NULL),
(5, 5, 'Ho alcuni esercizi extra che posso condividere.', '2024-01-19', 'l.bianchi', NULL),
(6, 5, 'Sarebbe fantastico, grazie!', '2024-01-19', 'giulia_verdi', 5),
(7, 7, 'Puoi bilanciare le reazioni redox usando il metodo delle semireazioni.', '2024-01-21', 'matteo.longo', NULL),
(8, 9, "Grazie per l'informazione sull'esame!", '2024-01-23', 'l.bianchi', NULL);

INSERT INTO `likes` (`post_id`, `user_id`, `is_like`) VALUES 
(1, 'giulia_verdi', true),
(1, 'l.bianchi', true),
(2, 'andrea.ferrari', true),
(3, 'francesca.gialli', false),
(5, 'l.bianchi', true),
(6, 'simone.neri', true),
(7, 'laura.monti', true),
(8, 'matteo.longo', false),
(9, 'mrossi', true),
(10, 'l.bianchi', true);

INSERT INTO `post_tags` (`post_id`, `course_id`, `tag_id`) VALUES 
(1, 1, 1),
(2, 2, 5),
(3, 3, 9),
(4, 5, 13),
(5, 2, 6),
(6, 4, 11),
(7, 6, 15),
(8, 6, 17),
(9, 2, 8),
(10, 1, 2);


