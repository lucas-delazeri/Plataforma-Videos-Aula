<?php
require_once("Conexao.php");
require_once("Visualizacao.php");
require_once("Video.php");
require_once("Aluno.php");

// Busca vídeos no banco
$videos = [];
$stmt = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $video = new Video($row['titulo'], $row['descricao'], $row['id']);
    $video->setCurtidas($row['curtidas']);
    $video->setViews($row['views']);
    $video->setAvaliacao($row['avaliacao']);
    $videos[] = $video;
}

// Busca alunos do banco ou sessão 
session_start();
if (!isset($_SESSION['alunos'])) {
    $_SESSION['alunos'] = [];
}
$alunos = $_SESSION['alunos'];

// Tratamento de ações 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $acao = $_POST['acao'] ?? null;

    if ($id !== null && isset($videos[$id])) {
        $videoId = $videos[$id]->getId();
        switch ($acao) {
            case 'like':
                $videos[$id]->like();
                $stmt = $pdo->prepare("UPDATE videos SET curtidas = ? WHERE id = ?");
                $stmt->execute([$videos[$id]->getCurtidas(), $videoId]);
                break;

            case 'play':
                $videos[$id]->play();
                $stmt = $pdo->prepare("UPDATE videos SET views = ? WHERE id = ?");
                $stmt->execute([$videos[$id]->getViews(), $videoId]);
                break;

            case 'pause':
                $videos[$id]->pause();
                break;

           case 'remover':
                $videos[$id]->remover($pdo);
                unset($videos[$id]);
                $videos = array_values($videos);
                break;

           case 'avaliar':
                $videos[$id]->avaliar();
                $stmt = $pdo->prepare("UPDATE videos SET avaliacao = ? WHERE id = ?");
                $stmt->execute([$videos[$id]->getAvaliacao(), $videoId]);
                break;
            
            case 'editar':
                header("Location: EditarVideo.php?id=" . $videoId);
                exit;

        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Plataforma de Vídeos</title>
    <link rel="stylesheet" href="Styles/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>🎬 Plataforma de Vídeos</h1>
        <a href="CadastroVideo.php">
            <button class="add-video">➕ Adicionar Vídeo</button>
        </a>
        <a href="Cadastro.php">
            <button class="Cadastro-aluno">Cadastrar Aluno</button>
        </a>
    </header>

    <!-- Lista os vídeos -->
    <?php foreach ($videos as $i => $v): ?>
        <div class="video-card">
            <h2><?= htmlspecialchars($v->getTitulo()); ?></h2>
            <p><?= htmlspecialchars($v->getDesc()); ?></p>
            <small>Criado em: <?= $v->getDataCriacao() instanceof DateTime ? $v->getDataCriacao()->format("d/m/Y H:i:s") : $v->getDataCriacao(); ?></small>

            <div class="stats">
                👍 Curtidas: <?= $v->getCurtidas(); ?> |
                👀 Views: <?= $v->getViews(); ?> |
                ⭐ Avaliação: <?= (is_numeric($v->getAvaliacao()) && $v->getAvaliacao() > 0) ? number_format($v->getAvaliacao(), 1) : "Sem avaliações"; ?> |
                🎵 Reproduzindo: <?= $v->getReproduzindo() ? "Sim" : "Não"; ?>
            </div>

            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $i ?>">
                <input type="hidden" name="acao" value="play">
                <button class="btn play">▶ Play</button>
            </form>

            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $i ?>">
                <input type="hidden" name="acao" value="pause">
                <button class="btn pause">⏸ Pausar</button>
            </form>

            <form method="post" style="display:inline;">
                <input type="hidden" name="id" value="<?= $i ?>">
                <input type="hidden" name="acao" value="like">
                <button class="btn like">❤️ Like</button>
            </form>

             <form method="post" style="display:inline;" onsubmit="return confirmarEdicao()">
                <input type="hidden" name="id" value="<?= $i ?>">
                <input type="hidden" name="acao" value="editar">
                <button class="btn editar">Editar</button>
            </form>
         
            <form method="post" style="display:inline;" onsubmit="return confirmarRemocao()">
                <input type="hidden" name="id" value="<?= $i ?>">
                <input type="hidden" name="acao" value="remover">
                <button class="btn remover">Remover</button>
            </form>

<script>
function confirmarRemocao() {
    return confirm("Você realmente deseja remover este vídeo?");
}

function confirmarEdicao() {
    return confirm("Deseja realmente editar este vídeo?");
}

document.addEventListener("DOMContentLoaded", 
    function() {
        const btn = document.getElementById("toggle-alunos");
        if (btn) {
            btn.addEventListener("click", function() {
                const hiddenAlunos = document.querySelectorAll(".hidden-aluno");
                const isHidden = hiddenAlunos[0].style.display === "none" || hiddenAlunos[0].style.display === "";

                hiddenAlunos.forEach(li => {
                    li.style.display = isHidden ? "list-item" : "none";
                });

                btn.textContent = isHidden ? "Mostrar menos" : "Mostrar mais";
            });
        }
    }
);


    </script>
</div>

<?php endforeach; ?>

    <!-- Lista os alunos cadastrados -->
    <h2>👤 Alunos cadastrados</h2>

<ul id="lista-alunos">
    <?php
    if (!empty($alunos)) {
        $i = 0;
        foreach ($alunos as $aluno) {
            $classe = $i > 2 ? " class='hidden-aluno'" : "";
            echo "<li{$classe}>" . htmlspecialchars($aluno->getNome()) .
                 " (" . htmlspecialchars($aluno->getLogin()) . ") - Idade: " . htmlspecialchars($aluno->getIdade()) . "</li>";
            $i++;
        }
        if (count($alunos) > 4) {
            echo "<button id='toggle-alunos' class='btn mostrar-mais'>Mostrar mais</button>";
        }
    } else {
        echo "<li>Nenhum aluno cadastrado.</li>";
    }
    ?>
</ul>

</div>
</body>
</html>
