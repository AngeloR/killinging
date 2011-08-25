<!DOCTYPE html>
<html>
	<head>
		<title>Rising Legends - Make sure they're dead, because sometimes they're not</title>
		<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie|Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/style.css">
	</head>
	<body>
		<h1>Rising Legends - Make sure they're dead, because sometimes they're not</h1>
		
		<table class="thegame">
			<tr>
				<td width="50%">
					<h2>This Side is for logging in and saving the human race.</h2>
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
				</td>
				
				<td width="50%">
					<h2>This is is for signing up to save the human race.</h2>
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
						<select name="class_id">
							<?php 
							foreach($classes as $i => $class) {
								echo '<option value="'.$class->id.'">'.$class->name.'</a>';
							}
							?>
						</select> <a href="#" id="class_info" class="help">about this class</a><br>
						<button type="submit">Sign up</button>
					</form>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<p>The year is tomorrow. The time is now. Or maybe 7:00 am, because it's bright out. But it's also summer. So it is questionable. We no longer have clocks.</p>
					<p>Zombies! They are attacking. With their faces. Turning innocent humans into their zombie kind. You have been chosen by the chooser of people protectors. The CPP. 
					Your job is simple. Make sure that you protect the humans from turning into zombies.</p>
					<p>Kill the uninfected humans before they become infected and save them from becoming infected. From all the humans you are about to save, we salute you soldier.</p>
				</td>
			</tr>
		</table>
		
	</body>
</html>