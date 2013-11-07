<div class="item">
    <div>
        <strong><?= $this->__('Username') ?>:</strong> <?= $this->username ?>
    </div>
    <div>
        <strong><?= $this->__('Roles') ?>:</strong> <?= $this->role ?>
    </div>
    <div>
        <a href="<?= $this->url('panel.users.manage', ['name' => $this->username]) ?>"><?= $this->__('Edit') ?></a>
        <a href="<?= $this->url('panel.users.remove', ['name' => $this->username]) ?>"><?= $this->__('Remove') ?></a>
    </div>
</div>