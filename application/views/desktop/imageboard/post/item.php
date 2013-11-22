<div class="item" id="post_<?= $this->id ?>">
    ID: <?= $this->id ?> (<?= $this->time ?>)
    <?= $this->sage ? '<span class="sage">Sage</span>' : '' ?>
    <p>
        <?= $this->image ?>
        <?= $this->text ?>
    </p>
</div>