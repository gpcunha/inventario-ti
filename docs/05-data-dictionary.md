# Data Dictionary

Este documento descreve todas as entidades do banco de dados do projeto Sistema de Gestão de Ativos de TI.

Versão: 1.0

Autor:
Glauco Paiva Cunha

Data:
01/08/2026

## Tabela: departamentos       
                                
    Descrição:
    Armazena os departamentos da empresa, permitindo a organização e alocação dos equipamentos.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do departamento. |
| nome | VARCHAR(100) | NOT NULL | Nome do departamento. |
| descricao | TEXT | NULL | Descrição detalhada das atividades ou escopo do departamento. |
| status | ENUM('ATIVO', 'INATIVO') | NOT NULL, DEFAULT 'ATIVO' | Status de operação do departamento. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |
                                
## 2. Tabela: usuarios       
                                
    Descrição:
    Armazena os dados do colaborador responsável pelo uso do equipamento.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do usuário. |
| nome | VARCHAR(100) | NOT NULL | Nome completo do colaborador. |
| matricula | VARCHAR(20) | NOT NULL | Número de matrícula corporativa do colaborador. |
| login | VARCHAR(50) | UNIQUE | Nome de usuário/login para autenticação no sistema. |
| senha | VARCHAR(255) | NOT NULL | Campo reservado para autenticação do usuário/login no sistema. |
| perfil | ENUM('ADMIN', 'TECNICO', 'CONSULTA') | NOT NULL, DEFAULT 'CONSULTA'| Definição de perfil por usuário/login para prioridade e segurança de acesso. |
| email | VARCHAR(100) | NOT NULL, UNIQUE | Endereço de e-mail corporativo do usuário. |
| ramal | VARCHAR(10) | NULL | Número do ramal telefônico do usuário. |
| cargo | VARCHAR(100) | NOT NULL | Cargo ou função exercida pelo colaborador na empresa. |
| departamento_id | INT | FK (departamentos.id), NULL | Chave Estrangeira referente ao departamento do colaborador. |
| status | ENUM('ATIVO', 'INATIVO') | NOT NULL, DEFAULT 'ATIVO' | Status do colaborador no sistema. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |

    Relacionamentos
    • departamento_id → departamentos(id)
                                
## 3. Tabela: fabricantes       
                                
    Descrição:
    Armazena os dados do fabricante do equipamento.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do fabricante. |
| nome | VARCHAR(100) | NOT NULL | Nome da empresa fabricante. |
| site | VARCHAR(255) | NOT NULL | URL do site oficial do fabricante. |
| observacoes | TEXT | NULL | Notas ou informações adicionais sobre o fabricante. |
| status | ENUM('ATIVO', 'INATIVO') | NOT NULL, DEFAULT 'ATIVO' | Status do cadastro do fabricante. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |
                                
## 4. Tabela: tipos_equipamento       
                                
    Descrição:
    Armazena os tipos de equipamentos (ex: notebook, desktop, servidor, impressora).       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do tipo de equipamento. |
| nome | VARCHAR(100) | NOT NULL | Nome/categoria do equipamento. |
| descricao | TEXT | NULL | Descrição e detalhes sobre o tipo de equipamento. |
| status | ENUM('ATIVO', 'INATIVO') | NOT NULL, DEFAULT 'ATIVO' | Status da categoria do equipamento. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |
                                
## 5. Tabela: equipamentos       
                                
    Descrição:
    Armazena os dados dos equipamentos de TI, incluindo informações de hardware, software e localização.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do equipamento. |
| patrimonio | VARCHAR(30) | NOT NULL, UNIQUE | Código da etiqueta de patrimônio do ativo. |
| hostname | VARCHAR(50) | NULL | Nome de rede do computador/dispositivo no domínio. |
| numero_serie | VARCHAR(100) | NOT NULL, UNIQUE | Número de série atribuído pelo fabricante. |
| fabricante_id | INT | FK (fabricantes.id), NULL | Chave Estrangeira referente ao fabricante do equipamento. |
| garantia_ate | DATE | NULL | Data de expiração da garantia de fábrica. |
| modelo | VARCHAR(50) | NOT NULL | Modelo específico do equipamento. |
| tipo_equipamento_id | INT | FK (tipos_equipamento.id), NULL | Chave Estrangeira referente à categoria/tipo do equipamento. |
| departamento_id | INT | FK (departamentos.id), NULL | Chave Estrangeira referente ao departamento alocado. |
| localizacao_fisica | VARCHAR(100) | NOT NULL | Prédio, sala ou setor onde o equipamento está alocado. |
| usuario_id | INT | FK (usuarios.id), NULL | Chave Estrangeira referente ao usuário responsável atual. |
| sistema_operacional | VARCHAR(50) | NOT NULL | Nome e versão do Sistema Operacional instalado. |
| memoria_ram | VARCHAR(50) | NULL | Capacidade e especificação da memória RAM (Ex: 16GB DDR4). |
| armazenamento | VARCHAR(50) | NULL | Tipo e capacidade de armazenamento (Ex: SSD 512GB NVMe). |
| processador | VARCHAR(50) | NULL | Modelo do processador (Ex: Intel i7-12700H, Ryzen 7). |
| data_aquisicao | DATE | NULL | Data em que o equipamento foi comprado/incorporado ao patrimônio. |
| observacoes | TEXT | NULL | Histórico técnico adicional ou detalhes específicos do ativo. |
| status | ENUM('EM USO', 'DISPONIVEL', 'EM ESTOQUE', 'RESERVADO', 'EMPRESTADO', 'MANUTENCAO', 'DESCARTE') | NOT NULL, DEFAULT 'EM USO' | Situação física e operacional do equipamento. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |

    Relacionamentos
    • fabricante_id → fabricantes(id)
    • tipo_equipamento_id → tipos_equipamentos(id)
    • departamento_id → departamentos(id)
    • usuario_id → usuarios(id)

                                
## 6. Tabela: movimentacoes       
                                
    Descrição:
    Histórico de transferências, alocações e empréstimos dos equipamentos.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do registro de movimentação. |
| equipamento_id | INT | FK (equipamentos.id), NOT NULL | Chave Estrangeira referente ao equipamento movimentado. |
| tipo_movimentacao | ENUM('ALOCACAO', 'DEVOLUCAO', 'TRANSFERENCIA', 'EMPRESTIMO', 'MANUTENCAO', 'DESCARTE') | NOT NULL | Tipo/natureza da movimentação realizada. |
| motivo_movimentacao | VARCHAR(100) | NULL | Justificativa para a alteração de estado ou alocação do ativo. |
| usuario_origem_id | INT | FK (usuarios.id), NULL | Chave Estrangeira do usuário que devolveu/possuía a posse anterior. |
| departamento_origem_id | INT | FK (departamentos.id), NULL | Chave Estrangeira do departamento onde o ativo estava alocado. |
| localizacao_origem | VARCHAR(100) | NULL | Local físico onde o equipamento estava posicionado antes. |
| usuario_destino_id | INT | FK (usuarios.id), NULL | Chave Estrangeira do usuário que recebeu a nova posse do ativo. |
| departamento_destino_id | INT | FK (departamentos.id), NULL | Chave Estrangeira do novo departamento recebedor. |
| localizacao_destino | VARCHAR(100) | NULL | Novo local físico para onde o ativo foi transferido. |
| responsavel_ti_id | INT | FK (usuarios.id), NOT NULL | Chave Estrangeira do técnico de TI que realizou a transação. |
| data_movimentacao | DATETIME | DEFAULT CURRENT_TIMESTAMP | Data e hora exata em que ocorreu a movimentação. |
| observacoes | TEXT | NULL | Anotações operacionais sobre a transição do ativo. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro de auditoria no sistema. |

    Relacionamentos 
    • equipamento_id → equipamentos(id)
    • usuario_origem_id → usuarios(id)
    • usuario_destino_id → usuarios(id)
    • departamento_destino_id → departamentos(id)
    • responsavel_ti_id → usuarios(id)
                                
## 7. Tabela: prestadores_servico       
                                
    Descrição:
    Cadastro de prestadores de serviços para registro de reparos e prevenções externas, garantindo rastreabilidade e histórico de manutenção.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único do prestador de serviço. |
| nome | VARCHAR(100) | NOT NULL | Razão social ou nome fantasia da empresa contratada. |
| cnpj | VARCHAR(20) | NOT NULL, UNIQUE | Número de CNPJ do prestador de serviço. |
| telefone | VARCHAR(20) | NULL | Telefone de contato comercial do prestador. |
| email | VARCHAR(100) | NULL | Endereço de e-mail de contato comercial/suporte. |
| endereco | TEXT | NULL | Endereço físico completo da empresa prestadora. |
| observacoes | TEXT | NULL | Informações sobre contratos, prazos de atendimento ou acordos. |
| status | ENUM('ATIVO', 'INATIVO') | NOT NULL, DEFAULT 'ATIVO' | Status de cadastro do prestador de serviço. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |
                                
## 8. Tabela: manutencoes       
                                
    Descrição:
    Registro de intervenções técnicas, reparos e prevenções.       
                                
| Campo | Tipo | Restrições | Descrição |
| id | INT | PK, AUTO_INCREMENT | Identificador único da ordem de manutenção. |
| equipamento_id | INT | FK (equipamentos.id), NOT NULL | Chave Estrangeira referente ao equipamento reparado. |
| tipo_manutencao | ENUM('PREVENTIVA', 'CORRETIVA', 'UPGRADE') | NOT NULL, DEFAULT 'CORRETIVA' | Tipo da intervenção técnica efetuada. |
| defeito_relatado | TEXT | NOT NULL | Descrição detalhada da falha ou motivo da ordem de serviço. |
| solucao_realizada | TEXT | NULL | Relatório das ações executadas para a solução do problema. |
| prestador_servico_id | INT | FK (prestadores_servico.id), NULL | Chave Estrangeira do fornecedor externo contratado. |
| numero_nota_fiscal | VARCHAR(50) | NULL | Número do comprovante fiscal/ordem de serviço referente ao reparo. |
| custo | DECIMAL(10, 2) | DEFAULT 0.00 | Valor financeiro total gasto na manutenção. |
| coberto_por_garantia | ENUM('SIM', 'NAO') | NOT NULL, DEFAULT 'NAO' | Indica se o custo da manutenção foi coberto pela garantia. |
| data_abertura | DATE | NOT NULL, DEFAULT CURRENT_DATE | Data de abertura da solicitação interna de manutenção. |
| data_envio | DATE | NOT NULL | Data em que o equipamento foi entregue/enviado para conserto. |
| data_previsao_retorno | DATE | NULL | Prazo estimado para a devolução do equipamento reparado. |
| data_conclusao | DATE | NULL | Data efetiva em que o reparo foi finalizado e testado. |
| status | ENUM('AGUARDANDO_ENVIO', 'EM_ANALISE', 'EM_MANUTENCAO', 'CONCLUIDO', 'CANCELADO', 'SEM_CONSERTO') | NOT NULL, DEFAULT 'EM_MANUTENCAO' | Estágio atual do processo de manutenção. |
| solicitante_ti_id | INT | FK (usuarios.id), NOT NULL | Chave Estrangeira do analista de TI que abriu a solicitação. |
| observacoes | TEXT | NULL | Anotações operacionais complementares sobre o reparo. |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Data e hora de criação do registro no sistema. |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data e hora da última atualização do registro (atualizado automaticamente). |

    Relacionamentos 
    • equipamento_id → equipamentos(id)
    • prestador_servico_id → prestadores_servico(id)
    • solicitante_ti_id → usuarios(id)

## Convenções utilizadas

PK → Primary Key

FK → Foreign Key

NN → Not Null

UQ → Unique

AI → Auto Increment