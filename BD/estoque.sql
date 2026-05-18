create database Estoque;
use Estoque;

create table produtos(
	id int primary key auto_increment,
	nome varchar(50) not null,
    descricao text,
    secao varchar(20) not null,
    quantidade int not null default 0,
	data_entrada varchar(25),
    data_saida varchar(25)
);

create table secoes(
	id int primary key auto_increment,
    nome varchar(50) not null unique
);

create table usuarios(
	id int primary key auto_increment,
    nome varchar(100) not null,
    senha varchar(255) not null,
    posto varchar(20) not null,
    secao varchar(50) not null,
    privilegio_id int not null,
    foreign key (privilegio_id) references privilegios(id)
);


create table usuarioCautela(
	id int primary key auto_increment,
    nome varchar(100) not null,
    posto varchar(20) not null,
    om varchar(40) not null,
    telefone varchar(30) not null,
    cautela_id int not null,
    foreign key (cautela_id) references cautela(id)
);

create table cautela(
	id int primary key auto_increment,
    status boolean not null default false
);

create table cautelas_materiais(
	id int primary key auto_increment,
    produto_id int not null,
    quantidade_cautelada int not null,
    responsavel_nome varchar(100) not null,
    responsavel_secao varchar(50) not null,
    responsavel_telefone varchar(30),
    data_cautela datetime not null default current_timestamp,
    data_prevista_devolucao date,
    data_devolucao datetime,
    foreign key (produto_id) references produtos(id)
);

create table privilegios(
	id int primary key auto_increment,
    nome varchar(20) not null unique
);

create table relatorio(
	id int primary key auto_increment,
    produto_id int not null,
    usuario_id int not null,
    cautela_id int not null,
    foreign key (produto_id) references produtos(id),
    foreign key (usuario_id) references usuarios(id),
    foreign key (cautela_id) references cautela(id)
);

insert into privilegios (nome) values ('Administrador'), ('Usuario');
insert into secoes (nome) values ('STI'), ('SGO'), ('SSGIE');
insert into usuarios (nome, senha, posto, secao, privilegio_id) values ('admin', '123', 'admin', 'STI', 1);
select * from usuarios;
