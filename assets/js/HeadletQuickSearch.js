/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

Todoyu.Ext.search.Headlet.QuickSearch = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:		Todoyu.Ext.search,

	query:		null,	
	button:		null,
	content:	null,
		
	bodyClickObserver: null,



	/**
	 * Enter description here...
	 */
	init: function() {
		this.query	= $('headlet-quicksearch-query');
		this.button	= this.getButton();
		this.content= this.getContent();
				
		this.query.observe('click', this.onQueryClick.bindAsEventListener(this));

		this.Suggest.init();
		this.Mode.init();
	},
	
	
	onButtonClick: function(event) {
		if( this.isContentVisible() ) {
			this.hide();
		} else {
			this.hideOthers();
			this.showContent();
			this.focus();

			this.saveOpenStatus();
		}		
	},
	
	onContentClick: function(event) {
		console.log('onContentClick');

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},
	
	
	onQueryClick: function(event) {
		this.Mode.hideModes();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},

	onBodyClick: function(event) {
		this.hideExtras();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},
	
	
	hide: function() {
		this.hideContent();
		this.hideExtras();
		this.saveOpenStatus();
	},

	hideExtras: function() {
		this.Mode.hideModes();
		this.Suggest.hideResults();
	},

	
	
	focus: function() {
		this.query.select();
	},
	

	/**
	 * Enter description here...
	 */
	submit: function() {
		//$('headlet-quicksearch-form').submit();
		Todoyu.notifyInfo('redirect to full search disabled at the moment');
	},



	/**
	 * Enter description here...
	 */
	submitIfNotEmpty: function() {
		if( ! this.isEmpty() ) {
			this.submit();
		}
	},



	/**
	 * Enter description here...
	 *
	 * @return	{String}
	 */
	getValue: function() {
		return $F(this.query).strip();
	},



	/**
	 * Enter description here...
	 *
	 * @return	{Boolean}
	 */
	isEmpty: function() {
		return this.getValue() === '';
	},
	


/* ---------------------------------------------------------
	Todoyu.Ext.search.Headlet.Quicksearch.Mode
------------------------------------------------------------ */

	/**
	 * Enter description here...
	 */
	Mode: {
		
		ext: Todoyu.Ext.search,
		
		headlet: null,

		mode: 0,

		button: null,
		
		modes: null,
		
		positioned: false,
		

		/**
		 * Enter description here...
		 *
		 * @param {Number} idFilterset
		 */
		init: function() {
			this.headlet = this.ext.Headlet.QuickSearch;
			
			this.button = $('headlet-quicksearch-mode-button');
			this.modes	= $('headlet-quicksearch-modes');

			this.button.observe('click', this.showModes.bindAsEventListener(this));
		},


		/**
		 * Enter description here...
		 *
		 * @param	{String}	mode
		 */
		showModes: function(event) {
			var modes	= $('headlet-quicksearch-modes');
			
			if(modes.visible() == true)	{
				this.hideModes();
			} else {
				if( ! this.positioned ) {
					var contentOffset	= this.headlet.content.cumulativeOffset();
					var contentDim		= this.headlet.content.getDimensions();
					var modeWidth		= this.modes.getWidth();
		
					var top			= contentDim.height;
					var left		= contentDim.width - modeWidth;
		
					modes.setStyle({
						'left':		left + 'px',
						'top':		top + 'px'
					});
					
					this.positioned = true;
				}
				
				modes.show();			
				
				this.headlet.Suggest.hideResults();
			}
		},



		/**
		 * Enter description here...
		 *
		 * @param	{String}	mode
		 */
		setMode: function(mode) {
			$('headlet-quicksearch-mode').value = mode;
			$('headlet-quicksearch-form').writeAttribute('class', 'icon searchmode' + mode.capitalize());
			
			this.hideModes();
			this.headlet.focus();
			
			this.headlet.Suggest.updateResults();			
		},



		/**
		 * Enter description here...
		 *
		 * @return	{String}
		 */
		getMode: function() {
			return $F('headlet-quicksearch-mode');
		},


		/**
		 * Enter description here...
		 */
		hideModes: function() {
			$('headlet-quicksearch-modes').hide();
		}

	},








/* ---------------------------------------------------------
	Todoyu.Ext.search.Headlet.Quicksearch.Suggest
------------------------------------------------------------ */

	/**
	 * Quicksearch headlet suggestions
	 */
	Suggest: {

		/**
		 *	Ext shortcut
		 */
		ext:			Todoyu.Ext.search,

		headlet:		null,

		suggest:		null,

		delay:			0.5,

		navigatePos:	-1,

		navigateActive:	null,

		numElements:	0,

		timeout:		null,
		
		

		/**
		 * Enter description here...
		 */
		init: function() {
			this.headlet	= this.ext.Headlet.QuickSearch;
			this.suggest	= $('headlet-quicksearch-suggest');
			
				// Move suggest to body (to scroll)
			document.body.appendChild(this.suggest);
			
			this.headlet.query.observe('keyup', this.onQueryChange.bind(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param	{Object}	event
		 */
		onQueryChange: function(event) {
			window.clearTimeout(this.timeout);

				// Pressed ENTER
			if( event.keyCode === Event.KEY_RETURN ) {
				if( this.isNavigating() ) {
					this.goToActiveElement();
				} else {
					this.timeout = this.updateResults.bind(this).delay(this.delay);
				}
				return;
			}

				// Pressed navigation arrows
			if( event.keyCode === Event.KEY_DOWN || event.keyCode === Event.KEY_UP ) {
				if( this.suggest.visible() ) {
					var down = event.keyCode === Event.KEY_DOWN;
					this.navigate(down);
				}
				return;
			}
			
				// Pressed ESC (hide results or whole headlet)
			if( event.keyCode === Event.KEY_ESC ) {
				if( this.isResultsVisible() ) {
					this.hideResults();
				} else {
					this.headlet.toggleContent();
				}				
				return;
			}

			if( this.headlet.isEmpty() ) {
				this.hideResults();
			} else {
				this.timeout = this.updateResults.bind(this).delay(this.delay);
			}
		},



		/**
		 * Check if user is navigating in result list (up and down)
		 *
		 * @return	{Boolean}
		 */
		isNavigating: function() {
			return this.navigatePos > -1;
		},



		/**
		 * Enter description here...
		 */
		goToActiveElement: function() {
			eval(this.navigateActive.down().readAttribute('onclick'));
			this.hide();
		},



		/**
		 * Navigate in result list (up and down)
		 *
		 * @param	Boole	down		Navigate down. Yes or No?
		 */
		navigate: function(down) {
				// Deactivate selection
			if( this.navigateActive !== null ) {
				this.navigateActive.removeClassName('active');
			}

				// Increment or decrement to new position
			if( down ) {
				this.navigatePos++;
			} else {
				this.navigatePos--;
			}

				// If navigating over the top, stop walking upwards and do nothing
			if( this.navigatePos <= -1 ) {
				this.navigatePos = -1;
				this.navigateActive = null;
				return;
			}

				// If navigating over the last element, set position to last element (stay on last element)
			if( this.navigatePos >= this.numElements ) {
				this.navigatePos = this.numElements-1;
			}

				// Select active element
			this.navigateActive = this.suggest.down('li li', this.navigatePos);

				// Set element active
			this.navigateActive.addClassName('active');
		},



		/**
		 * Update suggest container with new results
		 */
		updateResults: function() {
			if( this.headlet.isEmpty() ) {
				return;
			}
			
			var url		= Todoyu.getUrl('search', 'suggest');
			var options	= {
				'parameters': {
					'action':	'suggest',
					'query':	this.headlet.getValue(),
					'mode':		this.headlet.Mode.getMode()
				},
				'onComplete':	this.onResultsUpdated.bind(this)
			};
			
			Todoyu.Ui.update(this.suggest, url, options);
		},
		
		
		/**
		 * Handler when results have been updated
		 * 
		 *
		 * @param	{Object}	response
		 */
		onResultsUpdated: function(response) {
			this.navigatePos = -1;
			this.numElements = this.suggest.select('li li').size();
			
			this.showResults();
		},
		
		
		
		/**
		 * Show suggested results container on right position
		 */
		showResults: function() {
			var contentDim		= this.headlet.content.getDimensions();
			var contentOffset	= this.headlet.content.cumulativeOffset();
			var suggestDim		= this.suggest.getDimensions();
			
			this.suggest.setStyle({
				'left':	contentOffset.left - suggestDim.width + contentDim.width - 1 + 'px',
				'top':	contentOffset.top + contentDim.height + 'px'
			});
			
			Todoyu.Ui.scrollToTop();
			this.suggest.show();
		},



		/**
		 * Hide suggested restults
		 */
		hideResults: function() {
			this.suggest.hide();
		},
		
		
		isResultsVisible: function() {
			return this.suggest.visible();
		}
	}

};