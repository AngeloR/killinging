sandbox.register_module('ui', util.extend({
	title: 'UI Manager'
	, description: 'Fixes ui'
	, initialize: function() {
		$('#menu').tabify();
		
		$('#menu a').click(function(){
			nobi.notify($(this).attr('href').split('#')[1]);
		});
		
		$.modal.defaults.minWidth = 325;
		$.modal.defaults.maxWidth = 325;
		$.modal.defaults.minHeight = null;
	}
}, sandbox.module));

sandbox.register_module('store', util.extend({
	title: 'Store manager'
	, description: 'Handles store management'
	, add_to_store: function(id,price) {
		$.ajax({
			url: 'index.php/?/store/add'
			, dataType: 'json'
			, data: {id: id, price: price}
			, type: 'post'
			, success: function(res) {
				console.log(res);
				$('#im-'+id).remove();
			}
			, error: function(r) {
				console.log(r.responseText);
			}
		});
	}
	, initialize: function() {
		$('.add-item-to-store').live('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var tr = $(this).closest('tr')
				, price = $('#'+tr.attr('id') + ' .price input').val();
			
			sandbox.request_module('store').add_to_store(tr.attr('id').split('im-')[1],price);
		});
	}
}, sandbox.module));

sandbox.register_module('item',util.extend({
	title: 'Item Info'
	, description: 'Loads item info into simple modal'
	, render: function(res) {
		var tmp = '<div class="title">'+res.name+'</div>';
			tmp += '<table class="modal-info item-stats"><tr><th>Price</th><td>'+res.cost+'</td></tr>';
			tmp += '<tr><th>Minimum Level: </th><td>'+res.level+'</td></tr>';
			tmp += '<tr><th>Strength: </th><td>'+res.str+'</td></tr>';
			tmp += '<tr><th>Defence: </th><td>'+res.def+'</td></tr>';
			tmp += '<tr><th>Agility: </th><td>'+res.agi+'</td></tr>';
			tmp += '<tr><th>Luck: </th><td>'+res.luck+'</td></tr></table>';
			
		return tmp;
	}
	, initialize: function() {
		$('.item-info').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			$.ajax({
				url: $(this).attr('href')
				, dataType: 'json'
				, type: 'get'
				, success: function(res) {
					
					$.modal(sandbox.request_module('item').render(res), {
						
					});
				}
			});
		});
		
		$('.inventory-info').live('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			
			$.ajax({
				url: $(this).attr('href')
				, dataType: 'json'
				, type: 'get'
				, success: function(res) {
					
					$.modal(sandbox.request_module('item').render(res), {
						
					});
				}
			});
		});
		
		$('.buy').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			var _this = this;
			
			$.ajax({
				url: $(this).attr('href').split('/inventory')[0]+'/inventory'
				, dataType: 'json'
				, type: 'post'
				, data: {item_id: $(this).attr('href').split('/inventory/')[1]}
				, success: function(res){ 
					if(gold) {
						$('#gold').html(res.gold);
						$(_this).closest('tr').remove();
					}
				}
			})
		});
	}
}, sandbox.module));

sandbox.register_module('fight-club', util.extend({
	title: 'Fight Club'
	, description: 'Handles the JS fights'
	, render: function(res) {
		var tmp = '<ul class="fight-messages">';
		for(var i = 0, l = res.messages.length; i < l; ++i) {
			tmp += '<li>'+res.messages[i]+'</li>';
		}
		tmp += '</ul>';
		
		if(res.stats.current_hp === 0) {
			tmp += '<div class="error notification">The '+res.monster+' killed you in '+res.rounds+' round'+((res.rounds < 2)?'':'\s')+'!</div>';
		}
		else {
			tmp += '<div class="good notification">You killed the '+res.monster+' in '+res.rounds+' round'+((res.rounds < 2)?'':'\s')+'!</div>';
		}
		
		$('#current_hp').html(res.stats.current_hp);
		$('#total_hp').html(res.stats.total_hp);
		$('#current_mp').html(res.stats.current_mp);
		$('#total_mp').html(res.stats.total_mp);
		$('#current_exp').html(res.stats.current_exp);
		$('#total_exp').html(res.stats.total_exp);
		$('#exp_percent').html(Math.round(res.stats.current_exp/res.stats.total_exp * 100));
		$('#gold').html(res.stats.gold);
		
		if($('#level').html() != res.stats.level) {
			$('#level').html(res.stats.level);
		}
		
		$('#fight_notification').html(tmp);
	}
	, initialize: function() {
		$('#fight-form').submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			$('#fight-button').attr('disabled',true);
			
			$.ajax({
				url: $(this).attr('action')
				, dataType: 'json'
				, type: 'post'
				, data: {monster_id: $('#monster option:selected').val()}	
				, complete: function() {
					setTimeout(function(){
						$('#fight-button').attr('disabled',false);
					}, 2000);
				}
				, success: function(res) {
					sandbox.request_module('fight-club').render(res);
				}
			});
			
			return false;
		});
	}
}, sandbox.module));


sandbox.register_module('building-info', util.extend({
	title: 'Building Info'
	, description: 'Gets info about a building'
	, render: function(res) {
		var tmp = '<div class="title">'+res.name+'</div>';
			tmp += '<table class="modal-info item-stats"><tr><th>Price</th><td>'+res.cost+'</td></tr>';
			tmp += '<tr><th>Time to build: </th><td>'+res.time+' H</td></tr>';
			tmp += '<tr><th>description: </th><td>'+res.description+'</td></tr></table>';
			
		return tmp;
	}
	, initialize: function() {
		$('.building-info').click(function(e){http://www.google.ca/url?sa=t&source=web&cd=2&ved=0CB4QFjAB&url=http%3A%2F%2Fapi.jquery.com%2Fval%2F&ei=3P1DTqzmIKO20AHI9oz2CQ&usg=AFQjCNH-GtfNf_4YLXDtVLm7PPh0POeseA
			e.preventDefault();
			e.stopPropagation();
			
			$.ajax({
				url: $(this).attr('href')+'/'+$('#building option:selected').val()
				, dataType: 'json'
				, type: 'get'
				, success: function(res) {
					$.modal(sandbox.request_module('building-info').render(res), {
						
					});
				}
			})
		});
	}
}, sandbox.module));

sandbox.register_module('inventory', util.extend({
	title: 'Inventory Manager'
	, description: 'Handles loading of inventory'
	, load: function(items) {
		console.log(items);
		var tmp = '<table>';
		for(var i = 0, l = items.length; i < l; ++i) {
			tmp += this.add_item_to_list(items[i]);
		}
		tmp += '</table>';
		$('#inventory').html(tmp);
	}
	, add_item_to_list: function(item) {
		var tmp = '<tr><td><a href="index.php/?/inventory/info/'+item.id+'" class="inventory-info">'+item.name+'</a></td></tr>';
		
		return tmp;
	}
	, initialize: function() {
		nobi.bind('new-item', function(item) {
			this.load(item);
		}, this);
		
		nobi.bind('stats-tab', function(){
			$.ajax({
				url: 'index.php/?/inventory'
				, dataType: 'json'
				, type: 'get'
				, success: function(res) {
					if(res) {
						sandbox.request_module('inventory').load(res);
					}
				}
			});
		});
		
	}
}, sandbox.module));

sandbox.register_module('building', util.extend({
	title: 'Building Manager'
	, description: 'Handles generic building queries (like upgrades)'
	, initialize: function() {
		$('#building-upgrade').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			$.ajax({
				url: $(this).attr('href')
				, dataType: 'json'
				, type: 'post'
				, success: function(res) {
					if(res !== undefined && res) {
						
					}
					else {
						$('#building-upgrade').after('<p>Sorry, you don\'t have enough resources for this upgrade.</p>');
					}
				}
				, error: function(r) {
					console.log(r.responseText);
				}
			});
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
	}
}, sandbox.module));

sandbox.register_module('mine', util.extend({
	title: 'Mining System'
	, description: 'Handles repetitive mining'
	, interval: undefined
	, current_run: 0
	, total_runs: 0
	, mine: function() {
		$.ajax({
			url: 'index.php/?/mine'
			, dataType: 'json'
			, type: 'post'
			, data: {type: $('#resource-type').val()}
			, complete: function() {
				var mi = sandbox.request_module('mine');
				++mi.current_run;
				if(mi.current_run >= mi.total_runs) {
					clearTimeout(mi.interval);
					$('#mine-button').attr('disabled',false);
				}
				else {
					mi.interval = setTimeout(mi.mine,2000);
				}
			}
			, success: function(res) {
				if(res !== undefined) {
					console.log(res);
					$('#resource-results').append('<li>You found '+res[0]+' '+res[2]+'</li>');
					$('#'+res[2]).html(parseInt($('#'+res[2]).html()) + res[0]);
					
					if(res[1] === true) {
						$('#resource-results').append('<li>You gained a level in mining!</li>');
					}
				}
			}
			, error: function(r){
				console.log(r.responseText);
			}
		});
	}
	,initialize: function() {
		
		$('#mine').submit(function(e){
			var mine = sandbox.request_module('mine');
			
			$('#mine-button').attr('disabled',true);
			
			if(mine.interval !== undefined) {
				clearTimeout(mine.interval);
				mine.total_runs = $('#length').val();
			}
			
			mine.interval = setTimeout(mine.mine, 2000);
			
			return false;
		});
	}
}, sandbox.module));

sandbox.register_module('stats', util.extend({
	title: 'Skill up manager'
	, description: 'Manages when users update their stats'
	, initialize: function() {
		$('.skillup').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			if(parseInt($('#skill_points').html()) !== 0) {
				$.ajax({
					url: $(this).attr('href')
					, dataType: 'json'
					, type: 'post'
					, complete: function() {
						
					}
					, success: function(res) {
						if(res) {
							$('#'+res.type).html(res[res.type]);
							$('#total_hp').html(res.total_hp);
							$('#damage').html(res.damage);
							$('#defence').html(res.defence);
							$('#skill_points').html(parseInt($('#skill_points').html())-1);
						}
					}
					, error: function(r) {
						console.log(r.responseTexT);
					}
				});
			}
			else {
				alert('You don\'t have enough Skill Points to do that.');
			}
		});
	}
},sandbox.module));

sandbox.register_module('movement', util.extend({
	title: 'Movement Manager'
	, description: 'Handles movement and redrawing the map'
	, move: function() {
		
	}
	, initialize: function() {
		// grab the key presses!
	}
}, sandbox.module));