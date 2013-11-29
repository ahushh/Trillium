<div class="title">
    <a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a>
    &raquo;
    <?= $this->__('Boards') ?>
</div>
<div><a class="button" href="<?= $this->url('panel.imageboard.board.manage') ?>"><?= $this->__('Create board') ?></a></div>
<?= !empty($this->list) ? '<div class="list">' . $this->list . '</div>' : '<div class="listEmpty">' . $this->__('List is empty') . '</div>' ?>