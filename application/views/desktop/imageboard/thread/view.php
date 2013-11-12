<div class="title">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->board]) ?>">/<?= strtoupper($this->board) ?>/</a>
    &raquo;
    <?= $this->theme ?>
</div>
<div class="list"><?= $this->answer ?></div>
<div class="list"><?= $this->posts ?></div>