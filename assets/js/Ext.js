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