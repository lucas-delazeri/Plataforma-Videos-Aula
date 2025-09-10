<?php
require_once("AcoesVideo.php");
require_once("Conexao.php");

class Video implements AcoesVideo {
    private $id;
    private $titulo;
    private $desc;
    private $avaliacao;
    private $views;
    private $curtidas;
    private $reproduzindo;
    private $estrelas;
    private $addVideo;
    private $dataCriacao;

   
    public function __construct($titulo, $desc, $id = null, $dataCriacao = null)
    {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->desc = $desc;
        $this->avaliacao = 0;
        $this->views = 0;
        $this->curtidas = 0;
        $this->reproduzindo = false;
        $this->estrelas = 0;
        $this->dataCriacao = $dataCriacao 
            ? (is_string($dataCriacao) ? new DateTime($dataCriacao) : $dataCriacao)
            : new DateTime();
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function getTitulo() {
        return $this->titulo;
    }
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }
    public function getDesc() {
        return $this->desc;
    }
    public function setDesc($desc) {
        $this->desc = $desc;
    }
    public function getAvaliacao() {
        return $this->avaliacao;
    }
    public function setAvaliacao($avaliacao) {
        if ($this->views > 0) {
            $this->avaliacao = (($this->avaliacao * ($this->views - 1)) + $avaliacao) / $this->views;
        } else {
            $this->avaliacao = $avaliacao;
        }
    }
    public function getViews() {
        return $this->views;
    }
    public function setViews($views) {
        $this->views = $views;
    }
    public function getCurtidas() {
        return $this->curtidas;
    }
    public function setCurtidas($curtidas) {
        $this->curtidas = $curtidas;
    }
    public function getReproduzindo() {
        return $this->reproduzindo;
    }
    public function setReproduzindo($reproduzindo) {
        $this->reproduzindo = $reproduzindo;
    }
    public function getEstrelas() {
        return $this->estrelas;
    }
    public function setEstrelas($estrelas) {
        $this->estrelas = $estrelas;
    }
    public function getDataCriacao() {
        return $this->dataCriacao;
    }
    public function setDataCriacao($dataCriacao) {
        $this->dataCriacao = is_string($dataCriacao) ? new DateTime($dataCriacao) : $dataCriacao;
    }

    public function like() {
        $this->curtidas++;
    }
    public function pause() {
        $this->reproduzindo = false;
    }
    public function play() {
        $this->reproduzindo = true;
        $this->views++;
    }
    public function addVideo() {
        $this->addVideo = true;
    }
    public function avaliar() {
        $this->estrelas++;
        $this->avaliacao = $this->estrelas / max($this->views, 1);
    }

    public function remover(PDO $pdo) {
        if ($this->id) {
            $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
