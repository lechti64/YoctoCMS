<form method="post">
    <div class="form-group">
        <?php echo $this->getTemplate()->label('title', 'Titre de la page'); ?>
        <?php echo $this->getTemplate()->input('title', [
            'value' => $this->_page->title,
        ]); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getTemplate()->textarea('content', $this->_type->content); ?>
    </div>
    <?php echo $this->getTemplate()->button('submit', 'Enregistrer', [
        'type' => 'submit',
    ]); ?>
</form>

