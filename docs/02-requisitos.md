# Documento de Requisitos

# Projeto

Inventário de Equipamentos de TI

---

# Objetivo

Este documento descreve os requisitos funcionais e não funcionais do sistema de Inventário de Equipamentos de TI.

---

# Requisitos Funcionais

## RF001 - Cadastro de Departamentos

O sistema deverá permitir cadastrar departamentos.

Campos:

- Nome
- Descrição
- Status

---

## RF002 - Cadastro de Usuários

O sistema deverá permitir cadastrar usuários responsáveis pelos equipamentos.

Campos:

- Nome
- Matrícula
- E-mail
- Ramal
- Departamento
- Status

---

## RF003 - Cadastro de Equipamentos

O sistema deverá permitir cadastrar equipamentos de TI.

Campos:

- Patrimônio
- Tipo
- Fabricante
- Modelo
- Número de Série
- Data de Aquisição
- Garantia
- Status
- Departamento
- Usuário Responsável
- Observações

---

## RF004 - Cadastro de Tipos de Equipamentos

O sistema deverá permitir cadastrar categorias.

Exemplos:

- Desktop
- Notebook
- Impressora
- Monitor
- Switch
- Roteador
- Access Point
- Scanner
- Coletor de Dados
- PDV
- Balança
- Leitor de Código de Barras

---

## RF005 - Movimentação de Equipamentos

O sistema deverá registrar toda movimentação realizada.

Informações:

- Equipamento
- Origem
- Destino
- Responsável
- Data
- Observação

---

## RF006 - Registro de Manutenção

O sistema deverá armazenar o histórico de manutenção.

Informações:

- Equipamento
- Defeito
- Diagnóstico
- Solução
- Técnico
- Data de Entrada
- Data de Saída
- Status

---

## RF007 - Pesquisa

O sistema deverá permitir localizar equipamentos por:

- Patrimônio
- Número de Série
- Usuário
- Departamento
- Fabricante
- Modelo

---

## RF008 - Dashboard

O sistema deverá apresentar indicadores.

Exemplos:

- Total de Equipamentos
- Equipamentos em Uso
- Equipamentos em Manutenção
- Equipamentos Disponíveis
- Quantidade por Departamento
- Quantidade por Tipo

---

# Requisitos Não Funcionais

## RNF001

Sistema responsivo.

---

## RNF002

Interface simples e intuitiva.

---

## RNF003

Compatível com os principais navegadores.

---

## RNF004

Banco de Dados MySQL.

---

## RNF005

Backend desenvolvido em PHP.

---

## RNF006

Frontend desenvolvido em HTML, CSS e JavaScript.

---

## RNF007

Código organizado em módulos.

---

## RNF008

Utilização do Git para controle de versões.