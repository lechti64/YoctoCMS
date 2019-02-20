<form method="post">
    <div class="form-group">
        <?php echo $this->getTemplate()->label('email', 'Email'); ?>
        <?php echo $this->getTemplate()->input('email', [
            'type' => 'email',
        ]); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getTemplate()->label('password', 'Mot de passe'); ?>
        <?php echo $this->getTemplate()->input('password', [
            'type' => 'password',
        ]); ?>
    </div>
    <?php echo $this->getTemplate()->button('submit', 'Se connecter', [
        'type' => 'submit',
    ]); ?>
</form>
