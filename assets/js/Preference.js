/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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

Todoyu.Ext.search.Preference = {

	/**
	 * Ext shortcut
	 */
	ext:	Todoyu.Ext.search,



	/**
	 * Enter description here...
	 *
	 * @param	{String}	action
	 * @param	{String}	value
	 * @param	{String}	idItem
	 * @param	unknown_type onComplete
	 */
	save: function(action, value, idItem, onComplete) {
		Todoyu.Pref.save('search', action, value, idItem, onComplete);
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	tab
	 */
	saveActiveTab: function(tab)	{
		var action = 'saveActiveTab';

		this.sendAction(action, tab);
	},



	/**
	 * Enter description here...
	 */
	saveCurrentFilter: function()	{
		var action	= 'saveCurrentFilterSet';
		var currentFilterSet = Todoyu.Ext.search.Filter.FilterID;

		this.sendAction(action, currentFilterSet);
	},



	/**
	 * Enter description here...
	 */
	removeCurrentFilter: function()	{
		var action	= 'removeCurrentFilterSet';

		this.sendAction(action, '');
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	elementID
	 * @param	{Boolean}	elementDisplay
	 */
	saveToggeling: function(elementID, elementDisplay)	{
		var action = 'saveToggleStatus';

		var value = Object.toJSON({
			elementID:		elementID,
			elementDisplay:	elementDisplay
		});

		this.sendAction(action, value);
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	list
	 */
	saveOrder: function(value)	{
		var action = 'saveOrder';

		this.sendAction(action, value);
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	action
	 * @param	{String}	value
	 */
	sendAction: function(action, value)	{
		var url = Todoyu.getUrl('search', 'preference');
		var options = {
			'parameters': {
				'action':	action,
				'value':	value
			}
		};

		Todoyu.send(url, options);
	}
};