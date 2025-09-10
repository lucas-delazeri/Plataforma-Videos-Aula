<?php
require_once("Conexao.php");

$mensagem = "";
$h = new DateTime();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $desc = trim($_POST['desc']);

    if (!empty($titulo) && !empty($desc)) {
        $stmt = $pdo->prepare("INSERT INTO videos (titulo, descricao) VALUES (?, ?)");
        $stmt->execute([$titulo, $desc]);
        $mensagem = "✅ Vídeo '$titulo' cadastrado com sucesso!";
    } else {
        $mensagem = "⚠️ Preencha todos os campos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Vídeo</title>
    <link rel="stylesheet" href="Styles/CadastroVideo.css">
</head>
<body>
    <div class="box">
        <h1>Registrar Vídeo</h1>

        <?php if($mensagem): ?>
            <p><?= $mensagem ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Título:</label>
            <input type="text" name="titulo" required>

            <label>Descrição:</label>
            <textarea name="desc" rows="3" required></textarea>

            <button type="submit">Adicionar Vídeo</button>
        </form>

        <a href="index.php" class="voltar">Voltar</a>
    </div>
</body>
</html>
