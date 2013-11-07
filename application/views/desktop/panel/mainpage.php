<div class="title">
    <a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a>
    &raquo;
    <?= $this->__('Homepage') ?>
</div>
<form method="post" action="">
    <p>
        <label for="text"><?= $this->__('Text') ?></label><br />
        <textarea id="text" name="text"><?= $this->escape($this->text) ?></textarea>
        <?= !empty($this->error) ? '<span class="error">' . $this->error . '</span>' : '' ?>
    </p>
    <p><input type="submit" name="save" value="<?= $this->__('Save') ?>"></p>
</form>