
create table stories
( id			integer		not null auto_increment
, created		datetime	not null

, modified		timestamp	not null
, title			varchar(80)
, description	varchar(160)

, primary key	(id)
) type = innodb ;
