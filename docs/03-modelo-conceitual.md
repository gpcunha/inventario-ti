# Modelo Conceitual

## Entidades

### Departamento

Representa os setores atribuídas na empresa.

Exemplos:

- TI 
- RH 
- Financeiro 
- Frente de Caixa
- Depósito

---

### Função

Representa a função atribuida a cada departamento na empresa.

Exemplos:

- Assistente de Informático
- Auxíliar de Informática
- Analista de RH
- Auxíliar de RH

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