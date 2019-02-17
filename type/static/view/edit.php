<div class="ckeditor"><?php echo $this->db->select('page', $this->pageId, 'content'); ?></div>
<?php echo $this->template->input('title', [
    'label' => 'Title',
    'value' => $this->db->select('page', $this->pageId, 'title'),
]); ?>
<script>
    BalloonEditor
        .create(document.querySelector(".ckeditor"))
        .catch(error => {
            console.error(error);
        });
</script>