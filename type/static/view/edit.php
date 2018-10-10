<div class="ckeditor"><?php echo $this->db->select('page', $this->pageId, 'content'); ?></div>
<script>
    BalloonEditor
        .create(document.querySelector(".ckeditor"))
        .catch(error => {
            console.error(error);
        });
</script>