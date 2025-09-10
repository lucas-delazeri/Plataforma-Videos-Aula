<?php
require 'conexao.php';

$nome = $email = $idade = $sexo = "";
$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $idade = $_POST['idade'];
    $sexo = $_POST['sexo'];

    // Verifica se o email já existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $mensagem = "⚠️ Este e-mail já está cadastrado!";
    } 
    else {
        // Só insere se não existir
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, idade, sexo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $idade, $sexo]);

        $mensagem = "✅ Cadastro realizado com sucesso!";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="Styles/Cadastro.css">
</head>
<body>
    <div class="box">
        <h2>Cadastro</h2>
        <form method="post">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Idade:</label>
            <input type="number" name="idade" required>

            <label>Sexo:</label>
            <div class="sexo">
                <label><input type="radio" name="sexo" value="Masculino" required> Masculino</label>
                <label><input type="radio" name="sexo" value="Feminino"> Feminino</label>
                <label><input type="radio" name="sexo" value="Outro"> Outro</label>
            </div>

            <button type="submit">Cadastrar</button>
        </form>

        <a href="index.php" class="voltar">Voltar</a>
    </div>

    <!-- resultado do formulário -->
    <?php if ($mensagem): ?>
        <div class="resultado">
            <h3><?= $mensagem ?></h3>
            <p><strong>Nome:</strong> <?= htmlspecialchars($nome) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Idade:</strong> <?= $idade ?></p>
            <p><strong>Sexo:</strong> <?= $sexo ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
