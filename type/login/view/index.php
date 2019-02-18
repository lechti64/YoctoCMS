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
    <?php echo $this->template->button('password', 'Se connecter', [
        'type' => 'submit',
    ]); ?>
</form>
