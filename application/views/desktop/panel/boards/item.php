<div class="item">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->name])?>">/<?= $this->name ?>/<?= !empty($this->summary) ? ' - ' . $this->summary : '' ?></a>
    <div>
        <a href="<?= $this->url('panel.boards.manage', ['name' => $this->name]) ?>"><?= $this->__('Edit') ?></a>
        <a href="<?= $this->url('panel.boards.remove', ['name' => $this->name]) ?>"><?= $this->__('Remove') ?></a>
    </div>
</div>