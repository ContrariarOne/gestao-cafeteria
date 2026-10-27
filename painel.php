<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
include 'conexao.php';

$mensagem = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome_item = $_POST['nome_item'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $preco = str_replace(',', '.', $_POST['preco']); // Substitui vírgula por ponto para o banco

    $sql = "INSERT INTO cardapio (nome_item, categoria, descricao, preco, disponivel) VALUES (?, ?, ?, ?, 1)";
    $stmt = mysqli_prepare($conexao, $sql);
    mysqli_stmt_bind_param($stmt, "sssd", $nome_item, $categoria, $descricao, $preco);
    
    if (mysqli_stmt_execute($stmt)) {
        $mensagem = "Item cadastrado com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar item.";
    }
}


$resultado_cardapio = mysqli_query($conexao, "SELECT * FROM cardapio ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Cafeteria</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 15px; }
        .btn-sair { background: #d9534f; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; }
        .btn-sair:hover { background: #c9302c; }
        form { margin-top: 20px; background: #fdfdfd; padding: 20px; border: 1px solid #eee; border-radius: 6px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #444; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button[type="submit"] { background: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button[type="submit"]:hover { background: #45a049; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .msg { padding: 10px; background: #dff0d8; color: #3c763d; margin-top: 15px; border-radius: 4px; }
        .acoes a { padding: 5px 10px; text-decoration: none; border-radius: 3px; color: white; margin-right: 5px; font-size: 14px; }
        .btn-editar { background: #f0ad4e; }
        .btn-editar:hover { background: #ec971f; }
        .btn-excluir { background: #d9534f; }
        .btn-excluir:hover { background: #c9302c; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Painel de Gestão - Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!</h2>
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>

        <?php if(!empty($mensagem)) echo "<div class='msg'>$mensagem</div>"; ?>

        <h3>Cadastrar Novo Item no Cardápio</h3>
        <form method="POST">
            <input type="hidden" name="cadastrar" value="1">
            <div class="form-group">
                <label>Nome do Item:</label>
                <input type="text" name="nome_item" required>
            </div>
            <div class="form-group">
                <label>Categoria:</label>
                <select name="categoria" required>
                    <option value="">Selecione...</option>
                    <option value="Café">Café</option>
                    <option value="Doce">Doce</option>
                    <option value="Salgado">Salgado</option>
                    <option value="Bebida Gelada">Bebida Gelada</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Preço (R$):</label>
                <input type="text" name="preco" placeholder="Ex: 8.50" required>
            </div>
            <button type="submit">Cadastrar Item</button>
        </form>

        <h3>Cardápio Atual</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = mysqli_fetch_assoc($resultado_cardapio)) { ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['nome_item']); ?></td>
                    <td><?php echo htmlspecialchars($item['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($item['descricao']); ?></td>
                    <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                    <td class="acoes">
                        <a href="editar_item.php?id=<?php echo $item['id']; ?>" class="btn-editar">Editar</a>
                        <a href="excluir_item.php?id=<?php echo $item['id']; ?>" class="btn-excluir" onclick="return confirm('Deseja realmente excluir este item?');">Excluir</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>