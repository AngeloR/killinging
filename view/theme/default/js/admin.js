sandbox.register_module('monster', util.extend({
  title: 'Monster Manager'
  , description: 'Handles the monster form'
  , can_save: false
  , update_stats: function() {
    var str = parseInt($('#str').val())
      , agi = parseInt($('#agi').val())
      , vit = parseInt($('#vit').val())
      , tough = parseInt($('#tough').val()) 
      , damage = Math.round(str*agi*(agi/2))
      , defence = Math.round(tough*vit*(tough/2))
      , current_hp = Math.round(vit*tough + vit*(tough/2));
      

    $('#damage').html(damage);
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
  }
}, sandbox.module));