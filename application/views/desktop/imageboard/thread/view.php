<script type="text/javascript">
    $(document).ready(function () {
        answers.build();
    });
</script>
<div class="title">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->board]) ?>">/<?= strtoupper($this->board) ?>/</a>
    &raquo;
    <?= $this->theme ?>
</div>
<?=($this->isGranted('ROLE_ADMIN')
    ? '<a href="' . $this->url('panel.threads.remove', ['id' => $this->id]) . '">' . $this->__('Remove') . '</a>'
    : ''
)?>
<div class="list"><?= $this->answer ?></div>
<div class="list"><?= $this->posts ?></div>