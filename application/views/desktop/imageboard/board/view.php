<div class="title"><?= $this->title ?></div>
<?= $this->messageForm ?>
<div class="list">
<?=( !empty($this->threads)
    ? '<div class="list">' . $this->threads . '</div>'
    : '<div class="listEmpty">' . $this->__('List is empty') . '</div>'
)?>
</div>
<?= $this->pagination ?>