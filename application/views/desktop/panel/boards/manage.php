<div class="title">
    <a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a>
    &raquo;
    <a href="<?= $this->url('panel.boards') ?>"><?= $this->__('Boards') ?></a>
    &raquo;
    <?= $this->edit ? $this->__('Edit board') : $this->__('Create board') ?>
</div>
<form method="post" action="">
    <?php
    if (!$this->edit) {
        echo '<p><label>' . $this->__('Name') . ':<br />'
            . '<input type="text" name="name" value="' . $this->escape($this->data['name']) . '">'
            . '</label>'
            . (!empty($this->error['name']) ? '<span class="error">' . $this->error['name'] . '</span>' : '')
            . '</p>';
    } else {
        echo '<p><strong>' . $this->__('Name') . ':</strong> ' . $this->escape($this->data['name']) . '</p>';
    }
    ?>
    <p>
        <label><?= $this->__('Summary') ?>:<br />
            <input type="text" name="summary" value="<?= $this->escape($this->data['summary']) ?>">
        </label>
        <?= !empty($this->error['summary']) ? '<span class="error">' . $this->error['summary'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="save" value="<?= $this->__('Save') ?>">
    </p>
</form>