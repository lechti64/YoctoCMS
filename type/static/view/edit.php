<?php echo $this->getTemplate()->input('title', [
    'label' => 'Title',
    'value' => $this->_page->title,
]); ?>
<?php echo $this->getTemplate()->textarea('title', $this->_type->content); ?>