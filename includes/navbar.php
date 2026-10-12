<?php
$perfil = $_SESSION['usuario_perfil'] ?? '';
$nomeUsuario = $_SESSION['usuario_nome'] ?? 'Usuário';

$isAdmin = $perfil === 'ADMINISTRADOR';
$isTecnico = $perfil === 'TECNICO';
$podeOperar = $isAdmin || $isTecnico;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="/public/index.php">
            Inventário TI
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menuPrincipal"
            aria-controls="menuPrincipal"
            aria-expanded="false"
            aria-label="Abrir ou fechar menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link" href="/public/index.php">
                        Dashboard
                    </a>
                </li>

                <!-- Ativos -->
                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Ativos
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                               href="/modules/equipamentos/index.php">
                                Equipamentos
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="/modules/tipos_componentes/index.php">
                                Tipos de componentes
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                               href="/modules/tipos_equipamento/index.php">
                                Tipos de equipamento
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Operações -->
                
                <?php if ($podeOperar): ?>
                    <!-- Operações -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Operações
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                href="/modules/movimentacoes/index.php">
                                    Movimentações
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                href="/modules/manutencoes/index.php">
                                    Manutenções
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Cadastros -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Cadastros
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item"
                                   href="/modules/departamentos/index.php">
                                    Departamentos e funções
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                               href="/modules/fabricantes/index.php">
                                    Fabricantes
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item"
                                   href="/modules/prestadores_servico/index.php">
                                    Prestadores de serviço
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Usuários -->
                <?php if ($isAdmin): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="/modules/usuarios/index.php">
                            Usuários
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Relatórios -->
                <li class="nav-item">
                    <a class="nav-link" href="/relatorios/index.php">
                        Relatórios
                    </a>
                </li>

            </ul>

            <!-- Usuário conectado -->
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text text-white">
                    <?= htmlspecialchars(
                        $nomeUsuario,
                        ENT_QUOTES,
                        'UTF-8'
                    ); ?>

                    <small class="d-block text-light">
                        <?= htmlspecialchars(
                            $perfil,
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>
                    </small>
                </span>

                <a
                    href="/public/logout.php"
                    class="btn btn-outline-light btn-sm"
                >
                    Sair
                </a>
            </div>

        </div>
    </div>
</nav>
