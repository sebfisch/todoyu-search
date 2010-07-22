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

/**
 * Handles the filter
 */

Todoyu.Ext.search.Filter = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:		Todoyu.Ext.search,

	activeTab:	null,


	/**
	 * Initialize search filters: inits active tab, active filterset + control + conditions
	 * and updates to show the resp. results
	 *
	 * @param	{String}	activeTab
	 * @param	{Number}	idFilterset
	 * @param	{Array}		conditions			List of conditions saved in active filterset
	 * @param	{Boolean}	updateResults		Update results. Not necessary on initial load
	 */
	init: function(activeTab, idFilterset, conditions, updateResults) {
		this.setActiveTab(activeTab);
		this.setFiltersetID(idFilterset);

		this.ext.FilterControl.init();

		this.initConditions(activeTab, conditions);

		if( updateResults === true )	{
			this.updateResults();
		}
	},



	/**
	 * Init search conditions as given for given tab, install filters and consequent autocompleter, negations to given tab
	 *
	 * @param	{String}	tab
	 * @param	{Array}		conditions
	 */
	initConditions: function(tab, conditions) {
		this.Conditions.clear();

		conditions.each(function(tab, condition) {
			var name	= condition.filter + '-' + condition.id;

			this.Conditions.add(condition.id, tab, condition.filter, condition.value, condition.negate);

			this.WidgetArea.installAutocomplete(name);
			this.WidgetArea.installNegation(name);
		}.bind(this, tab));
	},



	/**
	 * Set active tab to given tab
	 *
	 * @param	{String}	tab
	 */
	setActiveTab: function(tab)	{
		this.activeTab = tab;

		Todoyu.Tabs.setActive('search', tab);
	},



	/**
	 * Get currently active tab
	 *
	 * @return	{String}
	 */
	getActiveTab: function() {
		return this.activeTab;
	},



	/**
	 * Set current search filter to given ID
	 *
	 * @param {Number} idFilterset
	 */
	setFiltersetID: function(idFilterset) {
		this.FilterID = idFilterset;
	},



	/**
	 * Get ID of current search filter
	 *
	 * @return	{Number}
	 */
	getFiltersetID: function() {
		return this.FilterID;
	},



	/**
	 * Get logical filters conjunction (AND / OR)
	 *
	 * @return	{String}
	 */
	getConjunction: function() {
		return this.ext.FilterControl.getConjunction();
	},



	/**
	 * Handle tab click: activate, save pref, evoke content update
	 *
	 * @param	{Object}	event
	 * @param	{String}	tab
	 */
	onTabClick: function(event, tab)	{
		if( tab !== this.getActiveTab() ) {
			this.setActiveTab(tab);
			this.ext.Preference.saveActiveTab(tab);
			this.updateFilterArea(tab, 0);
		}
	},


	/**
	 * Reset filters
	 */
	reset: function() {
		this.Conditions.clear();
		this.WidgetArea.clear();
		this.setFiltersetID(0);

		$('search-results').update('');
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	tab
	 */
	updateControll: function(tab) {
		var url		= Todoyu.getUrl('search', 'filteractioncontroll');
		var options = {
			'parameters': {
				'action':	'load',
				'tab':		tab
			}
		};
		var target	= 'filterActionControls';

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param	{String}	type
	 * @param	{String}	condition
	 * @param	{String}	value
	 * @param	{Boolean}	negate
	 */
	addNewCondition: function(type, condition, value, negate) {
		var name = this.makeNewWidgetName(condition);

		this.Conditions.add(name, type, condition, value, negate);
		this.WidgetArea.add(name, type, condition, value, negate);

		this.updateResults();
	},



	/**
	 * Remove given condition from current search filter conditions and evoke refresh of results
	 *
	 * @param	{String}	conditionName
	 */
	removeCondition: function(conditionName) {
		this.Conditions.remove(conditionName);
		this.WidgetArea.remove(conditionName);

		this.updateResults();
	},



	/**
	 * Build a new name for a new added widget
	 *
	 * @param	{String}	condition
	 * @return	{String}	new + counter number
	 */
	makeNewWidgetName: function(condition) {
		var numOfWidgets = this.Conditions.size();

		return 'new' + (numOfWidgets + 1);
	},



	/**
	 * Load a filterSet by ID, update the widget area and the result list
	 *
	 * @param	{String}	tab
	 * @param	{Number}	idFilterSet
	 */
	loadFilterset: function(tab, idFilterSet) {
		if( tab !== this.getActiveTab() ) {
			this.updateFilterArea(tab, idFilterSet);
		} else {
			this.updateWidgetArea(tab, idFilterSet);
			this.updateResults(tab, idFilterSet);
		}
	},



	/**
	 * Update the filter area for a filterSet, update tab, widgets and results
	 *
	 * @param	{String}	tab
	 * @param	{Number}	idFilterSet
	 */
	updateFilterArea: function(tab, idFilterSet) {
		var url		= Todoyu.getUrl('search', 'filterarea');
		var options	= {
			'parameters': {
				'action':		'load',
				'tab':			tab,
				'filterset':	idFilterSet
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};

		this.setActiveTab(tab);

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Update the widget area with the widget of the selected filterSet
	 *
	 * @param	{String}	tab
	 * @param	{Number}	idFilterSet
	 */
	updateWidgetArea: function(tab, idFilterSet) {
		var url		= Todoyu.getUrl('search', 'widgetarea');
		var options	= {
			'parameters': {
				'action':		'load',
				'tab':			tab,
				'filterset':	idFilterSet
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};
		var target	= 'widget-area';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Replace search results by result of current filter
	 *
	 * @param	{Number}		idFilterSet
	 * @param	{Array}			conditions
	 * @param	{String}		conjunction
	 */
	updateResults: function(tab, idFilterSet, conditions, conjunction) {
		tab 		= ( tab === undefined ) ? this.getActiveTab() : tab ;
		idFilterSet	= ( idFilterSet === undefined ) ? this.getFiltersetID() : idFilterSet ;
		conditions	= ( conditions === undefined ) ? this.Conditions.getAll() : conditions ;
		conjunction	= ( conjunction === undefined ) ? this.getConjunction() : conjunction ;

		var url		= Todoyu.getUrl('search', 'searchresults');
		var options	= {
			'parameters': {
				'action':		'update',
				'tab':			tab,
				'filterset':	idFilterSet,
				'conditions':	Object.toJSON(conditions),
				'conjunction':	conjunction
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};
		var target	= 'search-results';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handler when search results are updated
	 *
	 * @param	{String}	tab
	 */
	onResultsUpdated: function(tab) {
		Todoyu.Hook.exec('searchResultsUpdated', tab);
	},



	/**
	 * Update the value of a condition
	 *
	 * @param	{String}	name
	 * @param	{String}	value
	 */
	updateConditionValue: function(name, value) {
		this.setConditionValue(name, value);
		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Set the new value of a condition without updating the results
	 *
	 * @param	{String}		name		Name of the condition/widget
	 * @param	{String}		value		New value
	 */
	setConditionValue: function(name, value) {
		this.Conditions.updateValue(name, value);
	},



	/**
	 * Update negation of conditon with given state
	 *
	 * @param	{String}	name
	 * @param	{Boolean}	negate
	 */
	updateConditionNegation: function(name, negate) {
		this.Conditions.updateNegation(name, negate);
		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Change the negation of a condition
	 *
	 * @param	{String}	 name
	 */
	toggleConditionNegation: function(name) {
		this.Conditions.toggleNegated(name);
		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Save the current widget collection as a new filterset
	 *
	 * @param	{Function}		onComplete
	 */
	saveCurrentAreaAsNewFilterset: function(onComplete) {
		if( this.Conditions.size() > 0 ) {
				// Get name for new filter
			var title 	= prompt('[LLL:search.newFilterLabel]', '[LLL:search.newFilterLabel.preset]');
			
				// Canceled saving
			if( title === null ) {
				return;
			}
				// No name entered
			if( title.strip() === '' ) {
				alert('[LLL:search.filterset.error.saveEmptyName]');
				return;
			}
			
				// Save filterSet
			var url		= Todoyu.getUrl('search', 'filterset');
			var options	= {
				'parameters': {
					'action':		'saveAsNew',
					'title':		title,
					'type':			this.getActiveTab(),
					'conditions':	this.Conditions.getAll(true),
					'conjunction':	this.getConjunction()
				}
			};

			if( onComplete !== undefined ) {
				options.onComplete = onComplete;
			}

			Todoyu.send(url, options);			
		} else {
			alert('[LLL:search.filterset.error.saveNoConditions]');
		}
	},



	/**
	 * Save current collection of filters as filterSet
	 *
	 * @param	{Number}	idFilterSet
	 * @param	{Function}	 onComplete
	 */
	saveCurrentAreaAsFilterset: function(idFilterSet, onComplete) {
		var url		= Todoyu.getUrl('search', 'filterset');
		var options	= {
			'parameters': {
				'action':		'save',
				'filterset':	idFilterSet,
				'tab':			this.getActiveTab(),
				'conditions':	this.Conditions.getAll(true),
				'conjunction':	this.getConjunction()
			}
		};

		if( onComplete !== undefined ) {
			options.onComplete = onComplete;
		}

		Todoyu.send(url, options);
	},



	/**
	 * Save pref: currently active filterSet ID
	 *
	 * @param {String}	tab
	 * @param {Number}	idFilterSet
	 */
	saveActiveFilterset: function(tab, idFilterSet) {
		var action		= 'activeFilterset';

		this.ext.Preference.save(action, tab, idFilterSet);
	}

};