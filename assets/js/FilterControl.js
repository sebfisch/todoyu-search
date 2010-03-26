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

Todoyu.Ext.search.FilterControl = {

	/**
	 *	Ext shortcut
	 */
	ext:					Todoyu.Ext.search,

	conditionsObserver:		null,
	conjunctionObserver:	null,



	/**
	 * Initialize search filter controls: install observers
	 */
	init: function() {
		this.installObservers();
	},



	/**
	 * Install observers
	 */
	installObservers: function() {
		this.conditionsObserver = this.onConditionsChange.bindAsEventListener(this);
		this.conjunctionObserver = this.onConjunctionChange.bindAsEventListener(this);

		$('filtercontrol-conditions').observe('change', this.conditionsObserver);
		$('filtercontrol-conjunction').observe('change', this.conjunctionObserver);
	},



	/**
	 * Uninstall observers
	 */
	uninstallObservers: function() {
		$('filtercontrol-conditions').stopObserving('change', this.conditionsObserver);
		$('filtercontrol-conjunction').stopObserving('change', this.conjunctionObserver);

		this.conditionsObserver = null;
		this.conjunctionObserver = null;
	},



	/**
	 * Enter description here...
	 *
	 * @param	Object	event
	 */
	onConditionsChange: function(event) {
		var value 		= event.element().getValue();
		var type		= value.split('_').first();
		var condition	= value.split('_').last();

		event.element().selectedIndex = 0;

		this.ext.Filter.addNewCondition(type, condition, null, false);
	},



	/**
	 * Enter description here...
	 *
	 * @param	Object	event
	 */
	onConjunctionChange: function(event) {
		var value	= event.element().getValue();

		this.ext.Filter.updateResults();
	},



	/**
	 * Enter description here...
	 *
	 * @return	String
	 */
	getConjunction: function() {
		return $F('filtercontrol-conjunction')
	}

};