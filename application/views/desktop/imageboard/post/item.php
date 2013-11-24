<div class="item" id="post_<?= $this->id ?>">
    ID: <?= $this->id ?> (<?= $this->time ?>)
    <?= $this->sage ? '<span class="sage">Sage</span>' : '' ?>
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