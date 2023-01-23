create table user_role
(
    id   int auto_increment
        primary key,
    type varchar(100) not null
);

create table user
(
    id           int(11) unsigned auto_increment
        primary key,
    user_role_id int          null,
    firstname    varchar(255) not null,
    lastname     varchar(255) not null,
    email        varchar(200) not null,
    login        varchar(100) not null,
    password     varchar(60)  not null,
    workplace    varchar(255) null,
    constraint user_email_unique
        unique (email),
    constraint user_login_unique
        unique (login),
    constraint users_FK
        foreign key (user_role_id) references user_role (id)
);

create table project
(
    id          int(11) unsigned auto_increment
        primary key,
    name        varchar(100)     not null,
    user_id     int(11) unsigned not null comment 'Project manager id only',
    `from`      date             not null,
    `to`        date             null,
    description text             null,
    constraint project_user_fk
        foreign key (user_id) references user (id)
);

