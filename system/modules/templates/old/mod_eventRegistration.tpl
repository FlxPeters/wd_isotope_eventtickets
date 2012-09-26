<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>

<?php if($this->error): ?>
    <p class="error"><?php echo $this->error; ?></p>
<?php else: ?>
    <h2><span class="red">Anmeldung zum Kurs </span><span class="black"><?php echo $this->event['title']; ?></span></h2>
    <?php echo $this->form; ?>
<?php endif; ?>
</div>
<!-- indexer::continue -->
