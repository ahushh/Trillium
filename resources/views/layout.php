<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="<?= $this->static('styles.css') ?>" />
    <script type="text/javascript" src="<?= $this->static('scripts.js') ?>"></script>
    <title><?= $this->title ?></title>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="navbar-header"><a class="navbar-brand" href="<?= $this->url('hello') ?>">Trillium</a></div>
    <div class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
            <li><a href="#">Temporary Link</a></li>
        </ul>
    </div>
</nav>
<div class="container">
    <?= $this->content ?>
</div>
</body>
</html>