document.addEventListener('DOMContentLoaded', function () {
    const editProductModal = document.getElementById('editProductModal');
    if (editProductModal) {
        editProductModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-bs-id');
            const nome = button.getAttribute('data-bs-nome');
            const preco = button.getAttribute('data-bs-preco');
            const variacoes = button.getAttribute('data-bs-variacoes');
            const estoque = button.getAttribute('data-bs-estoque');

            const modalTitle = editProductModal.querySelector('.modal-title');
            const idInput = editProductModal.querySelector('#edit_produto_id');
            const nomeInput = editProductModal.querySelector('#edit_nome');
            const precoInput = editProductModal.querySelector('#edit_preco');
            const variacoesInput = editProductModal.querySelector('#edit_variacoes_descricao');
            const estoqueInput = editProductModal.querySelector('#edit_estoque_quantidade');

            modalTitle.textContent = 'Editar Produto: ' + nome;
            idInput.value = id;
            nomeInput.value = nome;
            precoInput.value = preco;
            variacoesInput.value = variacoes;
            estoqueInput.value = estoque;
        });
    }

    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.erro) {
                            alert('CEP não encontrado.');
                            document.getElementById('endereco_completo').value = '';
                        } else {
                            const endereco = `${data.logradouro}, ${data.bairro} - ${data.localidade}/${data.uf}`;
                            document.getElementById('endereco_completo').value = endereco;
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CEP:', error);
                        alert('Erro ao buscar CEP. Verifique sua conexão.');
                        document.getElementById('endereco_completo').value = '';
                    });
            } else if (cep.length > 0) {
                alert('CEP inválido.');
                document.getElementById('endereco_completo').value = '';
            }
        });
    }
});