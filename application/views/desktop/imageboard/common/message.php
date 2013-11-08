<h3><?= $this->title ?></h3>
<form method="post" action="<?= $this->formAction ?>">
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
        <input type="submit" name="send" value="<?= $this->__('Send') ?>" />
    </p>
</form>