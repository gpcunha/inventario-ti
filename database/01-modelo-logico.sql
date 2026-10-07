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
-- ## Tabela: departamentos
-- Descrição: Armazena os departamentos da empresa, permitindo a organização e alocação dos equipamentos.
-- ==========================================
CREATE TABLE departamentos(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- ## Tabela: funcoes
-- Descrição: Armazena os funçoes da empresa, determinadas a cada usuários na empresa.
-- ==========================================
CREATE TABLE funcoes(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NULL,
    departamento_id INT NOT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_funcoes_departamentos
    FOREIGN KEY (departamento_id)
    REFERENCES departamentos(id)
);

-- ==========================================
-- ## Tabela: usuarios
-- Descrição: Armazena os dados do colaborador responsável pelo uso do equipamento.
-- ==========================================
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    matricula VARCHAR(20) NOT NULL,
    login VARCHAR(50) UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('ADMINISTRADOR', 'TECNICO', 'CONSULTA') NOT NULL DEFAULT 'CONSULTA',
    email VARCHAR(100) NOT NULL UNIQUE,
    ramal VARCHAR(10),
    departamento_id INT NULL,
    funcao_id INT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_departamentos
    FOREIGN KEY (departamento_id)
    REFERENCES departamentos(id)
    CONSTRAINT fk_usuarios_funcoes
    FOREIGN KEY (funcao_id)
    REFERENCES funcoes(id)
);

-- ==========================================
-- ## Tabela: fabricantes
-- Descrição: Armazena os dados do fabricante do equipamento.
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
-- ## Tabela: tipos_componentes
-- Descrição: Armazena os tipos de componentes como memória RAM, processadores, coolers, placa de vídeo, etc.
-- ==========================================
CREATE TABLE tipos_componentes(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP
);

-- ## Tabela: tipos_equipamento
-- Descrição: Armazena os tipos de equipamentos, como notebook, desktop, servidor, impressora, etc.
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
-- ## Tabela: equipamentos
-- Descrição: Armazena os dados dos equipamentos de TI, incluindo informações de hardware, software e localização.
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
    data_aquisicao DATE,
    observacoes TEXT,
    status ENUM('EM USO', 'DISPONIVEL', 'EM ESTOQUE', 'RESERVADO', 'EMPRESTADO', 'MANUTENCAO', 'DESCARTE') NOT NULL DEFAULT 'EM USO',
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

-- ==========================================
-- ## Tabela: componentes
-- Descrição: Armazena a descrição componentes&#x20;
-- ==========================================
CREATE TABLE componentes(
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipamento_id INT NULL,
    tipo_componente_id INT NULL,
    fabricante_id INT NULL,
    modelo VARCHAR(50) NOT NULL,
    numero_serie VARCHAR(100) UNIQUE,
    especificacao VARCHAR(255) NULL,
    status ENUM('INSTALADO', 'EM ESTOQUE', 'EM MANUTENCAO', 'REMOVIDO', 'DESCARTADO') NOT NULL DEFAULT 'INSTALADO',
    observacoes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_componentes_equipamentos
    FOREIGN KEY (equipamento_id)
    REFERENCES equipamentos(id),
    CONSTRAINT fk_componentes_tipos
    FOREIGN KEY (tipo_componente_id)
    REFERENCES tipos_componentes(id),
    CONSTRAINT fk_componentes_fabricantes
    FOREIGN KEY (fabricante_id)
    REFERENCES fabricantes(id)
);

-- ==========================================
-- ## Tabela: movimentacoes
-- Descrição: Histórico de transferências, alocações e empréstimos dos equipamentos.
-- ==========================================
CREATE TABLE movimentacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipamento_id INT NOT NULL,
    tipo_movimentacao ENUM('ALOCACAO', 'DEVOLUCAO', 'TRANSFERENCIA', 'EMPRESTIMO', 'MANUTENCAO', 'DESCARTE') NOT NULL,
    motivo_movimentacao VARCHAR(100) NULL,
    usuario_origem_id INT NULL,
    departamento_origem_id INT NULL,
    localizacao_origem VARCHAR(100) NULL,
    usuario_destino_id INT NULL,
    departamento_destino_id INT NULL,
    localizacao_destino VARCHAR(100) NULL,
    responsavel_ti_id INT NOT NULL,
    data_movimentacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_movimentacoes_equipamentos
    FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id),
    CONSTRAINT fk_movimentacoes_usr_origem
    FOREIGN KEY (usuario_origem_id) REFERENCES usuarios(id),
    CONSTRAINT fk_movimentacoes_usr_destino
    FOREIGN KEY (usuario_destino_id) REFERENCES usuarios(id),
    CONSTRAINT fk_movimentacoes_dept_origem
    FOREIGN KEY (departamento_origem_id) REFERENCES departamentos(id),
    CONSTRAINT fk_movimentacoes_dept_destino
    FOREIGN KEY (departamento_destino_id) REFERENCES departamentos(id),
    CONSTRAINT fk_movimentacoes_resp_ti
    FOREIGN KEY (responsavel_ti_id) REFERENCES usuarios(id)
);

-- ==========================================
-- ## Tabela: prestadores_servico
-- Descrição: Cadastro de prestadores de serviços para registro de reparos e prevenções externas, garantindo rastreabilidade e histórico de manutenção.
-- ==========================================
CREATE TABLE prestadores_servico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cnpj VARCHAR(20) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    email VARCHAR(100),
    endereco TEXT,
    observacoes TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- ## Tabela: manutencoes
-- Descrição: Registro de intervenções técnicas, reparos e prevenções.
-- ==========================================
CREATE TABLE manutencoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipamento_id INT NOT NULL, tipo ENUM('PREVENTIVA', 'CORRETIVA', 'PREDITIVA' ) NOT NULL,
    descricao_problema TEXT NOT NULL,
    servico_realizado TEXT NULL,
    prestador_id INT NULL,
    usuario_id INT NULL,
    data_abertura DATE NOT NULL,
    data_conclusao DATE NULL,
    custo DECIMAL(10,2) NULL,
    status ENUM('ABERTA', 'EM ANDAMENTO', 'AGUARDANDO PEÇA', 'AGUARDANDO PRESTADOR', 'CONCLUÍDA', 'CANCELADA') NOT NULL DEFAULT 'ABERTA',
    observacoes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_manutencoes_equipamentos FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id),
    CONSTRAINT fk_manutencoes_prestadores  FOREIGN KEY (prestador_id)  REFERENCES prestadores_servico(id),
    CONSTRAINT fk_manutencoes_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);