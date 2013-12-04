<h3><?= $this->__($this->newThread ? 'Create thread' : 'Answer') ?></h3>
<form method="post" action="" enctype="multipart/form-data">
    <?php
    if ($this->newThread) {
        echo '<p><label>'
            . $this->__('Theme') . ':<br />'
            . '<input type="text" name="theme" value="' . $this->escape($this->theme) . '" />'
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
    <?php
    if (!$this->newThread) {
        echo '<p><input type="checkbox" name="sage" value="1" id="sage" /> <label for="sage">' . $this->__('Sage') . '</label></p>';
    }
    ?>
    <p>
        <label><?= $this->__('Images') ?>:</label><br />
        <?php
        for ($i = 0; $i < $this->imagesNumber; $i++) {
            echo '<input type="file" name="images[]" value="" /><br />';
        }
        ?>
        <?= !empty($this->error['images']) ? '<span class="error">' . $this->error['images'] . '</span>' : '' ?>
    </p>
    <p>
        <label><?= $this->__('Video') ?> (Youtube):<br />
            <input type="text" name="video" value="<?= $this->escape($this->video) ?>" />
            <?= !empty($this->error['video']) ? '<span class="error">' . $this->error['video'] . '</span>' : '' ?>
        </label>
    </p>
    <?php if (is_array($this->captcha)): ?>
        <p>
            <img src="<?= $this->captcha['image'] ?>" alt="Captcha" /><br />
            <label><?= sprintf($this->__('Type only the "%s" symbols'), $this->captcha['chars']) ?>:<br />
                <input type="text" name="captcha" value="" />
            </label>
            <?= isset($this->error['captcha']) ? '<span class="error">' . $this->error['captcha'] . '</span>' : '' ?>
        </p>
    <?php endif; ?>
    <p>
        <input type="submit" name="send" value="<?= $this->__('Send') ?>" />
    </p>
</form>