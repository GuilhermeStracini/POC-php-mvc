<?php
$this->startSection('scripts');
?>
<script>
    console.log('Section scripts loaded.');
    console.log('This is rendered after the main scripts has been loaded.')
</script>
<?php
$this->endSection();
?>
<h1>Welcome to My Application</h1>
<p>This is the home page.</p>
<?php
