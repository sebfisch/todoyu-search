/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Filter widget in search area
 */
Todoyu.Ext.search.FilterWidget = {

	/**
	 *	Ext shortcut
	 */
	ext:		Todoyu.Ext.search,

	/**
	 * Timeouts of widgets
	 */
	timeout:	{},



	/**
	 * Add a new widget to the filter area
	 * 
	 * @param	DomElement		select			The select element where the new widget has ben chosen
	 */
	addWidgetToFilterArea: function(select)	{
		var chosenWidget = $(select).getValue();

			// reset selector
		select.options[0].selected = true;

			// count of equal elements. (used for the ID)
		var numOfWidget = this.detectNumOfWidget(chosenWidget);
		var url			= Todoyu.getUrl('search', 'filtercontroller');
		var options		= {
			'parameters': {
				'choosenWidget':	chosenWidget,
				'numOfWidget':		'new' + numOfWidget,
				'action':			'addfilterwidget',
				'filterID':			Todoyu.Ext.search.Filter.FilterID
			},
			'onComplete': function(response)	{
				var WidgetID = chosenWidget.split('_')[1] + '-new' + numOfWidget;
				if( $(WidgetID) )	{
					Todoyu.Ext.search.Filter.addFilterWidgetToList($(WidgetID));
					this.initAutocompletionSingle(WidgetID);
					this.initNegationSingle(WidgetID);
				}
			}.bind(this)
		};

		Todoyu.Ui.insert('widget-area', url, options);
	},



	/**
	 * Remove given filter widget from area
	 *
	 * @param	String	widgetID
	 */
	removeWidgetFromFilterArea: function(widgetID)	{
		$(widgetID).remove();
		this.ext.Filter.removeConditionFromFilter(widgetID);
	},



	/**
	 * Detect number of widget
	 *
	 * @param	String	classNameOfWidget
	 * @return	Integer
	 */
	detectNumOfWidget: function(classNameOfWidget)	{
		var className = classNameOfWidget.split('_');
		if( $('widget-area').select('.' + className[1]) ){
			return $('widget-area').select('.' + className[1]).length;
		} else {
			return 0;
		}
	},



	/**
	 * Autocompletion Part
	 */
	initAutocompletion: function()	{
		var foundAutocompleter = $('widget-area').select('.autocomplete');
		if( foundAutocompleter.length > 0 )	{
			foundAutocompleter.each(
				function(autocompleter)	{
					this.setUpAutocompleter(autocompleter.id);
				}.bind(this)
			);
		}
	},



	/**
	 * Initialize given autocompleter
	 *
	 *	@param String	elementID
	 */
	initAutocompletionSingle: function(elementID)	{
		var autocompletion = $(elementID).select('.autocomplete');
		if( autocompletion.length > 0 )	{
			this.setUpAutocompleter(autocompletion[0].id);
		}
	},



	/**
	 * Init widget negation: find and start observing all negation buttons
	 */
	initNegation: function()	{
		var foundNegations = $('widget-area').select('.negation');
		if( foundNegations.length > 0 )	{
			foundNegations.each(
				function(negation)	{
					this.setUpNegation(negation.id);
				}.bind(this)
			);
		}
	},



	/**
	 * Init single negation: find and start observing negation button to given widget
	 *
	 * @param	String	elementID
	 */
	initNegationSingle: function(elementID)	{
		var negation = $(elementID).select('.negation');
		if( negation.length > 0 )	{
			this.setUpNegation(negation[0].id);
		}
	},



	/**
	 * Setup given autocompleter
	 *
	 * @param String	autoCompleterID
	 */
	setUpAutocompleter: function(autoCompleterID)	{
		var url = Todoyu.getUrl('search', 'filtercontroller');

		widgetID = this.filterWidgetIDFromAutoCompleterID(autoCompleterID);

		var options = {
			parameters:			'&action=autocompletion&completionID=' + widgetID + '&filtertype=' + Todoyu.Ext.search.Filter.FilterType,
			paramName:			'sword',
			minChars:			2,
			afterUpdateElement:	Todoyu.Ext.search.FilterWidget.handleAutocompleteInput
		};

		var autocompleter = new Ajax.Autocompleter(autoCompleterID, autoCompleterID + '-suggestions', url, options);
	},



	/**
	 * Install click observer on given negation button element
	 *
	 *	@param	String	negationID
	 */
	setUpNegation: function(negationID)	{
		$(negationID).observe('click', 
			Todoyu.Ext.search.Filter.setNegation.bind(Todoyu.Ext.search.Filter, negationID)
		);
	},



	/**
	 * Extract respective widget ID from given autocompleter element ID
	 *
	 * @param	String	autocompleterID
	 * @return	String
	 */
	filterWidgetIDFromAutoCompleterID: function(autocompleterID)	{
		splittedID = autocompleterID.split('-');

		return splittedID[2] + '-' + splittedID[3];
	},



	/**
	 * Enter description here...
	 *
	 * @param	String	elementText
	 * @param	String	elementLi
	 */
	handleAutocompleteInput: function(elementText, elementLi)	{
		var hiddenElement = $('widget-autocompleter-' + elementLi.parentNode.id.replace(/ul/, 'hidden'));
		hiddenElement.setValue(elementLi.id);
		Todoyu.Ext.search.Filter.setValueToCondition(hiddenElement, elementLi.parentNode.id.replace(/-ul/, ''));
	},
	
	
	
	/**
	 * Handler when text in a text-widget is entered
	 * The update is delayed, so no every key will force a result update
	 * 
	 * @param	DomElement	input	The textinput
	 */
	onTextEntered: function(input) {
			// Get widget value and name
		var name	= $(input).up('div.filterWidget').id;
		var value	= $F(input);
		
			// Clear existing timeout of previous inputs
		if( this.timeout[name] ) {
			window.clearTimeout(this.timeout[name]);
			delete this.timeout[name];
		}

			// Update filter condition
		this.ext.Filter.setConditionValue(name, value);

			// Create new timeout to update results (can be cleared by new inputs)
		this.timeout[name] = this.ext.Filter.updateResults.bind(this.ext.Filter).delay(0.4);
	}

};