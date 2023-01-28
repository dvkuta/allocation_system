create table role
(
    id   int(11) unsigned auto_increment
        primary key,
    type varchar(100) not null
);

create table user
(
    id        int(11) unsigned auto_increment
        primary key,
    firstname varchar(255) not null,
    lastname  varchar(255) not null,
    email     varchar(200) not null,
    login     varchar(100) not null,
    password  varchar(60)  not null,
    workplace varchar(255) null,
    constraint user_email_unique
        unique (email),
    constraint user_login_unique
        unique (login)
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

create table project_user
(
    id         int(11) unsigned auto_increment
        primary key,
    user_id    int(11) unsigned not null,
    project_id int(11) unsigned not null,
    constraint project_user_project_FK
        foreign key (project_id) references project (id),
    constraint project_user_user_FK
        foreign key (user_id) references user (id)
);

create table project_user_allocation
(
    id              int(11) unsigned auto_increment
        primary key,
    project_user_id int(11) unsigned                                   not null,
    allocation      tinyint(4) unsigned                                not null,
    `from`          date                                               not null,
    `to`            date                                               not null,
    description     text                                               null,
    state           enum ('active', 'draft', 'cancel') default 'draft' not null,
    constraint project_user_assignment_PROJECT_USER_FK
        foreign key (project_user_id) references project_user (id)
);

create table user_role
(
    id      int(11) unsigned auto_increment
        primary key,
    user_id int(11) unsigned not null,
    role_id int(11) unsigned not null,
    constraint user_role_ROLE_FK
        foreign key (role_id) references role (id),
    constraint user_role_USER_FK
        foreign key (user_id) references user (id)
)
    comment 'role uživatelů';

