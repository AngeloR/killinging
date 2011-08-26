<?php 

class ChatManager {

  private $msg;
  private $time;
  private $player;

  public function __construct($message) {
    $this->player = unserialize($_SESSION['player']);
    
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
      $this->msg->touser = $this->player->username;
      $this->msg->text = trim($message);
      $this->msg->classification = 0;
    }
    else {
      // send only that user a message saying they failed
      $this->msg->fromuser = 'Server';
      $this->msg->touser = $this->player->username;
      $this->msg->text = 'Your message could not be sent to '.$to;
      $this->msg->classification = 2;
    }

  }

  public function execute() {
		R::store($this->msg);
    return $this->time;
  }
  
  
}
?>