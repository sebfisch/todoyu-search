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
 * @module	Search
 */

/**
 * @class		search.ActionPanel
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.search.ActionPanel = {

	/**
	 * @property	filter
	 * @type		Object
	 */
	filter: Todoyu.Ext.search.Filter,



	/**
	 * Evoke results CSV export
	 *
	 * @method	exportResults
	 * @param	{String}	name
	 */
	exportResults: function(name) {
		if( Todoyu.Ext.search.Filter.Conditions.size() > 0 ) {
		var idFilterSet = this.filter.getFiltersetID();
		var conditions	= this.filter.Conditions.getAll(true);
		var conjunction	= this.filter.getConjunction();

		var options = {
				action:		'export',
				'tab':			this.filter.getActiveTab(),
				'exportname':	name,
				'idFilterSet':	idFilterSet,
				'conditions':	conditions,
				'conjunction':	conjunction
		};

		Todoyu.goTo('search', 'actionpanel', options , '', false);
		} else {
			alert('[LLL:search.ext.export.error.saveEmpty]');
		}
	}

};
