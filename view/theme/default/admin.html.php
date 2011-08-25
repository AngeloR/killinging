<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Rising Legends - Admin</title>
  <link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/theme.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/admin.css" />
</head>

<body>
	<div id="container">
    <div id="header">
      <h2>Rising Legends Admin Panel</h2>
      <div id="topmenu">
            	<ul>
                	<li<?php echo (strpos(request_uri(),'/admin')==0 && request_uri() == '/admin')?' class="current"':''; ?>><a href="<?php echo url_for('admin'); ?>">Dashboard</a></li>
                    <li<?php echo (strpos(request_uri(),'/monsters')==6)?' class="current"':''; ?>><a href="<?php echo url_for('admin','monsters'); ?>">Monsters</a></li>
                    <li><a href="users.html">Users</a></li>
                    <li><a href="#">Items</a></li>
                    <li><a href="#">Buildings</a></li>
                    <li><a href="#">Statistics</a></li>
                    <li><a href="#">Moderation</a></li>
              </ul>
          </div>
      </div>
      <div id="top-panel">
        <div id="panel">
          <ul>
            <?php foreach($submenu as $i => $item): ?>
              <li><a href="<?php echo $item['url']; ?>"><?php echo $item['name']; ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
        <div id="wrapper">
            <div id="content"><?php echo $content; ?></div>
            <div id="sidebar">
              <ul>
                <li><h3><a href="#" class="house">Admin Notifications</a></h3></li>
                <?php foreach($news as $i=>$new): ?>
                <a href="<?php echo url_for('admin','news',$new->id); ?>"><?php echo $new->title; ?></a>
                <?php endforeach; ?>
              </ul>
                
            </div>
      </div>
        <div id="footer">
        <div id="credits">
   		Template by <a href="http://www.bloganje.com">Bloganje</a>
        </div>
       <br />

        </div>
</div>
</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.simplemodal.1.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/vader.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/admin.js"></script>
</html>
