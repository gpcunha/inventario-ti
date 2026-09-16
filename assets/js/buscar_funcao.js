const departamento = document.getElementById('departamento');
const funcao = document.getElementById('funcao_id');
const funcaoSelecionada = funcao.dataset.funcaoSelecionada;

function carregarFuncoes(departamentoId) {
    funcao.innerHTML = '<option value="" disabled selected>Carregando...</option>';

    fetch(`buscar_funcao.php?id=${departamentoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao buscar funções');
            }

            return response.json();
        })
        .then(funcoes => {
            funcao.innerHTML = '';

            if (funcoes.length === 0) {
                funcao.innerHTML =
                    '<option value="" disabled selected>Nenhuma função cadastrada</option>';

                return;
            }

            funcao.innerHTML =
                '<option value="" disabled selected>Selecione uma função</option>';

            funcoes.forEach(item => {
                const option = document.createElement('option');

                option.value = item.id;
                option.textContent = item.nome;

                if (String(item.id) === String(funcaoSelecionada)) {
                    option.selected = true;
                }

                funcao.appendChild(option);
            });
        })
        .catch(error => {
            funcao.innerHTML =
                '<option value="" disabled selected>Erro ao carregar funções</option>';

            console.error(error);
        });
}

departamento.addEventListener('change', function () {
    carregarFuncoes(this.value);
});

if (departamento.value) {
    carregarFuncoes(departamento.value);
}