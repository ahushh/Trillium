<div class="item" id="post_<?= $this->id ?>">
    ID: <?= $this->id ?> (<?= $this->time ?>)
    <?= $this->sage ? '<span class="sage">Sage</span>' : '' ?>
    <?=($this->isGranted('ROLE_ADMIN')
        ? '<a href="' . $this->url('panel.imageboard.post.remove', ['id' => $this->id]) . '">' . $this->__('Remove') . '</a> '
            . '<input type="checkbox" name="posts[]" value="' . $this->id . '" />'
        : ''
    )?>
    <p>
        <?= $this->image ?>
        <?=(is_array($this->video)
            ? '<div class="playVideo" onclick="showVideo(\'' . $this->id . '\')">'
                . '<div class="videoTitle" id="videoTitle' . $this->id . '"">Youtube</div>'
                . '<input id="videoSRC' . $this->id . '" type="hidden" value="' . $this->video['source'] . '" />'
                . '<img src="' . $this->video['image'] . '" alt="" id="videoIMG' . $this->id . '" />'
                . '</div>'
            : ''
        )?>
        <?= $this->text ?>
    </p>
</div>