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

Todoyu.Ext.search.Headlet.QuickSearch = {

	/**
	 * Ext namespace shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:		Todoyu.Ext.search,

	query:		null,

	button:		null,

	content:	null,

	bodyClickObserver: null,



	/**
	 * Initialize quick search headlet: install click observer, initialize search input autoCompleter value suggestion and modes selector
	 *
	 * @method	init
	 */
	init: function() {
		this.query	= $('todoyusearchheadletquicksearch-query');
		this.button	= this.getButton();
		this.content= this.getContent();

		this.query.observe('click', this.onQueryClick.bindAsEventListener(this));

		this.Suggest.init();
		this.Mode.init();
	},



	/**
	 * Handle headlet button clicks: toggle headlet content visibility
	 *
	 * @method	onButtonClick
	 * @param	{Event}		event
	 */
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



	/**
	 * Callback for quicksearch headlet content click
	 *
	 * @method	onContentClick
	 * @param	{Event}		event
	 */
	onContentClick: function(event) {
		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 * Upon clicking search query input: hide modes selection
	 *
	 * @method	onQueryClick
	 * @param	{Event}		event
	 */
	onQueryClick: function(event) {
		this.Mode.hideModes();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 * Upon click: hide mode selector and result suggestions.
	 *
	 * @method	onBodyClick
	 * @param	{Event}		event
	 */
	onBodyClick: function(event) {
		this.hideExtras();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 * Hide quick search content and extras
	 *
	 * @method	hide
	 */
	hide: function() {
		this.hideContent();
		this.hideExtras();
		this.saveOpenStatus();
	},



	/**
	 * Hide extras of quick search: mode selector, result suggestions
	 *
	 * @method	hideExtras
	 */
	hideExtras: function() {
		this.Mode.hideModes();
		this.Suggest.hideResults();
	},



	/**
	 * Focus search query input field
	 *
	 * @method	focus
	 */
	focus: function() {
		this.query.select();
	},




	/**
	 * Submit quick search form
	 *
	 * @method	submit
	 * @todo	is disabled, check and enable
	 */
	submit: function() {
		//$('headlet-quicksearch-form').submit();
		Todoyu.notifyInfo('redirect to full search disabled at the moment');
	},



	/**
	 * If any search query given: submit search form
	 *
	 * @method	submitIfNotEmpty
	 */
	submitIfNotEmpty: function() {
		if( ! this.isEmpty() ) {
			this.submit();
		}
	},



	/**
	 * Get search query input
	 *
	 * @method	getValue
	 * @return	{String}
	 */
	getValue: function() {
		return $F(this.query).strip();
	},



	/**
	 * Check whether search query is empty
	 *
	 * @method	isEmpty
	 * @return	{Boolean}
	 */
	isEmpty: function() {
		return this.getValue() === '';
	}

};