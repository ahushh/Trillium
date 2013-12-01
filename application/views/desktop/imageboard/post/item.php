<div class="item" id="post_<?= $this->post['id'] ?>">
    ID: <?= $this->post['id'] ?> (<?= $this->post['time'] ?>)
    <?= $this->post['sage'] ? '<span class="sage">Sage</span>' : '' ?>
    <?=($this->isGranted('ROLE_ADMIN')
        ? '<a href="' . $this->url('panel.imageboard.post.remove', ['id' => $this->post['id']]) . '">' . $this->__('Remove') . '</a> '
            . '<input type="checkbox" name="posts[]" value="' . $this->post['id'] . '" />'
        : ''
    )?>
    <p>
        <?= $this->image ?>
        <?=(is_array($this->post['video'])
            ? '<div class="playVideo" onclick="showVideo(\'' . $this->post['id'] . '\')">'
                . '<div class="videoTitle" id="videoTitle' . $this->post['id'] . '"">Youtube</div>'
                . '<input id="videoSRC' . $this->post['id'] . '" type="hidden" value="' . $this->post['video']['source'] . '" />'
                . '<img src="' . $this->post['video']['image'] . '" alt="" id="videoIMG' . $this->post['id'] . '" />'
                . '</div>'
            : ''
        )?>
        <?= $this->post['text'] ?>
    </p>
</div>