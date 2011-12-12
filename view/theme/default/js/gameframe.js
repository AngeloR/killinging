// FIX FOR MULTIPLE INCLUDES
sandbox = parent.sandbox;
util = parent.util;
nobi = parent.nobi;


sandbox.register_module('frame-ui', util.extend({
	title: 'UI Fixes'
	, description: 'Fixes weird uiness'
	, initialize: function() {
		
		$('#menu').tabify();
		
		$('#menu a').click(function(){
			nobi.notify($(this).attr('href').split('#')[1]);
		});
		
		$.extend($.modal.defaults, {
			containerCss: {
				height: 400
				, width: 375
			}
		});
		
		// display the main frame
		parent.document.getElementById('gameframe').style.display = 'block';

	}
}, sandbox.module), true);

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
}, sandbox.module), true);

sandbox.register_module('bank', util.extend({
	title: 'Bank manager'
	, description: 'Handles banking'
	, transaction: function(url,gold,mode) {
		$.ajax({
			url: url+'/'+mode
			, dataType: 'json'
			, data: {gold: gold}
			, type: 'post'
			, success: function(res) {
				if(res.status == 'success') {
					parent.document.getElementById('gold').innerHTML = res.data.player;
					$('#total-in-bank').html(res.data.bank);
					if(gold < 0) {
						if(res.data.bank == 0) {
							gold = res.data.player;
						}
						else {
							gold = res.data.bank;
						} 
					}
					if(mode == 'withdraw') {
						$.gritter.add({title: 'Game Notice', text: 'You withdrew '+gold+' gold.'});
					}
					else {
						$.gritter.add({title: 'Game Notice', text: 'You deposited '+gold+' gold.'});
					}
					
				}
				else {
					$.gritter.add({title: 'Game Notice', text: res.data});
				}
			}
			, error: function(r) {
				$.gritter.add({title: 'Game Notice', text: 'Server Error'});
			}
		});
	}
	, initialize: function() {
		$('#bank-transaction').submit(function(e){
			var bank = sandbox.request_module('bank')
				, url = $('#bank-transaction').attr('action')
				, gold = $('#transaction-gold').val();
			switch($('#transaction-action').val()) {
				case 'deposit':
					bank.transaction(url,gold,'deposit');
				break;
				case 'withdraw':
					bank.transaction(url,gold,'withdraw');
				break;
				case 'deposit-all':
					bank.transaction(url,-1,'deposit');
				break;
				case 'withdraw-all':
					bank.transaction(url,-1,'withdraw');
				break;
			}
			
			sandbox.request_module('bank').deposit
			
			return false;
		});
	}
}, sandbox.module), true);

sandbox.register_module('crafting', util.extend({
	title: 'Crafting Hall'
	, description: 'Handles hitting the craft button in the crafting hall'
	, create_progress_bar: function($obj) {
		var url = $($obj).attr('href')
			, id = url.split('/');
		id = id[id.length - 1];
		$obj.parent().html('<span class="progressabr" id="craft-bar-'+id+'"></span>');
		$('#craft-bar-'+id).progressBar(100,{
			steps: 30
			, value: 1
			, boxImage: 'view/theme/default/images/progressbar.gif'
			, barImage: {
				0: 'view/theme/default/images/progressbg_red.gif'
				, 30: 'view/theme/default/images/progressbg_orange.gif'
				, 70: 'view/theme/default/images/progressbg_green.gif'
			}, showText: true, callback: function(data){

				if(data.running_value == data.max) {
					sandbox.request_module('crafting').craft(url,id);
				}
				
		}});
	}
	, craft: function(url,id) {
		$.ajax({
			url: url
			, dataType: 'json'
			, type: 'get'
			, beforeSend: function() {
				//$obj.removeAttr('href');
			}
			, success: function(res) {
				console.log(res);	
				p = parent.document; 
				if(res[0]) {
					p.getElementById('copper').innerHTML = res.resources.copper;
					p.getElementById('tin').innerHTML = res.resources.tin;
					
					$('#bronze_bar').html(res.resources.bronze);
					var tmp = '<li><img src="view/theme/default/images/icons/'+res.recipe.icon+'" class="item-icon"> You crafted a(n) ';
					if(res.recipe.id) {
						tmp += '<a href="index.php/?/item/info/'+res.recipe.id+'" class="info" data-type="'+res.type+'">'+res.recipe.name+'</a></li>';
					}
					else {
						tmp += res.recipe.name+'</li>';
					}
					$('#crafting-log').prepend(tmp);
				}
				else {
					// Display message to user
				}
			}
			, complete: function() {
				$('#craft-bar-'+id).parent().html('<a href="'+url+'" class="craft-item">Craft</a>');
			}
		});
	}
	, render: function(res) {
		var tmp = '<div class="title"><img src="view/theme/default/images/icons/'+res.icon+'" class="item-icon"> '+res.name+'</div>';
		tmp += '<table class="modal-info item-stats"><tr><th>Level</th><td>'+res.level+'</td></tr>';
		tmp += '<tr><th>Strength: </th><td>'+res.str+'</td></tr>';
		tmp += '<tr><th>Toughness: </th><td>'+res.tough+'</td></tr>';
		tmp += '<tr><th>Vitality: </th><td>'+res.vit+'</td></tr>';
		tmp += '<tr><th>Agility: </th><td>'+res.agi+'</td></tr>';
		tmp += '<tr><th>Luck: </th><td>'+res.luck+'</td></tr>';
		tmp += '<tr><th>Cost: </th><td>'+res.cost+'</td></tr></table>'
		
	return tmp;
	}
	, info: function(url,type) {
		$.ajax({
			url: url
			, dataType: 'json'
			, type: 'post'
			, data: {type: type}
			, success: function(res) {
				$.modal(sandbox.request_module('crafting').render(res), {
					
				});
			}
		});
	}
	, initialize: function() {
		$('.craft-item').live('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			
			sandbox.request_module('crafting').create_progress_bar($(this),$(this).attr('href'));
		});
		
		$('.info').live('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			
			sandbox.request_module('crafting').info($(this).attr('href'), $(this).attr('data-type'));
		});
	}
}, sandbox.module), true);

sandbox.register_module('tavern', util.extend({
	title: 'Tavern'
	, description: 'Handles healing at the tavern'
	, heal: function(url) {
		var tavern = sandbox.request_module('tavern');
		
		$.ajax({
			url: url
			, dataType: 'json'
			, type: 'post'
			, success: function(res) {
				if(res.status == 'success') {
					p = parent.document; 
					p.getElementById('current_hp').innerHTML = p.getElementById('total_hp').innerHTML;

					p.getElementById('hp-progress-bar').style.width = '100%';
					
					p.getElementById('gold').innerHTML = res.data.gold;
					$('#interact').html('<p>You have been healed!</p>');
				}
				else {
					$.gritter.add({title: 'Game Notice', text: res.data});
				}
			}
		});
	}
	, initialize: function() {
		$('#form-tavern').submit(function(e){
			sandbox.request_module('tavern').heal($('#form-tavern').attr('action'));
			return false;
		});
	}
}, sandbox.module), true);

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
}, sandbox.module), true);

sandbox.register_module('gamemessages', util.extend({
	title: 'Game Message'
	, description: 'Displays notifications to users. Don\'t use this. Use Gritter directly'
	, initialize: function() {
		
		$.extend($.gritter.options, { 
        position: 'bottom-right'
			});
		
		$.each(gamemessages, function(i,obj){
			$.gritter.add({title: 'Game Notice', text: obj});
		});
	}
}, sandbox.module), true);

sandbox.register_module('fight-club', util.extend({
	title: 'Fight Club'
	, description: 'Handles the JS fights'
	, render: function(res) {
		var tmp = '<ul class="fight-messages">';
		for(var i = 0, l = res.messages.length; i < l; ++i) {
			tmp += '<li>'+res.messages[i]+'</li>';
		}
		tmp += '</ul>';
		
		if(!res.tie) {
			tmp += '<div class="good notification">You killed the '+res.monster+' in '+res.rounds+' round'+((res.rounds < 2)?'':'\s')+'!<br>';
			tmp += '<table id="earnings"><tr><td rowspan="2">You gained:</td><td> <font color="yellow">'+res.stats.gold+' gold</font></td></tr></table></div>';
			
			var p = parent.document;
	
			p.getElementById('current_hp').innerHTML = res.stats.current_hp;
			p.getElementById('total_hp').innerHTML = res.stats.total_hp;
			p.getElementById('hp-progress-bar').style.width = 100*(res.stats.current_hp/res.stats.total_hp)+ '%';
			
			p.getElementById('current_mp').innerHTML = res.stats.current_mp;
			p.getElementById('total_mp').innerHTML = res.stats.total_mp;
			p.getElementById('mp-progress-bar').style.width = 100*(res.stats.current_mp/res.stats.total_mp)+ '%';
			
			p.getElementById('current_exp').innerHTML = res.stats.current_exp;
			p.getElementById('total_exp').innerHTML = res.stats.total_exp;
			p.getElementById('exp_percent').innerHTML = Math.round(res.stats.current_exp/res.stats.total_exp * 100);
			p.getElementById('exp-progress-bar').style.width = 100*(res.stats.current_exp/res.stats.total_exp)+ '%';
			
			p.getElementById('gold').innerHTML = parseInt(p.getElementById('gold').innerHTML) + res.stats.gold;
			
			if(p.getElementById('level').innerHTML != res.stats.level) {
				p.getElementById('level').innerHTML = res.stats.level;
				$('#skill_points').html(parseInt($('#skill_points').html())+1);
				$.gritter.add({title: 'Game Notice', text: 'You levelled up! You are now level '+res.stats.level});
			}
		}
		
		document.getElementById('fight_notification').innerHTML = tmp;
	}
	, initialize: function() {
		$('#fight-form').submit(function(e){
			e.preventDefault();
			e.stopPropagation();
			
			$('#fight-button').remove();
			
			$.ajax({
				url: $(this).attr('action')
				, dataType: 'json'
				, type: 'post'
				, success: function(res) {
					if(res !== 'f331d3ad') {
						sandbox.request_module('fight-club').render(res);
					}
					else {
						window.location = '/?/gameframe';
					}
				}
			});
			
			return false;
		});
	}
}, sandbox.module), true);


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
		$('.building-info').click(function(e){
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
}, sandbox.module), true);

sandbox.register_module('inventory', util.extend({
	title: 'Inventory Manager'
	, description: 'Handles loading of inventory'
	, load: function(items) {
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
}, sandbox.module), true);

sandbox.register_module('building', util.extend({
	title: 'Building Manager'
	, description: 'Handles generic building queries'
	, purchase: function () {
		$.ajax({
			url: 'index.php/?/building'
			, dataType: 'json'
			, type: 'post'
			, data: {build: $('#building').val()}
			, success: function(res) {
				if(res.status == 'success') {
					parent.document.getElementById('gold').innerHTML = res.data.gold;
					parent.document.getElementById('stone').innerHTML = res.data.stone;
					
					window.location = '/game';
				}
				else {
					$.gritter.add({title: 'Game Notice', text: res.data});
				}
			}
			, error: function(res) {
				$.gritter.add({title: 'Game Notice', text: 'Sorry, there was a problem creating the building.'});
			}
		});
	}
	, initialize: function() {
		$('#purchase-building').submit(function(e){
			sandbox.request_module('building').purchase();
			return false;
		});
		
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
}, sandbox.module), true);

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
					$('#resource-results').append('<li>You found '+res[0]+' '+res[2]+'</li>');

					parent.document.getElementById(res[2]).innerHTML = parseInt(parent.document.getElementById(res[2]).innerHTML) + res[0]
					
					
					if(res[1] === true) {
						$('#resource-results').append('<li class="success">You gained a level in mining!</li>');
						$('#mining').html(parseInt($('#mining').html()) + 1);
					}
				}
				
				$('#mining-current-exp').html(res[3]);
				$('#mining-total-exp').html(res[4]);
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
			}
			mine.total_runs = $('#length').val();
			
			mine.interval = setTimeout(mine.mine, 2000);
			
			return false;
		});
	}
}, sandbox.module), true);

sandbox.register_module('stats', util.extend({
	title: 'Skill up manager'
	, description: 'Manages when users update their stats'
	, update_ui: function(res) {
		// pass in a bunch of values and we'll update the main ui!
		var p = parent.document; 
		
		p.getElementById('current_hp').innerHTML = res.stats.current_hp;
		p.getElementById('total_hp').innerHTML = res.stats.total_hp;
		p.getElementById('hp-progress-bar').style.width = 100*(res.stats.current_hp/res.stats.total_hp)+ '%';
		
		p.getElementById('current_mp').innerHTML = res.stats.current_mp;
		p.getElementById('total_mp').innerHTML = res.stats.total_mp;
		p.getElementById('mp-progress-bar').style.width = 100*(res.stats.current_mp/res.stats.total_mp)+ '%';
		
		p.getElementById('current_exp').innerHTML = res.stats.current_exp;
		p.getElementById('total_exp').innerHTML = res.stats.total_exp;
		p.getElementById('exp_percent').innerHTML = Math.round(res.stats.current_exp/res.stats.total_exp * 100);
		p.getElementById('exp-progress-bar').style.width = 100*(res.stats.current_exp/res.stats.total_exp)+ '%';
		
		p.getElementById('gold').innerHTML = parseInt(p.getElementById('gold').innerHTML) + res.gold;
		
	}
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
							if(parent.document.getElementById(res.type)) {
								parent.document.getElementById(res.type).innerHTML = res[res.type];
							}
							else {
								document.getElementById(res.type).innerHTML = res[res.type];
							}
							parent.document.getElementById('total_hp').innerHTML = res.total_hp

							$('#damage').html(res.damage);
							$('#defence').html(res.defence);
							$('#skill_points').html(parseInt($('#skill_points').html())-1);
						}
					}
					, error: function(r) {
						console.log(r.responseText);
					}
				});
			}
			else {
				alert('You don\'t have enough Skill Points to do that.');
			}
		});
	}
},sandbox.module), true);