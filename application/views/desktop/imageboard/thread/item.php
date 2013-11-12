<div class="item">
    <a href="<?= $this->url('imageboard.thread.view', ['id' => $this->id]) ?>"><?= $this->theme ?></a> (<?= $this->created ?>)
    <p>
        <?= '№' . $this->postID . '<br />' . $this->text ?>
    </p>
</div>