sandbox.register_module('chat', util.extend({
	title: 'Chat'
	, description: 'Chat manager'
	, interval: undefined
	, since: 0
	, last_check: 0
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
					
					document.getElementById('message').value = '';
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
				var chat = sandbox.request_module('chat');
				chat.last_check = sandbox.request_module('chat').since;
				chat.since = res.time;
				
				var tmp = '', message;
				for(var i = 0, l = res.messages.length; i < l; ++i) {
					message = '<div class="message';
					if(res.messages[i].classification == 1) {
						message += ' admin';
					}
					else if(res.messages[i].classification == 2) {
						message += ' server';
					}
					
					if(res.messages[i].fromuser === 'Server') {
						
					}
					
					if(res.messages[i].touser !== null) {
						message += ' pm';
						if(res.messages[i].time > chat.last_check && res.messages[i].time < chat.since) {
							//$.gritter.add({title: 'PM from '+res.messages[i].from, text: res.messages[i].text});
						}
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