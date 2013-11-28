<script type="text/javascript">
    $(document).ready(function () {
        answers.build();
    });
</script>
<div class="title">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->board]) ?>">/<?= strtoupper($this->board) ?>/</a>
    &raquo;
    <?= $this->theme ?>
</div>
<div class="list"><?= $this->answer ?></div>
<?php if ($this->isGranted('ROLE_ADMIN')): ?>
    <a href="<?= $this->url('panel.threads.remove', ['id' => $this->id]) ?>"><?= $this->__('Remove') ?></a>
    <form method="post" action="<?= $this->url('panel.posts.mass_remove', ['id' => $this->id]) ?>">
<?php endif; ?>
<div class="list"><?= $this->posts ?></div>
<?php if ($this->isGranted('ROLE_ADMIN')): ?>
        <input type="submit" name="remove" value="<?= $this->__('Remove checked') ?>" />
    </form>
<?php endif; ?>