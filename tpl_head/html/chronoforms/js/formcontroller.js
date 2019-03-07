(function($) {

	fancyFormController = function (config) {
		if(!config) return;		

		this.config 	= config;
		this.form 		= $('#fancyform-' + config.id).parents('form.chronoform');

		if(this.form.length && this.formname != '')
		{
			this._init();
		}
	}
	fancyFormController.prototype = {
		_init : function (config) 
		{
			this.formname 	= this.form.attr('name');
			this.formaction = this.form.prop('action');
			this.formAjaxUrl = this.formaction + ((this.formaction.indexOf("?") > -1) ? "&" : "?") + 'tvout=ajax';
			this.dropzone 	= null;

			// Dropzone init
			let dropzoneEl 	= $('.dropzone');
			if(dropzoneEl.length)
			{
				this.initDropzone(dropzoneEl);
			}

			// Form Events init
			this.initFormEvents();
		},

		markRequired : function ()
		{

		},

		initFormEvents : function ()
		{	
			this.form.on('submit.fancyform', function(ev) 
			{
				ev.preventDefault();

				let gvalidator = this.form.data('gvalidation'); 
				if(gvalidator) // JavaScript Validator lauscht an dem Form.
				{
					// Auf Erfolg des JS Validators warten
					this.form.off('success.gvalidation'); // Ganz wichtig, sonst werden die Anweisungen in der nächsten Zeile so oft multipliziert wie der Validator error ausgibt, wenn man Senden klickt!
					this.form.one('success.gvalidation', function(ev) {
						if(this.dropzone)
						{
							this.submitFiles();
						}
						else 
						{
							this.submitFormData();
						}
					}.bind(this));
				}
				else {
					// Ohne Validator fortsetzen.
					if(this.dropzone)
					{
						this.submitFiles();
					}
					else 
					{
						this.submitFormData();
					}
				}
				return false;
			}.bind(this));
		},

		submitFormData : function ()
		{
			const 	self 	= this,
					loading = $app.showLoadingIndicator({t : this.form});

			$.ajax({
				type 		: "POST",
				url 		: this.formAjaxUrl,
				data 		: this.form.serialize(),
				success 	: function(result)
				{
					$app.hideLoadingIndicator(loading);
					self.oldForm = self.form.replaceWith(result);
				},
			});
		},

		initDropzone : function (el)
		{
			const self = this;
			
			this.dropzone = new Dropzone('#' + el.attr('id'), this.config.dropzone);
			
			this.dropzone.on('error', function(file, msg) 
			{
				this.removeFile(file);
				$app.messages.show({text : msg, type : 'error', subject : 'dropzone', hide : 5000});
			});

			this.dropzone.on('addedfile', function (f) 
			{
				let fnameContainer  = $('#dropzone_files'),
					filenames 		= new Array();

					/*
				for (let file of self.dropzone.files)
				{
					filenames.push(file.name);
				}*/

				for(let i = 0; i < self.dropzone.files.length; i++) 
				{
					filenames.push(self.dropzone.files[i].name);
				}

				fnameContainer.get(0).value = filenames.join(',');
			});

			this.dropzone.on('removedfile', function (f)
			{
				let fnameContainer  = $('#dropzone_files'),
					filenames 		= new Array();

				for(let i = 0; i < self.dropzone.files.length; i++) 
				{
					filenames.push(self.dropzone.files[i].name);
				}
				/*
				for (let file of self.dropzone.files)
				{
					filenames.push(file.name);
				}
				*/
				fnameContainer.get(0).value = filenames.join(',');
			});
		},

		submitFiles : function ()
		{
			const files = $('#dropzone_files').val();

			if(files !== '')
			{
				const loading = $app.showLoadingIndicator({t : this.form});

				$('.loading-inner', '#' + loading).append('<div class="loading-indicator-text">' + this.config.locale.iamUploadingFiles + '</div>');
				
				this.dropzone.on('queuecomplete', function() 
				{
					$app.hideLoadingIndicator(loading);
					this.submitFormData();
				}.bind(this));

				this.dropzone.on('success', this.dropzone.processQueue.bind(this.dropzone));
				this.dropzone.processQueue();
			}
			else 
			{
				this.submitFormData();
			}
		}
	}
})(jQuery);