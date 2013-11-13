<h3><?= $this->title ?></h3>
<form method="post" action="<?= $this->formAction ?>" enctype="multipart/form-data">
    <?php
    if (isset($this->theme)) {
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
        <!-- TODO: Use javascript -->
        <input type="file" name="images[]" value="" /><br />
        <input type="file" name="images[]" value="" /><br />
        <input type="file" name="images[]" value="" /><br />
        <input type="file" name="images[]" value="" />
        <?= !empty($this->error['images']) ? '<span class="error">' . $this->error['images'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="send" value="<?= $this->__('Send') ?>" />
    </p>
</form>