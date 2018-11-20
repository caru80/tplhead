(function($) {
	$(function() {
		let els  	= 'input, select, textarea',
			f 		= $('.fancy-form'),
			groups  = f.find('.form-group inline'),
			fields 	= f.find(els);

		for(let i = 0, len = fields.length; i < len; i++) {
			let e = fields.eq(i);

			e.on('focus.fform', function() {
				f.find('form-group inline.focus').removeClass('focus');
				$(this).parent('.form-group').addClass('focus');
				//$app.scroll.scrollTo({el : f, force : true});
			})
			.on('blur.fform', function() {
				$(this).parent('.form-group').removeClass('focus');
			});
		}

		for(let i = 0, len = groups.length; i < len; i++) {
			let g = groups.eq(i);

			g.on('click', function() {
				let e = $(this).children(els).get(0).focus();
			});
		}
	});
})(jQuery);