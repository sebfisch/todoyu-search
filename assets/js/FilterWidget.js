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

Todoyu.Ext.search.FilterWidget = {



	/**
	 * Enter description here...
	 *
	 * @param unknown_type select
	 */
	addWidgetToFilterArea: function(select)	{
		var choosenWidget = $(select).getValue();
		// set back selector
		select.options[0].selected = true;
		//count of equal elements. (used for the id)
		var numOfWidget = this.detectNumOfWidget(choosenWidget);

		var url		= Todoyu.getUrl('search', 'filtercontroller');
		var options	= {
			'parameters': {
				'choosenWidget': choosenWidget,
				'numOfWidget': 'new'+numOfWidget,
				'cmd': 'addfilterwidget',
				'filterID': Todoyu.Ext.search.Filter.FilterID
			},
			'onComplete': function(response)	{
				var WidgetID = choosenWidget.split('_')[1] + '-new' + numOfWidget;
				if($(WidgetID))	{
					Todoyu.Ext.search.Filter.addFilterWidgetToList($(WidgetID));
					this.initAutocompletionSingle(WidgetID);
					this.initNegationSingle(WidgetID);
				}
			}.bind(this)
		};

		Todoyu.Ui.insert('widget-area', url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type widgetID
	 */
	removeWidgetFromFilterArea: function(widgetID)	{
		$(widgetID).remove();
		Todoyu.Ext.search.Filter.removeConditionFromFilter(widgetID);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type classNameOfWidget
	 */
	detectNumOfWidget: function(classNameOfWidget)	{
		var className = classNameOfWidget.split('_');
		if($('widget-area').select('.'+className[1])){
			return $('widget-area').select('.'+className[1]).length;
		} else {
			return 0;
		}
	},

	/**
	 * Autocompletion Part
	 */
	initAutocompletion: function()	{
		var foundAutocompleter = $('widget-area').select('.autocomplete');
		if(foundAutocompleter.length > 0)	{
			foundAutocompleter.each(
				function(autocompleter)	{
					this.setUpAutocompleter(autocompleter.id);
				}.bind(this)
			);
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type elementID
	 */
	initAutocompletionSingle: function(elementID)	{
		var autocompletion = $(elementID).select('.autocomplete');
		if(autocompletion.length > 0)	{
			this.setUpAutocompleter(autocompletion[0].id);
		}
	},



	/**
	 * Enter description here...
	 *
	 */
	initNegation: function()	{
		var foundNegations = $('widget-area').select('.negation');
		if(foundNegations.length > 0)	{
			foundNegations.each(
				function(negation)	{
					this.setUpNegation(negation.id);
				}.bind(this)
			);
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type elementID
	 */
	initNegationSingle: function(elementID)	{
		var negation = $(elementID).select('.negation');
		if(negation.length > 0)	{
			this.setUpNegation(negation[0].id);
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type autoCompleterID
	 */
	setUpAutocompleter: function(autoCompleterID)	{
		var Url = Todoyu.getUrl('search', 'filtercontroller');

		widgetID = this.filterWidgetIDFromAutoCompleterID(autoCompleterID);

		var options = {
			parameters: '&cmd=autocompletion&completionID='+widgetID+'&filtertype='+Todoyu.Ext.search.Filter.FilterType,
			paramName: 'sword',
			minChars: 2,
			afterUpdateElement: Todoyu.Ext.search.FilterWidget.handleAutocompleteInput
		};

		var Autocompleter = new Ajax.Autocompleter(autoCompleterID, autoCompleterID+'-suggestions', Url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type negationID
	 */
	setUpNegation: function(negationID)	{
		$(negationID).observe('click', Todoyu.Ext.search.Filter.setNegation.bind(Todoyu.Ext.search.Filter, negationID));
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type autocompleterID
	 */
	filterWidgetIDFromAutoCompleterID: function(autocompleterID)	{
		splittedID = autocompleterID.split('-');
		return splittedID[2]+'-'+splittedID[3];
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type elementText
	 * @param unknown_type elementLi
	 */
	handleAutocompleteInput: function(elementText, elementLi)	{
		var hiddenElement = $('widget-autocompleter-'+elementLi.parentNode.id.replace(/ul/, 'hidden'));
		hiddenElement.setValue(elementLi.id);
		Todoyu.Ext.search.Filter.setValueToCondition(hiddenElement, elementLi.parentNode.id.replace(/-ul/, ''));
	}



};