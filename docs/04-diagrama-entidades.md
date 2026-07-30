Departamento
-------------
id
nome
descricao
status

Usuário
-------------
id
nome
matricula
email
ramal
departamento_id
status

TipoEquipamento
-------------
id
nome
descricao

Equipamento
-------------
id
patrimonio
numero_serie
fabricante
modelo
tipo_id
usuario_id
departamento_id
status

Movimentacao
-------------
id
equipamento_id
origem
destino
responsavel
data
observacao

Manutencao
-------------
id
equipamento_id
defeito
diagnostico
solucao
data_entrada
data_saida
status