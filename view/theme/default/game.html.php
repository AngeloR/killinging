<?php $player = unserialize($_SESSION['player']); ?>
<!DOCTYPE html>
<html>
	<head>
		<title>Killinging</title>
		<link href='http://fonts.googleapis.com/css?family=Reenie+Beanie|Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/style.css">
	</head>
	<body>
		<table class="thegame">
			<tr>
				<td id="header" colspan="3">
					<h1>Killinging - Make sure they're dead, because sometimes they're not</h1>
					<a href="<?php echo url_for('logout'); ?>">logout</a>
				</td>
			</tr>
			<tr>
				<td class="sidebar">
					<h2><?php echo $player->username; ?>, level <span id="level"><?php echo $player->level; ?></span></h2>
					<table width="100%">
						<tr>
							<th>HP: </th>
							<td><span id="current_hp"><?php echo $player->current_hp.'</span>/<span id="total_hp">'.$player->total_hp; ?></span></td>
							<th>Gold: </th>
							<td id="gold"><?php echo $player->gold; ?></td>
						</tr>
						<tr>
							<th>MP: </th>
							<td><span id="current_mp"><?php echo $player->current_mp.'</span>/<span id="total_mp">'.$player->total_mp; ?></span></td>
							<th>Stone: </th>
							<td id="stone"><?php echo $player->stone; ?></td>
						</tr>
						<tr>
							<th>Exp: </th>
							<td><span id="current_exp"><?php echo $player->current_exp.'</span>/<span id="total_exp">'.$player->exp_to_level(); ?></span></td>
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
					<?php if(isset($notification)) : ?>
					<div id="notification"><?php echo $notification; ?></div>					
					<?php endif; ?>
					<ul id="menu">
						<li><a href="#stats">Stats</a></li><li class="active"></a><a href="#map">Map</a></li><li><a href="#interact">Interact</a></li><li><a href="#build">Build</a></li>
					</ul>
					
					
					<div id="stats">
						<div id="inventory"></div>
					</div>
					
					
					<div id="map">
						<table id="map-holder">
							<tr>
								<td id="map-side" rowspan="3">
									<div id="map-wrapper">
										<div id="map-underlay">
										<?php $map_display = 3; echo $city->draw($player->loc_x,$player->loc_y,$map_display); ?>
										</div>
										<table id="map-overlay">
											<?php 
											
											for($y = ($map_display*-1); $y <= $map_display; ++$y) : ?>
												<tr>
													<?php for($x = ($map_display*-1); $x <= $map_display; ++$x) : ?>
														<td><div class="wrap"><?php 
															if($x == 0 && $y == 0) { ?>
																<span id="player"><img src="tiles/player.png"></span>
															<?php }
		
																$g = $city->at($player->loc_x + $x, $player->loc_y + $y);
																if(!empty($g) && $g) {
																	$g->display();
															}?>
														</div></td>
														<?php endfor; ?>
													</tr>
												<?php endfor; ?>
											</table>
										</div>
								</td>
								<th class="map-title"><?php echo $city->name; ?> <div id="location">(<?php echo $player->loc_x.','.$player->loc_y?>)</div></th>
							</tr>
							<tr>
								<td id="move-side">
									<div id="movement-wrapper">
										<div id="map-arrow-thing">
											<a href="<?php echo url_for('move','nw'); ?>" id="nw"><img src="<?php echo $THEMEDIR; ?>/images/nw.png"></a>
											<a href="<?php echo url_for('move','n'); ?>" id="n"><img src="<?php echo $THEMEDIR; ?>/images/n.png"></a>
											<a href="<?php echo url_for('move','ne'); ?>" id="ne"><img src="<?php echo $THEMEDIR; ?>/images/ne.png"></a>
											<a href="<?php echo url_for('move','w'); ?>" id="w"><img src="<?php echo $THEMEDIR; ?>/images/w.png"></a>
																	
											<a href="<?php echo url_for('move','e'); ?>" id="e"><img src="<?php echo $THEMEDIR; ?>/images/e.png"></a>
											<a href="<?php echo url_for('move','sw'); ?>" id="sw"><img src="<?php echo $THEMEDIR; ?>/images/sw.png"></a>
											<a href="<?php echo url_for('move','s'); ?>" id="s"><img src="<?php echo $THEMEDIR; ?>/images/s.png"></a>
											<a href="<?php echo url_for('move','se'); ?>" id="se"><img src="<?php echo $THEMEDIR; ?>/images/se.png"></a>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td id="fight-side">
									<div id="fight">
										<?php $place = $city->at($player->loc_x,$player->loc_y); 
										if(!$place) : ?>
											<form action="<?php echo url_for('fight');?>" method="post" id="fight-form">
												<select name="monster" id="monster">
													<?php foreach($monsters as $monster):?>
														<?php if($monster->id == $player->last_battled): ?>
														<option value="<?php echo $monster->id; ?>" selected="selected"><?php echo $monster->name; ?></option>
														<?php else: ?>
														<option value="<?php echo $monster->id; ?>"><?php echo $monster->name; ?></option>
														<?php endif; ?>
														
													<?php endforeach; ?>
												</select>
												<button type="submit" id="fight-button">Fight</button>
											</form>
											<div id="fight_notification"></div>
										<?php else: ?>
											<p>You look around, but you don't see anything to fight here...</p>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						</table>
					</div>

					
					<div id="interact">
						<?php if(isset($place) && !empty($place)) : // we are dealing with an interaction point 
							$place->render();
						endif; ?>
						
						
					</div>
					
					<div id="build">
						<?php if ($city->can_build_here($player->loc_x,$player->loc_y)):;// we are dealing with an empty point (claim || forage)?>
							<p>This land is unclaimed. To claim it, you need to build something on it.</p>
							<select name="build" id="building">
								<?php foreach ($buildings as $building):?>
									<option value="<?php echo $building->id; ?>"><?php echo $building->name; ?></option>
								<?php endforeach;?>
							</select>
							<button type="submit">Claim</button> <a href="<?php echo url_for('building','info'); ?>" class="help building-info">info</a>
						<?php elseif($city->player_owns_location($player->loc_x,$player->loc_y)) : 
							$place->management(); ?>
						<?php else: ?>
							<p>This land has already been claimed. You can only claim land that is far enough away from buildings you don't own.</p>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			
			
			<tr>
				<td id="footer" colspan="3">
					I made this &copy;
				</td>
			</tr>
		</table>
	</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.simplemodal.1.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.tabify.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/vader.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/init.js"></script>
</html>