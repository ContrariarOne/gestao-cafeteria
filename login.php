<?php
session_start();
include 'conexao.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_input = $_POST['usuario'];
    $senha_input = $_POST['senha'];

    $sql = "SELECT id, nome, usuario, senha FROM usuarios WHERE usuario = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "s", $usuario_input);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($resultado)) {
       
        if ($senha_input === $user['senha']) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            header("Location: painel.php");
            exit;
        } else {
            $erro = "Usuário ou senha incorretos!";
        }
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Gestão Cafeteria</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-top: 15px; color: #666; }
        input { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #4CAF50; color: white; border: none; padding: 10px; margin-top: 20px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #45a049; }
        .erro { color: red; text-align: center; margin-top: 10px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login - Cafeteria</h2>
        <?php if(!empty($erro)) echo "<p class='erro'>$erro</p>"; ?>
        <form method="POST">
            <label>Usuário:</label>
            <input type="text" name="usuario" required>
            
            <label>Senha:</label>
            <input type="password" name="senha" required>
            
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>