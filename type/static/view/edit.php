<div class="ckeditor"><?php echo $this->_page->content; ?></div>
<?php echo $this->template->input('title', [
    'label' => 'Title',
    'value' => $this->_page->title,
]); ?>
<script>
    BalloonEditor
        .create(document.querySelector(".ckeditor"))
        .catch(error => {
            console.error(error);
        });
</script>