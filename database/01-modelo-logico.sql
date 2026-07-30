-- ==========================================
-- Projeto: Sistema de Gestão de Ativos de TI
-- Arquivo: 01-modelo-logico.sql
-- Descrição: Estrutura lógica do banco de dados
-- Autor: Glauco Paiva Cunha
-- Data: 30/07/2026
-- ==========================================

CREATE DATABASE inventario_ti;
USE inventario_ti;

-- ==========================================
-- Tabela: departamentos
-- ==========================================

CREATE TABLE departamentos(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabela: usuarios
-- Descrição: Armazena os colaboradores responsáveis pelos ativos de TI.
-- ==========================================

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    matricula VARCHAR(20) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    ramal VARCHAR(10),
    cargo VARCHAR(100) NOT NULL,
    departamento_id INT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabela: fabricantes
-- ==========================================

CREATE TABLE fabricantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    site VARCHAR(255) NOT NULL,
    observacoes TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabela: tipos_equipamento
-- Descrição: Armazenar todos os dados do equipamento para facilitar sua identificação e localização.
-- ==========================================

CREATE TABLE tipos_equipamento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- Tabela: equipamentos
-- Descrição: Armazenar todos os dados do equipamento para facilitar sua identificação e localização.
-- ==========================================

CREATE TABLE equipamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patrimonio VARCHAR(30) NOT NULL UNIQUE,
    hostname VARCHAR(50),
    numero_serie VARCHAR(100) NOT NULL UNIQUE,
    fabricante_id INT NULL,
    garantia_ate DATE,    
    modelo VARCHAR(50) NOT NULL,
    tipo_equipamento_id INT,
    departamento_id INT NULL,
    localizacao_fisica VARCHAR(100) NOT NULL,
    usuario_id INT NULL,
    sistema_operacional VARCHAR(50) NOT NULL,
    memoria_ram VARCHAR(50),
    armazenamento VARCHAR(50),
    processador VARCHAR(50),
    data_aquisicao DATE,
    observacoes TEXT,
    status ENUM('EM USO', 'DISPONIVEL', 'EM ESTOQUE', 'RESERVADO', 'EMPRESTADO', 'MANUTENCAO', 'DESCARTE'), NOT NULL DEFAULT 'EM USO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_equipamentos_fabricantes
        FOREIGN KEY (fabricante_id)
        REFERENCES fabricantes(id),

    CONSTRAINT fk_equipamentos_tipos
        FOREIGN KEY (tipo_equipamento_id)
        REFERENCES tipos_equipamento(id),

    CONSTRAINT fk_equipamentos_departamentos
        FOREIGN KEY (departamento_id)
        REFERENCES departamentos(id),

    CONSTRAINT fk_equipamentos_usuarios
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
);