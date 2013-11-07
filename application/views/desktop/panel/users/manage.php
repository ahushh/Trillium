<div class="title">
    <a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a> &raquo;
    <a href="<?= $this->url('panel.users') ?>"><?= $this->__('List of the users') ?></a> &raquo;
    <?= isset($this->title) ? $this->title : '' ?>
</div>
<form method="post" action="">
    <?php
    if ($this->create === true) {
        echo '<p><label>'
            . $this->__('Username') . ':<br />'
            . '<input type="text" name="username" value="' . $this->escape($this->data['username']) . '">'
            . '</label></p>'
            . (isset($this->error['username']) ? '<span class="error">' . $this->escape($this->error['username']) . '</span>' : '');
    } else {
        echo '<p><strong>' . $this->__('Username') . ':</strong> ' . $this->escape($this->data['username']) . '</p>';
    }
    if (!empty($this->roles) && is_array($this->roles) && isset($this->data['roles']) && is_array($this->data['roles'])) {
        echo '<p><label>' . $this->__('Roles') . ':</label><br />';
        foreach ($this->roles as $role) {
            $role = $this->escape($role);
            echo '<input id="' . $role . '" type="checkbox" name="roles[]" value="' . $role . '"'
                . (in_array($role, $this->data['roles']) ? ' checked="checked"' : '') . '>&#160;'
                . '<label for="' . $role . '">' . $this->escape($this->__($role)) . '</label><br />';
        }
        echo (isset($this->error['roles']) ? '<span class="error">' . $this->escape($this->error['roles']) . '</span>' : '')
            . '</p>';
    }
    if ($this->create === true) {
        echo '<p><label>' . $this->__('Password') . ':<br />'
            . '<input type="password" name="password"></label>'
            . (isset($this->error['password']) ? '<span class="error">' . $this->escape($this->error['password']) . '</span>' : '')
            . '</p>';
    }
    ?>
    <p>
        <input type="submit" name="save" value="<?= $this->__('Save') ?>">
    </p>
</form>