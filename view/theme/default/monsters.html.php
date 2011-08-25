<?php
if(isset($page)) {
  echo $page;
}
else {
?>
  <?php if(isset($monster)) : ?>
    <form action="<?php echo url_for('admin','monsters',$monster->id); ?>" method="post" id="monster">
  <?php else: ?>
    <form action="<?php echo url_for('admin','monsters'); ?>" method="post" id="monster">
    <input type="hidden" name="_method" value="put" id="method">
  <?php endif; ?>
  
  <label>Name: </label>
    <input type="text" name="name" id="name" value="<?php echo (isset($monster))?$monster->name:''?>"><br>
    
  <label>Level: </label>
    <input type="text" name="level" id="level" value="<?php echo (isset($monster))?$monster->level:''?>"> <br>
    
  <label>Vitality: </label>
    <input type="text" name="vit" id="vit" value="<?php echo (isset($monster))?$monster->vit:''?>"> <br>
    
  <label>Strength: </label>
    <input type="text" name="str" id="str" value="<?php echo (isset($monster))?$monster->str:''?>"><br>
    
  <label>Toughness: </label>
    <input type="text" name="tough" id="tough" value="<?php echo (isset($monster))?$monster->tough:''?>"><br>
    
  <label>Agility: </label>
    <input type="text" name="agi" id="agi" value="<?php echo (isset($monster))?$monster->agi:''?>"><br>
    
  <label>Experience Payout: </label>
    <input type="text" name="exp" id="exp" value="<?php echo (isset($monster))?$monster->exp:''?>"><br>
    
  <label>Gold Payout: </label>
    <input type="text" name="gold" id="gold" value="<?php echo (isset($monster))?$monster->gold:''?>"><br>
    
  <label>Total Hit Points: </label>
    <input type="text" name="current_hp" id="current_hp" value="<?php echo (isset($monster))?$monster->current_hp:''?>"><br>
    
  <label>Damage: </label> <span id="damage">Click 'Calculate Stats' to Generate</span><br>
  <label>Defence: </label> <span id="defence">Click 'Calculate Stats' to Generate</span><br>
  
  <button type="button" id="calculate-stat">Calculate Stats</button>
  <button type="submit"><?php echo (isset($monster))?'Save':'Create'; ?></button>
  
  <?php if(isset($monster)) : ?>
    <button type="submit" id="delete-monster" class="secondary-action">Delete</button>
  <?php endif; ?>
<?php } ?>