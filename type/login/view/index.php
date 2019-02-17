<form method="post">
    <?php echo $this->template->input('email', [
        'label' => 'Email',
        'value',
    ]); ?>
    <?php echo $this->template->input('password', [
        'label' => 'Mot de passe',
        'type' => 'password',
        'value',
    ]); ?>
    <?php echo $this->template->submit('password', 'Se connecter', [
        'label' => 'Mot de passe',
        'value',
    ]); ?>
</form>
