<div class="item">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->name])?>">/<?= $this->name ?>/<?= !empty($this->summary) ? ' - ' . $this->summary : '' ?></a>
    <div>
        <a href="<?= $this->url('panel.imageboard.board.manage', ['name' => $this->name]) ?>"><?= $this->__('Edit') ?></a>
        <a href="<?= $this->url('panel.imageboard.board.remove', ['name' => $this->name]) ?>"><?= $this->__('Remove') ?></a>
    </div>
</div>