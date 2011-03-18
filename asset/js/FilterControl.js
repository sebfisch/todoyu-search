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

Todoyu.Ext.search.FilterControl = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:					Todoyu.Ext.search,

	/**
	 * @property	conditionsObserver
	 * @type		Observer
	 */
	conditionsObserver:		null,

	/**
	 * @property	conjunctionObserver
	 * @type		Observer
	 */
	conjunctionObserver:	null,



	/**
	 * Initialize search filter controls: install observers
	 *
	 * @method	init
	 */
	init: function() {
		this.installObservers();
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		this.conditionsObserver = this.onConditionsChange.bindAsEventListener(this);
		this.conjunctionObserver = this.onConjunctionChange.bindAsEventListener(this);

		$('filtercontrol-conditions').observe('change', this.conditionsObserver);
		$('filtercontrol-conjunction').observe('change', this.conjunctionObserver);
	},



	/**
	 * Uninstall observers
	 *
	 * @method	uninstallObservers
	 */
	uninstallObservers: function() {
		$('filtercontrol-conditions').stopObserving('change', this.conditionsObserver);
		$('filtercontrol-conjunction').stopObserving('change', this.conjunctionObserver);

		this.conditionsObserver = null;
		this.conjunctionObserver = null;
	},



	/**
	 * Handler when condition changes
	 *
	 * @method	onConditionsChange
	 * @param	{Event}		event
	 */
	onConditionsChange: function(event) {
		var value 		= event.element().getValue();
		var type		= value.split('_').first();
		var condition	= value.split('_').last();

		event.element().selectedIndex = 0;

		this.ext.Filter.setFiltersetID(0);

		this.ext.Filter.addNewCondition(type, condition, null, false);
	},



	/**
	 * Enter description here...
	 *
	 * @method	onConjunctionChange
	 * @param	{Event}	event
	 */
	onConjunctionChange: function(event) {
		this.ext.Filter.updateResults();
	},



	/**
	 * Enter description here...
	 *
	 * @method	getConjunction
	 * @return	{String}
	 */
	getConjunction: function() {
		return $F('filtercontrol-conjunction');
	},



	/**
	 * Set conjunction value
	 *
	 * @method	setConjunction
	 * @param	{String}	conjunction		AND or OR
	 */
	setConjunction: function(conjunction) {
		$('filtercontrol-conjunction').selectedIndex = (conjunction === 'AND' ? 0 : 1);
	}

};