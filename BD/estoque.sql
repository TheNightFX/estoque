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

create table usuarios(
	id int primary key auto_increment,
    nome varchar(100) not null,
    posto varchar(20) not null,
    secao varchar(50) not null,
    privilegio_id int not null,
    foreign key (privilegio_id) references privilegios(id)
);

create table privilegios(
	id int primary key auto_increment,
    nome varchar(20) not null unique
);
insert into privilegios (nome) values ('Administrador'), ('Usuario');

create table relatorio(
	id int primary key auto_increment,
    produto_id int not null,
    usuario_id int not null,
    foreign key (produto_id) references produtos(id),
    foreign key (usuario_id) references usuarios(id)
);

SELECT 
    r.id AS relatorio_numero,
    p.nome AS nome_produto,
    p.secao AS secao_produto,
    u.nome AS nome_usuario,
    u.posto AS posto_usuario,
    p.data_entrada AS data_entrada,
    p.data_saida AS data_saida
FROM relatorio r
INNER JOIN produtos p ON r.produto_id = p.id
INNER JOIN usuarios u ON r.usuario_id = u.id;
