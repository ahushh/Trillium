<h3><?= $this->__($this->newThread ? 'Create thread' : 'Answer') ?></h3>
<form method="post" action="" enctype="multipart/form-data">
    <?php
    if ($this->newThread) {
        echo '<p><label>'
            . $this->__('Theme') . ':<br />'
            . '<input type="text" name="theme" value="' . $this->escape($this->theme) . '">'
            . '</label>'
            . (!empty($this->error['theme']) ? '<span class="error">' . $this->error['theme'] . '</span>' : '')
            . '</p>';
    }
    ?>
    <p>
        <label for="text"><?= $this->__('Message') ?>:</label><br />
        <textarea id="text" name="text"><?= $this->escape($this->text) ?></textarea>
        <?= !empty($this->error['text']) ? '<span class="error">' . $this->error['text'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Images') ?>:</label><br />
        <?php
        for ($i = 0; $i < $this->imagesNumber; $i++) {
            echo '<input type="file" name="images[]" value="" /><br />';
        }
        ?>
        <?= !empty($this->error['images']) ? '<span class="error">' . $this->error['images'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="send" value="<?= $this->__('Send') ?>" />
    </p>
</form>