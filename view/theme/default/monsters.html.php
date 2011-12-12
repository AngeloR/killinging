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
  
  <fieldset>
  	<legend>Monster Details</legend>
  
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
	    
	  <label>Luck: </label>
	    <input type="text" name="luck" id="luck" value="<?php echo (isset($monster))?$monster->luck:''?>"><br>
	    
	  <label>Total Hit Points: </label>
	    <input type="text" name="current_hp" id="current_hp" value="<?php echo (isset($monster))?$monster->current_hp:''?>"><br>
	    
	  <label>Damage: </label> <span id="damage">Click 'Calculate Stats' to Generate</span><br>
	  <label>Defence: </label> <span id="defence">Click 'Calculate Stats' to Generate</span><br>
	  
	  <button type="button" id="calculate-stat">Calculate Stats</button>
  </fieldset>
  
 	<fieldset>
 		<legend>Drops</legend>
 		
 		<label>Experience: </label>
	    <input type="text" name="exp" id="exp" value="<?php echo (isset($monster))?$monster->exp:''?>"><br>
	    
	  <label>Gold: </label>
	    <input type="text" name="gold" id="gold" value="<?php echo (isset($monster))?$monster->gold:''?>"><br>
 	</fieldset>
   
  <fieldset>
  	<legend>Location</legend>
  	<label>City: </label>
  	<select name="city" id="city">
  		<?php foreach($cities as $i => $city): ?>
  			<?php if(isset($monster) && $city->id == $monster->city): $selected_city = $city; ?>
  					<option value="<?php echo $city->id; ?>" selected="selected" data-min="<?php echo $monster->min_x.','.$monster->min_y; ?>" data-max="<?php echo $monster->max_x.','.$monster->max_y; ?>"><?php echo $city->name; ?></option>
  			<?php else: ?>
  				<option value="<?php echo $city->id; ?>" data-min="<?php echo $city->min_x.','.$city->min_y; ?>" data-max="<?php echo $city->max_x.','.$city->max_y; ?>"><?php echo $city->name; ?></option>
  			<?php endif; ?>  		
  		<?php endforeach; ?>
  	</select><br>
  	
  	<label>Min Bounds</label>
  		<input type="text" name="min_bounds" id="min_bounds" value="<?php echo (isset($monster))?$monster->min_x.','.$monster->min_y:$city->min_x.','.$city->min_y; ?>"><br>
  		
  	<label>Max Bounds</label>
  		<input type="text" name="max_bounds" id="max_bounds" value="<?php echo (isset($monster))?$monster->max_x.','.$monster->max_y:$city->max_x.','.$city->max_y; ?>">
  </fieldset>
    
  
  <button type="submit"><?php echo (isset($monster))?'Save':'Create'; ?></button>
  
  <?php if(isset($monster)) : ?>
    <button type="submit" id="delete-monster" class="secondary-action">Delete</button>
  <?php endif; ?>
<?php } ?>