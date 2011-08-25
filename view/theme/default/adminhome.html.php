<div id="rightnow">
  <h3 class="reallynow">
    <span>Right Now</span><br />
  </h3>
  <p class="youhave">Eventually this will show an overview of who's online.</p>
</div>

<div id="infowrap">
  <div id="infobox">
    <h3>Highest Level</h3>
    <?php echo $levels; ?>
  </div>
  
  <div id="infobox" class="margin-left">
    <h3>Most Gold</h3>
    <?php echo $gold; ?>
  </div>
  
  <div id="infobox">
    <h3>Notifications</h3>
    <form action="<?php echo url_for('admin','news'); ?>" method="post">
    <label>Title</label> <input type="text" name="title"><br>
    <textarea name="news"></textarea><br>
    <button type="submit">Add new notification</button>
    </form>
  </div>
</div>