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
    descricao TEXT,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
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
    perfil ENUM('ADMIN', 'TECNICO', 'CONSULTA') NOT NULL DEFAULT 'CONSULTA',
    email VARCHAR(100) NOT NULL UNIQUE,
    ramal VARCHAR(10),
    cargo VARCHAR(100) NOT NULL,
    departamento_id INT NULL,
    status ENUM('ATIVO', 'INATIVO') NOT NULL DEFAULT 'ATIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuarios_departamentos
        FOREIGN KEY (departamento_id)
        REFERENCES departamentos(id)
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
    memoria_ram VARCHAR(50),
    armazenamento VARCHAR(50),
    processador VARCHAR(50),
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
    equipamento_id INT NOT NULL,
    tipo_manutencao ENUM('PREVENTIVA', 'CORRETIVA', 'UPGRADE') NOT NULL DEFAULT 'CORRETIVA',
    defeito_relatado TEXT NOT NULL,
    solucao_realizada TEXT NULL,
    prestador_servico_id INT NULL,
    numero_nota_fiscal VARCHAR(50) NULL,
    custo DECIMAL(10, 2) DEFAULT 0.00,
    coberto_por_garantia ENUM('SIM', 'NAO') NOT NULL DEFAULT 'NAO',
    data_abertura DATE NOT NULL DEFAULT (CURRENT_DATE),
    data_envio DATE NOT NULL,
    data_previsao_retorno DATE NULL,
    data_conclusao DATE NULL,
    status ENUM('AGUARDANDO_ENVIO', 'EM_ANALISE', 'EM_MANUTENCAO', 'CONCLUIDO', 'CANCELADO', 'SEM_CONSERTO') NOT NULL DEFAULT 'EM_MANUTENCAO',
    solicitante_ti_id INT NOT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_manutencoes_equipamentos
        FOREIGN KEY (equipamento_id) REFERENCES equipamentos(id),
        
    CONSTRAINT fk_manutencoes_solicitante
        FOREIGN KEY (solicitante_ti_id) REFERENCES usuarios(id),

    CONSTRAINT fk_manutencoes_prestadores
        FOREIGN KEY (prestador_servico_id) REFERENCES prestadores_servico(id)
);