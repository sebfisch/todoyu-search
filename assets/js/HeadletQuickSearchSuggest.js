/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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

/**
 * Quick search headlet input autoCompletion suggestions
 */
Todoyu.Ext.search.Headlet.QuickSearch.Suggest = {

	/**
	 * Ext shortcut
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
	 * Initialize quick search query input suggesting
	 *
	 * @method	init
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
	 * @method	onQueryChange
	 * @param	{Event}		event
	 */
	onQueryChange: function(event) {
		window.clearTimeout(this.timeout);

			// Pressed [ENTER]
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

			// Pressed [ESC] (hide results or whole headlet)
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
	 * @method	isNavigating
	 * @return	{Boolean}
	 */
	isNavigating: function() {
		return this.navigatePos > -1;
	},



	/**
	 * Enter description here...
	 *
	 * @method	goToActiveElement
	 */
	goToActiveElement: function() {
		eval(this.navigateActive.down().readAttribute('onclick'));
		this.hide();
	},



	/**
	 * Navigate in result list (up and down)
	 *
	 * @method	navigate
	 * @param	{Boolean}	down		Navigate down. Yes or No?
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
	 * Update suggestion container with new results
	 *
	 * @method	updateResults
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
	 * @method	onResultsUpdated
	 * @param	{Ajax.Response}		response
	 */
	onResultsUpdated: function(response) {
		this.navigatePos = -1;
		this.numElements = this.suggest.select('li li').size();

		this.showResults();
	},



	/**
	 * Show suggested results container on right position
	 *
	 * @method	showResults
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
	 * Hide suggested results
	 *
	 * @method	hideResults
	 */
	hideResults: function() {
		this.suggest.hide();
	},



	/**
	 * Check whether results are visible
	 *
	 * @method	isResultsVisible
	 * @return  {Boolean}
	 */
	isResultsVisible: function() {
		return this.suggest.visible();
	}

};