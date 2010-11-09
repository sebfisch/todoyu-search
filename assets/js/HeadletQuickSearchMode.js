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

Todoyu.Ext.search.Headlet.QuickSearch.Mode = {

	ext: Todoyu.Ext.search,

	headlet: null,

	mode: 0,

	button: null,

	modes: null,

	positioned: false,



	/**
	 * Initialize quick search modes option: declare properties, setup click observer
	 *
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
	 * @param	{Event}		event
	 */
	showModes: function(event) {
		var modes	= $('headlet-quicksearch-modes');

		if( modes.visible() ) {
			this.hideModes();
		} else {
			if( ! this.positioned ) {
				var contentDim		= this.headlet.content.getDimensions();
				var modeWidth		= this.modes.getWidth();

				var top		= contentDim.height;
				var left	= contentDim.width - modeWidth;

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
	 * Activate given quick search mode
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
	 * Get currently active quick search mode
	 *
	 * @return	{String}
	 */
	getMode: function() {
		return $F('headlet-quicksearch-mode');
	},



	/**
	 * Hide quick search modes selector
	 */
	hideModes: function() {
		$('headlet-quicksearch-modes').hide();
	}

};