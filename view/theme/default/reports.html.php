<?php
if(isset($page)) {
  echo $page;
}
else {
?>
  <?php if(isset($user)) : ?>
    <form action="<?php echo url_for('admin','users',$user->id); ?>" method="post" id="user">
  <?php else: ?>
    <form action="<?php echo url_for('admin','users'); ?>" method="post" id="user">
    <input type="hidden" name="_method" value="put" id="method">
  <?php endif; ?>
  
  <label>Username: </label>
    <input type="text" name="username" id="username" value="<?php echo (isset($user))?$user->username:''?>"><br>
    
  <label>Level: </label>
    <input type="text" name="level" id="level" value="<?php echo (isset($user))?$user->level:''?>"> <br>
    
  <label>Vitality: </label>
    <input type="text" name="vit" id="vit" value="<?php echo (isset($user))?$user->vit:''?>"> <br>
    
  <label>Strength: </label>
    <input type="text" name="str" id="str" value="<?php echo (isset($user))?$user->str:''?>"><br>
    
  <label>Toughness: </label>
    <input type="text" name="tough" id="tough" value="<?php echo (isset($user))?$user->tough:''?>"><br>
    
  <label>Agility: </label>
    <input type="text" name="agi" id="agi" value="<?php echo (isset($user))?$user->agi:''?>"><br>
    
  <label>Current Exp: </label>
    <input type="text" name="current_exp" id="current_exp" value="<?php echo (isset($user))?$user->current_exp:''?>"><br>
    
  <label>Gold on Hand: </label>
    <input type="text" name="gold" id="gold" value="<?php echo (isset($user))?$user->gold:''?>"><br>
    
  <label>Hit Points: </label>
    <input type="text" name="current_hp" id="current_hp" value="<?php echo (isset($user))?$user->current_hp:''?>"> / 
    <input type="text" name="total_hp" id="total_hp" value="<?php echo (isset($user))?$user->total_hp:''?>"> 
    <br>
    

  <button type="submit"><?php echo (isset($user))?'Save':'Create'; ?></button>
  
  <?php if(isset($user)) : ?>
    <button type="submit" id="delete-user" class="secondary-action">Delete</button>
  <?php endif; ?>
<?php } ?>