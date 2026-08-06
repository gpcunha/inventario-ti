<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
    include '../includes/header.php';
    include '../includes/navbar.php';
?>

<main class="container mt-4">
    <h1>Dashboard</h1>
    <p>Bem-vindo ao Sistema de Gestão de Ativos de TI.</p>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-primary custom-card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M240-120v-80l40-40H160q-33 0-56.5-23.5T80-320v-440q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v440q0 33-23.5 56.5T800-240H680l40 40v80H240Zm-80-200h640v-440H160v440Zm0 0v-440 440Z"/></svg> Equipamentos</h5>
                    <p class="card-text display-4 text-center"><?php echo $totalEquipamentos = '252';?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-success custom-card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M560-440h200v-80H560v80Zm0-120h200v-80H560v80ZM200-320h320v-22q0-45-44-71.5T360-440q-72 0-116 26.5T200-342v22Zm216.5-183.5Q440-527 440-560t-23.5-56.5Q393-640 360-640t-56.5 23.5Q280-593 280-560t23.5 56.5Q327-480 360-480t56.5-23.5ZM160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm0-80h640v-480H160v480Zm0 0v-480 480Z"/></svg> Usuários</h5>
                    <p class="card-text display-4 text-center"><?php echo $totalUsuarios = '120';?></p>
                </div>
            </div> 
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-warning custom-card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M120-120v-560h160v-160h400v320h160v400H520v-160h-80v160H120Zm80-80h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 320h80v-80h-80v80Zm0-160h80v-80h-80v80Zm0-160h80v-80h-80v80Zm160 480h80v-80h-80v80Zm0-160h80v-80h-80v80Z"/></svg> Departamentos</h5>
                    <p class="card-text display-4 text-center"><?php echo $totalDepartamentos = '10';?></p>
                </div>
            </div>
        </div> 
        <div class="col-md-6 mb-4">
            <div class="card border-danger custom-card">
                <div class="card-body">
                    <h5 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f"><path d="M756-120 537-339l84-84 219 219-84 84Zm-552 0-84-84 276-276-68-68-28 28-51-51v82l-28 28-121-121 28-28h82l-50-50 142-142q20-20 43-29t47-9q24 0 47 9t43 29l-92 92 50 50-28 28 68 68 90-90q-4-11-6.5-23t-2.5-24q0-59 40.5-99.5T701-841q15 0 28.5 3t27.5 9l-99 99 72 72 99-99q7 14 9.5 27.5T841-701q0 59-40.5 99.5T701-561q-12 0-24-2t-23-7L204-120Z"/></svg> Manutenções</h5>
                    <p class="card-text display-4 text-center"><?php echo $totalManutencoes = '12';?></p>
                </div>
            </div>
        </div> 
    </div>
</main>
<?php
    include '../includes/footer.php';
?>