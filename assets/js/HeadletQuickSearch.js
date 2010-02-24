/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

Todoyu.Ext.search.Headlet.QuickSearch = {

	/**
	 *	Ext shortcut
	 */
	ext:			Todoyu.Ext.search,

	searchField:	null,	
	button:			null,
	box:			null,



	/**
	 * Enter description here...
	 */
	init: function() {
		this.searchField= $('headletquicksearch-query');
		this.button		= $('headletquicksearch-button');
		this.box		= $('headletquicksearch-box');
		
		this.Suggest.init();
		this.Mode.init();
		
		this.button.observe('click', this.onButtonClick.bindAsEventListener(this));
	},
	
	
	onButtonClick: function(event) {
		if( this.box.visible() ) {
			this.box.hide();
			this.Mode.hideModes();
			this.Suggest.hideResults();
		} else {
			this.box.show();
			this.focus();
		}
	},
	
	focus: function() {
		this.searchField.focus();
	},
	





	/**
	 * Enter description here...
	 */
	submit: function() {
		//$('headletquicksearch-form').submit();
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
	 * @return	String
	 */
	getValue: function() {
		return $F(this.searchField).strip();
	},



	/**
	 * Enter description here...
	 *
	 * @return	Boolean
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
		

		/**
		 * Enter description here...
		 *
		 * @param Integer idFilterset
		 */
		init: function() {
			this.headlet = this.ext.Headlet.QuickSearch;
			
			this.button = $('headletquicksearch-mode-button');
			this.modes	= $('headletquicksearch-modes');

			this.button.observe('click', this.showModes.bindAsEventListener(this));
			this.modes.observe('click', this.onModeClick.bindAsEventListener(this));
		},


		/**
		 * Enter description here...
		 *
		 * @param	String	mode
		 */
		showModes: function(event) {
			var btnOffset	= this.button.cumulativeOffset();
			var btnHeight	= this.button.getHeight();
			var btnWidth	= this.button.getWidth();
			var modeWidth	= this.modes.getWidth();

			var top			= btnOffset.top + btnHeight;
			var left		= btnOffset.left - modeWidth + btnWidth;

			$('headletquicksearch-modes').setStyle({
				'display':	'block',
				'left':		left + 'px',
				'top':		top + 1 + 'px'
			});
			
			$(document.body).observe('click', this.onBodyClick.bindAsEventListener(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param	String	mode
		 */
		setMode: function(mode) {
			$('headletquicksearch-mode').value = mode;
			$('headletquicksearch-mode-icon').writeAttribute('class', 'icon searchmode-' + mode);
		},



		/**
		 * Enter description here...
		 *
		 * @return	String
		 */
		getMode: function() {
			return $F('headletquicksearch-mode');
		},



		/**
		 * Enter description here...
		 *
		 * @param	Object	event
		 */
		onModeClick: function(event) {
			var mode = event.findElement('li').readAttribute('mode');

			this.setMode(mode);
			this.hideModes();
			this.headlet.focus();
		},


		/**
		 * Enter description here...
		 */
		onBodyClick: function(event) {
			this.hideModes();
			$(document.body).stopObserving('click');
		},



		/**
		 * Enter description here...
		 */
		hideModes: function() {
			$('headletquicksearch-modes').hide();
		}

	},



/* ---------------------------------------------------------
	Todoyu.Ext.search.Headlet.Quicksearch.Suggest
------------------------------------------------------------ */

	/**
	 * Quicksearch headlet suggestions
	 *
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
			this.suggest	= $('headletquicksearch-suggest');
			//this.makeDiv();
			
			this.headlet.searchField.observe('keyup', this.onQueryChange.bind(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param	Object	event
		 */
		onQueryChange: function(event) {
			window.clearTimeout(this.timeout);

				// Pressed ENTER
			if( event.keyCode === Event.KEY_RETURN ) {
				if( this.isNavigating() ) {
					this.goToActiveElement();
				} else {
//					this.headlet.submitIfNotEmpty();
					this.timeout = this.show.bind(this).delay(this.delay);
				}
				return;
			}

				// Pressed navigation arrows
			if (event.keyCode === Event.KEY_DOWN || event.keyCode === Event.KEY_UP) {
				if( this.suggest.visible() ) {
					var down = event.keyCode === Event.KEY_DOWN;
					this.navigate(down);
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
		 * Enter description here...
		 *
		 * @return	Boolean
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
		 * Enter description here...
		 *
		 * @param	Boolean	down
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
		 * Show headlet
		 */
		updateResults: function() {
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
			
			
			//this.load(this.headlet.getValue());
		},
		
		
		/**
		 * Enter description here...
		 *
		 * @param	Object	response
		 */
		onResultsUpdated: function(response) {
			this.navigatePos = -1;
			this.numElements = this.suggest.select('li li').size();

			this.hideBind = this.hideResults.bind(this);

			this.observeCloseEvents();
			
			this.showResults();
		},
		
		
		showResults: function() {
			var boxDim	= this.headlet.box.getDimensions();
			var offset	= this.headlet.box.cumulativeOffset();
			var sugDim	= this.suggest.getDimensions();
			
			this.suggest.setStyle({
				'left': offset.left - sugDim.width + boxDim.width - 1 + 'px',
				'top': offset.top + boxDim.height + 'px'
			});
			
			this.suggest.show();
		},



		/**
		 * Hide headlet
		 */
		hideResults: function() {
			this.suggest.hide();

			Event.stopObserving(document.body, 'click', this.hideBind);
		},



		/**
		 * Enter description here...
		 */
		makeDiv: function() {
			/*
			if( ! this.suggest ) {
				this.suggest = new Element('div', {
					'id':		'headletquicksearch-suggest',
					'class': 	'searchSuggest',
					'style': 	'display:none'
				});

				document.body.appendChild(this.suggest);
			}
			*/
			

			//this.suggest.addClassName('searchSuggest');

			/*
			
			*/
		},



		/**
		 * Enter description here...
		 */
		observeCloseEvents: function() {
			
			//Event.observe(document.body, 'click', this.hideResults.bind(this));
		}

	}

};