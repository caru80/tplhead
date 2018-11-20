(function() {


	/**
		CRu. 2018-10-23
		v0.1
	*/
	function StickyObserver(options)
	{
		options = options || {};

		this.opt 		= this.deepExtend({}, StickyObserver.defaults, options);
		this.elements 	= document.querySelectorAll(this.opt.elements);
		this.observers 	= [];

		if(this.elements.length)
		{
			this._init();
		}
		else 
		{
			return;
		}
	}

	StickyObserver.defaults = {
		elements 	: '.observe-sticky',
		root 		: null, // null = body/document – Siehe Doku.
		offset 		: null,
		debug 		: false,
		className 	: 'sticked',
		threshold 	: 0
	}

	StickyObserver.prototype = {

		_init : function()
		{
			this.sentinels = this.addSentinels();
			this.bindEvents();
			this.observe();
		},

        // ~= jQuery.extend
		// Merged Objekte in ein Neues, oder in das Objekt „out”.
		// this.deepExtend({}, obj1, obj2, obj3 ... )
        
        deepExtend : function(out) 
		{
			out = out || {};

			for (let i = 1, len = arguments.length; i < len; i++) 
			{
				let obj = arguments[i];

				if (!obj) 
					continue;

				for (let key in obj) 
				{
					if (obj.hasOwnProperty(key)) 
					{
						if (typeof obj[key] === 'object' 
							&& obj[key] !== null 
							&& obj[key] !== NaN
							&& !Array.isArray(obj[key])) // Wenn obj[key] ein Array ist bleibt es unangetastet, weil das Array sonst in ein Objekt konvertiert wird.
						{
							out[key] = this.deepExtend(out[key], obj[key]);
						}
						else 
						{
							out[key] = obj[key];
						}
					}
				}
			}
			return out;
        },

		bindEvents : function ()
		{
			this.elements.forEach(function(el) 
			{
				el.addEventListener('sticky-change', function(ev) 
				{
					if(ev.detail.stuck) 
					{
						ev.target.classList.add(this.opt.className);
					}
					else 
					{
						ev.target.classList.remove(this.opt.className);
					}
				}.bind(this));
			}, this);
		},

		addSentinels : function()
		{
			let sentinelList = new Array();
			
			this.elements.forEach(function(el) {

				let coords   = el.getBoundingClientRect(),
					sentinel = document.createElement('div');

				sentinel.style.position 	= 'absolute';
				sentinel.style.left 		= '0px'; //coords.left; // Warum sollte das nicht auch "0px" sein?!
				sentinel.style.right 		= '0px';
				sentinel.style.height 		= el.offsetHeight + 'px';

				if(this.opt.offset)
				{
					sentinel.style.marginTop = this.opt.offset - el.offsetHeight + 'px';
				}
				/*else { 
					sentinel.style.marginTop = '-' + el.offsetHeight + 'px';
				}*/
				else if(this.opt.threshold === 0) {
					sentinel.style.marginTop = '-' + el.offsetHeight + 'px';
				}

				if(this.opt.debug) 
				{
					sentinel.style.backgroundColor = 'red';
					sentinel.style.zIndex 			= 5000;
				}
				else 
				{
					sentinel.style.visibility 	= 'hidden';
					sentinel.style.zIndex 		= -100;
				}
				sentinel.stickyObserver = {target : el};
				el.parentNode.insertBefore(sentinel, el);

				sentinelList.push(sentinel);
			}, this);

			return sentinelList;
		},

		observe : function() 
		{
			let observer;
			let self = this;

			for(let el of this.sentinels)
			{
				observer = new IntersectionObserver((records, observer) => {

					//console.log(records);

					for (const record of records) 
					{
						const targetInfo 		= record.boundingClientRect; 			// DOMRect von „sentinel”
						const stickyTarget 		= record.target.stickyObserver.target;  // Der Wächter, der den Callback ausgelöst hat.
						const rootBoundsInfo 	= record.rootBounds; 					// DOMRect vom Root Element (this.opt.root)
						const ratio 			= record.intersectionRatio;

						if(self.opt.threshold === 0)
						{
							if (targetInfo.bottom < rootBoundsInfo.top) 
							{
								self.fireEvent(true, stickyTarget);
							}
							// Stopped sticking.
							if(targetInfo.bottom >= rootBoundsInfo.top 
								&& targetInfo.bottom < rootBoundsInfo.bottom) 
							{
								self.fireEvent(false, stickyTarget);
							}
						}
						else
						{
							if (targetInfo.bottom > rootBoundsInfo.top) 
							{
								self.fireEvent(false, stickyTarget);
							}
							// Stopped sticking.
							if(targetInfo.bottom >= rootBoundsInfo.top && ratio === 1
								&& targetInfo.bottom < rootBoundsInfo.bottom) 
							{
								self.fireEvent(true, stickyTarget);
							}
						}
					}
				}, {threshold: [this.opt.threshold], root: this.opt.root});

				this.observers.push(observer);
				observer.observe(el);
			}
		},

		fireEvent : function(stuck, target) 
		{
			let e = new CustomEvent('sticky-change', {detail: {stuck, target}});
			target.dispatchEvent(e);
		}
	}

	$app.stickyObserver = StickyObserver;

})();