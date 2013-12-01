<div>
    <a href="<?= $this->image['original'] ?>"><img src="<?= $this->image['thumbnail'] ?>" alt="" /></a>
    <div><?= $this->image['resolution'] . ' / ' . $this->image['size'] . ' / ' . $this->image['type'] ?></div>
    <?=($this->isGranted('ROLE_ADMIN')
        ? '<a href="' . $this->url('panel.imageboard.image.remove', ['id' => $this->image['id']]) . '">' . $this->__('Remove image') . '</a>'
        : ''
    )?>
</div>