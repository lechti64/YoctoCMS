<form method="post">
    <?php echo $this->getTemplate()->input('email', [
        'label' => 'Email',
        'type' => 'email',
    ]); ?>
    <?php echo $this->getTemplate()->input('password', [
        'label' => 'Mot de passe',
        'type' => 'password',
    ]); ?>
    <?php echo $this->getTemplate()->button('submit', 'Se connecter', [
        'type' => 'submit',
    ]); ?>
</form>
