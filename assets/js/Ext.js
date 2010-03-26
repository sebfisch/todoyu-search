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

/**
 *	Ext: search
 */

Todoyu.Ext.search = {

	PanelWidget: {},

	Headlet: {},


	/**
	 * Initialize
	 */
	init: function() {
		
	},



	/**
	 * Get name of currently active tab (e.g 'task', 'project')
	 *
	 * @return	String
	 */
	getActiveTabKey: function() {
		return Todoyu.Tabs.getActiveKey('search-tabs');
	},



	/**
	 * Refresh search results: get current filters + conditions, active tab and conjunction and get, show the search results accordingly
	 *
	 * @param	Integer	filterID
	 */
	refreshSearchResults: function(idActiveFilter)	{
		var filterConditions = Object.toJSON({
			Conditions: Todoyu.Ext.search.Filter.Conditions
		});

		var url		= Todoyu.getUrl('search', 'searchresults');
		var options	= {
			'parameters': {
				'activeFilter':					idActiveFilter,
				'additionalFilterConditions':	filterConditions,
				'useConditionsFromJSON':		true,
				'ActiveTab':					this.activeTab,
				'Conjunction':					Todoyu.Ext.search.Filter.Conjunction
			}
		};

		Todoyu.Ui.replace('search-results', url, options);
	}

};