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
 * Handles the filter
 *
 */

Todoyu.Ext.search.Filter = {

	/**
	 *	Ext shortcut
	 */
	ext:		Todoyu.Ext.search,

	activeTab:	null,



	/**
	 * Initialize search filters: inits active tab, active filterset + control + conditions, and updates to show the resp. results
	 *
	 * @param	Integer	idProject
	 * @param	Array	conditions
	 * @param	Boolean	updateResults
	 */
	init: function(activeTab, idFilterset, conditions, updateResults) {
		this.setActiveTab(activeTab);
		this.setFiltersetID(idFilterset);

		this.ext.FilterControl.init();

		this.initConditions(activeTab, conditions);

		if( updateResults == true )	{
			this.updateResults();
		}
	},



	/**
	 * Init search conditions as given for given tab, install filters and consequent autocompleter, negations to given tab
	 *
	 * @param	String	tab
	 * @param	Object	conditions
	 */
	initConditions: function(tab, conditions) {
		this.Conditions.clear();

		conditions.each(function(item) {
			var name	= item['filter'] + '-' + item['id'];
			var negate	= item['negate']==1;

			this.Conditions.add(item['id'], tab, item['filter'], item['value'], negate);

			this.WidgetArea.installAutocomplete(name);
			this.WidgetArea.installNegation(name);
		}.bind(this));
	},



	/**
	 * Set active tab to given tab
	 *
	 * @param	String	tab
	 */
	setActiveTab: function(tab)	{
		this.activeTab = tab;

		Todoyu.Tabs.setActive('search', tab);
	},



	/**
	 * Get currently active tab
	 *
	 * @return	String
	 */
	getActiveTab: function() {
		return this.activeTab;
	},



	/**
	 * Set current search filter to given ID
	 *
	 * @param Integer idFilterset
	 */
	setFiltersetID: function(idFilterset) {
		this.FilterID = idFilterset;
	},



	/**
	 * Get ID of current search filter
	 *
	 * @return	Integer
	 */
	getFiltersetID: function() {
		return this.FilterID;
	},



	/**
	 * Enter description here...
	 *
	 * @return	String
	 */
	getConjunction: function() {
		return this.ext.FilterControl.getConjunction();
	},



	/**
	 * Enter description here...
	 *
	 * @param	Object	event
	 * @param	String	tab
	 */
	onTabClick: function(event, tab)	{
		if( tab !== this.getActiveTab() ) {
			this.setActiveTab(tab);

			this.ext.Preference.saveActiveTab(tab);

			this.updateFilterArea(tab, 0);
		}
	},


	/**
	 * Enter description here...
	 */
	reset: function() {
		this.Conditions.clear();
		this.WidgetArea.clear();

		$('search-results').update('');
	},



	/**
	 * Enter description here...
	 *
	 * @param	String	tab
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
	 * @param	String	type
	 * @param	String	condition
	 * @param	String	value
	 * @param	Boolean	negate
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
	 * @param	String	name
	 */
	removeCondition: function(conditionName) {
		this.Conditions.remove(conditionName);
		this.WidgetArea.remove(conditionName);

		this.updateResults();
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type condition
	 */
	makeNewWidgetName: function(condition) {
		var numOfWidgets = this.Conditions.size();

		return 'new' + (numOfWidgets + 1);
	},



	/**
	 * Enter description here...
	 *
	 * @param String	tab
	 * @param Integer	idFilterset
	 */
	loadFilterset: function(tab, idFilterset) {
		if ( tab !== this.getActiveTab() ) {
			this.updateFilterArea(tab, idFilterset);
		} else {
			this.updateWidgetArea(tab, idFilterset);
			this.updateResults(tab, idFilterset);
		}
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type tab
	 *	@param Integer idFilterset
	 */
	updateFilterArea: function(tab, idFiterset) {
		var url		= Todoyu.getUrl('search', 'filterarea');
		var options	= {
			'parameters': {
				'action':		'load',
				'tab':			tab,
				'filterset':	idFiterset
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};

		this.setActiveTab(tab);

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type tab
	 *	@param Integer idFilterset
	 */
	updateWidgetArea: function(tab, idFilterset) {
		var url		= Todoyu.getUrl('search', 'widgetarea');
		var options	= {
			'parameters': {
				'action':		'load',
				'tab':			tab,
				'filterset':	idFilterset
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};
		var target	= 'widget-area';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Replace search results by result of current filter
	 *
	 * @param	Integer			idFilterset
	 * @param	unknown_type	conditions
	 * @param	unknown_type	conjunction
	 */
	updateResults: function(tab, idFilterset, conditions, conjunction) {
		tab 		= tab === undefined ? this.getActiveTab() : tab ;
		idFilterste	= idFilterset === undefined ? this.getFiltersetID() : idFilterset ;
		conditions	= conditions === undefined ? this.Conditions.getAll() : conditions ;
		conjunction	= conjunction === undefined ? this.getConjunction() : conjunction ;

		var url		= Todoyu.getUrl('search', 'searchresults');
		var options	= {
			'parameters': {
				'action':		'update',
				'tab':			tab,
				'filterset':	idFilterset,
				'conditions':	Object.toJSON(conditions),
				'conjunction':	conjunction
			},
			'onComplete': 	this.onResultsUpdated.bind(this, tab)
		};
		var target	= 'search-results';

		Todoyu.Ui.update(target, url, options);
	},



	onResultsUpdated: function(tab) {

	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 *	@param unknown_type value
	 */
	updateConditionValue: function(name, value) {
		this.setConditionValue(name, value);

		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Set the new value of a condition without updating the results
	 *
	 *	@param	String		name		Name of the condition/widget
	 *	@param	Mixed		value		New value
	 */
	setConditionValue: function(name, value) {
		this.Conditions.updateValue(name, value);
	},



	/**
	 * Update negation of conditon with given state
	 *
	 *	@param	String	name
	 *	@param	Boolean	negate
	 */
	updateConditionNegation: function(name, negate) {
		this.Conditions.updateNegation(name, negate);

		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 */
	toggleConditionNegation: function(name) {
		this.Conditions.toggleNegated(name);

		this.updateResults(this.getActiveTab(), 0);
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type onComplete
	 */
	saveCurrentAreaAsNewFilterset: function(onComplete) {
		if( this.Conditions.size() > 0 ) {
				// Get name for new filter
			var title = prompt('[LLL:search.newFilterLabel]', '[LLL:search.newFilterLabel.preset]');

				// If name entered
			if( title !== null ) {
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
			}
		} else {
			alert('[LLL:search.filterset.error.saveNoConditions]');
		}
	},



	/**
	 * Enter description here...
	 *
	 *	@param Integer idFilterset
	 *	@param unknown_type onComplete
	 */
	saveCurrentAreaAsFilterset: function(idFilterset, onComplete) {
		var url		= Todoyu.getUrl('search', 'filterset');
		var options	= {
			'parameters': {
				'action':		'save',
				'filterset':	idFilterset,
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
	 * Enter description here...
	 *
	 *	@param unknown_type tab
	 *	@param Integer idFilterset
	 */
	saveActiveFilterset: function(tab, idFilterset) {
		var action		= 'activeFilterset';
		var value	= tab;
		var idItem	= idFilterset;

		this.ext.Preference.save(action, value, idItem);
	}
};