<?php 

class ChatManager {

  private $msg;
  private $time;
  private $player;

  public function __construct($message, $from = null) {
  	if(empty($from)) {
    	$this->player = unserialize($_SESSION['player']);
  	}
  	else {
  		$this->player = $from;
  	}
    
    $this->time = time();
    
    $this->msg = R::dispense('message');
    $this->msg->post_time = $this->time = time();
    $this->msg->fromuser = $this->player->username;
    $this->msg->classification = $this->player->admin;
    
    if(strpos($message,'/m ') === 0) {
      // private message
      $this->private_message($message);
    }
    else {
      // global message
      $this->global_message($message);
    }
  }
  
  public function global_message($message) {
		$this->msg->text = trim($message);
  }
  
  public function private_message($message) {
    $split = explode(' ',$message);
    unset($split[0]);
    $to = $split[1];
    unset($split[1]);
    $message = implode(' ',$split);
    $toplayer = R::findOne('player','username = ?',array($to));
    
    if(!empty($toplayer)) {
	    $this->msg->touser = $toplayer->username;
	    $this->msg->text = trim($message);
	    $this->msg->classification = 0;
    }
    else {
    	if(strtolower($to) == 'server') {
    		// if the user pm's Server it logs a "report" which we can see from
    		// the administration panel. At
    		$this->generate_report($message);
    	}
    	else {
	      // send only that user a message saying they failed
		    $this->msg->fromuser = 'Server';
		    $this->msg->touser = $this->player->username;
	      $this->msg->text = 'User \''.$to.'\' does not exist.';
	     	$this->msg->classification = 2;
    	}
    }
  }
  
  public function generate_report($message) {
  	$report = R::dispense('report');
  	$report->fromuser = $this->msg->fromuser;
  	$report->message = $message;
  	$report->post_time = $this->msg->post_time;
  	
  	$report_id = R::store($report);
  	
  	$this->msg->fromuser = 'Server';
  	$this->msg->touser = $this->player->username;
  	$this->msg->text = 'A report has been filed! Your case is: #'.$report_id;
  	$this->msg->classification = 2;
  	
  	// at some point, an email will be generated and sent out to mods.
  }

  public function execute() {
		R::store($this->msg);
    return $this->time;
  }
  
  
}
?>