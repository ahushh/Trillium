<script type="text/javascript">
    $(document).ready(function () {
        answers.build();
    });
</script>
<div class="title">
    <a href="<?= $this->url('imageboard.board.view', ['name' => $this->thread['board']]) ?>">/<?= strtoupper($this->thread['board']) ?>/</a>
    &raquo;
    <?= $this->thread['theme'] ?>
</div>
<?php
if (is_array($this->manageMenu)) {
    foreach ($this->manageMenu as $manageMenuItem) {
        echo '<a href="' . $manageMenuItem['url'] . '">[' . $manageMenuItem['title'] . ']</a> ';
    }
}
if ($this->isGranted('ROLE_ADMIN')) {
    echo '<form method="post" action="' . $this->url('panel.imageboard.post.remove', ['id' => $this->thread['id']]) . '">';
}
// Indicators. TODO: use icons or something else
if ($this->thread['auto_sage_bump'] > 0) {
    echo $this->thread['auto_sage_bump'] == 1 ? '#Autosage# ' : '#Autobump# ';
}
if ($this->thread['attach'] == 1) {
    echo '#Attached# ';
}
if ($this->thread['close'] == 1) {
    echo '#Closed# ';
}
// End of indicators ^^
echo '<div class="list">' . $this->posts . '</div>';
if ($this->isGranted('ROLE_ADMIN')) {
    echo '<input type="submit" name="remove" value="' . $this->__('Remove checked') . '" /></form>';
}
echo ($this->beforeBumpLimit !== null ? '<div>' . $this->__('Before bump limit') . ': ' . $this->beforeBumpLimit . '</div>' : '')
    . '<div class="list">' . $this->answer . '</div>';