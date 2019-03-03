<form method="post">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="form-group">
                        <?php echo $this->getForm()->label('username', 'Identifiant (admin)'); ?>
                        <?php echo $this->getForm()->input('username'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->getForm()->label('password', 'Mot de passe (password)'); ?>
                        <?php echo $this->getForm()->input('password', [
                            'type' => 'password',
                        ]); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
