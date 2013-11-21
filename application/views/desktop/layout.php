<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="<?= $this->assets('css/styles.css') ?>" />
        <script type="text/javascript" src="<?= $this->assets('js/scripts.js') ?>"></script>
        <title><?= $this->title ?></title>
        <script type="text/javascript">
            $(document).ready(function () {
                var boardsList = $('#boardsList');
                boardsList.dialog({autoOpen: false, modal: true, draggable: false, resizable: false, title: "<?= $this->__('Boards') ?>"});
                $('#boardsListToggle').click(function () {
                    boardsList.dialog("open");
                });
            });
        </script>
    </head>
    <body>
        <div class="header">
            <ul>
                <li><a class="homepage" href="/"><?= $this->__('Homepage') ?></a></li>
                <li><a href="#"><?= $this->__('News') ?></a></li>
                <li><a id="boardsListToggle" href="javascript://"><?= $this->__('Boards') ?></a></li>
                <li><a href="#"><?= $this->__('Information') ?></a></li>
                <li><a href="#"><?= $this->__('Online') ?></a></li>
            </ul>
        </div>
        <div id="boardsList" style="display: none;">
        <?php
            if (!empty($this->boards)) {
                echo '<ul>';
                foreach ($this->boards as $board) {
                    $board['name'] = $this->escape($board['name']);
                    $board['summary'] = $this->escape($board['summary']);
                    echo '<li><a href="' . $this->url('imageboard.board.view', ['name' => $board['name']]) . '">/' . $board['name'] . '/'
                        . (!empty($board['summary']) ? ' - ' . $board['summary'] : '') . '</a></li>';
                }
                echo '</ul>';
            } else {
                echo $this->__('List is empty');
            }
        ?>
        </div>
        <div class="content"><?= $this->content ?></div>
        <div class="footer">Powered by <a href="/">Trillium Engine</a></div>
    </body>
</html>