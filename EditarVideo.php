<?php
require_once("Conexao.php");
require_once("Video.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("ID do vídeo inválido.");
}

    $videoId = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    
if (!$row) {
        die("Vídeo não encontrado.");
}

    $video = new Video($row['titulo'], $row['descricao'], $row['id']);
    $video->setCurtidas($row['curtidas']);
    $video->setViews($row['views']);
    $video->setAvaliacao($row['avaliacao']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novoTitulo = $_POST['titulo'] ?? $video->getTitulo();
    $novaDescricao = $_POST['descricao'] ?? $video->getDesc();
    $stmt = $pdo->prepare("UPDATE videos SET titulo = ?, descricao = ? WHERE id = ?");
    $stmt->execute([$novoTitulo, $novaDescricao, $videoId]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Vídeo</title>
    <link rel="stylesheet" href="Styles/EditarVideo.css">
</head>
<body>
    <div class="form-edit">
        <h2>Editar Vídeo</h2>
        <form method="post">
            <label>Título:</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($video->getTitulo()); ?>" required>

            <label>Descrição:</label>
            <textarea name="descricao" rows="5" required><?= htmlspecialchars($video->getDesc()); ?></textarea>

            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="index.php" style="display:inline-block; margin-top:10px; text-decoration: none; color: #5858589f;" >Voltar</a>
    </div>
</body>
</html>
