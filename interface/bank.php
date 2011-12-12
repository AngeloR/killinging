<?php

class BankInterface implements BuildingInterface{
	
	public $bank;
	public $transaction;
	public $owner;
	
	public function __construct($bank) {
		$this->bank = $bank;
	}
	
	public function __get($key) {
		if(isset($this->bank->$key)) {
			return $this->bank->$key;
		}
	}
	
	public function __set($key,$value) {
		if(isset($this->bank->$key)) {
			$this->bank->$key = $value;
		}
	}
	
	public function cost_to_level() {
		$array = array();
	
		$array['cost'] = $this->bank->cost * ($this->bank->level+1);
		$array['stone'] = $this->bank->stone * ($this->bank->level * 1.25);
	
		return $array;
	}
	
	public function display() {
		echo '<img src="/tiles/bank.png" width="48" height="48" class="building">';
	}
	
	public function render() {
		$player = unserialize($_SESSION['player']);
		$this->transaction = R::findOne('transaction','bank_id = ? and player = ?',array($this->bank->id,$player->id));
		?>
		<p>You can bank your gold here for a rate of <?php echo $this->bank->rate; ?>%/day</p>
		<?php if(empty($this->transaction)): ?>
			<p>You have <span id="total-in-bank">0</span> gold stored at this bank.
		<?php else: ?>
			<p>You have <span id="total-in-bank"><?php echo number_format($this->transaction->gold,0); ?></span> gold stored at this bank.
		<?php endif; ?>
		
		<form action="<?php echo url_for('bank',$this->id); ?>" method="post" id="bank-transaction">
		<input type="text" id="transaction-gold" name="transaction-gold"> 
		<select name="action" id="transaction-action">
			<option value="deposit">Deposit</option>
			<option value="withdraw">Withdraw</option>
			<option value="deposit-all">Deposit All</option>
			<option value="withdraw-all">Withdraw All</option>
		</select> 
		<button type="submit">Bank</button>
		</form>
		<?php 
	}
	
	public function management() {
		?>
		<form action="<?php echo url_for('manage',5,$this->id); ?>" method="post">
			<fieldset>
				<legend>Rename</legend>
				<p>You can rename your bank to whatever you want, as long as it falls within our guidelines.</p>
				<label>Rename To: </label><input type="text" name="rename-bank" id="rename-bank" value="<?php echo $this->name; ?>"> <button type="submit">Go!</button>
			</fieldset>
		</form>
		<?php 
	}
}
