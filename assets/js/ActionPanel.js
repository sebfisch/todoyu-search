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

Todoyu.Ext.search.ActionPanel = {

	filter: Todoyu.Ext.search.Filter,



	/**
	 *
	 * @param name
	 */
	exportResults: function(name) {
		var idFilterSet = this.filter.getFiltersetID();
		var conditions	= this.filter.Conditions.getAll(true);
		var conjunction	= this.filter.getConjunction();

		var options = {
				'action':		'export',
				'tab':			this.filter.getActiveTab(),
				'exportname':	name,
				'idFilterSet':	idFilterSet,
				'conditions':	conditions,
				'conjunction':	conjunction
		};

		Todoyu.goTo('search', 'actionpanel',options , '', false);
	}
};
