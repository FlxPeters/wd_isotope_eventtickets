
<?php if(strpos($this->classHeader, 'first')): ?>

<div class="TableHead">
  <div class="TableHead01">Kursstart</div>
  <div class="TableHead02">Daten</div>
  <div class="TableHead03">Details</div>
</div>

<?php endif; ?>

<!--
<tr class="event layout_table <?php echo $this->class; ?>">

    <td class="col_0 kursart">
        <span class="date"><?php echo $this->date; ?></span><br>
        <?php if ($this->lecturer): ?><span class="slotes"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_slots'],$this->evr_slots); ?></span><br><?php endif; ?>
        <?php if($this->hrefRegister): ?>
        <a class="registerLink" href="<?php echo $this->hrefRegister; ?>"
           title="<?php echo $GLOBALS['TL_LANG']['MSC']['evr_register']; ?>">
            <?php echo $GLOBALS['TL_LANG']['MSC']['evr_register']; ?>
        </a>
        <?php endif; ?>

    </td>
    <td class="col_1 kursdaten">
        <? if($this->firstDay): ?><span class="day"><?php echo $this->firstDay; ?></span><?php endif; ?>
        <?php if ($this->time || $this->span): ?><span class="time"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_time'],$this->time); ?></span></span><br><?php endif; ?>
        <?php if ($this->evr_place): ?><span class="street"><?php echo $this->place_street; ?></span><br>
        <span class="city"><?php echo $this->place_city; ?></span><?php endif; ?>
    </td>
    <td class="col_2 details">
        <?php if ($this->evr_scope): ?><span class="scope"><?php echo $this->evr_scope; ?></span><br><?php endif; ?>
         <?php if ($this->evr_price): ?><span class="price"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_price'],$this->evr_price); ?></span><br><?php endif; ?>
         <?php if ($this->lecturer): ?><span class="lecturer"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_lecturer'],$this->lecturer); ?></span><br><?php endif; ?>
    </td>
    <td class="col_3 status">
        <?php echo $GLOBALS['TL_LANG']['MSC']['evr_status'][$this->status]; ?>
    </td>

</tr>
-->


<div class="TableContent">
  <div class="TableContent01">
    <div class="Inside01">
      <strong><?php echo $this->date; ?></strong>
      <div class="ButtonLink01">
        <p><a href="<?php echo $this->hrefRegister; ?>" title="<?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_slots'],$this->evr_slots); ?>"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_slots'],$this->evr_slots); ?></a></p>
      </div>
      <a href="<?php echo $this->hrefRegister; ?>" title="<?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_slots'],$this->evr_slots); ?>"><strong class="BlueText"><img src="tl_files/wsw/img/content/dummy.gif" alt="Icon" class="uiIcon icon-06">&nbsp;Anmelden</strong></a>
    </div>
  </div>

  <div class="TableContent02">
    <div class="Inside02">
      <? if($this->firstDay): ?><span class="day"><?php echo $this->firstDay; ?></span><?php endif; ?>
        <?php if ($this->time || $this->span): ?><span class="time"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_time'],$this->time); ?></span></span><br><?php endif; ?>
        <?php if ($this->evr_place): ?><span class="street"><?php echo $this->place_street; ?></span><br>
        <span class="city"><?php echo $this->place_city; ?></span><?php endif; ?>
    </div>
  </div>

  <div class="TableContent03">
    <div class="Inside03">
        <?php if ($this->evr_scope): ?><span class="scope"><?php echo $this->evr_scope; ?></span><br><?php endif; ?>
        <?php if ($this->evr_price): ?><span class="price"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_price'],str_replace(".",",",$this->evr_price)); ?></span><br><?php endif; ?>
        <?php if ($this->lecturer): ?><span class="lecturer"><?php echo sprintf($GLOBALS['TL_LANG']['MSC']['evr_lecturer'],$this->lecturer); ?></span><br><?php endif; ?>
    </div>
    <div class="StatusSection">
      <img src="tl_files/wsw/img/content/<?php echo $GLOBALS['TL_LANG']['MSC']['evr_status'][$this->status];?>.png" alt="Kurstatus <?php echo $GLOBALS['TL_LANG']['MSC']['evr_status'][$this->status];?>" class="uiImage initImage01">
    </div>
  </div>
</div>

<?php if(strpos($this->class, 'last')): ?>

<?php endif; ?>