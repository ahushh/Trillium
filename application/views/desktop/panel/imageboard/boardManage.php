<div class="title">
    <a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a>
    &raquo;
    <a href="<?= $this->url('panel.imageboard.board.list') ?>"><?= $this->__('Boards') ?></a>
    &raquo;
    <?= $this->edit ? $this->__('Edit board') : $this->__('Create board') ?>
</div>
<form method="post" action="">
    <?php
    if (!$this->edit) {
        echo '<p><label>' . $this->__('Name') . ':<br />'
            . '<input type="text" name="name" value="' . (isset($this->data['name']) ? $this->escape($this->data['name']) : '') . '" />'
            . '</label>'
            . (!empty($this->error['name']) ? '<span class="error">' . $this->error['name'] . '</span>' : '')
            . '</p>';
    } else {
        echo '<p><strong>' . $this->__('Name') . ':</strong> ' . $this->escape($this->data['name']) . '</p>';
    }
    ?>
    <p>
        <label><?= $this->__('Summary') ?>:<br />
            <input
                type="text"
                name="summary"
                value="<?= isset($this->data['summary']) ? $this->escape($this->data['summary']) : '' ?>" />
        </label>
        <?= !empty($this->error['summary']) ? '<span class="error">' . $this->error['summary'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Bump limit') ?>:<br />
            <input
                type="text"
                name="bump_limit"
                maxlength="3"
                size="3"
                value="<?= isset($this->data['bump_limit']) ? (int)$this->data['bump_limit'] : '' ?>" />
            <small>[100-999]</small>
        </label>
        <?= !empty($this->error['bump_limit']) ? '<span class="error">' . $this->error['bump_limit'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Max file size (Kb)') ?>:<br />
            <input
                type="text"
                name="max_file_size"
                maxlength="5"
                size="5"
                value="<?= isset($this->data['max_file_size']) ? (int) $this->data['max_file_size'] : '' ?>" />
            <small>[1024 - 10240]</small>
        </label>
        <?= !empty($this->error['max_file_size']) ? '<span class="error">' . $this->error['max_file_size'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Images per post') ?>:<br />
            <input
                type="text"
                name="images_per_post"
                maxlength="2"
                size="2"
                value="<?= isset($this->data['images_per_post']) ? (int) $this->data['images_per_post'] : '' ?>" />
            <small>[1 - 10]</small>
        </label>
        <?= !empty($this->error['images_per_post']) ? '<span class="error">' . $this->error['images_per_post'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Thumbnail width') ?> (px):<br />
            <input
                type="text"
                name="thumb_width"
                maxlength="3"
                size="3"
                value="<?= isset($this->data['thumb_width']) ? (int) $this->data['thumb_width'] : '' ?>" />
            <small>[64 - 999]</small>
        </label>
        <?= !empty($this->error['thumb_width']) ? '<span class="error">' . $this->error['thumb_width'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Number of the pages') ?>:<br />
            <input
                type="text"
                name="pages"
                maxlength="2"
                size="2"
                value="<?= isset($this->data['pages']) ? (int) $this->data['pages'] : '' ?>" />
            <small>[1 - 99]</small>
        </label>
        <?= !empty($this->error['pages']) ? '<span class="error">' . $this->error['pages'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Threads per page') ?>:<br />
            <input
                type="text"
                name="threads_per_page"
                maxlength="2"
                size="2"
                value="<?= isset($this->data['threads_per_page']) ? (int) $this->data['threads_per_page'] : '' ?>" />
            <small>[1 - 99]</small>
        </label>
        <?= !empty($this->error['threads_per_page']) ? '<span class="error">' . $this->error['threads_per_page'] . '</span>' : '' ?>
    </p>
    <p>
        <label>
            <input
                type="checkbox"
                name="hidden"
                value="1"<?= isset($this->data['hidden']) && (bool) $this->data['hidden'] === true ? ' checked="checked"' : '' ?> />
            <?= $this->__('Hidden') ?>
        </label>
    </p>
    <p>
        <label><?= $this->__('Limit for create posts by one IP in seconds (0 - unlimited)') ?>:<br />
            <input
                type="text"
                name="ip_seconds_limit"
                size="3"
                maxlength="3"
                value="<?= isset($this->data['ip_seconds_limit']) ? (int) $this->data['ip_seconds_limit'] : '' ?>" />
            <small>[0 - 300]</small>
        </label>
        <?= !empty($this->error['ip_seconds_limit']) ? '<span class="error">' . $this->error['ip_seconds_limit'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="save" value="<?= $this->__('Save') ?>" />
    </p>
</form>