<div class="title"><?= $this->__('Login') ?></div>
<form action="<?= $this->url('panel_login_check') ?>" method="post">
    <p>
        <label><?= $this->__('Username') ?>:<br />
            <input type="text" name="_username" value="<?= isset($this->last_username) ? $this->escape($this->last_username) : '' ?>" />
        </label><br />
        <label><?= $this->__('Password') ?>:<br />
            <input type="password" name="_password" value="" />
        </label><br />
        <?= isset($this->error) ? '<span class="error">' . $this->error . '</span>' : '' ?>
        <input type="submit" name="send" value="<?= $this->__('Enter') ?>" />
    </p>
</form>