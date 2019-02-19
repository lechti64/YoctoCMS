<form method="post">
    <?php echo $this->getTemplate()->input('title', [
        'label' => 'Titre de la page',
        'value' => $this->_page->title,
    ]); ?>
    <?php echo $this->getTemplate()->textarea('content', $this->_type->content); ?>
    <?php echo $this->getTemplate()->button('submit', 'Enregistrer', [
        'type' => 'submit',
    ]); ?>
</form>

