(function() {
	$app.searchbar = {

		init : function() 
		{
			this.bar 		= document.getElementById('site-searchbar');
			this.triggers 	= document.querySelectorAll('[data-toggle-search]');

			this.triggers.forEach(function(el){
				this.addListener(el);
			}, this);
		},

		addListener : function(trigger) 
		{
			trigger.addEventListener('click', function(ev) 
			{
				this.toggle();
			}.bind(this));
		},

		toggle : function()
		{
			if(this.bar.classList.contains('in'))
			{
				this.close();
			}
			else 
			{
				this.open();
			}
		},

		open : function()
		{
			this.bar.classList.add('in');
		},

		close : function()
		{
			this.bar.classList.remove('in');
		}
	}
})();