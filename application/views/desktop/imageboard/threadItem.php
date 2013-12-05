<div class="threadItem">
    <?=($this->isGranted('ROLE_ADMIN') ? '<input type="checkbox" name="threads[]" value="' . $this->thread['id'] . '" />' : '')?>
    <a href="<?= $this->url('imageboard.thread.view', ['id' => $this->thread['id']]) ?>"><?= $this->thread['theme'] ?></a>
    <img
        class="openThread"
        src="<?= $this->assets('images/openThread.png') ?>"
        alt="open"
        onclick="previewThread.run('<?= $this->thread['op'] ?>');" />
    <?= !empty($this->thread['text']) ? '<div>' . $this->thread['text'] . '</div>' : '' ?>
    <span class="threadCreated"><?= $this->__('Created') . ': ' . $this->thread['created'] ?><span>
</div>