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



	/**
	 *	Enter description here...
	 *
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
	 *	Enter description here...
	 *
	 */
	onContentClick: function(event) {
		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 *	Enter description here...
	 *
	 */
	onQueryClick: function(event) {
		this.Mode.hideModes();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 *	Enter description here...
	 *
	 */
	onBodyClick: function(event) {
		this.hideExtras();

		if( this.isEventInOwnContent(event) ) {
			event.stop();
		}
	},



	/**
	 *	Enter description here...
	 */
	hide: function() {
		this.hideContent();
		this.hideExtras();
		this.saveOpenStatus();
	},



	/**
	 *	Enter description here...
	 */
	hideExtras: function() {
		this.Mode.hideModes();
		this.Suggest.hideResults();
	},



	/**
	 *	Enter description here...
	 */
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
	}

};