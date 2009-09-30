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

Todoyu.Ext.search.Preference = {

	ext: Todoyu.Ext.search,



	/**
	 * Enter description here...
	 *
	 * @param unknown_type cmd
	 * @param unknown_type value
	 * @param unknown_type idItem
	 * @param unknown_type onComplete
	 */
	save: function(cmd, value, idItem, onComplete) {
		Todoyu.Pref.save('search', cmd, value, idItem, onComplete)
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type tab
	 */
	saveActiveTab: function(tab)	{
		var cmd = 'saveActiveTab';

		this.sendCommand(cmd, tab);
	},



	/**
	 * Enter description here...
	 *
	 */
	saveCurrentFilter: function()	{
		var cmd	= 'saveCurrentFilterSet';
		var currentFilterSet = Todoyu.Ext.search.Filter.FilterID;

		this.sendCommand(cmd, currentFilterSet);
	},



	/**
	 * Enter description here...
	 *
	 */
	removeCurrentFilter: function()	{
		var cmd	= 'removeCurrentFilterSet';

		this.sendCommand(cmd, '');
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type elementID
	 * @param unknown_type elementDisplay
	 */
	saveToggeling: function(elementID, elementDisplay)	{
		var cmd = 'saveToggleStatus';

		var value = Object.toJSON({
			elementID: elementID,
			elementDisplay: elementDisplay
		});

		this.sendCommand(cmd, value);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type list
	 */
	saveOrder: function(list)	{
		var cmd = 'saveOrder';
		var value = list;

		this.sendCommand(cmd, value);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type cmd
	 * @param unknown_type value
	 */
	sendCommand: function(cmd, value)	{
		var url = Todoyu.getUrl('search', 'preference');
		var options = {
			'parameters': {
				'cmd': cmd,
				'value': value
			}
		};

		Todoyu.send(url, options);
	}
};