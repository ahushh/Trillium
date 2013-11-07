<div class="title"><a href="<?= $this->url('panel') ?>"><?= $this->__('Control panel') ?></a> &raquo; <?= $this->__('Change password') ?></div>
<form method="post" action="">
    <p>
        <label><?= $this->__('Old password') ?>:<br />
            <input type="password" name="old_password">
        </label>
        <?= isset($this->error['old_password']) ? '<span class="error">' . $this->error['old_password'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('New password') ?>:<br />
            <input type="password" name="new_password">
        </label>
        <?= isset($this->error['new_password']) ? '<span class="error">' . $this->error['new_password'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Confirm password') ?>:<br />
            <input type="password" name="confirm_password">
        </label>
        <?= isset($this->error['confirm_password']) ? '<span class="error">' . $this->error['confirm_password'] . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="save" value="<?= $this->__('Save') ?>">
    </p>
</form>