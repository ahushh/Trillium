<div class="title"><?= $this->__('Rename thread') ?></div>
<form method="post" action="">
    <p>
        <label><?= $this->__('Theme') ?>:<br />
            <input type="text" name="theme" value="<?= $this->theme ?>" />
        </label>
        <?= !empty($this->error) ? '<span class="error">' . $this->error . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="submit" value="<?= $this->__('Save') ?>" />
        <input type="submit" name="cancel" value="<?= $this->__('Cancel') ?>" />
    </p>
</form>