<!DOCTYPE html>
<html>
	<head>
		<link href='http://fonts.googleapis.com/css?family=UnifrakturCook:700|Droid+Sans' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="<?php echo $THEMEDIR; ?>/css/style.css">
	</head>
	<body>
		<?php if(isset($notification)) : ?>
			<div id="notification"><?php echo $notification; ?></div>					
		<?php endif; ?>

		<ul id="menu">
			<li><a href="#stats">Stats</a></li><li class="active"><a href="#map">Map</a></li><li><a href="#interact">Interact</a></li><li><a href="#build">Build</a></li>
		</ul>
							
							
		<div id="stats" class="tab">
			<table class="stats-tab">
				<tr>
					<td rowspan="2" valign="top">
						<table class="stats">
							<tr>
								<th>Vitality:</th><td><span id="vit"><?php echo $player->vit; ?></span></td>
								<td><a href="<?php echo url_for('skill','vit'); ?>" class="skillup">+1</a></td>
								<th>Damage: </th><td><span id="damage"><?php echo $player->damage_uncalc(); ?></span></td>
							</tr>
							<tr>
								<th>Strength:</th><td><span id="str"><?php echo $player->str; ?></span></td>
								<td><a href="<?php echo url_for('skill','str'); ?>" class="skillup">+1</a></td>
								<th>Defence: </th><td><span id="defence"><?php echo $player->defence(); ?></span></td>
							</tr>
							<tr>
								<th>Toughness:</th><td><span id="tough"><?php echo $player->tough; ?></span></td>
								<td><a href="<?php echo url_for('skill','tough'); ?>" class="skillup">+1</a></td>
							</tr>
							<tr>
								<th>Agility:</th><td><span id="agi"><?php echo $player->agi; ?></span></td>
								<td><a href="<?php echo url_for('skill','agi'); ?>" class="skillup">+1</a></td>
							</tr>
							<tr>
								<th>Luck:</th><td><span id="luck"><?php echo $player->luck; ?></span></td>
								<td><a href="<?php echo url_for('skill','luck'); ?>" class="skillup">+1</a></td>
							</tr>
							<tr>
								<th>Skill Points:</th><td></td><td><span id="skill_points"><?php echo $player->skill_points; ?></span></td>
							</tr>
						</table>
						<hr>
						<table class="stats">
							<tr>
								<th>Crafting</th>
								<td><span id="crafting"><?php echo number_format($player->crafting,0); ?></span> - (<span id="crafting-current-exp"><?php echo number_format($player->crafting_exp,0).'</span>/<span id="crafting-total-exp">'.number_format($player->exp_to_crafting(),0); ?></span> exp)</td>
							</tr>
							<tr>
								<th>Mining: </th><td><span id="mining"><?php echo number_format($player->mining,0); ?></span> - (<span id="mining-current-exp"><?php echo number_format($player->mining_exp,0).'</span>/<span id="mining-total-exp">'.number_format($player->exp_to_mining(),0);?></span> exp)</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table class="inventory-section">
							<tr>
								<th>Helm</th>
								<td>
								<select name="equip-helm" id="equip-helm" class="item">
									<?php foreach($helms as $i => $helm): ?>
									<option value="<?php echo $helm['id']; ?>"><?php echo $helm['name']; ?></option>
									<?php endforeach; ?>
								</select>
								</td>
								<td>
									<a href="#" class="small button">info</a>
								</td>
							</tr>
							<tr>
								<th>Shoulders</th>
							</tr>
							<tr>
								<th>Body</th>
							</tr>
							<tr>
								<th>Left Hand</th>
								<td>
									<select name="equip-left-hand" id="equip-left-hand" class="item">
										<?php foreach($weapons as $i => $weapon): ?>
										<option value="<?php echo $weapon['id']; ?>"><?php echo $weapon['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<a href="#" class="small button">equip</a>
								</td>
							</tr>
							<tr>
								<th>Right Hand</th>
								<td>
									<select name="equip-right-hand" id="equip-right-hand" class="item">
										<?php foreach($weapons as $i => $weapon): ?>
										<option value="<?php echo $weapon['id']; ?>"><?php echo $weapon['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<a href="#" class="small button">equip</a>
								</td>
							</tr>
							<tr>
								<th>Legs</th>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<!-- This is where the info about the item is loaded whenever you change your item -->
					</td>
				</tr>
			</table>
		</div>
					
					
					<div id="map" class="tab">
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
										<div class="clear"></div>
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
										<?php $place = $city->at($player->loc_x,$player->loc_y); ?>
										<?php if(!$place && !empty($players) && count($players) >= 1): ?>
										<form action="<?php echo url_for('pvp'); ?>" method="post" id="pvp-form">
											<p><select name="player_id">
												<?php foreach($players as $p): ?>
												<option value="<?php echo $p->id; ?>"><?php echo $p->username.' ('.$p->level.')'; ?></option>
												<?php endforeach; ?>
											</select>
											<button type="submit" id="pvp-button">Attack</button></p>
										</form>
										<?php endif; ?>
										<?php if(!$place && isset($monster)) : ?>
											<form action="<?php echo url_for('fight');?>" method="post" id="fight-form">
												<p>You were attacked by a <?php echo $monster->name; ?> (<?php echo $monster->level; ?>)! <button type="submit" id="fight-button">Fight</button></p>
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

					
					<div id="interact" class="tab">
						<?php if(isset($place) && !empty($place)) : // we are dealing with an interaction point 
							$place->render();
						endif; ?>
						
						
					</div>
					
					<div id="build" class="tab">
						<?php if ($city->can_build_here($player->loc_x,$player->loc_y)):;// we are dealing with an empty point (claim)?>
							<p>This land is unclaimed. To claim it, you need to build something on it.</p>
							<form action="<?php echo url_for('building'); ?>" method="post" id="purchase-building">
								<select name="build" id="building">
									<?php foreach ($buildings as $building):?>
										<option value="<?php echo $building->id; ?>"><?php echo $building->name; ?></option>
									<?php endforeach;?>
								</select>
								<button type="submit">Claim</button> <a href="<?php echo url_for('building','info'); ?>" class="help building-info">info</a>
							</form>
						<?php elseif($city->player_owns_location($player->loc_x,$player->loc_y)) : 
							$place->management(); ?>
						<?php else: ?>
							<p>This land has already been claimed. You can only claim land that is far enough away from buildings you don't own.</p>
						<?php endif; ?>
					</div>
	</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.simplemodal.1.4.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.gritter.min.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.tabify.js"></script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/jquery.progressbar.min.js"></script>
	<script type="text/javascript">
	<?php
		if(isset($gamemessages)) : ?>
			window.gamemessages = [];
			<?php foreach($gamemessages as $message) : ?>		
				window.gamemessages.push('<?php echo $message; ?>');
			<?php endforeach; ?>
		<?php endif; ?>
	</script>
	<script type="text/javascript" src="<?php echo $THEMEDIR; ?>/js/gameframe.js"></script>
</html>
