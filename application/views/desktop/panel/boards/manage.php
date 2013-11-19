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
        <label><?= $this->__('Max file size (Kb)') ?>:<br />
            <input type="text" name="max_file_size" maxlength="5" size="5" value="<?= (int) $this->data['max_file_size'] ?>">
            <small>[1024 - 10240]</small>
        </label>
        <?= !empty($this->error['max_file_size']) ? '<span class="error">' . $this->error['max_file_size'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Images per post') ?>:<br />
            <input type="text" name="images_per_post" maxlength="2" size="2" value="<?= (int) $this->data['images_per_post'] ?>">
            <small>[1 - 10]</small>
        </label>
        <?= !empty($this->error['images_per_post']) ? '<span class="error">' . $this->error['images_per_post'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Thumbnail width') ?> (px):<br />
            <input type="text" name="thumb_width" maxlength="3" size="3" value="<?= (int) $this->data['thumb_width'] ?>">
            <small>[64 - 999]</small>
        </label>
        <?= !empty($this->error['thumb_width']) ? '<span class="error">' . $this->error['thumb_width'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Number of the pages') ?>:<br />
            <input type="text" name="pages" maxlength="2" size="2" value="<?= (int) $this->data['pages'] ?>">
            <small>[1 - 99]</small>
        </label>
        <?= !empty($this->error['pages']) ? '<span class="error">' . $this->error['pages'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Threads per page') ?>:<br />
            <input type="text" name="threads_per_page" maxlength="2" size="2" value="<?= (int) $this->data['threads_per_page'] ?>">
            <small>[1 - 99]</small>
        </label>
        <?= !empty($this->error['threads_per_page']) ? '<span class="error">' . $this->error['threads_per_page'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="save" value="<?= $this->__('Save') ?>">
    </p>
</form>