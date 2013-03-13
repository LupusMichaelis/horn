
create table stories
( id			integer		not null auto_increment
, created		datetime	not null
, modified		timestamp	not null

, caption		varchar(80)
, description	varchar(160)

, primary key	(id)
) engine=innodb, character set utf8 ;

create table legacy_stories
( path			varchar(255)	not null
, story_id		integer			not null

, primary key (path, story_id)
) engine=innodb, character set utf8 ;

