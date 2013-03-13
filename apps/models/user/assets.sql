
insert into accounts (name, email)
	values ('admin', 'root@localhost') ;

insert into roles (label)
	values ('admin') ;

insert into accounts_roles (account_id, role_id)
	values
		( (select id from accounts where name = 'admin')
		, (select id from roles where label = 'admin')
	) ;

