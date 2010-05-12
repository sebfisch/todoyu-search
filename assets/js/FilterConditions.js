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

Todoyu.Ext.search.Filter.Conditions = {

	conditions: {},



	/**
	 * Add condition
	 *
	 * @param	{String}	name
	 * @param	{String}	type
	 * @param	{String}	condition
	 * @param	{String}	value
	 * @param	{Boolean}	negate
	 */
	add: function(name, type, condition, value, negate) {
		var conditionName = condition + '-' + name;

		this.conditions[conditionName] = {
			'name':			conditionName,
			'type':			type,
			'condition':	condition,
			'value':		value,
			'negate':		negate === true || negate == 1 ? true : false
		};
	},



	/**
	 * Update value of given filter conditon to given value 
	 *
	 * @param	{String}	conditionName
	 * @param	{String}	value
	 */
	updateValue: function(conditionName, value) {
		this.conditions[conditionName].value = value;
	},



	/**
	 * Update negation of given search filter condition
	 *
	 * @param	{String}	conditionName
	 * @param	{Boolean}	negate
	 */
	updateNegation: function(conditionName, negate) {
		this.conditions[conditionName].negate = negate === true;
	},



	/**
	 * Check whether given search filter condition is currently negated
	 *
	 * @param	{String}	conditionName
	 * @return	{Boolean}
	 */
	isNegated: function(conditionName) {
		return this.conditions[conditionName].negate === true;
	},



	/**
	 * Toggle negation flag of given condition
	 *
	 * @param	{String}	conditionName
	 */
	toggleNegated: function(conditionName) {
		this.conditions[conditionName].negate = ! this.conditions[conditionName].negate;
	},



	/**
	 * Remove given condition from current search filter conditions
	 *
	 * @param	{String}	conditionName
	 */
	remove: function(conditionName) {
		delete this.conditions[conditionName];
	},



	/**
	 * Clear current search filter conditions
	 */
	clear: function() {
		this.conditions = {};
	},



	/**
	 * Get all current search filter conditions, optionally as JSON
	 *
	 * @param	{Boolean}	asJSON
	 * @return	{Mixed}
	 */
	getAll: function(asJSON) {
		if( asJSON ) {
			return Object.toJSON(this.conditions);
		} else {
			return this.conditions;
		}
	},



	/**
	 * Get amount of current set search filter conditions
	 * 
	 * @return	{Number}
	 */
	size: function() {
		return Object.keys(this.conditions).size();
	}
};