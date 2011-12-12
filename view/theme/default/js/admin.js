sandbox.register_module('monster', util.extend({
  title: 'Monster Manager'
  , description: 'Handles the monster form'
  , can_save: false
  , update_stats: function() {
    var str = parseInt($('#str').val())
      , agi = parseInt($('#agi').val())
      , vit = parseInt($('#vit').val())
      , tough = parseInt($('#tough').val()) 
      , damage = function() {

    		var $damage = Math.floor($('#str').val()/3)
    			, $runs = Math.floor($('#str').val()/6);

    		if($runs == 0) {
    			return str + ' + 0d6';
    		}
			return $damage + ' + '+ $runs+'d6';
	    }
      , defence = function() {
    	  return tough + Math.floor(tough*0.3);
      }
      , current_hp = Math.round(vit*tough + vit*(tough/2));
      

    $('#damage').html(damage());
    $('#defence').html(defence);
    $('#current_hp').val(current_hp);
  }
  , initialize: function() {
    $('#calculate-stat').click(function(e){
      e.preventDefault();
      e.stopPropagation();
      
      sandbox.request_module('monster').update_stats();
      sandbox.request_module('monster').can_save = true;
      return false;
    });
    
    $('#monster').submit(function(e){
      if(!sandbox.request_module('monster').can_save) {
        alert('Please "Calculate Stats" before saving.');
        return false;
      }
      return true;
    });
    
    $('#delete').click(function(e){
      var confirm = confirm('Are you sure you want to delete this monster?');
      
      if(confirm) {
        $('#method').val('delete');
      }
    });
    
    $('#city').change(function(e){
    	var $obj = $('#city option:selected');
    	
    	$('#min_bounds').val($obj.attr('data-min'));
    	$('#max_bounds').val($obj.attr('data-max'));
    });
  }
}, sandbox.module));

sandbox.register_module('chat', util.extend({
	title: 'Chat'
	, description: 'Chat manager'
	, interval: undefined
	, since: undefined
	, say: function(message) {
		var chat = sandbox.request_module('chat');
		if(chat.interval !== undefined) {
			clearInterval(chat.interval);
		}

		$.ajax({
			url: 'index.php/?/chat'
			, dataType: 'json'
			, type: 'post'
			, data: {message: message}
			, complete: function(){
				$('#chat-button').attr('disabled',false);
				sandbox.request_module('chat').interval = setInterval(sandbox.request_module('chat').receive, 10000);
			}
			, success: function(res) {
				if(res !== undefined) {
					
					$('#message').val('');
					sandbox.request_module('chat').since = res;
					sandbox.request_module('chat').receive();
				}
			}
		});
	}
	, receive: function() {
		$.ajax({
			url: 'index.php/?/chat/'+sandbox.request_module('chat').since
			, dataType: 'json'
			, type: 'get'
			, success: function(res) {
				sandbox.request_module('chat').since = res.time;
				
				var tmp = '', message;
				for(var i = 0, l = res.messages.length; i < l; ++i) {
					message = '<div class="message';
					if(res.messages[i].classification == 1) {
						message += ((res.messages[i].classification == 1)?' admin':'');
					}
					else if(res.messages[i].classification == 2) {
						message += ((res.messages[i].classification == 1)?' server':'');
					}
					
					if(res.messages[i].classification !== 2 && res.messages[i].touser !== null) {
						message += ' pm';
					}
					
					message += '">';
					message += '<span class="from">'+res.messages[i].from+':</span> '+res.messages[i].text+'</div>';
					tmp += message;
				}
				$('#chat-messages').prepend(tmp);
				
				
			}
			, error: function(r) {
				console.log(r.responseText);
			}
		});
	}
	, initialize: function() {
		$('#chat-form').submit(function(e){
			sandbox.request_module('chat').say($('#message').val());
			$('#chat-button').attr('disabled',true);
			return false;
		});
		
		if(this.interval === undefined) {
			this.interval = setInterval(sandbox.request_module('chat').receive, 10000);
			sandbox.request_module('chat').receive();
		}
		
		$('.from').live('click',function(e){
			$('#message').val('/m '+$(this).html().split(':')[0]+' ');
      $('#message').focus();
		});
	}
}, sandbox.module));