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

	ext: Todoyu.Ext.search,

	searchField: null,

	/**
	 * Enter description here...
	 *
	 */
	init: function() {
		this.searchField = $('headletquicksearch-query');
		this.Suggest.init();
		this.Mode.init();
	},



	/**
	 * Enter description here...
	 *
	 */
	submit: function() {
		//$('headletquicksearch-form').submit();
		console.log('redirect to full search disabled at the moment');
	},



	/**
	 * Enter description here...
	 *
	 */
	submitIfNotEmpty: function() {
		if( ! this.isEmpty() ) {
			this.submit();
		}
	},



	/**
	 * Enter description here...
	 *
	 */
	getValue: function() {
		return $F(this.searchField).strip();
	},



	/**
	 * Enter description here...
	 *
	 */
	isEmpty: function() {
		return this.getValue() === '';
	},


	/**
	 * Enter description here...
	 *
	 */
	Mode: {

		mode: 0,

		button: null,

		/**
		 * Enter description here...
		 *
		 * @param unknown_type idFilterset
		 */
		init: function() {
			this.button = $('headletquicksearch-mode-btn');

			this.installObserver();
		},



		/**
		 * Enter description here...
		 *
		 */
		installObserver: function() {
			this.button.observe('click', this.show.bindAsEventListener(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type mode
		 */
		show: function(event) {
			var btnOffset	= this.button.cumulativeOffset();
			var btnHeight	= this.button.getHeight();

			var top			= btnOffset.top + btnHeight;
			var left		= btnOffset.left;

			$('headletquicksearch-modes').setStyle({
				'display': 'block',
				'left': left + 'px',
				'top': top + 1 + 'px'
			});

			$('headletquicksearch-modes').observe('click', this.onSelect.bindAsEventListener(this));
			Event.observe.delay(0.1, document.body, 'click', this.onBodyClick.bindAsEventListener(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type mode
		 */
		setMode: function(mode) {
			$('headletquicksearch-mode').value = mode;
			$('headletquicksearch-mode-icon').writeAttribute('class', 'searchmode-' + mode);
		},



		/**
		 * Enter description here...
		 *
		 */
		getMode: function() {
			return $F('headletquicksearch-mode');
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type event
		 */
		onSelect: function(event) {
			var mode = event.findElement('li').readAttribute('mode');

			this.setMode(mode);
			this.hide();
		},

		onBodyClick: function(event) {
			this.hide();
			$(document.body).stopObserving('click');
		},



		/**
		 * Enter description here...
		 *
		 */
		hide: function() {
			$('headletquicksearch-modes').hide();
		}

	},


	/**
	 * Quicksearch headlet suggestions
	 *
	 */
	Suggest: {

		ext: Todoyu.Ext.search,

		headlet: null,

		suggestID: 'headletquicksearch-suggest',

		frequency: 700,

		navigatePos: -1,

		navigateActive: null,

		numElements: 0,

		timeout: null,

		/**
		 * Enter description here...
		 *
		 */
		init: function() {
			this.headlet = this.ext.Headlet.QuickSearch;
			this.makeDiv();
			this.installObserver();
		},



		/**
		 * Enter description here...
		 *
		 */
		installObserver: function() {
			this.headlet.searchField.observe('keyup', this.onFieldUpdate.bind(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type event
		 */
		onFieldUpdate: function(event) {
			if( this.timeout !== null ) {
				window.clearTimeout(this.timeout);
			}

				// Pressed ENTER
			if( event.keyCode === Event.KEY_RETURN ) {
				if( this.isNavigating() ) {
					this.goToActiveElement();
				} else {
//					this.headlet.submitIfNotEmpty();
					this.timeout = this.show.bind(this).delay(this.frequency / 1000);
				}
				return;
			}

				// Pressed navigation arrows
			if (event.keyCode === Event.KEY_DOWN || event.keyCode === Event.KEY_UP) {
				if ($(this.suggestID).visible()) {
					var down = event.keyCode === Event.KEY_DOWN;
					this.navigate(down);
				}
				return;
			}

			if( this.headlet.isEmpty() ) {
				this.hide();
			} else {
				this.timeout = this.show.bind(this).delay(this.frequency / 1000);
			}
		},



		/**
		 * Enter description here...
		 *
		 */
		isNavigating: function() {
			return this.navigatePos > -1;
		},



		/**
		 * Enter description here...
		 *
		 */
		goToActiveElement: function() {
			eval(this.navigateActive.down().readAttribute('onclick'));
			this.hide();
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type down
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
			this.navigateActive = $(this.suggestID).down('li li', this.navigatePos);

				// Set element active
			this.navigateActive.addClassName('active');
		},



		/**
		 * Show headlet
		 *
		 */
		show: function() {
			this.load(this.headlet.getValue());
		},



		/**
		 * Hide headlet
		 *
		 */
		hide: function() {
			$(this.suggestID).hide();

			Event.stopObserving(document.body, 'click', this.hideBind);
		},



		/**
		 * Enter description here...
		 *
		 */
		makeDiv: function() {
			var suggest = '';

			if( ! Todoyu.exists(this.suggestID) ) {
				suggest = new Element('div', {
					'id': this.suggestID,
					'class': 'searchSuggest',
					'style': 'display:none'
				});

				document.body.appendChild(suggest);
			} else {
				suggest = $(this.suggestID);
			}

			var dim		= this.headlet.searchField.getDimensions();
			var offset	= this.headlet.searchField.cumulativeOffset();

			suggest.addClassName('searchSuggest');

			suggest.setStyle({
				'left': offset.left - suggest.getDimensions().width + dim.width - 1 + 'px',
				'top': offset.top + dim.height + 'px'
			});
		},



		/**
		 * Enter description here...
		 *
		 */
		observeCloseEvents: function() {
			Event.observe(document.body, 'click', this.hide.bind(this));
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type query
		 */
		load: function(query) {
			var url		= Todoyu.getUrl('search', 'suggest');
			var options	= {
				'parameters': {
					'action':	'getSuggestions',
					'query':	query,
					'mode':		this.headlet.Mode.getMode()
				},
				'onComplete':	this.display.bind(this)
			};
			var target	= this.suggestID;

			Todoyu.Ui.update(target, url, options);
		},



		/**
		 * Enter description here...
		 *
		 * @param unknown_type response
		 */
		display: function(response) {
			var el = $(this.suggestID);

			el.show();

			this.navigatePos = -1;
			this.numElements = el.select('li li').size();

			this.hideBind = this.hide.bind(this);

			this.observeCloseEvents();
		}

	}

};