<?php
    session_start();
    include '../config/database.php';
    include '../includes/header.php';
    include '../includes/navbar.php';

    if (isset($_SESSION['erro_login'])) {
        echo '<div class="alert alert-danger text-center" role="alert">' . $_SESSION['erro_login'] . '</div>';
        unset($_SESSION['erro_login']);
    }
?>

<main class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card shadow p-4 login-card">
        <h1 class="text-center mb-2">Inventário TI</h1>
            <p class="text-center">Acesse o sistema de gestão de ativos de TI.</p>
        <div class="form-container">
            <div class="col-lg-6 text-center">
                <form action="autenticar.php" method="post">
                    <div class="mb-3">
                        <label class="form-label"> Login </label>
                        <input class="form-control" type="text" name="login" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"> Senha </label>
                        <input class="form-control" type="password" name="senha" required autocomplete="current-password">
                    </div>                    
                    <div class="d-grid">
                    <button class="btn btn-primary" type="submit">Entrar</button>
                </div>
                </form>
            </div>
       </div>
    </div>       
</main>

<?php
    include '../includes/footer.php';
?>