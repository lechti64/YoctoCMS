<form method="post">
    <div class="form-group">
        <?php echo $this->getTemplate()->label('username', 'Identifiant'); ?>
        <?php echo $this->getTemplate()->input('username'); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getTemplate()->label('password', 'Mot de passe'); ?>
        <?php echo $this->getTemplate()->input('password', [
            'type' => 'password',
        ]); ?>
    </div>
    <?php echo $this->getTemplate()->button('Se connecter', [
        'type' => 'submit',
    ]); ?>
</form>
