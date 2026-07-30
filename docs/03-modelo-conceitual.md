# Modelo Conceitual

## Entidades

### Departamento

Representa os setores da empresa.

Exemplos:

- TI
- RH
- Financeiro
- Frente de Caixa
- Depósito

---

### Usuário

Representa os colaboradores responsáveis pelos equipamentos.

Cada usuário pertence a um departamento.

---

### Tipo de Equipamento

Classificação dos ativos.

Exemplos:

- Desktop
- Notebook
- Impressora
- Coletor
- PDV
- Balança
- Monitor
- Switch

---

### Equipamento

Representa cada ativo de TI.

Cada equipamento possui:

- um tipo;
- um departamento;
- um usuário responsável.

---

### Movimentação

Armazena o histórico de transferências de equipamentos entre departamentos ou usuários.

---

### Manutenção

Registra todas as manutenções realizadas nos equipamentos.