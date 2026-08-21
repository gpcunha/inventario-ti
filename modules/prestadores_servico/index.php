<?php
    session_start();

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../../public/login.php');
        exit();
    }

    include '../../config/database.php';
    include '../../includes/header.php';
    include '../../includes/navbar.php';

    if (isset($_SESSION['sucesso_prestadorServico'])){
?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['sucesso_prestadorServico']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php
        unset($_SESSION['sucesso_prestadorServico']); 
        }
        if(isset($_SESSION['erro_prestadorServico'])){
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['erro_prestadorServico']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
        unset($_SESSION['erro_prestadorServico']);
        }
        $query = "SELECT id, nome, cnpj, telefone, email, endereco, observacoes, status, created_at FROM prestadores_servico ORDER BY nome ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $prestadores_servico = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <main class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Prestadores de Serviço</h1>
            <a href="create.php" class="btn btn-primary">Adicionar Prestador de Serviço</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Endereço</th>
                        <th>Observação</th>
                        <th>Status</th>
                        <th>Criação em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($prestadores_servico)): ?>
                        <tr>
                            <td colspan="10" class="text-center">Prestador de Serviço não cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prestadores_servico as $prestador_servico): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars((string)$prestador_servico['id'])  ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($prestador_servico['nome']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($prestador_servico['cnpj']); ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($prestador_servico['telefone']); ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($prestador_servico['email']); ?>
                        </td>                        
                        <td>
                            <?= htmlspecialchars($prestador_servico['endereco']); ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($prestador_servico['observacoes'] ?:'Não informado') ?>
                        </td>                        

                        <td>
                            <?php if ($prestador_servico['status'] === 'ATIVO'): ?>
                                <span class="badge text-bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inativo</span>
                                <?php endif; ?>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($prestador_servico['created_at'])); ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= (int) $prestador_servico['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <?php if ($prestador_servico['status'] === 'ATIVO'): ?>
                                <form action="inactivate.php" method="post" class="d-inline" onsubmit="return confirm('Deseja inativar este Prestador de Serviço?');">
                                            <input type="hidden" name="id" value="<?= (int) $prestador_servico['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Inativar</button>
                                        </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
<?php include '../../includes/footer.php'; ?>

