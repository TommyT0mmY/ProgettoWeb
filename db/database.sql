-- Database Section
-- ________________ 

SET NAMES utf8mb4;

-- CREA DATABASE
CREATE DATABASE IF NOT EXISTS `unibostu` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_bin;
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
     comment_text text not null,
     created_at timestamp not null,
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

-- Post Attachments table for multiple file uploads
create table post_attachments (
     attachment_id int not null auto_increment,
     post_id int not null,
     file_name varchar(255) not null,
     original_name varchar(255) not null,
     mime_type varchar(100) not null,
     file_size int not null,
     created_at timestamp not null default current_timestamp,
     constraint ID_post_attachments primary key (attachment_id)
);

alter table post_attachments add constraint FK_attachment_post
     foreign key (post_id)
     references posts (post_id)
     on delete cascade;

-- Index Section
-- _____________

create index IDX_likes_post on likes (post_id);
create index IDX_posts_course on posts (course_id);
create index IDX_comments_post on comments (post_id);
create index IDX_posts_user on posts (user_id);
create index IDX_attachments_post on post_attachments (post_id);


















































-- ========================================
-- =                                      =
-- =         Mock Data Section            =
-- =                                      =
-- ========================================

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
('laura.monti', 'Laura', 'Monti', 3, ''),
-- password: 'aaaaaa'
('a', 'a', 'a', 1, '$2y$12$IopdjTqt.gNIuYN7FDXZJOZo.02BGca/Jw0uf0v0xhsN89gf9HoCW');

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
('laura.monti', 6),
('a', 1),
('a', 2),
('a', 3),
('a', 4),
('a', 5),
('a', 6);

INSERT INTO `posts` (`post_id`, `title`, `description`, `created_at`, `user_id`, `course_id`, `category_id`) VALUES 
-- Algebra (Corso 1)
(1, 'Domanda su Matrici', 'Come si calcola il determinante di una matrice 3x3? Ho provato con la regola di Sarrus ma non riesco a ottenere il risultato giusto.', '2024-01-15 10:00:00', 'mrossi', 1, 1),
(2, 'Appunti su Spazi Vettoriali', 'Ecco gli appunti sugli spazi vettoriali e le loro proprietà.', '2024-01-24 09:40:00', 'l.bianchi', 1, 2),
(3, 'Discussione: Rango di una Matrice', 'Qualcuno sa spiegare bene il concetto di rango? Non lo trovo molto chiaro dalle lezioni.', '2024-01-26 14:30:00', 'giulia_verdi', 1, 1),
(4, 'Esercizi d\'esame - Algebra', 'Ho trovato degli esercizi degli anni passati, chi me li spiega?', '2024-01-27 15:45:00', 'a', 1, 1),

-- Analisi 1 (Corso 2)
(5, 'Appunti Analisi 1 - Capitolo su Limiti', 'Ecco gli appunti del capitolo sui limiti, con esempi pratici.', '2024-01-16 12:30:00', 'l.bianchi', 2, 2),
(6, 'Esercizi su Derivate', 'Qualcuno ha esercizi extra sulle derivate? Mi serve un po\' di pratica in più.', '2024-01-19 11:20:00', 'giulia_verdi', 2, 1),
(7, 'Domanda su Integrali', 'Qual è il metodo migliore per calcolare integrali impropri? Mi confondo tra i vari casi.', '2024-01-25 14:25:00', 'a', 2, 1),
(8, 'Informazioni Esami di Analisi 1', "L'esame coprirà i primi tre capitoli, mi ha comunicato il professore.", '2024-01-23 10:50:00', 'mrossi', 2, 4),
(9, 'Dubbio su Continuità', 'Non capisco la differenza tra continuità e derivabilità. Qualcuno riesce a spiegarmelo semplicemente?', '2024-02-01 11:00:00', 'l.bianchi', 2, 1),

-- Programmazione (Corso 3)
(10, 'Notizie sul Corso di Programmazione', 'Il professore ha annunciato che farà una revisione sui fondamenti della sintassi.', '2024-01-17 09:15:00', 'andrea.ferrari', 3, 3),
(11, 'Aiuto con i puntatori in C', 'Sto avendo difficoltà con i puntatori in C, qualcuno potrebbe darmi una mano?', '2024-01-28 10:20:00', 'francesca.gialli', 3, 1),
(12, 'Condivisione progetto di esempio', 'Ho creato un programma di gestione liste che potrebbe essere utile per esercitarsi.', '2024-01-29 16:50:00', 'a', 3, 2),

-- Algoritmi e Strutture Dati (Corso 4)
(13, 'Appunti su Algoritmi di Ordinamento', 'Condivido i miei appunti sugli algoritmi di ordinamento con complessità e spiegazioni.', '2024-01-20 16:00:00', 'simone.neri', 4, 2),
(14, 'Domanda su Grafi', 'Come funziona l\'algoritmo di Dijkstra? Qualcuno ha un buon video o spiegazione?', '2024-01-30 13:15:00', 'andrea.ferrari', 4, 1),
(15, 'Esercizi d\'esame - ASD', 'Sto risolvendo gli esercizi degli anni scorsi, chi vuole confrontarsi?', '2024-02-02 14:40:00', 'simone.neri', 4, 1),

-- Meccanica (Corso 5)
(16, 'Informazioni Esami di Meccanica', "L'esame si terrà il 20 Febbraio secondo il calendario ufficiale.", '2024-01-18 14:45:00', 'alice.testa', 5, 4),
(17, 'Appunti su Cinematica', 'Ecco i miei appunti sulla cinematica con esercizi svolti.', '2024-02-03 11:30:00', 'matteo.longo', 5, 2),
(18, 'Dinamica: ricerca partner studio', 'Cerco qualcuno per fare una sessione di studio su dinamica e forze. Magari sabato pomeriggio?', '2024-02-04 09:00:00', 'alice.testa', 5, 1),

-- Chimica (Corso 6)
(19, 'Domanda su Reazioni Chimiche', 'Come bilanciare una reazione redox? Mi confondo con i numeri di ossidazione.', '2024-01-21 13:10:00', 'laura.monti', 6, 1),
(20, 'Notizie sul Laboratorio di Chimica', 'Il laboratorio di questa settimana è cancellato, ci vedremo la prossima settimana.', '2024-01-22 15:30:00', 'matteo.longo', 6, 3),
(21, 'Composti Organici - Appunti', 'Ho fatto un riassunto dei composti organici principali con strutture e proprietà.', '2024-02-05 10:15:00', 'laura.monti', 6, 2),
(22, 'Attenzione al Laboratorio', 'Ricordatevi: protezioni (guanti, occhiali) obbligatorie in laboratorio! Sono stato richiamato dal tecnico.', '2024-02-06 08:45:00', 'matteo.longo', 6, 3),

-- Altri Post Algebra (Corso 1)
(23, 'Autovalori e Autovettori', 'Sto cercando di capire il significato geometrico di autovalori e autovettori. Le slide del prof non mi sono chiarissime, qualcuno ha materiale alternativo?', '2024-02-07 10:30:00', 'mrossi', 1, 1),
(24, 'Teorema di Rouché-Capelli', 'Il teorema di Rouché-Capelli dice quando un sistema ha soluzioni, ma non capisco bene la dimostrazione. Qualcuno me la spiega?', '2024-02-08 14:20:00', 'giulia_verdi', 1, 1),
(25, 'Esercizi su Applicazioni Lineari', 'Ho risolto tutti gli esercizi su applicazioni lineari del libro. Se avete dubbi chiedete pure!', '2024-02-09 16:00:00', 'l.bianchi', 1, 2),

-- Altri Post Analisi 1 (Corso 2)
(26, 'Serie Numeriche - Aiuto!', 'Non riesco proprio a capire il criterio del rapporto per le serie. Quando una serie converge?', '2024-02-10 09:45:00', 'a', 2, 1),
(27, 'Teorema di Weierstrass', 'Qualcuno ha capito bene le ipotesi del teorema di Weierstrass? Il prof è andato troppo veloce.', '2024-02-11 11:30:00', 'mrossi', 2, 1),
(28, 'Raccolta esercizi integrali per sostituzione', 'Ho creato una raccolta di 50 esercizi sugli integrali per sostituzione con soluzioni dettagliate. La condivido con tutti!', '2024-02-12 15:15:00', 'giulia_verdi', 2, 2),
(29, 'Studio di funzione completo', 'Qualcuno vuole fare insieme uno studio di funzione completo? Dominio, limiti, derivate, asintoti, grafico...', '2024-02-13 10:00:00', 'l.bianchi', 2, 1),

-- Altri Post Programmazione (Corso 3)
(30, 'Allocazione dinamica della memoria', 'Ho problemi con malloc e free in C. Continuo a fare memory leak secondo valgrind. Consigli?', '2024-02-14 13:45:00', 'francesca.gialli', 3, 1),
(31, 'Progetto liste concatenate', 'Ho implementato liste concatenate con inserimento, cancellazione e ricerca. Codice disponibile su GitHub!', '2024-02-15 17:30:00', 'andrea.ferrari', 3, 2),
(32, 'Debugging in GDB', 'Qualcuno sa usare bene GDB per fare debugging? Mi servirebbe un tutorial veloce.', '2024-02-16 10:20:00', 'francesca.gialli', 3, 1),
(33, 'Esame di programmazione - cosa aspettarsi?', 'Per chi ha già dato l\'esame: com\'è strutturato? Più teoria o pratica al computer?', '2024-02-17 14:00:00', 'a', 3, 1),

-- Altri Post ASD (Corso 4)
(34, 'Alberi binari di ricerca', 'Non mi è chiara la differenza tra albero binario e albero binario di ricerca. Qualcuno può fare un esempio?', '2024-02-18 11:15:00', 'andrea.ferrari', 4, 1),
(35, 'Complessità algoritmi - tabella riassuntiva', 'Ho creato una tabella con tutti gli algoritmi visti e le loro complessità (best, avg, worst case). Utile per il ripasso!', '2024-02-19 09:30:00', 'simone.neri', 4, 2),
(36, 'Hash table e collisioni', 'Come si gestiscono le collisioni nelle hash table? Concatenamento o indirizzamento aperto?', '2024-02-20 16:45:00', 'a', 4, 1),
(37, 'Programmazione dinamica vs greedy', 'Mi confondo sempre: quando usare programmazione dinamica e quando algoritmi greedy?', '2024-02-21 13:20:00', 'andrea.ferrari', 4, 1),

-- Altri Post Meccanica (Corso 5)
(38, 'Pendolo semplice - esercizio', 'Qualcuno ha risolto l\'esercizio 3.7 sul pendolo semplice? Non mi tornano i conti sul periodo.', '2024-02-22 10:40:00', 'alice.testa', 5, 1),
(39, 'Energia cinetica e potenziale', 'Quando un corpo cade, l\'energia potenziale si converte in cinetica. Ma se c\'è attrito cosa succede?', '2024-02-23 14:30:00', 'matteo.longo', 5, 1),
(40, 'Appunti completi Meccanica', 'Ecco i miei appunti completi di tutto il corso di meccanica, con formule e dimostrazioni!', '2024-02-24 11:00:00', 'alice.testa', 5, 2),
(41, 'Moto circolare uniforme', 'La forza centripeta punta sempre verso il centro, giusto? E l\'accelerazione centripeta è v²/r?', '2024-02-25 09:15:00', 'a', 5, 1),

-- Altri Post Chimica (Corso 6)
(42, 'pH e soluzioni tampone', 'Come si calcola il pH di una soluzione tampone? Ho la formula ma non capisco quando usarla.', '2024-02-26 15:20:00', 'laura.monti', 6, 1),
(43, 'Nomenclatura IUPAC', 'La nomenclatura IUPAC dei composti organici è un incubo! Qualcuno ha un metodo mnemonico?', '2024-02-27 10:45:00', 'matteo.longo', 6, 1),
(44, 'Relazione di laboratorio - modello', 'Ho preparato un modello per le relazioni di laboratorio con tutte le sezioni richieste. Lo condivido!', '2024-02-28 13:30:00', 'laura.monti', 6, 2),
(45, 'Stechiometria delle reazioni', 'Esercizi sulla stechiometria: qualcuno vuole fare una sessione di studio insieme?', '2024-03-01 11:00:00', 'matteo.longo', 6, 1);

INSERT INTO `comments` (`comment_id`, `post_id`, `comment_text`, `created_at`, `user_id`, `parent_comment_id`) VALUES 
-- Post 1: Domanda su Matrici
(1, 1, 'Puoi usare la regola di Sarrus per calcolare il determinante.', '2024-01-15 10:30:00', 'giulia_verdi', NULL),
(2, 1, 'Grazie per il consiglio! Provo subito!', '2024-01-15 11:00:00', 'mrossi', 1),
(3, 1, 'Anche il metodo di Laplace è valido, dipende da quale ti trovi meglio.', '2024-01-15 12:15:00', 'l.bianchi', NULL),

-- Post 2: Appunti su Spazi Vettoriali
(4, 2, 'Ottimi appunti, grazie per condividerli! Molto chiari gli esempi.', '2024-01-24 11:00:00', 'mrossi', NULL),
(5, 2, 'Manca la parte sulle basi? O solo io non la trovo?', '2024-01-24 13:30:00', 'a', NULL),
(6, 2, 'La parte sulle basi è nella pagina 5, vicino agli esercizi!', '2024-01-24 14:00:00', 'l.bianchi', 5),

-- Post 3: Discussione su Rango
(7, 3, 'Il rango è il numero massimo di righe/colonne linearmente indipendenti. Prova a ridurre a scala!', '2024-01-26 15:00:00', 'l.bianchi', NULL),
(8, 3, 'Esattamente, anche la riducibilità di righe e colonne non cambia il rango.', '2024-01-26 15:45:00', 'mrossi', 7),
(9, 3, 'Ah ok, quindi se la matrice è in forma ridotta, il rango è il numero di righe non nulle?', '2024-01-26 16:10:00', 'giulia_verdi', 8),

-- Post 4: Esercizi d'esame Algebra
(10, 4, 'Guarda, il primo esercizio è proprio sul calcolo del rango, facciamolo insieme!', '2024-01-27 16:15:00', 'mrossi', NULL),
(11, 4, 'Ok! Allora io ho il determinante ma non so come ricavare il rango da quello.', '2024-01-27 17:00:00', 'a', 10),
(12, 4, 'Ragazzi, il determinante e il rango sono due cose diverse. Se det=0 allora il rango è <n.', '2024-01-28 10:00:00', 'l.bianchi', 11),

-- Post 5: Appunti Analisi 1
(13, 5, 'Grazie Luca! Mi hanno aiutato un sacco, specie gli esempi sui limiti notevoli.', '2024-01-16 13:00:00', 'a', NULL),
(14, 5, 'Buon lavoro! Mi hai tolto un dubbio sulla forma 0/0.', '2024-01-16 14:30:00', 'giulia_verdi', NULL),

-- Post 6: Esercizi su Derivate
(15, 6, 'Ho alcuni esercizi extra che posso condividere, con le soluzioni!', '2024-01-19 12:00:00', 'l.bianchi', NULL),
(16, 6, 'Sarebbe fantastico, grazie mille! Inizia a pesare questo corso.', '2024-01-19 12:30:00', 'giulia_verdi', 15),
(17, 6, 'Io allego il pdf degli esercizi, scaricatelo pure tutti.', '2024-01-19 13:00:00', 'l.bianchi', 16),
(18, 6, 'Perfetto! Siete i migliori ragazzi.', '2024-01-19 14:20:00', 'giulia_verdi', 17),

-- Post 7: Domanda su Integrali
(19, 7, 'Di solito conviene spezzare l\'integrale in casi a seconda dei limiti di integrazione.', '2024-01-25 15:00:00', 'mrossi', NULL),
(20, 7, 'Esattamente, tipo quando hai ±∞ come limite devi applicare il limite.', '2024-01-25 15:40:00', 'l.bianchi', 19),
(21, 7, 'Ok, quindi bisogna sempre passare attraverso il limite per gli impropri? Non c\'è un metodo diretto?', '2024-01-25 16:15:00', 'a', 20),
(22, 7, 'No, il metodo è sempre quello. È la definizione stessa di integrale improprio!', '2024-01-25 16:45:00', 'mrossi', 21),

-- Post 8: Info Esami Analisi
(23, 8, 'Grazie per l\'informazione! Allora mi conviene iniziare a ripassare presto.', '2024-01-23 11:30:00', 'giulia_verdi', NULL),
(24, 8, 'Sì, e ricordate che il professore ha detto che può fare domande anche dal quarto capitolo a sorpresa!', '2024-01-23 12:00:00', 'l.bianchi', NULL),

-- Post 9: Dubbio su Continuità
(25, 9, 'Una funzione è continua se non ha "salti", derivabile se è "liscia" (no punti angolosi).', '2024-01-02 12:00:00', 'a', NULL),
(26, 9, 'Tutte le funzioni derivabili sono continue, ma non tutte le continue sono derivabili!', '2024-02-01 12:30:00', 'mrossi', 25),
(27, 9, 'Esempio: f(x) = |x| è continua in 0 ma non derivabile lì!', '2024-02-01 13:00:00', 'giulia_verdi', 26),

-- Post 10: Notizie Programmazione
(28, 10, 'Allora probabilmente il test su puntatori e array non sarà nella prima verifica?', '2024-01-17 10:00:00', 'francesca.gialli', NULL),
(29, 10, 'No, quelli saranno nella seconda parte del corso. Ora focus sui fondamenti!', '2024-01-17 10:45:00', 'andrea.ferrari', 28),

-- Post 11: Puntatori in C
(30, 11, 'I puntatori sono solo indirizzi di memoria. Leggi bene la parte teorica prima degli esercizi!', '2024-01-28 11:00:00', 'andrea.ferrari', NULL),
(31, 11, 'Vero! Tipo int *p = &variabile assegna l\'indirizzo di variabile a p, giusto?', '2024-01-28 11:45:00', 'francesca.gialli', 30),
(32, 11, 'Esatto! E *p ti serve per accedere al valore memorizzato a quell\'indirizzo.', '2024-01-28 12:15:00', 'a', 31),

-- Post 12: Progetto di esempio
(33, 12, 'Posso usare il tuo progetto come base per il mio esercizio?', '2024-01-30 09:00:00', 'francesca.gialli', NULL),
(34, 12, 'Certo! Sentiti libero, il codice è commentato così impari come funziona.', '2024-01-30 09:30:00', 'a', 33),

-- Post 13: Algoritmi di ordinamento
(35, 13, 'Fantastico Simone! Hai messo anche i grafici di complessità?', '2024-01-20 16:30:00', 'andrea.ferrari', NULL),
(36, 13, 'Sì, ho aggiunto i grafici e una tabella comparativa di tutti gli algoritmi.', '2024-01-20 17:00:00', 'simone.neri', 35),
(37, 13, 'Scusami, qual è la complessità del merge sort?', '2024-01-21 10:00:00', 'a', NULL),
(38, 13, 'O(n log n) in tutti i casi, è uno dei più efficienti!', '2024-01-21 10:30:00', 'simone.neri', 37),

-- Post 14: Domanda su Grafi
(39, 14, 'Dijkstra funziona solo con archi a peso positivo, è importante ricordarlo!', '2024-01-30 14:00:00', 'simone.neri', NULL),
(40, 14, 'Esattamente! Se hai pesi negativi devi usare Bellman-Ford invece.', '2024-01-30 14:45:00', 'andrea.ferrari', 39),
(41, 14, 'Ok grazie ragazzi! Ho trovato anche un video su youtube molto chiaro, se vi serve lo mando.', '2024-01-30 15:15:00', 'a', 40),

-- Post 15: Esercizi ASD
(42, 15, 'Sì, anch\'io! Particolarmente il terzo esercizio su grafi, che ne pensate della soluzione?', '2024-02-02 15:30:00', 'a', NULL),
(43, 15, 'Quello con l\'attraversamento BFS? Io l\'ho risolto così...', '2024-02-02 16:00:00', 'simone.neri', 42),

-- Post 16: Info Meccanica
(44, 16, 'Grazie! E quanti argomenti copre l\'esame? Tutto quello fatto a lezione?', '2024-01-18 15:15:00', 'matteo.longo', NULL),
(45, 16, 'Sì, tutto quello coperto dal secondo al quinto capitolo del libro.', '2024-01-18 15:45:00', 'alice.testa', 44),

-- Post 17: Appunti Cinematica
(46, 17, 'Bellissimi! La parte su moto uniformemente accelerato è spiegata benissimo.', '2024-02-03 12:00:00', 'alice.testa', NULL),
(47, 17, 'Grazie! Ho cercato di essere il più chiaro possibile con gli esercizi pratici.', '2024-02-03 12:45:00', 'matteo.longo', 46),

-- Post 18: Partner studio Dinamica
(48, 18, 'Io vengo! Mi serve anche a me fare pratica. Ci vediamo sabato alle 14?', '2024-02-04 10:00:00', 'matteo.longo', NULL),
(49, 18, 'Perfetto! Ci incontriamo in biblioteca allora, portiamo gli esercizi.', '2024-02-04 10:30:00', 'alice.testa', 48),
(50, 18, 'Posso unirmi anche io? Ho tempo sabato pomeriggio.', '2024-02-04 11:00:00', 'a', NULL),

-- Post 19: Reazioni Chimiche
(51, 19, 'Puoi bilanciare le reazioni redox usando il metodo delle semireazioni, é il più logico.', '2024-01-21 14:00:00', 'matteo.longo', NULL),
(52, 19, 'Metodo: separa ossidazione e riduzione, bilancia atomi e elettroni, somma tutto.', '2024-01-21 14:30:00', 'matteo.longo', 51),
(53, 19, 'Grazie! Provo con questo metodo, sembra più logico di quello che cercavo di fare.', '2024-01-21 15:00:00', 'laura.monti', 52),

-- Post 20: Notizie Lab Chimica
(54, 20, 'Ok, allora la prossima lezione è mercoledì alle 14?', '2024-01-22 16:00:00', 'laura.monti', NULL),
(55, 20, 'Sì, mercoledì come sempre, ma fate attenzione alle nuove regole di sicurezza!', '2024-01-22 16:45:00', 'matteo.longo', 54),

-- Post 21: Composti Organici
(56, 21, 'Questo semplifica molto! Che differenza hai messo tra alcani e alcheni?', '2024-02-05 11:00:00', 'laura.monti', NULL),
(57, 21, 'La differenza sta nei doppi legami! Alcani: solo C-C singoli, alcheni: hanno C=C.', '2024-02-05 11:45:00', 'laura.monti', 56),
(58, 21, 'Perfetto spiegazione, salvo tutto!', '2024-02-05 12:30:00', 'matteo.longo', 57),

-- Post 22: Sicurezza Lab
(59, 22, 'Ho notato anche che alcuni colleghi non mettevano gli occhiali... non sono opzionali vero?', '2024-02-06 09:30:00', 'laura.monti', NULL),
(60, 22, 'No! Sono obbligatori! Se il tecnico vi vede senza, potete essere rimossi dal laboratorio.', '2024-02-06 10:00:00', 'matteo.longo', 59),

-- Post 23: Autovalori e Autovettori
(61, 23, 'Geometricamente, un autovettore è un vettore che non cambia direzione dopo la trasformazione lineare!', '2024-02-07 11:00:00', 'l.bianchi', NULL),
(62, 23, 'Esatto! E l\'autovalore è il fattore di scala. Se λ=2, il vettore raddoppia la sua lunghezza.', '2024-02-07 11:30:00', 'giulia_verdi', 61),
(63, 23, 'Ah ok! Quindi se λ=-1 il vettore inverte la direzione?', '2024-02-07 12:00:00', 'mrossi', 62),
(64, 23, 'Esattamente! Hai capito perfettamente il concetto.', '2024-02-07 12:30:00', 'l.bianchi', 63),

-- Post 24: Rouché-Capelli
(65, 24, 'Il teorema dice: sistema compatibile se rango(A) = rango(A|b). Se anche = n, soluzione unica!', '2024-02-08 15:00:00', 'l.bianchi', NULL),
(66, 24, 'E se rango(A) ≠ rango(A|b)?', '2024-02-08 15:30:00', 'giulia_verdi', 65),
(67, 24, 'Allora il sistema è impossibile, nessuna soluzione!', '2024-02-08 16:00:00', 'mrossi', 66),

-- Post 25: Esercizi Applicazioni Lineari
(68, 25, 'Grazie Luca! L\'esercizio 4.12 sul nucleo e immagine mi stava facendo impazzire.', '2024-02-09 17:00:00', 'a', NULL),
(69, 25, 'Puoi spiegare come hai trovato la base del nucleo nell\'es. 4.15?', '2024-02-09 17:30:00', 'mrossi', NULL),
(70, 25, 'Certo! Devi risolvere T(v) = 0 e trovare tutti i vettori che vanno in zero.', '2024-02-09 18:00:00', 'l.bianchi', 69),

-- Post 26: Serie Numeriche
(71, 26, 'Il criterio del rapporto: se lim |a(n+1)/a(n)| < 1 converge, >1 diverge, =1 non si sa!', '2024-02-10 10:30:00', 'giulia_verdi', NULL),
(72, 26, 'Quando viene =1 cosa faccio?', '2024-02-10 11:00:00', 'a', 71),
(73, 26, 'Devi provare altri criteri: radice, confronto, condensazione...', '2024-02-10 11:30:00', 'l.bianchi', 72),
(74, 26, 'Ok grazie! Provo con il criterio della radice allora.', '2024-02-10 12:00:00', 'a', 73),

-- Post 27: Weierstrass
(75, 27, 'Le ipotesi sono: funzione continua su intervallo chiuso e limitato. Allora ha max e min!', '2024-02-11 12:00:00', 'l.bianchi', NULL),
(76, 27, 'Quindi su intervalli aperti non vale?', '2024-02-11 12:30:00', 'mrossi', 75),
(77, 27, 'Esatto! Esempio: f(x)=1/x su (0,1) è continua ma non ha massimo.', '2024-02-11 13:00:00', 'giulia_verdi', 76),

-- Post 28: Raccolta esercizi integrali
(78, 28, 'Sei una salvatrice Giulia! Proprio quello che mi serviva per prepararmi.', '2024-02-12 16:00:00', 'a', NULL),
(79, 28, 'Ho trovato un errore nell\'esercizio 23, il risultato dovrebbe essere ln|x+1| + C.', '2024-02-12 16:30:00', 'mrossi', NULL),
(80, 28, 'Ops hai ragione! Correggo subito e ricarico il file.', '2024-02-12 17:00:00', 'giulia_verdi', 79),

-- Post 29: Studio di funzione
(81, 29, 'Io ci sto! Quando ti va bene? Giovedì pomeriggio?', '2024-02-13 10:30:00', 'giulia_verdi', NULL),
(82, 29, 'Perfetto! Ci vediamo in aula studio alle 15. Portiamo calcolatrice e formulario.', '2024-02-13 11:00:00', 'l.bianchi', 81),
(83, 29, 'Posso venire anche io? Ho bisogno di fare pratica sugli asintoti.', '2024-02-13 11:30:00', 'a', NULL),
(84, 29, 'Certo! Più siamo meglio è.', '2024-02-13 12:00:00', 'l.bianchi', 83),

-- Post 30: Allocazione dinamica
(85, 30, 'Ricordati sempre: ogni malloc deve avere il suo free! Usa valgrind per trovare i leak.', '2024-02-14 14:15:00', 'andrea.ferrari', NULL),
(86, 30, 'Sì, ma dove metto il free? Dopo ogni funzione?', '2024-02-14 14:45:00', 'francesca.gialli', 85),
(87, 30, 'Free quando hai finito di usare quella memoria. Se allochi in main, free in main prima di return.', '2024-02-14 15:15:00', 'a', 86),

-- Post 31: Liste concatenate
(88, 31, 'Ottimo lavoro Andrea! Hai gestito anche il caso della lista vuota?', '2024-02-15 18:00:00', 'simone.neri', NULL),
(89, 31, 'Sì, ho messo controlli NULL ovunque. Il codice è ben commentato!', '2024-02-15 18:30:00', 'andrea.ferrari', 88),

-- Post 32: GDB
(90, 32, 'I comandi base: run, break, next, step, print, continue. Con questi vai lontano!', '2024-02-16 11:00:00', 'andrea.ferrari', NULL),
(91, 32, 'E per vedere il valore di una variabile?', '2024-02-16 11:30:00', 'francesca.gialli', 90),
(92, 32, 'Usa "print nome_variabile" o "p nome_variabile" in breve.', '2024-02-16 12:00:00', 'a', 91),

-- Post 33: Esame programmazione
(93, 33, 'L\'esame è 50% teoria (domande a risposta multipla) e 50% pratica (programma da scrivere).', '2024-02-17 14:30:00', 'andrea.ferrari', NULL),
(94, 33, 'Quanto tempo danno per la parte pratica?', '2024-02-17 15:00:00', 'a', 93),
(95, 33, 'Un\'ora e mezza. Di solito chiede di implementare una struttura dati o algoritmo.', '2024-02-17 15:30:00', 'francesca.gialli', 94),

-- Post 34: Alberi binari
(96, 34, 'In un BST, per ogni nodo: figlio sinistro < nodo < figlio destro. Negli alberi binari normali no.', '2024-02-18 12:00:00', 'simone.neri', NULL),
(97, 34, 'Ah! Quindi il BST permette ricerca veloce O(log n)?', '2024-02-18 12:30:00', 'andrea.ferrari', 96),
(98, 34, 'Esatto, se bilanciato! Se sbilanciato degenera a O(n).', '2024-02-18 13:00:00', 'a', 97),

-- Post 35: Tabella complessità
(99, 35, 'Fantastica questa tabella! Me la stampo e appendo sulla scrivania.', '2024-02-19 10:00:00', 'andrea.ferrari', NULL),
(100, 35, 'Hai incluso anche lo spazio oltre al tempo?', '2024-02-19 10:30:00', 'a', NULL),
(101, 35, 'Sì! C\'è sia complessità temporale che spaziale per ogni algoritmo.', '2024-02-19 11:00:00', 'simone.neri', 100),

-- Post 36: Hash table
(102, 36, 'Concatenamento: ogni slot ha una lista. Indirizzamento aperto: cerca il prossimo slot libero.', '2024-02-20 17:15:00', 'simone.neri', NULL),
(103, 36, 'Quale è meglio?', '2024-02-20 17:45:00', 'a', 102),
(104, 36, 'Dipende! Concatenamento è più semplice, indirizzamento aperto usa meno memoria.', '2024-02-20 18:15:00', 'andrea.ferrari', 103),

-- Post 37: DP vs Greedy
(105, 37, 'DP quando hai sottoproblemi sovrapposti. Greedy quando scelta locale ottima porta a soluzione globale.', '2024-02-21 14:00:00', 'simone.neri', NULL),
(106, 37, 'Esempio: Fibonacci è DP, cambio monete può essere greedy.', '2024-02-21 14:30:00', 'a', 105),
(107, 37, 'Attenzione! Cambio monete greedy funziona solo con certe monete, altrimenti serve DP!', '2024-02-21 15:00:00', 'simone.neri', 106),

-- Post 38: Pendolo
(108, 38, 'Il periodo è T = 2π√(L/g). Hai usato L in metri e g = 9.81 m/s²?', '2024-02-22 11:15:00', 'matteo.longo', NULL),
(109, 38, 'Ah ecco il problema! Avevo usato g = 10. Grazie!', '2024-02-22 11:45:00', 'alice.testa', 108),

-- Post 39: Energia e attrito
(110, 39, 'Con attrito, parte dell\'energia meccanica si dissipa in calore. E_mec non si conserva!', '2024-02-23 15:00:00', 'alice.testa', NULL),
(111, 39, 'Quindi devo calcolare il lavoro della forza di attrito?', '2024-02-23 15:30:00', 'matteo.longo', 110),
(112, 39, 'Esatto! L_attrito = -f_attrito × d, è sempre negativo.', '2024-02-23 16:00:00', 'a', 111),

-- Post 40: Appunti Meccanica
(113, 40, 'Incredibili Alice! Hai salvato tutti noi che siamo indietro.', '2024-02-24 12:00:00', 'matteo.longo', NULL),
(114, 40, 'La parte su quantità di moto è spiegata meglio del libro!', '2024-02-24 12:30:00', 'a', NULL),

-- Post 41: Moto circolare
(115, 41, 'Esatto su entrambi! E ricorda: a_c = v²/r è sempre diretta verso il centro.', '2024-02-25 10:00:00', 'alice.testa', NULL),
(116, 41, 'E la velocità tangenziale è perpendicolare alla centripeta?', '2024-02-25 10:30:00', 'a', 115),
(117, 41, 'Sì! Velocità tangenziale tangente alla traiettoria, accelerazione verso il centro.', '2024-02-25 11:00:00', 'matteo.longo', 116),

-- Post 42: pH tampone
(118, 42, 'Usa l\'equazione di Henderson-Hasselbalch: pH = pKa + log([A-]/[HA]).', '2024-02-26 16:00:00', 'matteo.longo', NULL),
(119, 42, 'E pKa come lo trovo?', '2024-02-26 16:30:00', 'laura.monti', 118),
(120, 42, 'pKa = -log(Ka), dove Ka è la costante di dissociazione acida. Di solito è data.', '2024-02-26 17:00:00', 'matteo.longo', 119),

-- Post 43: Nomenclatura IUPAC
(121, 43, 'Io uso questa regola: catena più lunga, numero più basso per sostituenti, ordine alfabetico.', '2024-02-27 11:15:00', 'laura.monti', NULL),
(122, 43, 'E per i gruppi funzionali, qual è la priorità?', '2024-02-27 11:45:00', 'matteo.longo', 121),
(123, 43, 'Acidi > esteri > ammidi > aldeidi > chetoni > alcoli > ammine > alcheni > alcani.', '2024-02-27 12:15:00', 'laura.monti', 122),

-- Post 44: Relazione lab
(124, 44, 'Perfetto! Hai incluso anche la parte su errori sperimentali?', '2024-02-28 14:00:00', 'matteo.longo', NULL),
(125, 44, 'Sì, c\'è una sezione dedicata a errori sistematici e casuali con esempi.', '2024-02-28 14:30:00', 'laura.monti', 124),

-- Post 45: Stechiometria
(126, 45, 'Io vengo! Mercoledì pomeriggio va bene?', '2024-03-01 11:30:00', 'laura.monti', NULL),
(127, 45, 'Perfetto! In biblioteca alle 14:30. Portiamo tavola periodica e calcolatrice.', '2024-03-01 12:00:00', 'matteo.longo', 126),
(128, 45, 'Posso unirmi? Mi servono esercizi sulle moli.', '2024-03-01 12:30:00', 'a', NULL),
(129, 45, 'Certo! Più siamo meglio è.', '2024-03-01 13:00:00', 'matteo.longo', 128);

INSERT INTO `likes` (`post_id`, `user_id`, `is_like`) VALUES 
-- Post 1
(1, 'giulia_verdi', true),
(1, 'l.bianchi', true),
(1, 'a', true),
-- Post 2
(2, 'mrossi', true),
(2, 'a', true),
(2, 'giulia_verdi', true),
-- Post 3
(3, 'l.bianchi', true),
(3, 'mrossi', true),
-- Post 4
(4, 'mrossi', true),
(4, 'l.bianchi', true),
-- Post 5
(5, 'a', true),
(5, 'giulia_verdi', true),
-- Post 6
(6, 'l.bianchi', true),
(6, 'a', true),
-- Post 7
(7, 'mrossi', true),
(7, 'l.bianchi', true),
-- Post 8
(8, 'giulia_verdi', true),
(8, 'a', true),
-- Post 9
(9, 'a', true),
(9, 'giulia_verdi', true),
-- Post 10
(10, 'francesca.gialli', false),
(10, 'a', true),
-- Post 11
(11, 'a', true),
(11, 'francesca.gialli', true),
(11, 'andrea.ferrari', true),
-- Post 12
(12, 'francesca.gialli', true),
(12, 'a', true),
-- Post 13
(13, 'simone.neri', true),
(13, 'andrea.ferrari', true),
(13, 'a', true),
-- Post 14
(14, 'simone.neri', true),
(14, 'a', true),
-- Post 15
(15, 'a', true),
(15, 'simone.neri', true),
-- Post 16
(16, 'matteo.longo', true),
(16, 'alice.testa', true),
-- Post 17
(17, 'alice.testa', true),
(17, 'a', true),
-- Post 18
(18, 'matteo.longo', true),
(18, 'a', true),
-- Post 19
(19, 'matteo.longo', true),
(19, 'laura.monti', true),
-- Post 20
(20, 'laura.monti', true),
(20, 'a', true),
-- Post 21
(21, 'laura.monti', true),
(21, 'matteo.longo', true),
-- Post 22
(22, 'laura.monti', true),
(22, 'matteo.longo', true),
-- Post 23
(23, 'l.bianchi', true),
(23, 'giulia_verdi', true),
(23, 'a', true),
-- Post 24
(24, 'l.bianchi', true),
(24, 'mrossi', true),
-- Post 25
(25, 'a', true),
(25, 'mrossi', true),
(25, 'giulia_verdi', true),
-- Post 26
(26, 'giulia_verdi', true),
(26, 'l.bianchi', true),
-- Post 27
(27, 'l.bianchi', true),
(27, 'giulia_verdi', true),
-- Post 28
(28, 'a', true),
(28, 'mrossi', true),
(28, 'l.bianchi', true),
-- Post 29
(29, 'giulia_verdi', true),
(29, 'a', true),
-- Post 30
(30, 'andrea.ferrari', true),
(30, 'a', true),
-- Post 31
(31, 'simone.neri', true),
(31, 'francesca.gialli', true),
(31, 'a', true),
-- Post 32
(32, 'andrea.ferrari', true),
(32, 'a', true),
-- Post 33
(33, 'andrea.ferrari', true),
(33, 'francesca.gialli', true),
-- Post 34
(34, 'simone.neri', true),
(34, 'a', true),
-- Post 35
(35, 'andrea.ferrari', true),
(35, 'a', true),
(35, 'simone.neri', true),
-- Post 36
(36, 'simone.neri', true),
(36, 'andrea.ferrari', true),
-- Post 37
(37, 'simone.neri', true),
(37, 'a', true),
-- Post 38
(38, 'matteo.longo', true),
(38, 'alice.testa', true),
-- Post 39
(39, 'alice.testa', true),
(39, 'a', true),
-- Post 40
(40, 'matteo.longo', true),
(40, 'a', true),
(40, 'alice.testa', true),
-- Post 41
(41, 'alice.testa', true),
(41, 'matteo.longo', true),
-- Post 42
(42, 'matteo.longo', true),
(42, 'laura.monti', true),
-- Post 43
(43, 'laura.monti', true),
(43, 'a', true),
-- Post 44
(44, 'matteo.longo', true),
(44, 'laura.monti', true),
-- Post 45
(45, 'laura.monti', true),
(45, 'a', true),
(45, 'matteo.longo', true);

INSERT INTO `post_tags` (`post_id`, `course_id`, `tag_id`) VALUES 
-- Post 1 - Matrici (Algebra)
(1, 1, 1),
-- Post 2 - Spazi vettoriali (Algebra)
(2, 1, 2),
-- Post 3 - Rango (Algebra) - usa tag matrici
(3, 1, 1),
-- Post 4 - Esercizi d'esame (Algebra)
(4, 1, 4),
-- Post 5 - Limiti (Analisi 1)
(5, 2, 5),
-- Post 6 - Derivate (Analisi 1)
(6, 2, 6),
-- Post 7 - Integrali (Analisi 1)
(7, 2, 7),
-- Post 8 - Esercizi d'esame (Analisi 1)
(8, 2, 8),
-- Post 9 - Continuità (Analisi 1) - usa tag limiti
(9, 2, 5),
-- Post 10 - Sintassi (Programmazione)
(10, 3, 9),
-- Post 11 - Puntatori (Programmazione) - usa sintassi
(11, 3, 9),
-- Post 12 - Progetto (Programmazione) - usa sintassi
(12, 3, 9),
-- Post 13 - Ordinamento (ASD)
(13, 4, 11),
-- Post 14 - Grafi (ASD)
(14, 4, 10),
-- Post 15 - Esercizi d'esame (ASD)
(15, 4, 12),
-- Post 16 - Cinematica (Meccanica)
(16, 5, 13),
-- Post 17 - Cinematica (Meccanica)
(17, 5, 13),
-- Post 18 - Dinamica (Meccanica)
(18, 5, 14),
-- Post 19 - Reazioni (Chimica)
(19, 6, 15),
-- Post 20 - Laboratorio (Chimica)
(20, 6, 17),
-- Post 21 - Composti organici (Chimica)
(21, 6, 16),
-- Post 22 - Laboratorio (Chimica)
(22, 6, 17),
-- Post 23 - Spazi vettoriali (Algebra)
(23, 1, 2),
-- Post 24 - Matrici (Algebra)
(24, 1, 1),
-- Post 25 - Applicazioni lineari (Algebra)
(25, 1, 3),
(25, 1, 4),
-- Post 26 - Limiti (Analisi 1)
(26, 2, 5),
-- Post 27 - Limiti (Analisi 1)
(27, 2, 5),
-- Post 28 - Integrali (Analisi 1)
(28, 2, 7),
-- Post 29 - Derivate e limiti (Analisi 1)
(29, 2, 5),
(29, 2, 6),
-- Post 30 - Sintassi (Programmazione)
(30, 3, 9),
-- Post 31 - Sintassi (Programmazione)
(31, 3, 9),
-- Post 32 - Sintassi (Programmazione)
(32, 3, 9),
-- Post 33 - Esame (Programmazione)
(33, 3, 9),
-- Post 34 - Grafi (ASD)
(34, 4, 10),
-- Post 35 - Algoritmi ordinamento (ASD)
(35, 4, 11),
(35, 4, 12),
-- Post 36 - Grafi (ASD)
(36, 4, 10),
-- Post 37 - Algoritmi (ASD)
(37, 4, 11),
-- Post 38 - Dinamica (Meccanica)
(38, 5, 14),
-- Post 39 - Dinamica (Meccanica)
(39, 5, 14),
-- Post 40 - Cinematica e Dinamica (Meccanica)
(40, 5, 13),
(40, 5, 14),
-- Post 41 - Cinematica (Meccanica)
(41, 5, 13),
-- Post 42 - Reazioni (Chimica)
(42, 6, 15),
-- Post 43 - Composti organici (Chimica)
(43, 6, 16),
-- Post 44 - Laboratorio (Chimica)
(44, 6, 17),
-- Post 45 - Reazioni (Chimica)
(45, 6, 15);

INSERT INTO `administrators` (`admin_id`, `password`) VALUES
-- password: aaaaaa
('a', '$2y$12$IopdjTqt.gNIuYN7FDXZJOZo.02BGca/Jw0uf0v0xhsN89gf9HoCW');
