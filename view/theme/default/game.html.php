<?php $player = unserialize($_SESSION['player']); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Killinging</title>
		<link href='http://fonts.googleapis.com/css?family=UnifrakturCook:700|Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/style.css">
	</head>
	<body>
		<table class="thegame">
			<tr>
				<th id="header" colspan="2">
					<h1>Killinging <div id="tagline">Protect the people. Save the people.</div></h1>
						<div id="extra-nav">
						<?php
						if($player->admin) { echo '<a href="'.url_for('admin').'" id="admin">admin panel</a>'; }
						?>
						<a href="<?php echo url_for('logout'); ?>" id="logout">logout</a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="sidebar">
					<h2><?php echo $player->username; ?>, the level <span id="level"><?php echo number_format($player->level,0).'</span> '.$player->class_name; ?></h2>
					<table width="100%" id="quick-stats">
						<tr>
							<th>HP: </th>
							<td colspan="3">
								<div class="health progressbar">
                  <div id="hp-progress-bar" class="percent" style="width: <?php echo ($player->current_hp/$player->total_hp)*100; ?>%;"></div>
									<span class="text"> <span id="current_hp"><?php echo number_format($player->current_hp,0).'</span>/<span id="total_hp">'.number_format($player->total_hp,0); ?></span></span>
                </div>
              </td>
						</tr>
						<tr>
							<th>MP: </th>
							<td colspan="3">
								<div class="mp progressbar">
                  <div id="mp-progress-bar" class="percent" style="width: <?php echo ($player->current_mp/$player->total_mp)*100; ?>%;"></div>
									<span class="text"> <span id="current_mp"><?php echo number_format($player->current_mp,0).'</span>/<span id="total_mp">'.number_format($player->total_mp,0); ?></span></span>
                </div>
              </td>
						</tr>
						<tr>
							<th>Exp: </th>
							<td colspan="3">
								<div class="exp progressbar">
                  <div id="exp-progress-bar" class="percent" style="width: <?php echo ($player->current_exp/$player->exp_to_level())*100; ?>%;"></div>
									<span class="text"> <span id="current_exp"><?php echo number_format($player->current_exp,0).'</span>/<span id="total_exp">'.number_format($player->exp_to_level(),0); ?></span> (<span id="exp_percent"><?php echo round($player->current_exp/$player->exp_to_level() * 100); ?></span>%)</span>
                </div>
							</td>
						</tr>
						<tr>
							<th>Gold: </th>
							<td id="gold"><?php echo number_format($player->gold,0); ?></td>
							<th>Stone: </th>
							<td id="stone"><?php echo number_format($player->stone,0); ?></td>
						</tr>
						<tr>
							<th>Copper: </th>
							<td id="copper"><?php echo number_format($player->copper,0); ?></td>
							<th>Tin: </th>
							<td id="tin"><?php echo number_format($player->tin,0); ?></td>
						</tr>
					</table>
					<hr>
					<form action="<?php echo url_for('chat'); ?>" method="post" id="chat-form">
						<input type="text" name="message" id="message"> <button type="submit" id="chat-button">Say</button>
					</form>
					<div id="chat-messages">
					
					</div>
				</td>
				
				<td id="content">
					<iframe name="gameframe" id="gameframe" src="<?php echo url_for('/gameframe'); ?>" border="0" width="700" height="500"></iframe>
				</td>
			</tr>
			<tr>
				<td id="footer" colspan="2">
				 Rising Legends is developed and maintained by <a href="http://xangelo.ca">Angelo R.</a>
				</td>
			</tr>
		</table>
		
	</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/vader.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/init.js"></script>
</html>