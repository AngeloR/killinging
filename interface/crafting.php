<?php

class CraftingInterface implements BuildingInterface{
	
	public $crafting_hall; 
	public $owner; 
	
	public function __construct($crafting_hall) {
		$this->crafting_hall = $crafting_hall;
	}
	
	public function __get($key) {
		if(isset($this->crafting_hall->$key)) {
			return $this->crafting_hall->$key;
		}
	}
	
	public function __set($key,$value) {
		if(isset($this->crafting_hall->$key)) {
			$this->crafting_hall->$key = $value;
		}
	}
	
	public function cost_to_level() {
		$array = array();
		
		$array['cost'] = $this->crafting_hall->cost * ($this->crafting_hall->level+1);
		$array['stone'] = $this->crafting_hall->stone * ($this->crafting_hall->level * 1.25);
		
		return $array;
	}
	
	public function display() {
		echo '<img src="/tiles/crafting_hall.png" width="32" height="32" class="building">';
	}
	
	public function render() {
		$player = unserialize($_SESSION['player']);
		$items = R::getAll('select * from crafting_recipe where level <= ? order by type asc, level asc, name asc, exp asc',array($player->crafting));
		
		$dg = new OPCDataGrid($items);
		$dg->fields(array(
			'name' => 'Name',
			'str' => 'Stats',
			'copper' => 'Cost',
			'id' => '&nbsp;'
		));
		
		$dg->modify('id', function($val,$row){
			return '<a href="'.url_for('craft',$val).'" class="button craft-item">Craft</a>';
		});
		
		$dg->modify('str', function($val,$row){
			$stats = array('total_hp','total_mp','str','tough','agi','luck','vit');
			$stats_display = array('Total HP','Total MP', 'Strength',' Toughness',' Agility','Luc','Vitality');
			$tmp = '';
			foreach($stats as $i => $stat) {
				if($row[$stat] != 0) {
					if($row[$stat] < 0) {
						$tmp .= '<font class="error">'.$row[$stat].' '.$stats_display[$i].'</font><br>';
					}
					else {
						$tmp .= '<font class="success">'.$row[$stat].' '.$stats_display[$i].'</font><br>';
					}
				}
			}	
			return $tmp;
		});
		
		$dg->modify('name', function($val,$row){
			return '<img src="view/theme/default/images/icons/'.$row['icon'].'" width="16" height="16" align="bottom" class="item-icon"> '.$val;
		});
		
		$dg->modify('copper', function($val,$row){
			$costs = array('copper','tin','bar_bronze','iron','bar_cast_iron');
			$tmp = '';
			foreach($costs as $i => $cost) {
				if($row[$cost] != 0) {
					$tmp .= $row[$cost].' '.ucfirst(implode(' ',explode('_',$cost))).'<br>';
				}
			}
			
			return $tmp;
		});
		
		?>
		<h3><?php echo $this->name; ?> Crafting Hall (level: <?php echo $this->level; ?>)</h3>
		<table id="crafting-window">
			<tr>
				<td width="50%" valign="top" id="crafting-resources"><h4>Resources</h4>
					<table width="100%">
						<?php 
						// Bar types
						$bars = R::getAll('select id,name,level,icon,type from crafting_recipe where crafting_recipe.type like "bar_%" order by level asc, name asc');
						$columns = 2;
						foreach($bars as $i => $bar): 
							if($i%2 == 0): ?>
								<tr>
							<?php endif; ?>
								<th><img src="view/theme/default/images/icons/<?php echo $bar['icon']; ?>" class="item-icon"> <?php echo $bar['name']; ?>: </th>
								<td id="bronze_bar"><?php echo (isset($player->$bar['type']))?$player->$bar['type']:0; ?></td>
							<?php if($i%2 != 0): ?>
								</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					</table>	
				</td>
				<td width="50%" valign="top"><h4>Crafting Log</h4><ul id="crafting-log"></ul> </td>
			</tr>
			<tr>
				<td colspan="2" valign="top" id="crafting-recipes">
					<?php $dg->render(); ?>
				</td>
			</tr>
		</table>
		<?php 
	}
	
	public function management() {
		$stats = $this->cost_to_level();
		?>
		<p>This mine is currently at level <?php echo $this->crafting_hall->level; ?></p>
		<p>It will cost <b><?php echo number_format($stats['cost']); ?></b> gold and <b><?php echo number_format($stats['stone']); ?></b> stone to take this building to the next level. 
		<p class="center"><a href="<?php echo url_for('upgrade',$this->crafting_hall->id); ?>" id="building-upgrade">Upgrade to Level <?php echo $this->crafting_hall->level+1; ?></a></p>
		<?php 
	}
}