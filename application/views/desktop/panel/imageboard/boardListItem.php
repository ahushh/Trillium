<div class="item">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->board['name']])?>">
        /<?= $this->board['name'] ?>/<?= !empty($this->board['summary']) ? ' - ' . $this->board['summary'] : '' ?>
    </a>
    <div>
        <a href="<?= $this->url('panel.imageboard.board.manage', ['name' => $this->board['name']]) ?>"><?= $this->__('Edit') ?></a>
        <a href="<?= $this->url('panel.imageboard.board.remove', ['name' => $this->board['name']]) ?>"><?= $this->__('Remove') ?></a>
    </div>
</div>