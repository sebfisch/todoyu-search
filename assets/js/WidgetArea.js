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
 *	Ext: search
 */

Todoyu.Ext.search.Filter.WidgetArea = {

	/**
	 * Extension backlink
	 * @var	{Object}	ext
	 */
	ext: Todoyu.Ext.search,

	areaID: 'widget-area',

	autocompleters: {},

	/**
	 * Special configuration added by some widgets
	 * This is a temporary container for widget config
	 */
	specialConfig: {},



	/**
	 * Add given filter widget to search page widget area
	 *
	 * @param	{String}	name
	 * @param	{String}	type
	 * @param	{String}	condition
	 * @param	{String}	value
	 * @param	{Boolean}	negate
	 */
	add: function(name, type, condition, value, negate) {
		var url		= Todoyu.getUrl('search', 'widgetarea');
		var options	= {
			'parameters': {
				'action':		'add',
				'name':			name,
				'type':			type,
				'condition':	condition,
				'value':		value,
				'negate':		negate ? 1 : 0
			},
			'onComplete':	this.onAdded.bind(this, name, condition)
		};
		var target	= this.areaID;

		Todoyu.Ui.insert(target, url, options);
	},



	/**
	 * Evoked after adding filter widget. Installs widget autoCompleter and negation handling
	 *
	 * @param	{String}			name
	 * @param	{String}			condition
	 * @param	{Ajax.Response}		response
	 */
	onAdded: function(name, condition, response) {
		var widgetID	= condition + '-' + name;

		this.installAutocomplete.bind(this).defer(widgetID);
		this.installNegation.bind(this).defer(widgetID);
	},



	/**
	 * Remove given widget from widget area
	 *
	 * @param	{String}	name
	 */
	remove: function(name) {
		$(name).remove();
	},



	/**
	 * Clear widget area (refresh)
	 */
	clear: function() {
		$(this.areaID).update('');
	},



	/**
	 * Get amount of filter widgets in widget area
	 *
	 * @return	{Number}
	 */
	getNumOfWidgets: function() {
		return $(this.areaID).select('.filterWidget').size();
	},



	/**
	 * Install autoCompleter to ('textAC' input field of) given filter widget
	 *
	 * @param	{String}	name
	 */
	installAutocomplete: function(name) {
		if( $(name) ) {
			var acField = $(name).select('input.textAC')[0];

			if( Object.isElement(acField) ) {
				var acUrl	= Todoyu.getUrl('search', 'filtercontroller');
				var widgetID= acField.id.split('-').slice(2, 4).join('-');
				var params	= Object.toQueryString({
					'action':				'autocompletion',
					'completionID':			name,
					'filtertype':			this.ext.Filter.getActiveTab()
				});
				var options	= {
					'parameters':			params,
					'paramName':			'sword',
					'minChars':				2,
					'afterUpdateElement':	this.onAutocompleteSelect.bind(this, name)
				};
				var suggestID= acField.id + '-suggestions';

					// Override config with specialConfig if available
				if( this.specialConfig[name] && this.specialConfig[name]['acOptions'] ) {
					options = $H(options).merge(this.specialConfig[name]['acOptions']).toObject();

					if( typeof options.afterUpdateElement === 'string' ) {
						options.afterUpdateElement = Todoyu.getFunctionFromString(options.afterUpdateElement, true).bind(this, name);
					}
				}

				this.autocompleters[name] = new Todoyu.Autocompleter(acField, suggestID, acUrl, options);
			}
		}
	},



	/**
	 * Handle selection of autoCompleter suggestion: send value to condition of widget
	 *
	 * @param	{String}	name
	 * @param	{Element}	textInput
	 * @param	{Element} 	listElement
	 */
	onAutocompleteSelect: function(name, textInput, listElement) {
		var idItem	= listElement.id;

		this.ext.Filter.updateConditionValue(name, idItem);
	},



	/**
	 * Install widget negating option click observer
	 *
	 * @param	{String}	name
	 */
	installNegation: function(name) {
		if( $(name) ) {
			var negElement = $(name).select('span.negation')[0];

			if( Object.isElement(negElement) ) {
				negElement.observe('click', this.onNegation.bindAsEventListener(this, name));
			}
		}
	},



	/**
	 * Handle filter widget negation: invoke toggle of child elements' conditions
	 *
	 * @param	{Event}		event
	 * @param	{String}	name
	 */
	onNegation: function(event, name) {
		this.ext.Filter.toggleConditionNegation(name);

		event.findElement('span.negation').childElements().invoke('toggle');
	},



	/**
	 * Add given "special" configuration to widget area
	 *
	 * @param	{String}		name
	 * @param	{unknown_type}	config
	 */
	addSpecialConfig: function(name, config) {
		this.specialConfig[name] = config;
	}

};