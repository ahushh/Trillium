<div>
    <a href="<?= $this->original ?>"><img src="<?= $this->thumbnail ?>" alt="" /></a>
    <div><?= $this->resolution . ' / ' . $this->size . ' / ' . $this->type ?></div>
    <?=($this->isGranted('ROLE_ADMIN')
        ? '<a href="' . $this->url('panel.images.remove', ['id' => $this->id]) . '">' . $this->__('Remove image') . '</a>'
        : ''
    )?>
</div>