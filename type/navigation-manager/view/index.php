<div class="row">
    <div class="col-md-6">
        <form method="post">
            <div class="text-right mb-2">
                <button id="add" type="button" class="btn btn-secondary btn-sm">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <ul id="navigation-links" class="navigation-links list-group"></ul>
            <button type="submit" class="btn btn-primary mt-3">
                Enregistrer
            </button>
        </form>
    </div>
    <div class="col-md-6 mt-3 mt-md-0">
        <div id="navigation-link-fields" class="card bg-light d-none">
            <div class="card-body">
                <?php echo $this->getForm()->input('edit-uid', [
                    'type' => 'hidden',
                ]); ?>
                <?php echo $this->getForm()->input('edit-icon', [
                    'type' => 'hidden',
                ]); ?>
                <div class="form-group row">
                    <?php echo $this->getForm()->label('edit-icon', 'Icône', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <div class="btn-group" role="group">
                            <button id="edit-icon-delete" type="button" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </button>
                            <button id="edit-icon-dropdown-toggle" type="button"
                                    class="btn btn-secondary dropdown-toggle iconpicker-component"
                                    data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <i id="edit-icon-helper"></i>
                            </button>
                            <div class="dropdown-menu"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <?php echo $this->getForm()->label('edit-title', 'Titre', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getForm()->input('edit-title'); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <?php echo $this->getForm()->label('edit-page-id', 'Page', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getForm()->select('edit-page-id', $this->pages); ?>
                    </div>
                </div>
                <div class="form-group row">
                    <?php echo $this->getForm()->label('', 'Visibilité', [
                        'class' => 'col-lg-3 col-form-label',
                    ]); ?>
                    <div class="col-lg-9">
                        <?php echo $this->getForm()->radio('edit-visibility', 'Hériter de la page', [
                            'id' => 'edit-visibility-inherit',
                            'value' => 'inherit',
                        ]); ?>
                        <?php echo $this->getForm()->radio('edit-visibility', 'Publique', [
                            'id' => 'edit-visibility-public',
                            'value' => 'public',
                        ]); ?>
                        <?php echo $this->getForm()->radio('edit-visibility', 'Privée', [
                            'id' => 'edit-visibility-private',
                            'value' => 'private',
                        ]); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $this->getForm()->checkbox('edit-blank', 'Ouvrir dans une nouvelle fenêtre'); ?>
                </div>
                <button id="edit-delete" type="button" class="btn btn-danger" data-toggle="modal"
                        data-target="#delete-modal">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="delete-modal-label"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-modal-label">Supprimer un lien</h5>
            </div>
            <div class="modal-body">Êtes-vous sûr de vouloir supprimer ce lien ?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="delete-modal-submit">Supprimer</button>
            </div>
        </div>
    </div>
</div>