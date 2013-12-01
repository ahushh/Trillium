<div class="threadItem">
    <?=($this->isGranted('ROLE_ADMIN') ? '<input type="checkbox" name="threads[]" value="' . $this->thread['id'] . '" />' : '')?>
    <a href="<?= $this->url('imageboard.thread.view', ['id' => $this->thread['id']]) ?>"><?= $this->thread['theme'] ?></a>
    <div onclick="previewThread.run('<?= $this->thread['op'] ?>');"><?= $this->thread['text'] ?></div>
    <span class="threadCreated"><?= $this->__('Created') . ': ' . $this->thread['created'] ?><span>
</div>