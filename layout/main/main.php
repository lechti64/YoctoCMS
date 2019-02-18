<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <?php
    $configuration = Yocto\Database::instance('configuration')
        ->where('id', '=', 'configuration')
        ->find();
    ?>
    <title><?php echo $configuration->title; ?></title>
    <meta name="description" content="<?php echo $configuration->description ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700|Montserrat:100">
    <link rel="stylesheet" href="vendor/normalize/normalize.min.css">
    <link rel="stylesheet" href="layout/common/common.css">
    <link rel="stylesheet" href="layout/main/main.css">
    <script src="vendor/jquery/jquery.min.js"></script>
    <?php if ($this->vendors['ckeditor']): ?>
        <script src="vendor/ckeditor/ckeditor.min.js"></script>
    <?php endif; ?>
    <script src="layout/main/main.js"></script>
</head>
<body class="body">
<nav class="navigation">
    <div class="navigation__inner">
        <ul class="navigation__nav">
            <?php
            $navigationItems = Yocto\Database::instance('navigation-item')
                ->orderBy('position', 'ASC')
                ->findAll();
            ?>
            <?php foreach ($navigationItems as $navigationItem): ?>
                <li class="navigation__item">
                    <a class="navigation__link <?php if ($this->_page->id === $navigationItem->id): ?>navigation__link--active<?php endif; ?>" href="?pageId=<?php echo $navigationItem->id; ?>">
                        <?php
                        echo Yocto\Database::instance('page')
                            ->where('id', '=', $navigationItem->id)
                            ->find()
                            ->title;
                        ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a class="navigation__title" href="./">
            <?php echo $configuration->title; ?>
        </a>
    </div>
</nav>
<header class="header">
    <h1 class="header__title">
        <?php echo $this->_page->title; ?>
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