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

Todoyu.Ext.search.PanelWidget.SearchFilterList = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.search,

	/**
	 * @property	key
	 * @type		String
	 */
	key:		'searchfilterlist',

	/**
	 * @property	sortables
	 * @type		Array
	 */
	sortables:	[],



	/**
	 * Initialize filter list sortable
	 *
	 * @method	init
	 */
	init: function() {
		//this.initSortable();

		this.initSortableList();
	},



	/**
	 * Initialize sortability of filterset list items
	 *
	 * @method	initSortableList
	 */
	initSortableList: function() {
		new Todoyu.SortablePanelList('filterset-list', this.toggleList.bind(this), this.saveFiltersetOrder.bind(this));
	},



	/**
	 * Refresh filter list
	 *
	 * @method	refresh
	 */
	refresh: function() {
		var url		= Todoyu.getUrl('search', 'panelwidgetsearchfilterlist');
		var options = {
			'parameters': {
				'action':	'update'
			},
			'onComplete': this.initSortableList.bind(this)
		};
		var target	= 'panelwidget-searchfilterlist-content';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Toggle visibility of given type's listing in widget
	 *
	 * @method	toggleList
	 * @param	{String}	type
	 */
	toggleList: function(type, isExpanded) {
		this.saveListToggle(type, isExpanded);
	},



	/**
	 * Prompt for new name and rename given filterSet
	 *
	 * @method	renameFilterset
	 * @param	{Number}	idFilterSet
	 */
	renameFilterset: function(idFilterSet) {
		var currentName	= $('filterset-' + idFilterSet + '-label').title.stripScripts().strip();
		var newName		= prompt('[LLL:search.ext.filterset.rename]', currentName);

		if( newName !== null && newName.strip() !== '' ) {
			newName = newName.stripScripts().strip();

			$('filterset-' + idFilterSet + '-label').update(newName.escapeHTML());

			this.saveFiltersetRename(idFilterSet, newName);
		}
	},



	/**
	 * Hide given filterSet (visual and pref)
	 *
	 * @method	hideFilterset
	 * @param	{Number}	idFilterSet
	 */
	hideFilterset: function(idFilterSet) {
		var element = $('filterset-' + idFilterSet + '-control-visibility');
		var isHidden= element.hasClassName('hidden');

		element.toggleClassName('hidden');
		element.up('li').toggleClassName('hidden');

		if( isHidden === false ) {
			element.title		= '[LLL:core.global.unhide]';
			element.update('[LLL:core.global.unhide]');
		} else {
			element.title		= '[LLL:core.global.hide]';
			element.update('[LLL:core.global.hide]');
		}

		this.saveFiltersetVisibility(idFilterSet, isHidden);
	},



	/**
	 * Save given filterSet
	 *
	 * @method	saveFilterset
	 * @param	{Number}	idFilterSet
	 * @param	{String}	tab
	 */
	saveFilterset: function(idFilterSet, tab) {
		if( tab === this.ext.Filter.getActiveTab() ) {
			if( confirm('[LLL:search.ext.filterset.confirm.overwrite]') ) {
				this.ext.Filter.saveCurrentAreaAsFilterset(idFilterSet, this.onFiltersetSaved.bind(this, idFilterSet));
			}
		} else {
			alert('[LLL:search.ext.filterset.error.saveWrongType]');
		}
	},



	/**
	 * Handler being evoked after saving of given filterSet
	 *
	 * @method	onFiltersetSaved
	 * @param	{Number}			idFilterSet
	 * @param	{Ajax.Response}		response
	 */
	onFiltersetSaved: function(idFilterSet, response) {
		var tab = this.ext.Filter.getActiveTab();
		this.showFilterset(tab, idFilterSet);
	},



	/**
	 * Delete given filterSet (visual and from prefs)
	 *
	 * @method	deleteFilterset
	 * @param	{Number}	idFilterSet
	 */
	deleteFilterset: function(idFilterSet) {
		if( confirm('[LLL:search.ext.filterset.confirm.delete]') ) {
			$('filterset_' + idFilterSet).remove();
			this.saveFiltersetDelete(idFilterSet);
		}
	},



	/**
	 * Saves a new filter
	 *
	 * @method	saveCurrentAreaAsNewFilterset
	 */
	saveCurrentAreaAsNewFilterset: function() {
		this.ext.Filter.saveCurrentAreaAsNewFilterset(this.onNewFiltersetSaved.bind(this));
	},



	/**
	 * Handler being evoked after saving of new (= creating) filterSet (evokes refresh of widget).
	 *
	 * @method	onNewFiltersetSaved
	 * @param	{Ajax.Response}		response
	 */
	onNewFiltersetSaved: function(response) {
		this.refresh();
	},



	/**
	 * Load or Refresh and activate given filterSet
	 *
	 * @method	showFilterset
	 * @param	{String}	type
	 * @param	{Number}	idFilterset
	 */
	showFilterset: function(type, idFilterset) {
		if( type === this.ext.Filter.getActiveTab() ) {
			this.ext.Filter.loadFilterset(type, idFilterset);
		} else {
			this.ext.Filter.updateFilterArea(type, idFilterset);
		}
		this.ext.Filter.setFiltersetID(idFilterset);
		this.markActiveFilterset(idFilterset);
	},



	/**
	 * Mark currently active filterSet visually
	 *
	 * @method	markActiveFilterset
	 * @param	{Number}	idFilterSet
	 */
	markActiveFilterset: function(idFilterSet) {
		//$('filterset_' + idFilterSet).up('div').select('.filterset').invoke('removeClassName', 'current');
		//$('filterset_' + idFilterSet).addClassName('current');
	},



	/**
	 * Remove current from active filterSet (called on reset)
	 *
	 * @method	unmarkActiveFilterset
	 */
	unmarkActiveFilterset: function() {
		$('panelwidget-searchfilterlist').select('.filterset').invoke('removeClassName', 'current');
	},



	/**
	 * Remove all conditions from filter area
	 *
	 * @method	clearFilterArea
	 */
	clearFilterArea: function() {
		this.ext.Filter.reset();
		this.unmarkActiveFilterset();

		this.saveCleanArea();
	},



	/**
	 * Save preference of clean area: active filterSet, tab
	 *
	 * @method	saveCleanArea
	 */
	saveCleanArea: function() {
		var action	= 'activeFilterset';
		var value	= this.ext.Filter.getActiveTab();
		var	idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Save order of filterSet items (conditions)
	 *
	 * @method	saveFiltersetOrder
	 * @param	{String}	type
	 * @param	{Array}		items
	 */
	saveFiltersetOrder: function(type, items) {
		var action		= 'filtersetOrder';
		var value	= Object.toJSON({
			'type':		type,
			'items':	items
		});
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Save expanded-state of given type list
	 *
	 * @method	saveListToggle
	 * @param	{String}		type
	 * @param	{Boolean}		expanded
	 */
	saveListToggle: function(type, expanded) {
		var action	= 'filterlistToggle';
		var value	= type + ':' + ( expanded ) ? 1 : 0;
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Save preference: given renamed title of filterSet
	 *
	 * @method	saveFiltersetRename
	 * @param	{Number}	idFilterSet
	 * @param	{String}	name
	 */
	saveFiltersetRename: function(idFilterSet, name) {
		var action	= 'renameFilterset';

		this.ext.Preference.save(action, name, idFilterSet);
	},



	/**
	 * Save preference: visibility of given filterSet
	 *
	 * @method	saveFiltersetVisibility
	 * @param	{Number}	idFilterSet
	 * @param	{Boolean}	visible
	 */
	saveFiltersetVisibility: function(idFilterSet, visible) {
		var action	= 'toggleFiltersetVisibility';
		var value	= visible ? 1 : 0;

		this.ext.Preference.save(action, value, idFilterSet);
	},



	/**
	 * Save preference: deleted filterSet
	 *
	 * @method	saveFiltersetDelete
	 * @param	{Number}	idFilterSet
	 */
	saveFiltersetDelete: function(idFilterSet) {
		var action	= 'deleteFilterset';
		var value	= 1;

		this.ext.Preference.save(action, value, idFilterSet);
	}

};