sandbox.register_module('ui', util.extend({
	title: 'UI Manager'
	, description: 'Fixes ui'
	, initialize: function() {
		$('#menu').tabify();
		
		$.modal.defaults.minWidth = 325;
		$.modal.defaults.maxWidth = 325;
	}
}, sandbox.module));

sandbox.register_module('item-info',util.extend({
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
					
					$.modal(sandbox.request_module('item-info').render(res), {
						
					});
				}
			});
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
			
			$.ajax({
				url: $(this).attr('action')
				, dataType: 'json'
				, type: 'post'
				, data: {monster_id: $('#monster option:selected').val()}	
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