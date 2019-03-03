<form method="post">
    <div class="form-group">
        <?php echo $this->getForm()->label('title', 'Titre de la page'); ?>
        <?php echo $this->getForm()->input('title', [
            'value' => $this->_page->title,
        ]); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getForm()->textarea('content', $this->_type->content); ?>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>