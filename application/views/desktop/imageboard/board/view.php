<div class="title"><?= $this->title ?></div>
<div class="boardViewLeft">
    <?php if (!empty($this->threads)): ?>
        <?= $this->isGranted('ROLE_ADMIN') ? '<form method="post" action="' . $this->url('panel.threads.mass_remove') . '">' : '' ?>
        <div class="list"><?= $this->threads ?></div>
        <?= $this->isGranted('ROLE_ADMIN') ? '<input type="submit" name="remove" value="' . $this->__('Remove') . '" /> </form>' : '' ?>
    <?php else: ?>
        <div class="listEmpty"><?= $this->__('List is empty') ?></div>
    <?php endif; ?>
    <div class="pagination"><?= $this->pagination ?></div>
</div>
<div class="boardViewRight" id="previewContainer">
    <div class="write" id="write"><img src="<?= $this->assets('images/write.png') ?>" alt="write" /></div>
    <div id="messageForm"><?= $this->messageForm ?></div>
    <img src="<?= $this->assets('images/wait.gif') ?>" alt="wait" id="wait" style="display: none;" />
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#write').click(function () {
            previewThread.hideThreads();
            $('#messageForm').slideDown();
        });
    });
</script>