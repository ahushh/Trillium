<div class="mainpage">
    <div class="left"><div class="logo"><a href="<?= $this->url('panel') ?>"></a></div></div>
    <div class="right"><?= $this->content ?></div>
    <?php if (!empty($this->boards)): ?>
        <div class="boards">
            <?php
                foreach ($this->boards as $board):
                    $board['name'] = $this->escape($board['name']);
                    $board['summary'] = $this->escape($board['summary']);
            ?>
                <a title="<?= $board['summary'] ?>" href="<?= $this->url('imageboard.board.view', ['name' => $board['name']])?>">/<?= substr($board['name'], 0, 1) ?>/</a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>