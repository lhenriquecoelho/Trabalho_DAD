CREATE DATABASE IF NOT EXISTS bd_censo;
USE bd_censo;

CREATE TABLE DadosPessoais (
  matricula INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NULL,
  email VARCHAR(60) NULL,
  celular VARCHAR(14) NULL,
  data_nascimento DATE NULL,
  PRIMARY KEY(matricula)
);