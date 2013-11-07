<div class="mainpage">
    <div class="left">
        <div class="logo"><a href="<?= $this->url('panel') ?>"></a></div>
    </div>
    <div class="right">
        <strong>Trillium</strong> is an imageboard engine, written in PHP &amp; MySQL.<br />
        This engine is based on the <a href="http://silex.sensiolabs.org/">Silex</a> micro framework.<br />
        <a href="http://github.com/Kilte/trillium">Fork</a> me on GitHub.<br />
        <p>
            <strong>Requirements:</strong><br />
            PHP 5.4 or higher. MySQL 5.<br />
        </p>
        <p>
            <strong>Installation:</strong><br />
            1. Unpack.<br />
            2. Run: <code>composer install.</code><br />
            3. Make directory <code>application/cache</code> writable<br />
            4. ???<br />
            5. PROFIT!!!
        </p>
    </div>
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