
create table stories
( id			integer		not null auto_increment
, created		datetime	not null

, modified		timestamp	not null
, caption		varchar(80)
, description	varchar(160)

, primary key	(id)
) engine=innodb, character set utf8 ;
