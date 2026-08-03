## Sistema de Gestão de Ativos de TI
# Regras de Negócio

Projeto: Sistema de Gestão de Ativos de TI

Versão: 1.0

Autor: Glauco Paiva Cunha

Data: 02/08/2026

---

## Objetivo

Este documento descreve as regras de negócio do Sistema de Gestão de Ativos de TI, estabelecendo as condições, restrições e comportamentos esperados para o funcionamento da aplicação.

## RN-001 – Cadastro de Equipamentos

Todo equipamento cadastrado deverá possuir um número de patrimônio exclusivo.
Não será permitido cadastrar dois equipamentos com o mesmo patrimônio.

## RN-002 – Número de Série

Todo equipamento deverá possuir um número de série único.
O sistema não permitirá equipamentos com números de série duplicados.

## RN-003 – Usuário Responsável

Um equipamento poderá estar vinculado a apenas um usuário por vez.
Um usuário poderá ser responsável por vários equipamentos.

## RN-004 – Departamento

Todo equipamento deverá estar vinculado a um departamento.
Não será permitido deixar o departamento em branco.

## RN-005 – Movimentações

Toda alteração de responsável, departamento ou localização deverá gerar automaticamente um registro na tabela de movimentações.

## RN-006 – Histórico

Nenhuma movimentação poderá ser excluída.
O histórico deverá permanecer disponível para auditoria.

## RN-007 – Equipamento em Manutenção

Equipamentos enviados para manutenção deverão possuir uma Ordem de Serviço ou descrição do defeito.
Durante a manutenção o equipamento não poderá ser atribuído a outro usuário.

## RN-008 – Baixa de Equipamento

Equipamentos com status DESCARTE não poderão receber novas movimentações nem ser atribuídos a usuários.

## RN-009 – Prestadores de Serviço

Somente prestadores cadastrados poderão ser utilizados em registros de manutenção.

## RN-010 – Auditoria

Todas as movimentações deverão registrar:
- responsável pela operação;
- data e hora;
- motivo da movimentação.

## RN-011 – Equipamentos sem patrimônio

Equipamentos permanentes deverão possuir patrimônio.
Equipamentos temporários poderão permanecer sem patrimônio mediante justificativa.

## RN-012 – Histórico de responsável
O sistema deverá manter o histórico completo de todos os responsáveis por um equipamento.
Nenhuma alteração substituirá informações anteriores.

## RN-013 – Troca de Departamento
Toda transferência de equipamento entre departamentos deverá registrar:
- departamento de origem;
- departamento de destino;
- responsável pela movimentação;
- motivo da transferência.

## RN-014 – Backup antes da manutenção

Sempre que possível, equipamentos enviados para manutenção deverão possuir backup dos dados antes da retirada de operação.

## RN-015 – Equipamento reservado
Equipamentos com status RESERVADO não poderão ser atribuídos a outro usuário até que a reserva seja cancelada ou concluída.

## RN-016 – Controle de Garantia

O sistema deverá permitir registrar a data de término da garantia quando disponível. Equipamentos sem garantia deverão possuir o campo em branco.

## RN-017 — Cadastro de Colaboradores

Todo colaborador deverá possuir matrícula única.
O e-mail corporativo, quando informado, deverá ser único no sistema.

## RN-018 – Inativação de Usuários e Desalocação

Usuários com status `INATIVO` (desligados ou afastados) não poderão receber novos equipamentos. Caso um usuário seja inativado, todos os equipamentos vinculados a ele deverão ter seus status alterados automaticamente para `DISPONIVEL` ou `EM ESTOQUE`, gerando um registro automático de movimentação de devolução.

## RN-019 – Exigência de Prestador para Manutenção Externa

Toda manutenção cujo tipo ou solução exija intervenção externa deverá ter obrigatoriamente um prestador de serviço vinculado (`prestador_servico_id`). Não será permitido registrar nota fiscal ou custo externo sem um prestador de serviço válido e com status `ATIVO`.

## RN-020 – Registro Obrigatorio de Custo e Nota Fiscal

Manutenções concluídas que não forem cobertas por garantia (`coberto_por_garantia = 'NAO'`) e que tenham gerado despesas deverão registrar o valor do `custo` e, quando aplicável, o `numero_nota_fiscal` correspondente antes da alteração do status para `CONCLUIDO`.

## RN-021 – Restrição de Exclusão Lógica para Cadastros (Soft Delete)

Tabelas primárias de cadastro (`departamentos`, `usuarios`, `fabricantes`, `tipos_equipamento`, `prestadores_servico`) não poderão ter registros excluídos fisicamente (`DELETE`) caso já possuam históricos vinculados. Nesses casos, o registro deverá ter seu `status` alterado para `INATIVO`.

## RN-022 – Atribuição de Responsável de TI na Movimentação

Toda operação de movimentação ou abertura de manutenção de um equipamento deverá registrar obrigatoriamente o usuário de TI responsável pela ação (`responsavel_ti_id` / `solicitante_ti_id`). O sistema não aceitará operações anônimas.

## RN-023 – Prazo de Previsão de Retorno de Manutenção

Toda manutenção enviada a um prestador externo deverá conter uma `data_previsao_retorno` preenchida no momento da transição para o status `EM_MANUTENCAO`. O sistema deverá emitir alertas para a TI caso a data atual ultrapasse a previsão sem que o status tenha sido alterado para `CONCLUIDO`.

## RN-024 – Atualização Automática de Localização Física

A alteração do campo `departamento_id` ou `usuario_id` em um equipamento deverá obrigar a verificação e atualização da `localizacao_fisica` atual do ativo, garantindo a rastreabilidade exata do local (prédio, sala ou andar) do equipamento.

## RN-025 – Restrição de Transição de Status de Equipamentos

Um equipamento não poderá mudar de status arbitrariamente. As transições válidas devem respeitar o fluxo do ciclo de vida:
- Equipamentos `EM MANUTENCAO` só poderão transitar para `DISPONIVEL`, `EM ESTOQUE` ou `DESCARTE` (em caso de sem conserto);
- Equipamentos em `DESCARTE` são irreversíveis e não poderão retornar para status `DISPONIVEL` ou `EM USO`;

## RN-026 — Exclusividade do Patrimônio

Depois de criado, o patrimônio não poderá ser alterado manualmente, exceto por usuários administradores.

## RN-027 — Equipamento em Uso

Somente equipamentos com status EM USO poderão possuir um usuário responsável.

## RN-028 — Integridade das Movimentações

Uma movimentação deverá possuir pelo menos um destino válido:
- usuário;
- departamento;
- localização.

## RN-029 — Fabricantes

Não será permitido cadastrar dois fabricantes com o mesmo nome.

## RN-030 — Prestadores

Prestadores inativos não poderão receber novas ordens de manutenção.

## RN-031 – Hostname

Quando informado, o hostname deverá ser único para evitar conflitos de identificação na rede.