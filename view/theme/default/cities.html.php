<?php
if(isset($page)) {
  echo $page;
}
else {
?>
  <?php if(isset($city)) : ?>
    <form action="<?php echo url_for('admin','cities',$city->id); ?>" method="post" id="city">
  <?php else: ?>
    <form action="<?php echo url_for('admin','cities'); ?>" method="post" id="user">
    <input type="hidden" name="_method" value="put" id="method">
  <?php endif; ?>
  
  <legend>City Details</legend>
  
	  <label>Name: </label>
	    <input type="text" name="name" id="name" value="<?php echo (isset($city))?$city->name:''?>"><br>
	    
	  <label>Min X: </label>
	    <input type="text" name="min_x" id="min_x" value="<?php echo (isset($city))?$city->min_x:''?>"> <br>
	    
	  <label>Min Y: </label>
	    <input type="text" name="min_y" id="min_y" value="<?php echo (isset($city))?$city->min_y:''?>"> <br>
	    
	  <label>Max X: </label>
	    <input type="text" name="max_x" id="max_x" value="<?php echo (isset($city))?$city->max_x:''?>"><br>
	    
	  <label>Max Y: </label>
	    <input type="text" name="max_y" id="max_y" value="<?php echo (isset($city))?$city->max_y:''?>"><br>

  <button type="submit"><?php echo (isset($city))?'Save':'Create'; ?></button>
  
  <?php if(isset($city)) : ?>
    <button type="submit" id="delete-city" class="secondary-action">Delete</button>
  <?php endif; ?>
<?php } ?>