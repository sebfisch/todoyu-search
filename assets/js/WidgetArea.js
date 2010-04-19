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

Todoyu.Ext.search.Filter.WidgetArea = {

	ext: Todoyu.Ext.search,

	areaID: 'widget-area',

	autocompleters: {},
	
	/**
	 * Special configuration added by some widgets
	 * This is a temporary container for widget config
	 */
	specialConfig: {},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 * @param unknown_type type
	 * @param unknown_type condition
	 * @param unknown_type value
	 * @param unknown_type negate
	 */
	add: function(name, type, condition, value, negate) {
		var url		= Todoyu.getUrl('search', 'widgetarea');
		var options	= {
			'parameters': {
				'action': 'add',
				'name': name,
				'type': type,
				'condition': condition,
				'value': value,
				'negate': negate ? 1 : 0
			},
			'onComplete': this.onAdded.bind(this, name, condition)
		};
		var target	= this.areaID;

		Todoyu.Ui.insert(target, url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 * @param unknown_type condition
	 * @param unknown_type response
	 */
	onAdded: function(name, condition, response) {
		var widgetID	= condition + '-' + name;
		
		this.installAutocomplete.bind(this).defer(widgetID);
		this.installNegation.bind(this).defer(widgetID);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 */
	remove: function(name) {
		$(name).remove();
	},



	/**
	 * Enter description here...
	 */
	clear: function() {
		$(this.areaID).update('');
	},



	/**
	 * Enter description here...
	 */
	getNumOfWidgets: function() {
		return $(this.areaID).select('.filterWidget').size();
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 */
	installAutocomplete: function(name) {
		if( $(name) )	{
			var acField = $(name).select('input.textinputAC')[0];
	
			if( Object.isElement(acField) ) {
				var acUrl	= Todoyu.getUrl('search', 'filtercontroller');
				var widgetID= acField.id.split('-').slice(2, 4).join('-');
				var params	= Object.toQueryString({
					'action': 'autocompletion',
					'completionID': name,
					'filtertype': this.ext.Filter.getActiveTab()
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
						f = Todoyu.getFunctionFromString(options.afterUpdateElement);
						options.afterUpdateElement = f.bind(this, name);
					}
				}
	
				this.autocompleters[name] = new Todoyu.Autocompleter(acField.id, suggestID, acUrl, options);
			}
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 * @param unknown_type textInput
	 * @param unknown_type listElement
	 */
	onAutocompleteSelect: function(name, textInput, listElement) {
		var idItem	= listElement.id;

		this.ext.Filter.updateConditionValue(name, idItem);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type name
	 */
	installNegation: function(name) {
		if( $(name) )	{
			var negElement = $(name).select('span.negation')[0];
	
			if( Object.isElement(negElement) ) {
				negElement.observe('click', this.onNegation.bindAsEventListener(this, name));
			}
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type event
	 * @param unknown_type name
	 */
	onNegation: function(event, name) {
		this.ext.Filter.toggleConditionNegation(name);

		event.findElement('span.negation').childElements().invoke('toggle');
	},
	
	addSpecialConfig: function(name, config) {
		this.specialConfig[name] = config;
	}

};