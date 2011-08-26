<!DOCTYPE html>
<html>
	<head>
		<title>Killinging - Protect the people. Save the people</title>
		<link href='http://fonts.googleapis.com/css?family=UnifrakturCook:700|Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/homepage.css">
	</head>
	<body>
		<table class="thegame">
			<tr>
				<th colspan="2">
					<h1>Killinging <div id="tagline">Protect the people. Save the people.</div></h1>
				</th>
			</tr>
			<tr>
				<td colspan="2">
					<p>The year is tomorrow. The time is now. Or maybe 7:00 am, because it's bright out. But it's also summer. So it is questionable. We no longer have clocks.</p>
					<p>Zombies! They are attacking. With their faces. Turning innocent humans into their zombie kind. You have been chosen by the chooser of people protectors. The CPP. 
					Your job is simple. Make sure that you protect the humans from turning into zombies.</p>
					<p>Kill the uninfected humans before they become infected and save them from becoming infected. From all the humans you are about to save, we salute you soldier.</p>
				</td>
			</tr>
			
			<tr>
				<td width="50%">
					<h2>Log in and save the human race.</h2>
					<?php if(isset($login_notification)): ?>
					<div class="error" style="display: block;"><?php echo $login_notification; ?></div>
					<?php endif;?>
					<form action="<?php echo url_for('login'); ?>" method="post">
						<label>Username: </label>
						<input type="text" name="username" id="username"><br>
						<label>Password: </label>
						<input type="password" name="password" id="password"><br>
						<button type="submit">Log In</button>
					</form>
					
					<div id="announcement">
						<p>Killinging is under heavy development at the moment. There will be some bugs
						and probably a whole lot of things that you can't quite do. But that's ok.
						There are daily updates and bug fixes that should help keep things moving. </p>
						<p>I'll try and keep accounts up and work on porting updates instead of wiping,
						but there may be times where that is unavoidable.</p>
					</div>
				</td>
				
				<td width="50%">
					<h2>Sign up to save the human race.</h2>
					<p>You fill all these out, then you get to start saving the humans.</p>
					<?php if(isset($signup_notification)): ?>
					<div class="error" style="display: block;"><?php echo $signup_notification; ?></div>
					<?php endif;?>
					<form action="<?php echo url_for('signup'); ?>" method="post">
						<label>Username: </label>
						<input type="text" name="username" id="username"><br>
						<label>Password: </label>
						<input type="password" name="password" id="password"><br>
						<label>Confirm Password: </label>
						<input type="password" name="confirm" id="confirm"><br>
						<label>Email</label>
						<input type="email" name="email" id="email"><Br>
						<label>Class</label>
						<select name="class_id" id="class_id">
							<?php 
							foreach($classes as $i => $class) {
								echo '<option value="'.$class->id.'">'.$class->name.'</a>';
							}
							?>
						</select>
						<button type="submit">Sign up</button>
						<br><br>
						<div id="info"></div>
					</form>
				</td>
			</tr>
			
			
		</table>
		
	</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script>
	function get_class_info(id) {
		$.ajax({
			url: 'index.php/?/class/'+id
			, dataType: 'json'
			, type: 'get'
			, success: function(res) {
				var tmp = '<h3>'+res.name+'</h3>';
					tmp += '<p>'+res.description+'</p>';
					tmp += '<table class="stats"><tr><th>Vitality</th><td>'+res.vit+'</td>';
					tmp += '<th>Strength</th><td>'+res.str+'</td>';
					tmp += '<th>Toughness</th><td>'+res.tough+'</td>';
					tmp += '<th>Agility</th><td>'+res.agi+'</td></tr>';
					tmp += '<tr><th>Luck</th><td>'+res.luck+'</td>';
					tmp += '<th>Mining</th><td>'+res.mining+'</td>';
					tmp += '<th>Smithing</th><td>'+res.smithing+'</td></tr>';
					tmp += '</table>';
					
				$('#info').html(tmp);
			}
		});
	}
	$(document).ready(function(){
		get_class_info(4);
	});
	$('#class_id').change(function(e){
		get_class_info($(this).val());
	});
	</script>
</html>