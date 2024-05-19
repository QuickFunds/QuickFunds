document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('time_period');
    var options = select.getElementsByTagName('option');
    
    for (var i = 0; i < options.length; i++) {
      options[i].addEventListener('mouseover', function() {
        this.style.backgroundColor = 'blue';
        this.style.color = 'white';
      });
      
      options[i].addEventListener('mouseout', function() {
        this.style.backgroundColor = '';
        this.style.color = '';
      });
    }
  });
  