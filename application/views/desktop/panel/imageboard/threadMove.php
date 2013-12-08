<div class="title"><?= $this->__('Move thread') ?></div>
<form method="post" action="">
    <p>
        <label><?= $this->__('Choose board') ?>:<br />
            <select name="board">
                <?php
                foreach ($this->boards as $board) {
                    $board = $this->escape($board);
                    echo '<option value="' . $board . '">' . $board . '</option>';
                }
                ?>
            </select>
        </label>
        <?= !empty($this->error) ? '<span class="error">' . $this->error . '</span>' : '' ?>
    </p>
    <p>
        <input type="submit" name="submit" value="<?= $this->__('Move') ?>" />
        <input type="submit" name="cancel" value="<?= $this->__('Cancel') ?>" />
    </p>
</form>