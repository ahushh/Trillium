<div class="title"><?= $this->__('Control panel') ?></div>
<ul class="menu">
    <?php if ($this->isGranted('ROLE_ADMIN')): ?>
        <li><a href="<?= $this->url('panel.imageboard.board.list') ?>"><?= $this->__('Boards') ?></a></li>
        <li><a href="<?= $this->url('panel.users') ?>"><?= $this->__('List of the users') ?></a></li>
        <li><a href="<?= $this->url('panel.mainpage') ?>"><?= $this->__('Homepage') ?></a></li>
    <?php endif; ?>
    <li><a href="<?= $this->url('panel.users.change.password') ?>"><?= $this->__('Change password') ?></a></li>
    <li><a id="logoutOpen" href="javascript://"><?= $this->__('Logout') ?></a></li>
</ul>
<div style="display: none;" id="popup">
    <div>
        <p><?= $this->__('Do you really want to exit?') ?></p>
        <p>
            <a class="button" href="<?= $this->url('panel_logout') ?>"><?= $this->__('Logout') ?></a>
            <a id="logoutClose" class="button" href="javascript://"><?= $this->__('Cancel') ?></a>
        </p>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#popup').dialog({
            autoOpen: false,
            modal: true,
            draggable: false,
            resizable: false,
            title: '<?= $this->__('Logout') ?>'
        });
        $('#logoutOpen').click(function () {
            $('#popup').dialog("open");
        });
        $('#logoutClose').click(function () {
            $('#popup').dialog("close");
        });
    });
</script>