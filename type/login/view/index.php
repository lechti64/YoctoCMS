<form method="post">
    <div class="form-group">
        <?php echo $this->getForm()->label('username', 'Identifiant'); ?>
        <?php echo $this->getForm()->input('username'); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getForm()->label('password', 'Mot de passe'); ?>
        <?php echo $this->getForm()->input('password', [
            'type' => 'password',
        ]); ?>
    </div>
    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>
