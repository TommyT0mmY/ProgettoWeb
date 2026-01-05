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
     type varchar(30) not null,
     course_id int not null,
     constraint ID_tags primary key (tag_id, course_id),
     constraint UDX_type_course unique (type, course_id)
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
     foreign key (tag_id, course_id)
     references tags (tag_id, course_id)
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
