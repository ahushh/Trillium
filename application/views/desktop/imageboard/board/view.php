<div class="title"><?= $this->title ?></div>
<div class="boardViewLeft">
    <?=( !empty($this->threads)
        ? '<div class="list">' . $this->threads . '</div>'
        : '<div class="listEmpty">' . $this->__('List is empty') . '</div>'
    )?>
    <div class="pagination"><?= $this->pagination ?></div>
</div>
<div class="boardViewRight" id="previewContainer">
    <div class="write" id="write"><img src="<?= $this->writeImage ?>" alt="write" /></div>
    <div id="messageForm"><?= $this->messageForm ?></div>
    <img src="<?= $this->waitImage ?>" alt="wait" id="wait" style="display: none;" />
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#write').click(function () {
            previewThread.hideThreads();
            $('#messageForm').slideDown();
        });
    });
</script>