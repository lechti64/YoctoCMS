<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?php echo $this->_configuration->title; ?></title>
    <meta name="description" content="<?php echo $this->_configuration->description ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700|Montserrat:100">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="layout/main/main.css">
    <?php foreach ($this->vendors as $url => $sri): ?>
        <?php if ($sri): ?>
            <script src="<?php echo $url; ?>" integrity="<?php echo $sri; ?>" crossorigin="anonymous"></script>
        <?php else: ?>
            <script src="<?php echo $url; ?>" crossorigin="anonymous"></script>
        <?php endif; ?>
    <?php endforeach; ?>
</head>
<body class="body">
<nav role="navigation" class="navigation">
    <div class="container">
        <div class="row no-gutters align-items-center justify-content-between">
            <a class="navigation__title col-auto" href="./"><?php echo $this->_configuration->title; ?></a>
            <ul class="navigation__items col-auto">
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
        </div>
    </div>
</nav>
<header class="header">
    <div class="container">
        <h1 class="header__title"><?php echo $this->_page->title; ?></h1>
    </div>
</header>
<section class="section">
    <div class="container">
        <?php $this->loadView(); ?>
    </div>
</section>
<footer class="footer">
    <div class="container">
        Powered by <a class="footer__yocto" href="https://yoctocms.com" target="_blank">Yocto</a>.
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="layout/main/main.js"></script>
</body>
</html>