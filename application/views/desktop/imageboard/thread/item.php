<div class="threadItem">
    <a href="<?= $this->url('imageboard.thread.view', ['id' => $this->id]) ?>"><?= $this->theme ?></a>
    <div onclick="previewThread.run('<?= $this->postID ?>');"><?= $this->text ?></div>
    <span class="threadCreated"><?= $this->__('Created') . ': ' . $this->created ?><span>
</div>