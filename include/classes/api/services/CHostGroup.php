<?php
/*
** Bsm
** Copyright (C) 2001-2016 Bsm SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Class containing methods for operations with host groups.
 *
 * @package API
 */
class CHostGroup extends CApiService {

	protected $tableName = 'groups';
	protected $tableAlias = 'g';
	protected $sortColumns = ['groupid', 'name'];

	/**
	 * Get host groups.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function get($params) {
		$result = [];
		$userType = self::$userData['type'];
		$userid = self::$userData['userid'];

		$sqlParts = [
			'select'	=> ['groups' => 'g.groupid'],
			'from'		=> ['groups' => 'groups g'],
			'where'		=> [],
			'order'		=> [],
			'limit'		=> null
		];

		$defOptions = [
			'groupids'					=> null,
			'hostids'					=> null,
			'templateids'				=> null,
			'graphids'					=> null,
			'triggerids'				=> null,
			'maintenanceids'			=> null,
			'monitored_hosts'			=> null,
			'templated_hosts'			=> null,
			'real_hosts'				=> null,
			'with_hosts_and_templates'	=> null,
			'with_items'				=> null,
			'with_simple_graph_items'	=> null,
			'with_monitored_items'		=> null,
			'with_triggers'				=> null,
			'with_monitored_triggers'	=> null,
			'with_httptests'			=> null,
			'with_monitored_httptests'	=> null,
			'with_graphs'				=> null,
			'with_applications'			=> null,
			'editable'					=> null,
			'nopermissions'				=> null,
			// filter
			'filter'					=> null,
			'search'					=> null,
			'searchByAny'				=> null,
			'startSearch'				=> null,
			'excludeSearch'				=> null,
			'searchWildcardsEnabled'	=> null,
			// output
			'output'					=> API_OUTPUT_EXTEND,
			'selectHosts'				=> null,
			'selectTemplates'			=> null,
			'selectGroupDiscovery'		=> null,
			'selectDiscoveryRule'		=> null,
			'countOutput'				=> null,
			'groupCount'				=> null,
			'preservekeys'				=> null,
			'sortfield'					=> '',
			'sortorder'					=> '',
			'limit'						=> null,
			'limitSelects'				=> null
		];
		$options = bs_array_merge($defOptions, $params);

		// editable + PERMISSION CHECK
		if ($userType != USER_TYPE_SUPER_ADMIN && !$options['nopermissions']) {
			$permission = $options['editable'] ? PERM_READ_WRITE : PERM_READ;

			$userGroups = getUserGroupsByUserId($userid);

			$sqlParts['where'][] = 'EXISTS ('.
				'SELECT NULL'.
				' FROM rights r'.
				' WHERE g.groupid=r.id'.
					' AND '.dbConditionInt('r.groupid', $userGroups).
				' GROUP BY r.id'.
				' HAVING MIN(r.permission)>'.PERM_DENY.
					' AND MAX(r.permission)>='.bs_dbstr($permission).
				')';
		}

		// groupids
		if (!is_null($options['groupids'])) {
			bs_value2array($options['groupids']);
			$sqlParts['where']['groupid'] = dbConditionInt('g.groupid', $options['groupids']);
		}

		// templateids
		if (!is_null($options['templateids'])) {
			bs_value2array($options['templateids']);

			if (!is_null($options['hostids'])) {
				bs_value2array($options['hostids']);
				$options['hostids'] = array_merge($options['hostids'], $options['templateids']);
			}
			else {
				$options['hostids'] = $options['templateids'];
			}
		}

		// hostids
		if (!is_null($options['hostids'])) {
			bs_value2array($options['hostids']);

			$sqlParts['from']['hosts_groups'] = 'hosts_groups hg';
			$sqlParts['where'][] = dbConditionInt('hg.hostid', $options['hostids']);
			$sqlParts['where']['hgg'] = 'hg.groupid=g.groupid';
		}

		// triggerids
		if (!is_null($options['triggerids'])) {
			bs_value2array($options['triggerids']);

			$sqlParts['from']['hosts_groups'] = 'hosts_groups hg';
			$sqlParts['from']['functions'] = 'functions f';
			$sqlParts['from']['items'] = 'items i';
			$sqlParts['where'][] = dbConditionInt('f.triggerid', $options['triggerids']);
			$sqlParts['where']['fi'] = 'f.itemid=i.itemid';
			$sqlParts['where']['hgi'] = 'hg.hostid=i.hostid';
			$sqlParts['where']['hgg'] = 'hg.groupid=g.groupid';
		}

		// graphids
		if (!is_null($options['graphids'])) {
			bs_value2array($options['graphids']);

			$sqlParts['from']['gi'] = 'graphs_items gi';
			$sqlParts['from']['i'] = 'items i';
			$sqlParts['from']['hg'] = 'hosts_groups hg';
			$sqlParts['where'][] = dbConditionInt('gi.graphid', $options['graphids']);
			$sqlParts['where']['hgg'] = 'hg.groupid=g.groupid';
			$sqlParts['where']['igi'] = 'i.itemid=gi.itemid';
			$sqlParts['where']['hgi'] = 'hg.hostid=i.hostid';
		}

		// maintenanceids
		if (!is_null($options['maintenanceids'])) {
			bs_value2array($options['maintenanceids']);

			$sqlParts['from']['maintenances_groups'] = 'maintenances_groups mg';
			$sqlParts['where'][] = dbConditionInt('mg.maintenanceid', $options['maintenanceids']);
			$sqlParts['where']['hmh'] = 'g.groupid=mg.groupid';
		}

		$sub_sql_parts = array();

		// monitored_hosts, real_hosts, templated_hosts, with_hosts_and_templates
		if ($options['monitored_hosts'] !== null) {
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status', array(HOST_STATUS_MONITORED));
		}
		elseif ($options['real_hosts'] !== null) {
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status',
				array(HOST_STATUS_MONITORED, HOST_STATUS_NOT_MONITORED)
			);
		}
		elseif ($options['templated_hosts'] !== null) {
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status', array(HOST_STATUS_TEMPLATE));
		}
		elseif ($options['with_hosts_and_templates'] !== null) {
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status',
				array(HOST_STATUS_MONITORED, HOST_STATUS_NOT_MONITORED, HOST_STATUS_TEMPLATE)
			);
		}

		// with_items, with_monitored_items, with_simple_graph_items
		if ($options['with_items'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('i.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}
		elseif ($options['with_monitored_items'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status', array(HOST_STATUS_MONITORED));
			$sub_sql_parts['where'][] = dbConditionInt('i.status', array(ITEM_STATUS_ACTIVE));
			$sub_sql_parts['where'][] = dbConditionInt('i.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}
		elseif ($options['with_simple_graph_items'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('i.value_type',
				array(ITEM_VALUE_TYPE_FLOAT, ITEM_VALUE_TYPE_UINT64)
			);
			$sub_sql_parts['where'][] = dbConditionInt('i.status', array(ITEM_STATUS_ACTIVE));
			$sub_sql_parts['where'][] = dbConditionInt('i.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}

		// with_triggers, with_monitored_triggers
		if ($options['with_triggers'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['from']['f'] = 'functions f';
			$sub_sql_parts['from']['t'] = 'triggers t';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where']['i-f'] = 'i.itemid=f.itemid';
			$sub_sql_parts['where']['f-t'] = 'f.triggerid=t.triggerid';
			$sub_sql_parts['where'][] = dbConditionInt('t.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}
		elseif ($options['with_monitored_triggers'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['from']['h'] = 'hosts h';
			$sub_sql_parts['from']['f'] = 'functions f';
			$sub_sql_parts['from']['t'] = 'triggers t';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where']['hg-h'] = 'hg.hostid=h.hostid';
			$sub_sql_parts['where']['i-f'] = 'i.itemid=f.itemid';
			$sub_sql_parts['where']['f-t'] = 'f.triggerid=t.triggerid';
			$sub_sql_parts['where'][] = dbConditionInt('h.status', array(HOST_STATUS_MONITORED));
			$sub_sql_parts['where'][] = dbConditionInt('i.status', array(ITEM_STATUS_ACTIVE));
			$sub_sql_parts['where'][] = dbConditionInt('t.status', array(TRIGGER_STATUS_ENABLED));
			$sub_sql_parts['where'][] = dbConditionInt('t.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}

		// with_httptests, with_monitored_httptests
		if ($options['with_httptests'] !== null) {
			$sub_sql_parts['from']['ht'] = 'httptest ht';
			$sub_sql_parts['where']['hg-ht'] = 'hg.hostid=ht.hostid';
		}
		elseif ($options['with_monitored_httptests'] !== null) {
			$sub_sql_parts['from']['ht'] = 'httptest ht';
			$sub_sql_parts['where']['hg-ht'] = 'hg.hostid=ht.hostid';
			$sub_sql_parts['where'][] = dbConditionInt('ht.status', array(HTTPTEST_STATUS_ACTIVE));
		}

		// with_graphs
		if ($options['with_graphs'] !== null) {
			$sub_sql_parts['from']['i'] = 'items i';
			$sub_sql_parts['from']['gi'] = 'graphs_items gi';
			$sub_sql_parts['from']['gr'] = 'graphs gr';
			$sub_sql_parts['where']['hg-i'] = 'hg.hostid=i.hostid';
			$sub_sql_parts['where']['i-gi'] = 'i.itemid=gi.itemid';
			$sub_sql_parts['where']['gi-gr'] = 'gi.graphid=gr.graphid';
			$sub_sql_parts['where'][] = dbConditionInt('gr.flags',
				array(BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED)
			);
		}

		// with_applications
		if ($options['with_applications'] !== null) {
			$sub_sql_parts['from']['a'] = 'applications a';
			$sub_sql_parts['where']['hg-a'] = 'hg.hostid=a.hostid';
		}

		if ($sub_sql_parts) {
			$sub_sql_parts['from']['hg'] = 'hosts_groups hg';
			$sub_sql_parts['where']['g-hg'] = 'g.groupid=hg.groupid';

			$sqlParts['where'][] = 'EXISTS ('.
				'SELECT NULL'.
				' FROM '.implode(',', $sub_sql_parts['from']).
				' WHERE '.implode(' AND ', array_unique($sub_sql_parts['where'])).
			')';
		}

		// filter
		if (is_array($options['filter'])) {
			$this->dbFilter('groups g', $options, $sqlParts);
		}

		// search
		if (is_array($options['search'])) {
			bs_db_search('groups g', $options, $sqlParts);
		}

		// limit
		if (bs_ctype_digit($options['limit']) && $options['limit']) {
			$sqlParts['limit'] = $options['limit'];
		}

		$sqlParts = $this->applyQueryOutputOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
		$sqlParts = $this->applyQuerySortOptions($this->tableName(), $this->tableAlias(), $options, $sqlParts);
		$res = DBselect($this->createSelectQueryFromParts($sqlParts), $sqlParts['limit']);
		while ($group = DBfetch($res)) {
			if (!is_null($options['countOutput'])) {
				if (!is_null($options['groupCount'])) {
					$result[] = $group;
				}
				else {
					$result = $group['rowscount'];
				}
			}
			else {
				$result[$group['groupid']] = $group;
			}
		}

		if (!is_null($options['countOutput'])) {
			return $result;
		}

		if ($result) {
			$result = $this->addRelatedObjects($options, $result);
		}

		// removing keys (hash -> array)
		if (is_null($options['preservekeys'])) {
			$result = bs_cleanHashes($result);
		}
		return $result;
	}

	/**
	 * Create host groups.
	 *
	 * @param array $groups array with host group names
	 * @param array $groups['name']
	 *
	 * @return array
	 */
	public function create(array $groups) {
		$groups = bs_toArray($groups);

		if (USER_TYPE_SUPER_ADMIN != self::$userData['type']) {
			self::exception(BS_API_ERROR_PERMISSIONS, _('Only Super Admins can create host groups.'));
		}

		foreach ($groups as $group) {
			if (!isset($group['name']) || bs_empty($group['name'])) {
				self::exception(BS_API_ERROR_PARAMETERS, _('Host group name cannot be empty.'));
			}
			$this->checkNoParameters(
				$group,
				['internal'],
				_('Cannot set "%1$s" for host group "%2$s".'),
				$group['name']
			);
		}

		// check host name duplicates in passed parameters
		$collectionValidator = new CCollectionValidator([
			'uniqueField' => 'name',
			'messageDuplicate' => _('Host group "%1$s" already exists.')
		]);
		$this->checkValidator($groups, $collectionValidator);

		// check host name duplicates in DB
		$dbHostGroups = API::getApiService()->select($this->tableName(), [
			'output' => ['name'],
			'filter' => ['name' => bs_objectValues($groups, 'name')],
			'limit' => 1
		]);

		if ($dbHostGroups) {
			$dbHostGroup = reset($dbHostGroups);
			self::exception(BS_API_ERROR_PARAMETERS, _s('Host group "%1$s" already exists.', $dbHostGroup['name']));
		}

		$groupids = DB::insert('groups', $groups);

		return ['groupids' => $groupids];
	}

	/**
	 * Update host groups.
	 *
	 * @param array $groups
	 * @param array $groups[0]['name'], ...
	 * @param array $groups[0]['groupid'], ...
	 *
	 * @return boolean
	 */
	public function update(array $groups) {
		$groups = bs_toArray($groups);
		$groupids = bs_objectValues($groups, 'groupid');

		if (empty($groups)) {
			self::exception(BS_API_ERROR_PARAMETERS, _('Empty input parameter.'));
		}

		// permissions
		$updGroups = $this->get([
			'output' => ['groupid', 'flags', 'name'],
			'groupids' => $groupids,
			'editable' => true,
			'preservekeys' => true
		]);
		foreach ($groups as $group) {
			if (!isset($updGroups[$group['groupid']])) {
				self::exception(BS_API_ERROR_PERMISSIONS,
					_('No permissions to referred object or it does not exist!')
				);
			}
			$this->checkNoParameters(
				$group,
				['internal'],
				_('Cannot update "%1$s" for host group "%2$s".'),
				isset($group['name']) ? $group['name'] : $updGroups[$group['groupid']]['name']
			);
		}

		// name duplicate check
		$groupsNames = $this->get([
			'filter' => ['name' => bs_objectValues($groups, 'name')],
			'output' => ['groupid', 'name'],
			'editable' => true,
			'nopermissions' => true
		]);
		$groupsNames = bs_toHash($groupsNames, 'name');

		$updateDiscoveredValidator = new CUpdateDiscoveredValidator([
			'messageAllowed' => _('Cannot update a discovered host group.')
		]);

		$update = [];
		foreach ($groups as $group) {
			if (isset($group['name'])) {
				if (bs_empty($group['name'])) {
					self::exception(BS_API_ERROR_PARAMETERS, _('Host group name cannot be empty.'));
				}

				// cannot update discovered host groups
				$this->checkPartialValidator($group, $updateDiscoveredValidator, $updGroups[$group['groupid']]);

				if (isset($groupsNames[$group['name']])
						&& !idcmp($groupsNames[$group['name']]['groupid'], $group['groupid'])) {
					self::exception(BS_API_ERROR_PARAMETERS, _s('Host group "%1$s" already exists.', $group['name']));
				}

				$update[] = [
					'values' => ['name' => $group['name']],
					'where' => ['groupid' => $group['groupid']]
				];
			}

			// prevents updating several groups with same name
			$groupsNames[$group['name']] = ['groupid' => $group['groupid']];
		}

		DB::update('groups', $update);

		return ['groupids' => $groupids];
	}

	/**
	 * Delete host groups.
	 *
	 * @param array $groupids
	 * @param bool 	$nopermissions
	 *
	 * @return array
	 */
	public function delete(array $groupids, $nopermissions = false) {
		if (empty($groupids)) {
			self::exception(BS_API_ERROR_PARAMETERS, _('Empty input parameter.'));
		}
		sort($groupids);

		$delGroups = $this->get([
			'groupids' => $groupids,
			'editable' => true,
			'output' => ['groupid', 'name', 'internal'],
			'preservekeys' => true,
			'nopermissions' => $nopermissions
		]);
		foreach ($groupids as $groupid) {
			if (!isset($delGroups[$groupid])) {
				self::exception(BS_API_ERROR_PERMISSIONS,
					_('No permissions to referred object or it does not exist!')
				);
			}
			if ($delGroups[$groupid]['internal'] == BS_INTERNAL_GROUP) {
				self::exception(BS_API_ERROR_PARAMETERS,
					_s('Host group "%1$s" is internal and can not be deleted.', $delGroups[$groupid]['name']));
			}
		}

		// check if a group is used in a group prototype
		$groupPrototype = DBFetch(DBselect(
			'SELECT groupid'.
			' FROM group_prototype gp'.
			' WHERE '.dbConditionInt('groupid', $groupids),
			1
		));
		if ($groupPrototype) {
			self::exception(BS_API_ERROR_PARAMETERS,
				_s('Group "%1$s" cannot be deleted, because it is used by a host prototype.',
					$delGroups[$groupPrototype['groupid']]['name']
				)
			);
		}

		$dltGroupids = getDeletableHostGroupIds($groupids);
		if (count($groupids) != count($dltGroupids)) {
			foreach ($groupids as $groupid) {
				if (isset($dltGroupids[$groupid])) {
					continue;
				}
				self::exception(BS_API_ERROR_PARAMETERS,
					_s('Host group "%1$s" cannot be deleted, because some hosts depend on it.',
						$delGroups[$groupid]['name']
					)
				);
			}
		}

		$dbScripts = API::Script()->get([
			'groupids' => $groupids,
			'output' => ['scriptid', 'groupid'],
			'nopermissions' => true
		]);

		if (!empty($dbScripts)) {
			foreach ($dbScripts as $script) {
				if ($script['groupid'] == 0) {
					continue;
				}
				self::exception(BS_API_ERROR_PARAMETERS,
					_s('Host group "%1$s" cannot be deleted, because it is used in a global script.',
						$delGroups[$script['groupid']]['name']
					)
				);
			}
		}

		// delete screens items
		$resources = [
			SCREEN_RESOURCE_HOSTGROUP_TRIGGERS,
			SCREEN_RESOURCE_HOSTS_INFO,
			SCREEN_RESOURCE_TRIGGERS_INFO,
			SCREEN_RESOURCE_TRIGGERS_OVERVIEW,
			SCREEN_RESOURCE_DATA_OVERVIEW
		];
		DB::delete('screens_items', [
			'resourceid' => $groupids,
			'resourcetype' => $resources
		]);

		// delete sysmap element
		if (!empty($groupids)) {
			DB::delete('sysmaps_elements', ['elementtype' => SYSMAP_ELEMENT_TYPE_HOST_GROUP, 'elementid' => $groupids]);
		}

		// disable actions
		// actions from conditions
		$actionids = [];
		$dbActions = DBselect(
			'SELECT DISTINCT c.actionid'.
			' FROM conditions c'.
			' WHERE c.conditiontype='.CONDITION_TYPE_HOST_GROUP.
				' AND '.dbConditionString('c.value', $groupids)
		);
		while ($dbAction = DBfetch($dbActions)) {
			$actionids[$dbAction['actionid']] = $dbAction['actionid'];
		}

		// actions from operations
		$dbActions = DBselect(
			'SELECT o.actionid'.
			' FROM operations o,opgroup og'.
			' WHERE o.operationid=og.operationid AND '.dbConditionInt('og.groupid', $groupids).
			' UNION'.
			' SELECT o.actionid'.
			' FROM operations o,opcommand_grp ocg'.
			' WHERE o.operationid=ocg.operationid AND '.dbConditionInt('ocg.groupid', $groupids)
		);
		while ($dbAction = DBfetch($dbActions)) {
			$actionids[$dbAction['actionid']] = $dbAction['actionid'];
		}

		if (!empty($actionids)) {
			$update = [];
			$update[] = [
				'values' => ['status' => ACTION_STATUS_DISABLED],
				'where' => ['actionid' => $actionids]
			];
			DB::update('actions', $update);
		}

		// delete action conditions
		DB::delete('conditions', [
			'conditiontype' => CONDITION_TYPE_HOST_GROUP,
			'value' => $groupids
		]);

		// delete action operation groups
		$operationids = [];
		$dbOperations = DBselect(
			'SELECT DISTINCT og.operationid'.
			' FROM opgroup og'.
			' WHERE '.dbConditionInt('og.groupid', $groupids)
		);
		while ($dbOperation = DBfetch($dbOperations)) {
			$operationids[$dbOperation['operationid']] = $dbOperation['operationid'];
		}
		DB::delete('opgroup', [
			'groupid' => $groupids
		]);

		// delete action operation commands
		$dbOperations = DBselect(
			'SELECT DISTINCT ocg.operationid'.
			' FROM opcommand_grp ocg'.
			' WHERE '.dbConditionInt('ocg.groupid', $groupids)
		);
		while ($dbOperation = DBfetch($dbOperations)) {
			$operationids[$dbOperation['operationid']] = $dbOperation['operationid'];
		}
		DB::delete('opcommand_grp', [
			'groupid' => $groupids
		]);

		// delete empty operations
		$delOperationids = [];
		$dbOperations = DBselect(
			'SELECT DISTINCT o.operationid'.
			' FROM operations o'.
			' WHERE '.dbConditionInt('o.operationid', $operationids).
				' AND NOT EXISTS (SELECT NULL FROM opgroup og WHERE o.operationid=og.operationid)'.
				' AND NOT EXISTS (SELECT NULL FROM opcommand_grp ocg WHERE o.operationid=ocg.operationid)'
		);
		while ($dbOperation = DBfetch($dbOperations)) {
			$delOperationids[$dbOperation['operationid']] = $dbOperation['operationid'];
		}

		DB::delete('operations', ['operationid' => $delOperationids]);

		DB::delete('groups', ['groupid' => $groupids]);

		DB::delete('profiles', [
			'idx' => 'web.dashconf.groups.groupids',
			'value_id' => $groupids
		]);

		DB::delete('profiles', [
			'idx' => 'web.dashconf.groups.hide.groupids',
			'value_id' => $groupids
		]);

		// TODO: remove audit
		foreach ($groupids as $groupid) {
			add_audit_ext(AUDIT_ACTION_DELETE, AUDIT_RESOURCE_HOST_GROUP, $groupid, $delGroups[$groupid]['name'], 'groups', null, null);
		}

		return ['groupids' => $groupids];
	}

	/**
	 * Add hosts and templates to host groups. All given hosts and templates are added to all given host groups.
	 *
	 * @param array $data
	 * @param array $data['groups']
	 * @param array $data['hosts']
	 * @param array $data['templates']
	 *
	 * @return array					returns array of group IDs that hosts and templates have been added to
	 */
	public function massAdd(array $data) {
		$data['groups'] = bs_toArray($data['groups']);
		$data['hosts'] = isset($data['hosts']) ? bs_toArray($data['hosts']) : [];
		$data['templates'] = isset($data['templates']) ? bs_toArray($data['templates']) : [];

		$this->validateMassAdd($data);

		$groupIds = bs_objectValues($data['groups'], 'groupid');
		$hostIds = bs_objectValues($data['hosts'], 'hostid');
		$templateIds = bs_objectValues($data['templates'], 'templateid');

		$objectIds = array_merge($hostIds, $templateIds);
		$objectIds = array_keys(array_flip($objectIds));

		$linked = [];
		$linkedDb = DBselect(
			'SELECT hg.hostid,hg.groupid'.
			' FROM hosts_groups hg'.
			' WHERE '.dbConditionInt('hg.hostid', $objectIds).
				' AND '.dbConditionInt('hg.groupid', $groupIds)
		);
		while ($pair = DBfetch($linkedDb)) {
			$linked[$pair['groupid']][$pair['hostid']] = 1;
		}

		$insert = [];
		foreach ($groupIds as $groupId) {
			foreach ($objectIds as $objectId) {
				if (isset($linked[$groupId][$objectId])) {
					continue;
				}
				$insert[] = ['hostid' => $objectId, 'groupid' => $groupId];
			}
		}

		DB::insert('hosts_groups', $insert);

		return ['groupids' => $groupIds];
	}

	/**
	 * Remove hosts and templates from host groups. All given hosts and templates are removed from all given host groups.
	 *
	 * @param array $data
	 * @param array $data['groupids']
	 * @param array $data['hostids']
	 * @param array $data['templateids']
	 *
	 * @return array				returns array of group IDs that hosts and templates have been removed from
	 */
	public function massRemove(array $data) {
		$data['groupids'] = bs_toArray($data['groupids'], 'groupid');
		$data['hostids'] = isset($data['hostids']) ? bs_toArray($data['hostids']) : [];
		$data['templateids'] = isset($data['templateids']) ? bs_toArray($data['templateids']) : [];

		$this->validateMassRemove($data);

		$objectIds = array_merge($data['hostids'], $data['templateids']);
		$objectIds = array_keys(array_flip($objectIds));

		DB::delete('hosts_groups', [
			'hostid' => $objectIds,
			'groupid' => $data['groupids']
		]);

		return ['groupids' => $data['groupids']];
	}

	/**
	 * Update host groups with new hosts and templates.
	 *
	 * @param array $data
	 * @param array $data['groups']
	 * @param array $data['hosts']
	 * @param array $data['templates']
	 *
	 * @return array				returns array of group IDs that hosts and templates have been added to and
	 *								removed from
	 */
	public function massUpdate(array $data) {
		$data['groups'] = bs_toArray($data['groups']);
		$data['hosts'] = isset($data['hosts']) ? bs_toArray($data['hosts']) : [];
		$data['templates'] = isset($data['templates']) ? bs_toArray($data['templates']) : [];

		$this->validateMassUpdate($data);

		$groupIds = bs_objectValues($data['groups'], 'groupid');
		$hostIds = bs_objectValues($data['hosts'], 'hostid');
		$templateIds = bs_objectValues($data['templates'], 'templateid');

		$objectIds = bs_toHash(array_merge($hostIds, $templateIds));

		// get old records and skip discovered hosts
		$oldRecords = DBfetchArray(DBselect(
			'SELECT hg.hostid,hg.groupid,hg.hostgroupid'.
			' FROM hosts_groups hg,hosts h'.
			' WHERE '.dbConditionInt('hg.groupid', $groupIds).
				' AND hg.hostid=h.hostid'.
				' AND h.flags='.BS_FLAG_DISCOVERY_NORMAL
		));

		// calculate new records
		$replaceRecords = [];
		$newRecords = [];

		foreach ($groupIds as $groupId) {
			$groupRecords = [];
			foreach ($oldRecords as $oldRecord) {
				if ($oldRecord['groupid'] == $groupId) {
					$groupRecords[] = $oldRecord;
				}
			}

			// find records for replace
			foreach ($groupRecords as $groupRecord) {
				if (isset($objectIds[$groupRecord['hostid']])) {
					$replaceRecords[] = $groupRecord;
				}
			}

			// find records for create
			$groupHostIds = bs_toHash(bs_objectValues($groupRecords, 'hostid'));

			$newHostIds = array_diff($objectIds, $groupHostIds);
			foreach ($newHostIds as $newHostId) {
				$newRecords[] = [
					'groupid' => $groupId,
					'hostid' => $newHostId
				];
			}
		}

		DB::replace('hosts_groups', $oldRecords, array_merge($replaceRecords, $newRecords));

		return ['groupids' => $groupIds];
	}

	/**
	 * Validate write permissions to host groups that are added to given hosts and templates.
	 *
	 * @param array $data
	 * @param array $data['groups']
	 * @param array $data['hosts']
	 * @param array $data['templates']
	 *
	 * @throws APIException		if user has no write permissions to any of the given host groups
	 */
	protected function validateMassAdd(array $data) {
		$groupIds = bs_objectValues($data['groups'], 'groupid');
		$hostIds = bs_objectValues($data['hosts'], 'hostid');
		$templateIds = bs_objectValues($data['templates'], 'templateid');

		$groupIdsToAdd = [];

		if ($hostIds) {
			$dbHosts = API::Host()->get([
				'output' => ['hostid'],
				'selectGroups' => ['groupid'],
				'hostids' => $hostIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($hostIds, $dbHosts);

			$this->checkValidator($hostIds, new CHostNormalValidator([
				'message' => _('Cannot update groups for discovered host "%1$s".')
			]));

			foreach ($dbHosts as $dbHost) {
				$oldGroupIds = bs_objectValues($dbHost['groups'], 'groupid');

				foreach (array_diff($groupIds, $oldGroupIds) as $groupId) {
					$groupIdsToAdd[$groupId] = $groupId;
				}
			}
		}

		if ($templateIds) {
			$dbTemplates = API::Template()->get([
				'output' => ['templateid'],
				'selectGroups' => ['groupid'],
				'templateids' => $templateIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($templateIds, $dbTemplates);

			foreach ($dbTemplates as $dbTemplate) {
				$oldGroupIds = bs_objectValues($dbTemplate['groups'], 'groupid');

				foreach (array_diff($groupIds, $oldGroupIds) as $groupId) {
					$groupIdsToAdd[$groupId] = $groupId;
				}
			}
		}

		if ($groupIdsToAdd && !$this->isWritable($groupIdsToAdd)) {
			self::exception(BS_API_ERROR_PERMISSIONS,
				_('No permissions to referred object or it does not exist!')
			);
		}
	}

	/**
	 * Validate write permissions to host groups that are added and removed from given hosts and templates. Also check
	 * if host and template has at least one host group left when removing host groups.
	 *
	 * @param array $data
	 * @param array $data['groups']
	 * @param array $data['hosts']
	 * @param array $data['templates']
	 *
	 * @throws APIException		if user has no write permissions to any of the given host groups or one of the hosts and
	 *							templates is left without a host group
	 */
	protected function validateMassUpdate(array $data) {
		$groupIds = bs_objectValues($data['groups'], 'groupid');
		$hostIds = bs_objectValues($data['hosts'], 'hostid');
		$templateIds = bs_objectValues($data['templates'], 'templateid');

		$dbGroups = $this->get([
			'output' => ['groupid'],
			'selectHosts' => ['hostid'],
			'selectTemplates' => ['templateid'],
			'groupids' => $groupIds
		]);

		if (!$dbGroups) {
			self::exception(BS_API_ERROR_PERMISSIONS, _('No permissions to referred object or it does not exist!'));
		}

		// Collect group IDs that will added to given hosts and templates.
		$groupIdsToAdd = [];

		// Collect group IDs that will removed from given hosts and templates.
		$groupIdsToRemove = [];

		/*
		 * When given hosts or templates belong to other groups and those group IDs are not passed in parameters,
		 * those groups will be removed from given hosts and templates. Collect those host and template IDs
		 * from groups that will be removed.
		 */
		$objectIds = [];

		// Collect both given host and template IDs.
		$currentObjectIds = array_merge($hostIds, $templateIds);
		if ($currentObjectIds) {
			$currentObjectIds = array_combine($currentObjectIds, $currentObjectIds);
		}

		// Collect both host and template IDs that also belong to given groups, but are not given in parameters.
		$objectIdsToRemove = [];

		/*
		 * New or existing hosts have been passed in parameters. First check write permissions to hosts
		 * and if hosts are not discovered. Then check if groups should be added and/or removed from given hosts.
		 */
		if ($hostIds) {
			$dbHosts = API::Host()->get([
				'output' => ['hostid'],
				'selectGroups' => ['groupid'],
				'hostids' => $hostIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($hostIds, $dbHosts);

			$this->checkValidator($hostIds, new CHostNormalValidator([
				'message' => _('Cannot update groups for discovered host "%1$s".')
			]));

			foreach ($dbHosts as $dbHost) {
				$oldGroupIds = bs_objectValues($dbHost['groups'], 'groupid');

				// Validate groups that are added for current host.
				foreach (array_diff($groupIds, $oldGroupIds) as $groupId) {
					$groupIdsToAdd[$groupId] = $groupId;
				}

				// Validate groups that are removed from current host.
				foreach (array_diff($oldGroupIds, $groupIds) as $groupId) {
					$groupIdsToRemove[$groupId] = $groupId;
				}

				if ($groupIdsToRemove) {
					$objectIds[] = $dbHost['hostid'];
				}
			}
		}

		/*
		 * Some existing hosts may not have been passed in parameters. In this case check what other hosts the groups
		 * have and if those hosts can be removed from given groups. This will also validate those hosts permissions and
		 * make sure hosts have at least one group left.
		 */
		foreach ($dbGroups as $dbGroup) {
			foreach ($dbGroup['hosts'] as $dbHost) {
				if (!isset($currentObjectIds[$dbHost['hostid']])) {
					$objectIdsToRemove[$dbHost['hostid']] = $dbHost['hostid'];
				}
			}
		}

		/*
		 * New or existing templates have been passed in parameters. First check write permissions to templates.
		 * Then check if groups should be added and/or removed from given templates.
		 */
		if ($templateIds) {
			$dbTemplates = API::Template()->get([
				'output' => ['templateid'],
				'selectGroups' => ['groupid'],
				'templateids' => $templateIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($templateIds, $dbTemplates);

			foreach ($dbTemplates as $dbTemplate) {
				$oldGroupIds = bs_objectValues($dbTemplate['groups'], 'groupid');

				// Validate groups that are added for current template.
				foreach (array_diff($groupIds, $oldGroupIds) as $groupId) {
					$groupIdsToAdd[$groupId] = $groupId;
				}

				// Validate groups that are removed from current template.
				foreach (array_diff($oldGroupIds, $groupIds) as $groupId) {
					$groupIdsToRemove[$groupId] = $groupId;
				}

				if ($groupIdsToRemove) {
					$objectIds[] = $dbTemplate['templateid'];
				}
			}
		}

		/*
		 * Some existing templates may not have been passed in parameters. In this case check what other templates the
		 * groups have and if those templates can be removed from given groups. This will also validate those template
		 * permissions and make sure templates have at least one group left.
		 */
		foreach ($dbGroups as $dbGroup) {
			foreach ($dbGroup['templates'] as $dbTemplate) {
				if (!isset($currentObjectIds[$dbTemplate['templateid']])) {
					$objectIdsToRemove[$dbTemplate['templateid']] = $dbTemplate['templateid'];
				}
			}
		}

		// Continue to check new, existing or removable groups for given hosts and templates.
		$groupIdsToUpdate = array_merge($groupIdsToAdd, $groupIdsToRemove);

		// Validate write permissions only to changed (added/removed) groups for given hosts and templates.
		if ($groupIdsToUpdate && !$this->isWritable($groupIdsToUpdate)) {
			self::exception(BS_API_ERROR_PERMISSIONS,
				_('No permissions to referred object or it does not exist!')
			);
		}

		// Check if groups can be removed from given hosts and templates. Only check if no groups are added.
		if (!$groupIdsToAdd && $groupIdsToRemove) {
			$unlinkableObjectIds = getUnlinkableHostIds($groupIdsToRemove, $objectIds);

			if (count($objectIds) != count($unlinkableObjectIds)) {
				self::exception(BS_API_ERROR_PARAMETERS, _('One of the objects is left without a host group.'));
			}
		}

		// Check the other way around - if other existing hosts and templates from those groups are left without groups.
		if ($objectIdsToRemove) {
			$unlinkableObjectIds = getUnlinkableHostIds($groupIds, $objectIdsToRemove);

			if (count($objectIdsToRemove) != count($unlinkableObjectIds)) {
				self::exception(BS_API_ERROR_PARAMETERS, _('One of the objects is left without a host group.'));
			}
		}
	}

	/**
	 * Validate write permissions to host groups that are removed from given hosts and templates. Also check
	 * if host and template has at least one host group left.
	 *
	 * @param array $data
	 * @param array $data['groupids']
	 * @param array $data['hostids']
	 * @param array $data['templateids']
	 *
	 * @throws APIException		if user has no write permissions to any of the given host groups or one of the hosts and
	 *							templates is left without a host group
	 */
	protected function validateMassRemove(array $data) {
		$groupIdsToRemove = [];
		$hostIds = isset($data['hostids']) ? $data['hostids'] : [];
		$templateIds = isset($data['templateids']) ? $data['templateids'] : [];
		$objectIds = [];

		if ($hostIds) {
			$dbHosts = API::Host()->get([
				'output' => ['hostid'],
				'selectGroups' => ['groupid'],
				'hostids' => $hostIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($hostIds, $dbHosts);

			$this->checkValidator($hostIds, new CHostNormalValidator([
				'message' => _('Cannot update groups for discovered host "%1$s".')
			]));

			foreach ($dbHosts as $dbHost) {
				$oldGroupIds = bs_objectValues($dbHost['groups'], 'groupid');

				// check if host belongs to the removable host group
				$hostGroupIdsToRemove = array_intersect($data['groupids'], $oldGroupIds);

				if ($hostGroupIdsToRemove) {
					$objectIds[] = $dbHost['hostid'];

					foreach ($hostGroupIdsToRemove as $groupId) {
						$groupIdsToRemove[$groupId] = $groupId;
					}
				}
			}
		}

		if ($templateIds) {
			$dbTemplates = API::Template()->get([
				'output' => ['templateid'],
				'selectGroups' => ['groupid'],
				'templateids' => $templateIds,
				'editable' => true,
				'preservekeys' => true
			]);

			$this->validateHostsPermissions($templateIds, $dbTemplates);

			foreach ($dbTemplates as $dbTemplate) {
				$oldGroupIds = bs_objectValues($dbTemplate['groups'], 'groupid');

				// check if template belongs to the removable host group
				$templateGroupIdsToRemove = array_intersect($data['groupids'], $oldGroupIds);

				if ($templateGroupIdsToRemove) {
					$objectIds[] = $dbTemplate['templateid'];

					foreach ($templateGroupIdsToRemove as $groupId) {
						$groupIdsToRemove[$groupId] = $groupId;
					}
				}
			}
		}

		if ($groupIdsToRemove) {
			if (!$this->isWritable($groupIdsToRemove)) {
				self::exception(BS_API_ERROR_PERMISSIONS,
					_('No permissions to referred object or it does not exist!')
				);
			}

			// check if host group can be removed from given hosts and templates leaving them with at least one more host group
			$unlinkableObjectIds = getUnlinkableHostIds($groupIdsToRemove, $objectIds);

			if (count($objectIds) != count($unlinkableObjectIds)) {
				self::exception(BS_API_ERROR_PARAMETERS,
					_('One of the objects is left without a host group.')
				);
			}
		}
	}

	/**
	 * Validate write permissions to hosts or templates by given host or template IDs.
	 *
	 * @param array $hostIds		array of host IDs or template IDs
	 * @param array $dbHosts		array of allowed hosts or templates
	 *
	 * @throws APIException			if user has no write permissions to one of the hosts or templates
	 */
	protected function validateHostsPermissions(array $hostIds, array $dbHosts) {
		foreach ($hostIds as $hostId) {
			if (!isset($dbHosts[$hostId])) {
				self::exception(BS_API_ERROR_PERMISSIONS,
					_('No permissions to referred object or it does not exist!')
				);
			}
		}
	}

	/**
	 * Check if user has read permissions for host groups.
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function isReadable(array $ids) {
		if (!is_array($ids)) {
			return false;
		}
		if (empty($ids)) {
			return true;
		}

		$ids = array_unique($ids);

		$count = $this->get([
			'groupids' => $ids,
			'countOutput' => true
		]);
		return count($ids) == $count;
	}

	/**
	 * Check if user has write permissions for host groups.
	 *
	 * @param array $ids
	 *
	 * @return bool
	 */
	public function isWritable(array $ids) {
		if (!is_array($ids)) {
			return false;
		}
		if (empty($ids)) {
			return true;
		}

		$ids = array_unique($ids);

		$count = $this->get([
			'groupids' => $ids,
			'editable' => true,
			'countOutput' => true
		]);

		return count($ids) == $count;
	}

	protected function addRelatedObjects(array $options, array $result) {
		$result = parent::addRelatedObjects($options, $result);

		$groupIds = array_keys($result);
		sort($groupIds);

		// adding hosts
		if ($options['selectHosts'] !== null) {
			if ($options['selectHosts'] !== API_OUTPUT_COUNT) {
				$relationMap = $this->createRelationMap($result, 'groupid', 'hostid', 'hosts_groups');
				$hosts = API::Host()->get([
					'output' => $options['selectHosts'],
					'hostids' => $relationMap->getRelatedIds(),
					'preservekeys' => true
				]);
				if (!is_null($options['limitSelects'])) {
					order_result($hosts, 'host');
				}
				$result = $relationMap->mapMany($result, $hosts, 'hosts', $options['limitSelects']);
			}
			else {
				$hosts = API::Host()->get([
					'groupids' => $groupIds,
					'countOutput' => true,
					'groupCount' => true
				]);
				$hosts = bs_toHash($hosts, 'groupid');
				foreach ($result as $groupid => $group) {
					if (isset($hosts[$groupid])) {
						$result[$groupid]['hosts'] = $hosts[$groupid]['rowscount'];
					}
					else {
						$result[$groupid]['hosts'] = 0;
					}
				}
			}
		}

		// adding templates
		if ($options['selectTemplates'] !== null) {
			if ($options['selectTemplates'] !== API_OUTPUT_COUNT) {
				$relationMap = $this->createRelationMap($result, 'groupid', 'hostid', 'hosts_groups');
				$hosts = API::Template()->get([
					'output' => $options['selectTemplates'],
					'templateids' => $relationMap->getRelatedIds(),
					'preservekeys' => true
				]);
				if (!is_null($options['limitSelects'])) {
					order_result($hosts, 'host');
				}
				$result = $relationMap->mapMany($result, $hosts, 'templates', $options['limitSelects']);
			}
			else {
				$hosts = API::Template()->get([
					'groupids' => $groupIds,
					'countOutput' => true,
					'groupCount' => true
				]);
				$hosts = bs_toHash($hosts, 'groupid');
				foreach ($result as $groupid => $group) {
					if (isset($hosts[$groupid])) {
						$result[$groupid]['templates'] = $hosts[$groupid]['rowscount'];
					}
					else {
						$result[$groupid]['templates'] = 0;
					}
				}
			}
		}

		// adding discovery rule
		if ($options['selectDiscoveryRule'] !== null && $options['selectDiscoveryRule'] != API_OUTPUT_COUNT) {
			// discovered items
			$discoveryRules = DBFetchArray(DBselect(
				'SELECT gd.groupid,hd.parent_itemid'.
					' FROM group_discovery gd,group_prototype gp,host_discovery hd'.
					' WHERE '.dbConditionInt('gd.groupid', $groupIds).
					' AND gd.parent_group_prototypeid=gp.group_prototypeid'.
					' AND gp.hostid=hd.hostid'
			));
			$relationMap = $this->createRelationMap($discoveryRules, 'groupid', 'parent_itemid');

			$discoveryRules = API::DiscoveryRule()->get([
				'output' => $options['selectDiscoveryRule'],
				'itemids' => $relationMap->getRelatedIds(),
				'preservekeys' => true
			]);
			$result = $relationMap->mapOne($result, $discoveryRules, 'discoveryRule');
		}

		// adding group discovery
		if ($options['selectGroupDiscovery'] !== null) {
			$groupDiscoveries = API::getApiService()->select('group_discovery', [
				'output' => $this->outputExtend($options['selectGroupDiscovery'], ['groupid']),
				'filter' => ['groupid' => $groupIds],
				'preservekeys' => true
			]);
			$relationMap = $this->createRelationMap($groupDiscoveries, 'groupid', 'groupid');

			$groupDiscoveries = $this->unsetExtraFields($groupDiscoveries, ['groupid'],
				$options['selectGroupDiscovery']
			);
			$result = $relationMap->mapOne($result, $groupDiscoveries, 'groupDiscovery');
		}

		return $result;
	}
}
