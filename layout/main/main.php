<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->db->select('setting', 'general', 'title'); ?></title>
    <meta name="description" content="<?php echo $this->db->select('setting', 'general', 'description'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700|Montserrat:100">
    <link rel="stylesheet" href="vendor/normalize/normalize.min.css">
    <link rel="stylesheet" href="layout/main/main.css">
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="layout/main/main.js"></script>
</head>
<body class="body">
<nav class="navbar">
    <div class="navbar__inner">
        <ul class="navbar__nav">
            <?php foreach($this->db->select('nav', 'navbar') as $pageId): ?>
                <li class="navbar__item">
                    <a class="navbar__link <?php if($this->pageId === $pageId): ?>navbar__link--active<?php endif; ?>" href="?pageId=<?php echo $pageId; ?>">
                        <?php echo $this->db->select('page', $pageId, 'title'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a class="navbar__title" href="./">
            <?php echo $this->db->select('setting', 'general', 'title'); ?>
        </a>
    </div>
</nav>
<header class="header">
    <h1 class="header__title">
        <?php echo $this->db->select('page', $this->pageId, 'title'); ?>
    </h1>
</header>
<section class="section">
    <div class="section__inner">
        <?php $this->loadView(); ?>
    </div>
</section>
<footer class="footer">
    <div class="footer__inner">
        Powered by <a class="footer__yocto" href="https://yoctocms.com" target="_blank">Yocto</a>.
    </div>
</footer>
</body>
</html>