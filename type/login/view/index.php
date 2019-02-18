<form method="post">
    <?php echo $this->getTemplate()->input('id', [
        'label' => 'Identifiant',
    ]); ?>
    <?php echo $this->getTemplate()->input('password', [
        'label' => 'Mot de passe',
        'type' => 'password',
    ]); ?>
    <?php echo $this->getTemplate()->button('password', 'Se connecter', [
        'type' => 'submit',
    ]); ?>
</form>
