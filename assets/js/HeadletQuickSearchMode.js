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

Todoyu.Ext.search.Headlet.QuickSearch.Mode = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext: Todoyu.Ext.search,

	headlet: null,

	mode: 0,

	button: null,

	modes: null,

	positioned: false,



	/**
	 * Initialize quick search modes option: declare properties, setup click observer
	 *
	 * @method	init
	 */
	init: function() {
		this.headlet = this.ext.Headlet.QuickSearch;

		this.button = $('headlet-quicksearch-mode-button');
		this.modes	= $('headlet-quicksearch-modes');

		this.button.observe('click', this.showModes.bindAsEventListener(this));
	},



	/**
	 * Show quick search modes selector
	 *
	 * @method	showModes
	 * @param	{Event}		event
	 */
	showModes: function(event) {
		if( this.modes.visible() ) {
			this.hideModes();
		} else {
			this.modes.show();

			if( ! this.positioned ) {
				this.positionModes();
			}

			var amountModes = $$('#headlet-quicksearch-modes li').length;
			$('headlet-quicksearch-form').style.height= ((amountModes * 21) + 18 ) + 'px';

			this.headlet.Suggest.hideResults();
		}
	},



	/**
	 * Hide quick search modes selector
	 *
	 * @method	hideModes
	 */
	hideModes: function() {
		this.modes.hide();
		$('headlet-quicksearch-form').style.height='16px';
	},



	/**
	 * Activate given quick search mode
	 *
	 * @method	setMode
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
	 * Get currently active quick search mode
	 *
	 * @method	getMode
	 * @return	{String}
	 */
	getMode: function() {
		return $F('headlet-quicksearch-mode');
	},



	/**
	 * Set search modes sub menu position
	 *
	 * @method	positionModes
	 */
	positionModes: function() {
		var contentDim		= this.headlet.content.getDimensions();
		var modeWidth		= this.modes.getWidth();

		var top		= contentDim.height - 24;
		var left	= contentDim.width - modeWidth + 1;

		this.modes.setStyle({
			'position':	'absolute',
			'left':		left + 'px',
			'top':		top + 'px'
		});

		this.positioned = true;
	}

};