<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
include 'conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mensagem = "";

// Processar a atualização (Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_item = $_POST['nome_item'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $preco = str_replace(',', '.', $_POST['preco']);

    $sql = "UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ? WHERE id = ?";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "sssdi", $nome_item, $categoria, $descricao, $preco, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: painel.php");
        exit;
    } else {
        $mensagem = "Erro ao atualizar item.";
    }
}

// Buscar dados atuais do item
$sql = "SELECT * FROM cardapio WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($resultado);

if (!$item) {
    header("Location: painel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Item - Cafeteria</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form { margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #444; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"] { background: #f0ad4e; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button[type="submit"]:hover { background: #ec971f; }
        .btn-voltar { display: inline-block; margin-top: 15px; text-decoration: none; color: #337ab7; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Item do Cardápio</h2>
        <?php if(!empty($mensagem)) echo "<p style='color:red;'>$mensagem</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nome do Item:</label>
                <input type="text" name="nome_item" value="<?php echo htmlspecialchars($item['nome_item']); ?>" required>
            </div>
            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoria" required>
                    <option value="Café" <?php if($item['categoria'] == 'Café') echo 'selected'; ?>>Café</option>
                    <option value="Doce" <?php if($item['categoria'] == 'Doce') echo 'selected'; ?>>Doce</option>
                    <option value="Salgado" <?php if($item['categoria'] == 'Salgado') echo 'selected'; ?>>Salgado</option>
                    <option value="Bebida Gelada" <?php if($item['categoria'] == 'Bebida Gelada') echo 'selected'; ?>>Bebida Gelada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="3"><?php echo htmlspecialchars($item['descricao']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Preço (R$):</label>
                <input type="text" name="preco" value="<?php echo number_format($item['preco'], 2, ',', '.'); ?>" required>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="painel.php" class="btn-voltar">&larr; Voltar ao Painel</a>
    </div>
</body>
</html>