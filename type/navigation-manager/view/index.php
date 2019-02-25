<div class="row">
    <div class="col-md-6">
        <form method="post">
            <div class="text-right mb-2">
                <?php echo $this->getTemplate()->button('<i class="fas fa-plus"></i>', [
                    'class' => 'btn-secondary btn-sm',
                ]); ?>
            </div>
            <ul id="navigation-links" class="navigation-links list-group"></ul>
            <?php echo $this->getTemplate()->button('Enregistrer', [
                'type' => 'submit',
                'class' => 'btn-primary mt-3',
            ]); ?>
        </form>
    </div>
    <div class="col-md-6 mt-3 mt-md-0">
        <div id="navigation-link-fields" class="card bg-light d-none">
            <div class="card-body">
                <?php echo $this->getTemplate()->input('edit-uid', [
                    'type' => 'hidden',
                ]); ?>
                <div class="form-group row">
                    <?php echo $this->getTemplate()->label('edit-title', 'Titre', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getTemplate()->input('edit-title'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <?php echo $this->getTemplate()->label('edit-page-id', 'Page', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getTemplate()->select('edit-page-id', $this->pages); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <?php echo $this->getTemplate()->label('', 'Visibilité', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getTemplate()->radio('edit-visibility', 'Hériter de la page', [
                            'id' => 'edit-visibility-inherit',
                            'value' => 'inherit',
                        ]); ?>
                        <?php echo $this->getTemplate()->radio('edit-visibility', 'Publique', [
                            'id' => 'edit-visibility-public',
                            'value' => 'public',
                        ]); ?>
                        <?php echo $this->getTemplate()->radio('edit-visibility', 'Privée', [
                            'id' => 'edit-visibility-private',
                            'value' => 'private',
                        ]); ?>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <?php echo $this->getTemplate()->checkbox('edit-blank', 'Ouvrir dans une nouvelle fenêtre'); ?>
                </div>
            </div>
        </div>
    </div>
</div>