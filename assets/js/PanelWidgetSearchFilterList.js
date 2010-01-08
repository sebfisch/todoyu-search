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

Todoyu.Ext.search.PanelWidget.SearchFilterList = {

	/**
	 *	Ext shortcut
	 */
	ext:		Todoyu.Ext.search,

	key:		'searchfilterlist',

	sortables:	[],



	/**
	 * Initialize filter list sortable
	 */
	init: function()	{
		this.initSortable();
	},



	/**
	 * Refresh filter list
	 */
	refresh: function()	{
		var url		= Todoyu.getUrl('search', 'panelwidgetsearchfilterlist');
		var options = {
			'parameters': {
				'action':	'update'
			},
			'onComplete': this.initSortable.bind(this)
		};
		var target	= 'panelwidget-searchfilterlist-content';

		this.disableSortable();

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type type
	 */
	toggleList: function(type) {
		var list = 'panelwidget-searchfilterlist-list-' + type;

		if( Todoyu.exists(list) ) {
			$(list).toggle();
			this.saveListToggle(type, $(list).visible());
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer idFilterset
	 */
	renameFilterset: function(idFilterset)	{
		var currentName	= $('filterset-' + idFilterset + '-label').innerHTML.stripScripts().stripTags().strip()
		var newName		= prompt('[LLL:search.filterset.rename]', currentName);

		if( newName !== null && newName.strip() !== '' ) {
			newName = newName.stripScripts().stripTags().strip();

			$('filterset-' + idFilterset + '-label').update(newName)

			this.saveFiltersetRename(idFilterset, newName);
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer idFilterset
	 */
	hideFilterset: function(idFilterset)	{
		var element = $('filterset-' + idFilterset + '-control-visibility');
		var isHidden= element.hasClassName('hidden');

		element.toggleClassName('hidden');
		element.up('li').toggleClassName('hidden');

		if( isHidden === false )	{
			element.title		= '[LLL:core.unhide]';
			element.update('[LLL:core.unhide]');
		} else {
			element.title		= '[LLL:core.hide]';
			element.update('[LLL:core.hide]');
		}

		this.saveFiltersetVisibility(idFilterset, isHidden);
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer	idFilterset
	 * @param String	tab
	 */
	saveFilterset: function(idFilterset, tab) {
		if( tab === this.ext.Filter.getActiveTab() ) {
			if(confirm('[LLL:search.overwriteFilterSet]'))	{
				this.ext.Filter.saveCurrentAreaAsFilterset(idFilterset, this.onFiltersetSaved.bind(this, idFilterset));
			}
		} else {
			alert('[LLL:search.filtersetSaveAsWrongType]');
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer	idFilterset
	 * @param Object	response
	 */
	onFiltersetSaved: function(idFilterset, response) {
		var tab = this.ext.Filter.getActiveTab();
		this.loadFilterset(tab, idFilterset);
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer idFilterset
	 */
	deleteFilterset: function(idFilterset) {
		if( confirm('[LLL:search.filterset.delete]') ) {
			$('filterset_' + idFilterset).remove();

			this.saveFiltersetDelete(idFilterset);
		}
	},



	/**
	 * Saves a new filter
	 */
	saveCurrentAreaAsNewFilterset: function()	{
		this.ext.Filter.saveCurrentAreaAsNewFilterset(this.onNewFiltersetSaved.bind(this));
	},



	/**
	 * Enter description here...
	 *
	 * @param Object response
	 */
	onNewFiltersetSaved: function(response) {
		this.refresh();
	},



	/**
	 * Enter description here...
	 *
	 * @param unknown_type tab
	 * @param Integer idFilterset
	 */
	loadFilterset: function(tab, idFilterset) {
		this.markActiveFilterset(idFilterset);

		this.ext.Filter.loadFilterset(tab, idFilterset);

		this.ext.Filter.saveActiveFilterset(tab, idFilterset);

		if( tab !== this.ext.Filter.getActiveTab() ) {
			this.ext.Preference.saveActiveTab(tab);
		}
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer idFilterset
	 */
	markActiveFilterset: function(idFilterset) {
		$('filterset_' + idFilterset).up('div').select('.filterset').invoke('removeClassName', 'current');
		$('filterset_' + idFilterset).addClassName('current');
	},



	/**
	 * Enter description here...
	 */
	initSortable: function() {
		this.disableSortable();

			// Define options for all sortables
		var options	= {
			'handle': 'dragPointListItem',
			'onUpdate': this.onSortableUpdate.bind(this)
		};

			// Get all sortable lists
		var lists	= $('panelwidget-searchfilterlist-content').select('.sortable');

			// Make each list sortable
		lists.each(function(element) {
				// Create a sortable
			Sortable.create(element, options);
				// Register sortable element
			this.sortables.push(element);
		}.bind(this));

			// Add hover effect to handles
		var handles = $('panelwidget-searchfilterlist-content').select('.handle');
		handles.each(function(item){
			Todoyu.Ui.addHoverEffect(item);
		});
	},



	/**
	 * Enter description here...
	 */
	disableSortable: function() {
		this.sortables.each(function(sortableElement){
			Sortable.destroy(sortableElement);
		});

		this.sortables = [];
	},




	/**
	 * Enter description here...
	 *
	 * @param	element		listElement
	 */
	onSortableUpdate: function(listElement) {
		var type	= listElement.id.split('-').last();
		var items	= Sortable.sequence(listElement);

		this.saveFiltersetOrder(type, items);
	},



	/**
	 * Enter description here...
	 */
	clearFilterArea: function() {
		this.ext.Filter.reset();
	},



	/**
	 * Enter description here...
	 *
	 * @param	String		type
	 * @param unknown_type	items
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
	 * Enter description here...
	 *
	 * @param	String		type
	 * @param	Boolean		expanded
	 */
	saveListToggle: function(type, expanded) {
		var action	= 'filterlistToggle';
		var value	= type + ':' + (expanded ? 1 : 0);
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer	idFilterset
	 * @param String	name
	 */
	saveFiltersetRename: function(idFilterset, name) {
		var action	= 'renameFilterset';
		var value	= name;
		var idItem	= idFilterset;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer	idFilterset
	 * @param Boolean	isHidden
	 */
	saveFiltersetVisibility: function(idFilterset, visible) {
		var action	= 'toggleFiltersetVisibility';
		var value	= visible ? 1 : 0;
		var idItem	= idFilterset;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Enter description here...
	 *
	 * @param Integer idFilterset
	 */
	saveFiltersetDelete: function(idFilterset) {
		var action	= 'deleteFilterset';
		var value	= 1;
		var idItem	= idFilterset;

		this.ext.Preference.save(action, value, idItem);
	}

};