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

Todoyu.Ext.search.Filter.Conditions = {

	conditions: {},


	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 *	@param unknown_type type
	 *	@param unknown_type condition
	 *	@param unknown_type value
	 *	@param unknown_type negate
	 */
	add: function(name, type, condition, value, negate) {
		name = condition + '-' + name;
		this.conditions[name] = {
			'name': name,
			'type': type,
			'condition': condition,
			'value': value,
			'negate': negate
		};
	},


	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 *	@param unknown_type value
	 */
	updateValue: function(name, value) {
		this.conditions[name].value = value;
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 *	@param unknown_type negate
	 */
	updateNegation: function(name, negate) {
		this.conditions[name].negate = negate === true;
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 */
	isNegated: function(name) {
		return this.conditions[name].negate === true;
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 */
	toggleNegated: function(name) {
		this.conditions[name].negate = !this.conditions[name].negate;
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type name
	 */
	remove: function(name) {
		delete this.conditions[name];
	},



	/**
	 * Enter description here...
	 *
	 */
	clear: function() {
		this.conditions = {};
	},



	/**
	 * Enter description here...
	 *
	 *	@param unknown_type asJSON
	 */
	getAll: function(asJSON) {
		if( asJSON ) {
			return Object.toJSON(this.conditions);
		} else {
			return this.conditions;
		}
	},



	/**
	 * Enter description here...
	 *
	 */
	size: function() {
		return Object.keys(this.conditions).size();
	}
};