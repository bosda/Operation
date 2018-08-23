drop table if exists `users`;
create table `users`(
	id tinyint primary key auto_increment,
	openid varchar(255),
	user_name varchar(20) not null,
	real_name varchar(20) not null default '',
	password char(255) not null,
	remember_token char(100) not null default '',
	email varchar(50) not null default '',
	wechat_name varchar(32) default '',
	wechat_avatar varchar(50) default '',
	notify tinyint not null default '1' comment '通知类型：0不通知，1微信通知，2邮件通知,3微信邮件通知',
	type tinyint not null default '0' comment '用户类型：0普通用户，1管理员',
	bind tinyint not null default '0' comment '是否绑定：0未绑定，1已绑定',
	create_time int(11)
)engine=Myisam default charset=utf8;

insert into users (user_name,real_name,password,type,create_time) values('admin','管理员','$2y$10$UR8iLoC7kRn7tvg/iuWLTu1qqFK4YObDePjMSLjttWf3ucSE7CZQ.',1,1531799449);

drop table if exists `user_project`;
create table `user_project`(
	id int primary key auto_increment,
	user_id int not null,
	project_id int not null,
	create_time int(11)
)engine=Myisam default charset=utf8;

drop table if exists `project`;
create table `project`(
	id int primary key auto_increment,
	project_name varchar(50) not null,
	token varchar(255) unique comment '签名',
	create_time int(11)
)engine=Myisam default charset=utf8;

drop table if exists `message`;
create table `message`(
	id int primary key auto_increment,
	project_id int not null,
	title varchar(1204) not null,
	type varchar(255) comment '错误类型',
	file varchar(255) comment '文件路径',
	line int comment '错误行号',
	code varchar(20) comment '错误码',
	content text not null,
	is_read tinyint not null default '0',
	create_time int(11)
)charset=utf8;


