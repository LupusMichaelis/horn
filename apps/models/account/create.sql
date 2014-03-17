
create table accounts
( id			integer			not null auto_increment
, created		datetime		not null
, modified		timestamp		not null

, name			varchar(255)	not null
, email			text			not null

, primary key	(id)

) engine=innodb, character set utf8 ;

create table roles
( id			integer			not null auto_increment

, label			varchar(255)	not null

, primary key	(id)

) engine=innodb, character set utf8 ;

create table accounts_roles
( account_id	integer			not null
, role_id		integer			not null

, foreign key (account_id) references accounts(id)
, foreign key (role_id) references roles(id)
, primary key (account_id, role_id)

) engine=innodb, character set utf8 ;

create table users
( id			integer			not null

, account_id	integer			default null

, connected_at	datetime		not null

, ip			char(45)		not null
, payload		text			default null -- cookie id

, foreign key (account_id) references accounts(id)
, primary key (id)

) engine=innodb, character set utf8 ;

