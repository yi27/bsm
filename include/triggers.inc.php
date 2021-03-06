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
** along with this program; ifnot, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
**/


function getSeverityStyle($severity, $type = true) {
	if (!$type) {
		return BS_STYLE_NORMAL_BG;
	}

	switch ($severity) {
		case TRIGGER_SEVERITY_DISASTER:
			return BS_STYLE_DISASTER_BG;
		case TRIGGER_SEVERITY_HIGH:
			return BS_STYLE_HIGH_BG;
		case TRIGGER_SEVERITY_AVERAGE:
			return BS_STYLE_AVERAGE_BG;
		case TRIGGER_SEVERITY_WARNING:
			return BS_STYLE_WARNING_BG;
		case TRIGGER_SEVERITY_INFORMATION:
			return BS_STYLE_INFO_BG;
		case TRIGGER_SEVERITY_NOT_CLASSIFIED:
			return BS_STYLE_NA_BG;
		default:
			return null;
	}
}

/**
 *	Get trigger severity name by given state and configuration.
 *
 * @param int	 $severity		trigger severity
 * @param array  $config		array containing configuration parameters containing severity names
 *
 * @return string
 */
function getSeverityName($severity, array $config) {
	switch ($severity) {
		case TRIGGER_SEVERITY_NOT_CLASSIFIED:
			return _($config['severity_name_0']);
		case TRIGGER_SEVERITY_INFORMATION:
			return _($config['severity_name_1']);
		case TRIGGER_SEVERITY_WARNING:
			return _($config['severity_name_2']);
		case TRIGGER_SEVERITY_AVERAGE:
			return _($config['severity_name_3']);
		case TRIGGER_SEVERITY_HIGH:
			return _($config['severity_name_4']);
		case TRIGGER_SEVERITY_DISASTER:
			return _($config['severity_name_5']);
		default:
			return _('Unknown');
	}
}

function getSeverityColor($severity, $value = TRIGGER_VALUE_TRUE) {
	if ($value == TRIGGER_VALUE_FALSE) {
		return 'AAFFAA';
	}
	$config = select_config();

	switch ($severity) {
		case TRIGGER_SEVERITY_DISASTER:
			$color = $config['severity_color_5'];
			break;
		case TRIGGER_SEVERITY_HIGH:
			$color = $config['severity_color_4'];
			break;
		case TRIGGER_SEVERITY_AVERAGE:
			$color = $config['severity_color_3'];
			break;
		case TRIGGER_SEVERITY_WARNING:
			$color = $config['severity_color_2'];
			break;
		case TRIGGER_SEVERITY_INFORMATION:
			$color = $config['severity_color_1'];
			break;
		case TRIGGER_SEVERITY_NOT_CLASSIFIED:
			$color = $config['severity_color_0'];
			break;
		default:
			$color = $config['severity_color_0'];
	}

	return $color;
}

/**
 * Returns HTML representation of trigger severity cell containing severity name and color.
 *
 * @param int	 $severity			trigger severity
 * @param array  $config			array of configuration parameters to get trigger severity name
 * @param string $text				trigger severity name
 * @param bool	 $forceNormal		true to return 'normal' class, false to return corresponding severity class
 *
 * @return CCol
 */
function getSeverityCell($severity, $config, $text = null, $forceNormal = false) {
	if ($text === null) {
		$text = CHtml::encode(getSeverityName($severity, $config));
	}

	return (new CCol($text))->addClass(getSeverityStyle($severity, !$forceNormal));
}

/**
 * Add color style and blinking to an object like CSpan or CDiv depending on trigger status
 * Settings and colors are kept in 'config' database table
 *
 * @param mixed $object             object like CSpan, CDiv, etc.
 * @param int $triggerValue         TRIGGER_VALUE_FALSE or TRIGGER_VALUE_TRUE
 * @param int $triggerLastChange
 * @param bool $isAcknowledged
 * @return void
 */
function addTriggerValueStyle($object, $triggerValue, $triggerLastChange, $isAcknowledged) {
	$config = select_config();

	// color of text and blinking depends on trigger value and whether event is acknowledged
	if ($triggerValue == TRIGGER_VALUE_TRUE && !$isAcknowledged) {
		$color = $config['problem_unack_color'];
		$blinks = $config['problem_unack_style'];
	}
	elseif ($triggerValue == TRIGGER_VALUE_TRUE && $isAcknowledged) {
		$color = $config['problem_ack_color'];
		$blinks = $config['problem_ack_style'];
	}
	elseif ($triggerValue == TRIGGER_VALUE_FALSE && !$isAcknowledged) {
		$color = $config['ok_unack_color'];
		$blinks = $config['ok_unack_style'];
	}
	elseif ($triggerValue == TRIGGER_VALUE_FALSE && $isAcknowledged) {
		$color = $config['ok_ack_color'];
		$blinks = $config['ok_ack_style'];
	}
	if (isset($color) && isset($blinks)) {
		// color
		$object->addStyle('color: #'.$color);

		// blinking
		$timeSinceLastChange = time() - $triggerLastChange;
		if ($blinks && $timeSinceLastChange < $config['blink_period']) {
			$object->addClass('blink'); // elements with this class will blink
			$object->setAttribute('data-time-to-blink', $config['blink_period'] - $timeSinceLastChange);
		}
	}
	else {
		$object->addClass(BS_STYLE_GREY);
	}
}

function trigger_value2str($value = null) {
	$triggerValues = [
		TRIGGER_VALUE_FALSE => _('OK'),
		TRIGGER_VALUE_TRUE => _('PROBLEM')
	];

	if ($value === null) {
		return $triggerValues;
	}
	elseif (isset($triggerValues[$value])) {
		return $triggerValues[$value];
	}
	else {
		return _('Unknown');
	}
}

function discovery_value($val = null) {
	$array = [
		DOBJECT_STATUS_UP => _('UP'),
		DOBJECT_STATUS_DOWN => _('DOWN'),
		DOBJECT_STATUS_DISCOVER => _('DISCOVERED'),
		DOBJECT_STATUS_LOST => _('LOST')
	];

	if (is_null($val)) {
		return $array;
	}
	elseif (isset($array[$val])) {
		return $array[$val];
	}
	else {
		return _('Unknown');
	}
}

function discovery_value_style($val) {
	switch ($val) {
		case DOBJECT_STATUS_UP:
			$style = BS_STYLE_GREEN;
			break;
		case DOBJECT_STATUS_DOWN:
			$style = BS_STYLE_RED;
			break;
		case DOBJECT_STATUS_DISCOVER:
			$style = BS_STYLE_GREEN;
			break;
		case DOBJECT_STATUS_LOST:
			$style = BS_STYLE_GREY;
			break;
		default:
			$style = '';
	}

	return $style;
}

function getParentHostsByTriggers($triggers) {
	$hosts = [];
	$triggerParent = [];

	while (!empty($triggers)) {
		foreach ($triggers as $tnum => $trigger) {
			if ($trigger['templateid'] == 0) {
				if (isset($triggerParent[$trigger['triggerid']])) {
					foreach ($triggerParent[$trigger['triggerid']] as $triggerid => $state) {
						$hosts[$triggerid] = $trigger['hosts'];
					}
				}
				else {
					$hosts[$trigger['triggerid']] = $trigger['hosts'];
				}
				unset($triggers[$tnum]);
			}
			else {
				if (isset($triggerParent[$trigger['triggerid']])) {
					if (!isset($triggerParent[$trigger['templateid']])) {
						$triggerParent[$trigger['templateid']] = [];
					}
					$triggerParent[$trigger['templateid']][$trigger['triggerid']] = 1;
					$triggerParent[$trigger['templateid']] += $triggerParent[$trigger['triggerid']];
				}
				else {
					if (!isset($triggerParent[$trigger['templateid']])) {
						$triggerParent[$trigger['templateid']] = [];
					}
					$triggerParent[$trigger['templateid']][$trigger['triggerid']] = 1;
				}
			}
		}
		$triggers = API::Trigger()->get([
			'triggerids' => bs_objectValues($triggers, 'templateid'),
			'selectHosts' => ['hostid', 'host', 'name', 'status'],
			'output' => ['triggerid', 'templateid'],
			'filter' => ['flags' => null]
		]);
	}

	return $hosts;
}

function get_trigger_by_triggerid($triggerid) {
	$db_trigger = DBfetch(DBselect('SELECT t.* FROM triggers t WHERE t.triggerid='.bs_dbstr($triggerid)));
	if (!empty($db_trigger)) {
		return $db_trigger;
	}
	error(_s('No trigger with triggerid "%1$s".', $triggerid));

	return false;
}

function get_hosts_by_triggerid($triggerids) {
	bs_value2array($triggerids);

	return DBselect(
		'SELECT DISTINCT h.*'.
		' FROM hosts h,functions f,items i'.
		' WHERE i.itemid=f.itemid'.
			' AND h.hostid=i.hostid'.
			' AND '.dbConditionInt('f.triggerid', $triggerids)
	);
}

function get_triggers_by_hostid($hostid) {
	return DBselect(
		'SELECT DISTINCT t.*'.
		' FROM triggers t,functions f,items i'.
		' WHERE i.hostid='.bs_dbstr($hostid).
			' AND f.itemid=i.itemid'.
			' AND f.triggerid=t.triggerid'
	);
}

// unescape Raw URL
function utf8RawUrlDecode($source) {
	$decodedStr = '';
	$pos = 0;
	$len = strlen($source);
	while ($pos < $len) {
		$charAt = substr($source, $pos, 1);
		if ($charAt == '%') {
			$pos++;
			$charAt = substr($source, $pos, 1);
			if ($charAt == 'u') {
				// we got a unicode character
				$pos++;
				$unicodeHexVal = substr($source, $pos, 4);
				$unicode = hexdec($unicodeHexVal);
				$entity = "&#".$unicode.';';
				$decodedStr .= html_entity_decode(utf8_encode($entity), ENT_COMPAT, 'UTF-8');
				$pos += 4;
			}
			else {
				$decodedStr .= substr($source, $pos-1, 1);
			}
		}
		else {
			$decodedStr .= $charAt;
			$pos++;
		}
	}

	return $decodedStr;
}

/**
 * Copies the given triggers to the given hosts or templates.
 *
 * Without the $srcHostId parameter it will only be able to copy triggers that belong to only one host. If the
 * $srcHostId parameter is not passed, and a trigger has multiple hosts, it will throw an error. If the
 * $srcHostId parameter is passed, the given host will be replaced with the destination host.
 *
 * This function takes care of copied trigger dependencies.
 * If trigger is copied alongside with trigger on which it depends, then dependencies is replaced directly using new ids,
 * If there is target host within dependency trigger, algorithm will search for potential matching trigger in target host,
 * if matching trigger is found, then id from this trigger is used, if not rise exception,
 * otherwise original dependency will be left.
 *
 *
 * @param int|array $srcTriggerIds triggers which will be copied to $dstHostIds
 * @param int|array $dstHostIds hosts and templates to whom add triggers, ids not present in DB (host table) will be ignored
 * @param int $srcHostId host id in which context trigger with multiple hosts will be treated
 *
 * @return bool
 */
function copyTriggersToHosts($srcTriggerIds, $dstHostIds, $srcHostId = null) {
	$options = [
		'triggerids' => $srcTriggerIds,
		'output' => ['triggerid', 'expression', 'description', 'url', 'status', 'priority', 'comments', 'type'],
		'filter' => ['flags' => BS_FLAG_DISCOVERY_NORMAL],
		'selectDependencies' => ['triggerid']
	];
	if ($srcHostId) {
		$srcHost = API::Host()->get([
			'output' => ['host'],
			'hostids' => $srcHostId,
			'preservekeys' => true,
			'templated_hosts' => true
		]);

		// If provided $srcHostId doesn't match any record in DB, return false.
		if (!($srcHost = reset($srcHost))) {
			return false;
		}
	}
	// If no $srcHostId provided we will need trigger host 'host'.
	else {
		$options['selectHosts'] = ['host'];
	}
	$dbSrcTriggers = API::Trigger()->get($options);

	$dbSrcTriggers = CMacrosResolverHelper::resolveTriggerExpressions($dbSrcTriggers);

	$dbDstHosts = API::Host()->get([
		'output' => ['hostid', 'host'],
		'hostids' => $dstHostIds,
		'preservekeys' => true,
		'templated_hosts' => true
	]);

	$newTriggers = [];

	// Create each trigger for each host.
	foreach ($dbDstHosts as $dstHost) {
		foreach ($dbSrcTriggers as $srcTrigger) {
			// If $srcHostId provided, get host 'host' for triggerExpressionReplaceHost().
			if ($srcHostId != 0) {
				$host = $srcHost['host'];
				$srcTriggerContextHostId = $srcHostId;
			}
			// If $srcHostId not provided, use source trigger first host 'host'.
			else {

				/*
				 * If we have multiple hosts in trigger expression and we haven't pointed ($srcHostId) which host to
				 * replace, call error.
				 */
				if (count($srcTrigger['hosts']) > 1) {
					error(_s('Cannot copy trigger "%1$s:%2$s", because it has multiple hosts in the expression.',
						$srcTrigger['description'], $srcTrigger['expression']
					));

					return false;
				}
				$host = $srcTrigger['hosts'][0]['host'];
				$srcTriggerContextHostId = $srcTrigger['hosts'][0]['hostid'];
			}

			$srcTrigger['expression'] = triggerExpressionReplaceHost($srcTrigger['expression'], $host,
				$dstHost['host']
			);

			// The dependddencies must be added after all triggers are created.
			$result = API::Trigger()->create([[
				'description' => $srcTrigger['description'],
				'expression' => $srcTrigger['expression'],
				'url' => $srcTrigger['url'],
				'status' => $srcTrigger['status'],
				'priority' => $srcTrigger['priority'],
				'comments' => $srcTrigger['comments'],
				'type' => $srcTrigger['type']
			]]);

			if (!$result) {
				return false;
			}

			$newTriggers[$srcTrigger['triggerid']][] = [
				'newTriggerId' => reset($result['triggerids']),
				'newTriggerExpression' => $srcTrigger['expression'],
				'newTriggerHostId' => $dstHost['hostid'],
				'newTriggerHost' => $dstHost['host'],
				'srcTriggerContextHostId' => $srcTriggerContextHostId,
				'srcTriggerContextHost' => $host
			];
		}
	}

	$depids = [];
	foreach ($dbSrcTriggers as $srcTrigger) {
		foreach ($srcTrigger['dependencies'] as $depTrigger) {
			$depids[] = $depTrigger['triggerid'];
		}
	}
	$depTriggers = API::Trigger()->get([
		'triggerids' => $depids,
		'output' => ['description', 'expression'],
		'selectHosts' => ['hostid'],
		'preservekeys' => true
	]);

	$depTriggers = CMacrosResolverHelper::resolveTriggerExpressions($depTriggers);

	// Map dependencies to the new trigger IDs and save.
	if ($newTriggers) {
		$dependencies = [];

		foreach ($dbSrcTriggers as $srcTrigger) {
			if ($srcTrigger['dependencies']) {
				// Get corresponding created triggers.
				$dst_triggers = $newTriggers[$srcTrigger['triggerid']];

				foreach ($dst_triggers as $dst_trigger) {
					foreach ($srcTrigger['dependencies'] as $depTrigger) {
						/*
						 * We have added $depTrigger trigger and we know corresponding trigger ID for newly
						 * created trigger.
						 */
						if (array_key_exists($depTrigger['triggerid'], $newTriggers)) {
							$dst_dep_triggers = $newTriggers[$depTrigger['triggerid']];

							foreach ($dst_dep_triggers as $dst_dep_trigger) {
								/*
								 * Dependency is within same host according to $srcHostId parameter or dep trigger has
								 * single host.
								 */
								if ($dst_trigger['srcTriggerContextHostId'] ==
										$dst_dep_trigger['srcTriggerContextHostId']) {
									$depTriggerId = $dst_dep_trigger['newTriggerId'];
									break;
								}
								// Dependency is to trigger from another host.
								else {
									$depTriggerId = $depTrigger['triggerid'];
								}
							}
						}
						// We need to search for $depTrigger trigger if target host is within dependency hosts.
						elseif (in_array(['hostid' => $dst_trigger['srcTriggerContextHostId']],
								$depTriggers[$depTrigger['triggerid']]['hosts'])) {
							// Get all possible $depTrigger matching triggers by description.
							$targetHostTriggersByDescription = API::Trigger()->get([
								'hostids' => $dst_trigger['newTriggerHostId'],
								'output' => ['hosts', 'triggerid', 'expression'],
								'filter' => ['description' => $depTriggers[$depTrigger['triggerid']]['description']],
								'preservekeys' => true
							]);

							$targetHostTriggersByDescription =
								CMacrosResolverHelper::resolveTriggerExpressions($targetHostTriggersByDescription);

							// Compare exploded expressions for exact match.
							$expr1 = $depTriggers[$depTrigger['triggerid']]['expression'];
							$depTriggerId = null;

							foreach ($targetHostTriggersByDescription as $potentialTargetTrigger) {
								$expr2 = triggerExpressionReplaceHost($potentialTargetTrigger['expression'],
									$dst_trigger['newTriggerHost'], $dst_trigger['srcTriggerContextHost']
								);

								if ($expr2 == $expr1) {
									// Matching trigger has been found.
									$depTriggerId = $potentialTargetTrigger['triggerid'];
									break;
								}
							}

							// If matching trigger wasn't found raise exception.
							if ($depTriggerId === null) {
								$expr2 = triggerExpressionReplaceHost($expr1, $dst_trigger['srcTriggerContextHost'],
									$dst_trigger['newTriggerHost']
								);

								error(_s(
									'Cannot add dependency from trigger "%1$s:%2$s" to non existing trigger "%3$s:%4$s".',
									$srcTrigger['description'], $dst_trigger['newTriggerExpression'],
									$depTriggers[$depTrigger['triggerid']]['description'], $expr2
								));

								return false;
							}
						}
						// Leave original dependency.
						else {
							$depTriggerId = $depTrigger['triggerid'];
						}

						$dependencies[] = [
							'triggerid' => $dst_trigger['newTriggerId'],
							'dependsOnTriggerid' => $depTriggerId
						];
					}
				}
			}
		}

		if ($dependencies) {
			if (!API::Trigger()->addDependencies($dependencies)) {
				return false;
			}
		}
	}

	return true;
}

/**
 * Purpose: Replaces host in trigger expression.
 * {localhost:agent.ping.nodata(5m)}  =>  {localhost6:agent.ping.nodata(5m)}
 *
 * @param string $expression	full expression with host names and item keys
 * @param string $src_host
 * @param string $dst_host
 *
 * @return string
 */
function triggerExpressionReplaceHost($expression, $src_host, $dst_host) {
	$new_expression = '';

	$function_macro_parser = new CFunctionMacroParser();
	$user_macro_parser = new CUserMacroParser();
	$macro_parser = new CMacroParser(['{TRIGGER.VALUE}']);
	$lld_macro_parser = new CLLDMacroParser();

	for ($pos = 0, $pos_left = 0; isset($expression[$pos]); $pos++) {
		if ($function_macro_parser->parse($expression, $pos) != CParser::PARSE_FAIL) {
			$host = $function_macro_parser->getHost();
			$item = $function_macro_parser->getItem();
			$function = $function_macro_parser->getFunction();

			if ($host === $src_host) {
				$host = $dst_host;
			}

			$new_expression .= substr($expression, $pos_left, $pos - $pos_left);
			$new_expression .= '{'.$host.':'.$item.'.'.$function.'}';
			$pos_left = $pos + $function_macro_parser->getLength();

			$pos += $function_macro_parser->getLength() - 1;
		}
		elseif ($user_macro_parser->parse($expression, $pos) != CParser::PARSE_FAIL) {
			$pos += $user_macro_parser->getLength() - 1;
		}
		elseif ($macro_parser->parse($expression, $pos) != CParser::PARSE_FAIL) {
			$pos += $macro_parser->getLength() - 1;
		}
		elseif ($lld_macro_parser->parse($expression, $pos) != CParser::PARSE_FAIL) {
			$pos += $lld_macro_parser->getLength() - 1;
		}
	}

	$new_expression .= substr($expression, $pos_left, $pos - $pos_left);

	return $new_expression;
}

/**
 * Implodes expression, replaces names and keys with IDs.
 *
 * For example: localhost:system.cpu.load.last(0)>10 will be translated to {12}>10 and created database representation.
 *
 * @throws Exception if error occurred
 *
 * @param string $expression Full expression with host names and item keys
 * @param numeric $triggerid
 * @param array optional $hostnames Reference to array which will be filled with unique visible host names.
 *
 * @return string Imploded expression (names and keys replaced by IDs)
 */
function implode_exp($expression, $triggerId, &$hostnames = []) {
	$expressionData = new CTriggerExpression();
	if (!$expressionData->parse($expression)) {
		throw new Exception($expressionData->error);
	}

	$newFunctions = [];
	$functions = [];
	$items = [];
	$triggerFunctionValidator = new CFunctionValidator();

	foreach ($expressionData->expressions as $exprPart) {
		if (isset($newFunctions[$exprPart['expression']])) {
			continue;
		}

		if (!isset($items[$exprPart['host']][$exprPart['item']])) {
			$result = DBselect(
				'SELECT i.itemid,i.value_type,h.name'.
				' FROM items i,hosts h'.
				' WHERE i.key_='.bs_dbstr($exprPart['item']).
					' AND '.dbConditionInt('i.flags', [BS_FLAG_DISCOVERY_NORMAL, BS_FLAG_DISCOVERY_CREATED, BS_FLAG_DISCOVERY_PROTOTYPE]).
					' AND h.host='.bs_dbstr($exprPart['host']).
					' AND h.hostid=i.hostid'
			);
			if ($row = DBfetch($result)) {
				$hostnames[] = $row['name'];
				$items[$exprPart['host']][$exprPart['item']] = [
					'itemid' => $row['itemid'],
					'valueType' => $row['value_type']
				];
			}
			else {
				throw new Exception(_s('Incorrect item key "%1$s" provided for trigger expression on "%2$s".',
						$exprPart['item'], $exprPart['host']));
			}
		}

		if (!$triggerFunctionValidator->validate([
				'function' => $exprPart['function'],
				'functionName' => $exprPart['functionName'],
				'functionParamList' => $exprPart['functionParamList'],
				'valueType' => $items[$exprPart['host']][$exprPart['item']]['valueType']])) {
			throw new Exception($triggerFunctionValidator->getError());
		}

		$newFunctions[$exprPart['expression']] = 0;

		$functions[] = [
			'itemid' => $items[$exprPart['host']][$exprPart['item']]['itemid'],
			'triggerid' => $triggerId,
			'function' => $exprPart['functionName'],
			'parameter' => $exprPart['functionParam']
		];
	}

	$functionIds = DB::insert('functions', $functions);

	$num = 0;
	foreach ($newFunctions as &$newFunction) {
		$newFunction = $functionIds[$num++];
	}
	unset($newFunction);

	$exprPart = end($expressionData->expressions);
	do {
		$expression = substr_replace($expression, '{'.$newFunctions[$exprPart['expression']].'}',
				$exprPart['pos'], strlen($exprPart['expression']));
	}
	while ($exprPart = prev($expressionData->expressions));

	$hostnames = array_unique($hostnames);

	return $expression;
}

function check_right_on_trigger_by_expression($permission, $expression) {
	$expressionData = new CTriggerExpression();
	if (!$expressionData->parse($expression)) {
		error($expressionData->error);
		return false;
	}
	$expressionHosts = $expressionData->getHosts();

	$hosts = API::Host()->get([
		'filter' => ['host' => $expressionHosts],
		'editable' => ($permission == PERM_READ_WRITE) ? 1 : null,
		'output' => ['hostid', 'host'],
		'templated_hosts' => true,
		'preservekeys' => true
	]);
	$hosts = bs_toHash($hosts, 'host');

	foreach ($expressionHosts as $host) {
		if (!isset($hosts[$host])) {
			error(_s('Incorrect trigger expression. Host "%1$s" does not exist or you have no access to this host.', $host));
			return false;
		}
	}

	return true;
}

function replace_template_dependencies($deps, $hostid) {
	foreach ($deps as $id => $val) {
		$sql = 'SELECT t.triggerid'.
				' FROM triggers t,functions f,items i'.
				' WHERE t.triggerid=f.triggerid'.
					' AND f.itemid=i.itemid'.
					' AND t.templateid='.bs_dbstr($val).
					' AND i.hostid='.bs_dbstr($hostid);
		if ($db_new_dep = DBfetch(DBselect($sql))) {
			$deps[$id] = $db_new_dep['triggerid'];
		}
	}

	return $deps;
}

/**
 * Creates and returns the trigger overview table for the given hosts.
 *
 * @param array  	$hosts							an array of hosts with host IDs as keys
 * @param string 	$hosts[hostid][name]
 * @param string 	$hosts[hostid][hostid]
 * @param array		$triggers
 * @param string	$triggers[][triggerid]
 * @param string	$triggers[][description]
 * @param string	$triggers[][expression]
 * @param int		$triggers[][value]
 * @param int		$triggers[][lastchange]
 * @param int		$triggers[][flags]
 * @param array		$triggers[][url]
 * @param int		$triggers[][priority]
 * @param array		$triggers[][hosts]
 * @param string	$triggers[][hosts][][hostid]
 * @param string	$triggers[][hosts][][name]
 * @param string 	$pageFile						the page where the element is displayed
 * @param int    	$viewMode						table display style: either hosts on top, or host on the left side
 * @param string 	$screenId						the ID of the screen, that contains the trigger overview table
 *
 * @return CTableInfo
 */
function getTriggersOverview(array $hosts, array $triggers, $pageFile, $viewMode = null, $screenId = null) {
	$data = [];
	$hostNames = [];
	$trcounter = [];

	$triggers = CMacrosResolverHelper::resolveTriggerNames($triggers, true);

	foreach ($triggers as $trigger) {
		$trigger_name = $trigger['description'];

		foreach ($trigger['hosts'] as $host) {
			// triggers may belong to hosts that are filtered out and shouldn't be displayed, skip them
			if (!isset($hosts[$host['hostid']])) {
				continue;
			}

			$hostNames[$host['hostid']] = $host['name'];

			if (!array_key_exists($host['name'], $trcounter)) {
				$trcounter[$host['name']] = [];
			}

			if (!array_key_exists($trigger_name, $trcounter[$host['name']])) {
				$trcounter[$host['name']][$trigger_name] = 0;
			}

			$data[$trigger_name][$trcounter[$host['name']][$trigger_name]][$host['name']] = [
				'groupid' => $trigger['groupid'],
				'hostid' => $host['hostid'],
				'triggerid' => $trigger['triggerid'],
				'value' => $trigger['value'],
				'lastchange' => $trigger['lastchange'],
				'priority' => $trigger['priority'],
				'flags' => $trigger['flags'],
				'url' => $trigger['url'],
				'hosts' => $trigger['hosts'],
				'items' => $trigger['items']
			];
			$trcounter[$host['name']][$trigger_name]++;
		}
	}

	$triggerTable = new CTableInfo();

	if (empty($hostNames)) {
		return $triggerTable;
	}

	$triggerTable->makeVerticalRotation();

	order_result($hostNames);

	if ($viewMode == STYLE_TOP) {
		// header
		$header = [_('Triggers')];

		foreach ($hostNames as $hostName) {
			$header[] = (new CColHeader($hostName))->addClass('vertical_rotation');
		}
		$triggerTable->setHeader($header);

		// data
		foreach ($data as $trigger_name => $trigger_data) {
			foreach ($trigger_data as $trigger_hosts) {
				$columns = [nbsp($trigger_name)];

				foreach ($hostNames as $hostName) {
					$columns[] = getTriggerOverviewCells(
						isset($trigger_hosts[$hostName]) ? $trigger_hosts[$hostName] : null,
						$pageFile,
						$screenId
					);
				}
				$triggerTable->addRow($columns);
			}
		}
	}
	else {
		// header
		$header = [_('Host')];

		foreach ($data as $trigger_name => $trigger_data) {
			foreach ($trigger_data as $trigger_hosts) {
				$header[] = (new CColHeader($trigger_name))->addClass('vertical_rotation');
			}
		}

		$triggerTable->setHeader($header);

		// data
		$scripts = API::Script()->getScriptsByHosts(bs_objectValues($hosts, 'hostid'));

		foreach ($hostNames as $hostId => $hostName) {
			$name = (new CSpan($hostName))->addClass(BS_STYLE_LINK_ACTION);
			$name->setMenuPopup(CMenuPopupHelper::getHost($hosts[$hostId], $scripts[$hostId]));

			$columns = [(new CCol($name))->addClass(BS_STYLE_NOWRAP)];
			foreach ($data as $trigger_data) {
				foreach ($trigger_data as $trigger_hosts) {
					$columns[] = getTriggerOverviewCells(
						isset($trigger_hosts[$hostName]) ? $trigger_hosts[$hostName] : null,
						$pageFile,
						$screenId
					);
				}
			}

			$triggerTable->addRow($columns);
		}
	}

	return $triggerTable;
}

/**
 * Creates and returns a trigger status cell for the trigger overview table.
 *
 * @see getTriggersOverview()
 *
 * @param array  $trigger
 * @param string $pageFile		the page where the element is displayed
 * @param string $screenid
 *
 * @return CCol
 */
function getTriggerOverviewCells($trigger, $pageFile, $screenid = null) {
	$ack = null;
	$css = null;
	$desc = [];
	$acknowledge = [];

	// for how long triggers should blink on status change (set by user in administration->general)
	$config = select_config();

	if ($trigger) {
		$css = getSeverityStyle($trigger['priority'], $trigger['value'] == TRIGGER_VALUE_TRUE);

		// problem trigger
		if ($trigger['value'] == TRIGGER_VALUE_TRUE) {
			$ack = null;

			if ($config['event_ack_enable']) {
				if ($event = get_last_event_by_triggerid($trigger['triggerid'])) {
					if ($screenid !== null) {
						$acknowledge = [
							'eventid' => $event['eventid'],
							'backurl' => $pageFile.'?screenid='.$screenid
						];
					}
					else {
						$acknowledge = [
							'eventid' => $event['eventid'],
							'backurl' => $pageFile
						];
					}

					if ($event['acknowledged'] == 1) {
						$ack = (new CSpan())->addClass(BS_STYLE_ICON_ACKN);
					}
				}
			}
		}

		// dependency: triggers on which depends this
		$triggerId = empty($trigger['triggerid']) ? 0 : $trigger['triggerid'];

		// trigger dependency DOWN
		$dependencyTable = (new CTableInfo())
			->setAttribute('style', 'width: 200px;')
			->addRow(bold(_('Depends on').':'));

		$isDependencyFound = false;
		$dbDependencies = DBselect('SELECT td.* FROM trigger_depends td WHERE td.triggerid_down='.bs_dbstr($triggerId));
		while ($dbDependency = DBfetch($dbDependencies)) {
			$dependencyTable->addRow(SPACE.'-'.SPACE.CMacrosResolverHelper::resolveTriggerNameById($dbDependency['triggerid_up']));
			$isDependencyFound = true;
		}

		if ($isDependencyFound) {
			$desc[] = (new CSpan())
				->addClass(BS_STYLE_ICON_DEPEND_DOWN)
				->setHint($dependencyTable, '', false);
		}

		// trigger dependency UP
		$dependencyTable = (new CTableInfo())
			->setAttribute('style', 'width: 200px;')
			->addRow(bold(_('Dependent').':'));

		$isDependencyFound = false;
		$dbDependencies = DBselect('SELECT td.* FROM trigger_depends td WHERE td.triggerid_up='.bs_dbstr($triggerId));
		while ($dbDependency = DBfetch($dbDependencies)) {
			$dependencyTable->addRow(SPACE.'-'.SPACE.CMacrosResolverHelper::resolveTriggerNameById($dbDependency['triggerid_down']));
			$isDependencyFound = true;
		}

		if ($isDependencyFound) {
			$desc[] = (new CSpan())
				->addClass(BS_STYLE_ICON_DEPEND_UP)
				->setHint($dependencyTable, '', false);
		}
	}

	$column = new CCol([$desc, $ack]);

	if ($css !== null) {
		$column
			->addClass($css)
			->addClass(BS_STYLE_CURSOR_POINTER);
	}

	if ($trigger && $config['blink_period'] > 0 && time() - $trigger['lastchange'] < $config['blink_period']) {
		$column->addClass('blink');
		$column->setAttribute('data-toggle-class', $css);
	}

	if ($trigger) {
		$column->setMenuPopup(CMenuPopupHelper::getTrigger($trigger, $acknowledge));
	}

	return $column;
}

/**
 * Calculate trigger availability.
 *
 * @param int $triggerId		trigger id
 * @param int $startTime		begin period
 * @param int $endTime			end period
 *
 * @return array
 */
function calculateAvailability($triggerId, $startTime, $endTime) {
	$startValue = TRIGGER_VALUE_FALSE;

	if ($startTime > 0 && $startTime <= time()) {
		$sql = 'SELECT e.eventid,e.value'.
				' FROM events e'.
				' WHERE e.objectid='.bs_dbstr($triggerId).
					' AND e.source='.EVENT_SOURCE_TRIGGERS.
					' AND e.object='.EVENT_OBJECT_TRIGGER.
					' AND e.clock<'.bs_dbstr($startTime).
				' ORDER BY e.eventid DESC';
		if ($row = DBfetch(DBselect($sql, 1))) {
			$startValue = $row['value'];
			$min = $startTime;
		}
	}

	$sql = 'SELECT COUNT(e.eventid) AS cnt,MIN(e.clock) AS min_clock,MAX(e.clock) AS max_clock'.
			' FROM events e'.
			' WHERE e.objectid='.bs_dbstr($triggerId).
				' AND e.source='.EVENT_SOURCE_TRIGGERS.
				' AND e.object='.EVENT_OBJECT_TRIGGER;
	if ($startTime) {
		$sql .= ' AND e.clock>='.bs_dbstr($startTime);
	}
	if ($endTime) {
		$sql .= ' AND e.clock<='.bs_dbstr($endTime);
	}

	$dbEvents = DBfetch(DBselect($sql));
	if ($dbEvents['cnt'] > 0) {
		if (!isset($min)) {
			$min = $dbEvents['min_clock'];
		}
		$max = $dbEvents['max_clock'];
	}
	else {
		if ($startTime == 0 && $endTime == 0) {
			$max = time();
			$min = $max - SEC_PER_DAY;
		}
		else {
			$ret['true_time'] = 0;
			$ret['false_time'] = 0;
			$ret['true'] = (TRIGGER_VALUE_TRUE == $startValue) ? 100 : 0;
			$ret['false'] = (TRIGGER_VALUE_FALSE == $startValue) ? 100 : 0;
			return $ret;
		}
	}

	$state = $startValue;
	$true_time = 0;
	$false_time = 0;
	$time = $min;
	if ($startTime == 0 && $endTime == 0) {
		$max = time();
	}
	if ($endTime == 0) {
		$endTime = $max;
	}

	$rows = 0;
	$dbEvents = DBselect(
		'SELECT e.eventid,e.clock,e.value'.
		' FROM events e'.
		' WHERE e.objectid='.bs_dbstr($triggerId).
			' AND e.source='.EVENT_SOURCE_TRIGGERS.
			' AND e.object='.EVENT_OBJECT_TRIGGER.
			' AND e.clock BETWEEN '.$min.' AND '.$max.
		' ORDER BY e.eventid'
	);
	while ($row = DBfetch($dbEvents)) {
		$clock = $row['clock'];
		$value = $row['value'];

		$diff = $clock - $time;
		$time = $clock;

		if ($state == 0) {
			$false_time += $diff;
			$state = $value;
		}
		elseif ($state == 1) {
			$true_time += $diff;
			$state = $value;
		}
		$rows++;
	}

	if ($rows == 0) {
		$trigger = get_trigger_by_triggerid($triggerId);
		$state = $trigger['value'];
	}

	if ($state == TRIGGER_VALUE_FALSE) {
		$false_time = $false_time + $endTime - $time;
	}
	elseif ($state == TRIGGER_VALUE_TRUE) {
		$true_time = $true_time + $endTime - $time;
	}
	$total_time = $true_time + $false_time;

	if ($total_time == 0) {
		$ret['true_time'] = 0;
		$ret['false_time'] = 0;
		$ret['true'] = 0;
		$ret['false'] = 0;
	}
	else {
		$ret['true_time'] = $true_time;
		$ret['false_time'] = $false_time;
		$ret['true'] = (100 * $true_time) / $total_time;
		$ret['false'] = (100 * $false_time) / $total_time;
	}

	return $ret;
}

function get_triggers_unacknowledged($db_element, $count_problems = null, $ack = false) {
	$elements = [
		'hosts' => [],
		'hosts_groups' => [],
		'triggers' => []
	];

	get_map_elements($db_element, $elements);
	if (empty($elements['hosts_groups']) && empty($elements['hosts']) && empty($elements['triggers'])) {
		return 0;
	}

	$config = select_config();

	$options = [
		'monitored' => true,
		'countOutput' => true,
		'filter' => [],
		'limit' => $config['search_limit'] + 1
	];

	if ($ack) {
		$options['withAcknowledgedEvents'] = 1;
	}
	else {
		$options['withUnacknowledgedEvents'] = 1;
	}

	if ($count_problems) {
		$options['filter']['value'] = TRIGGER_VALUE_TRUE;
	}
	if (!empty($elements['hosts_groups'])) {
		$options['groupids'] = array_unique($elements['hosts_groups']);
	}
	if (!empty($elements['hosts'])) {
		$options['hostids'] = array_unique($elements['hosts']);
	}
	if (!empty($elements['triggers'])) {
		$options['triggerids'] = array_unique($elements['triggers']);
	}

	return API::Trigger()->get($options);
}

function make_trigger_details($trigger) {
	$hostNames = [];

	$config = select_config();

	$hostIds = bs_objectValues($trigger['hosts'], 'hostid');

	$hosts = API::Host()->get([
		'output' => ['name', 'hostid', 'status'],
		'hostids' => $hostIds,
		'selectScreens' => API_OUTPUT_COUNT,
		'selectGraphs' => API_OUTPUT_COUNT
	]);

	if (count($hosts) > 1) {
		order_result($hosts, 'name', BS_SORT_UP);
	}

	$scripts = API::Script()->getScriptsByHosts($hostIds);

	foreach ($hosts as $host) {
		$hostName = new CSpan($host['name'], BS_STYLE_LINK_ACTION);
		$hostName->setMenuPopup(CMenuPopupHelper::getHost($host, $scripts[$host['hostid']]));
		$hostNames[] = $hostName;
		$hostNames[] = ', ';
	}
	array_pop($hostNames);

	$expression = CMacrosResolverHelper::resolveTriggerExpression($trigger['expression'],
		['html' => true, 'resolve_usermacros' => true, 'resolve_macros' => true]);

	$table = (new CTableInfo())
		->addRow([
			new CCol(_n('Host', 'Hosts', count($hosts))),
			new CCol($hostNames)
		])
		->addRow([
			new CCol(_('Trigger')),
			new CCol(CMacrosResolverHelper::resolveTriggerName($trigger))
		])
		->addRow([
			_('Severity'),
			getSeverityCell($trigger['priority'], $config)
		])
		->addRow([
			new CCol(_('Expression')),
			new CCol($expression)
		])
		->addRow([_('Event generation'), _('Normal').((TRIGGER_MULT_EVENT_ENABLED == $trigger['type'])
			? SPACE.'+'.SPACE._('Multiple PROBLEM events') : '')])
		->addRow([_('Disabled'), ((TRIGGER_STATUS_ENABLED == $trigger['status'])
			? (new CCol(_('No')))->addClass(BS_STYLE_GREEN) : (new CCol(_('Yes')))->addClass(BS_STYLE_RED))
		]);

	return $table;
}

/**
 * Analyze an expression and returns expression html tree
 *
 * @param string $expression
 *
 * @return array
 */
function analyzeExpression($expression) {
	if (empty($expression)) {
		return ['', null];
	}

	$expressionData = new CTriggerExpression();
	if (!$expressionData->parse($expression)) {
		error($expressionData->error);
		return false;
	}

	$expressionTree[] = getExpressionTree($expressionData, 0, strlen($expressionData->expression) - 1);

	$next = [];
	$letterNum = 0;
	return buildExpressionHtmlTree($expressionTree, $next, $letterNum);
}

/**
 * Builds expression html tree
 *
 * @param array 	$expressionTree 	output of getExpressionTree() function
 * @param array 	$next           	parameter only for recursive call; should be empty array
 * @param int 		$letterNum      	parameter only for recursive call; should be 0
 * @param int 		$level          	parameter only for recursive call
 * @param string 	$operator       	parameter only for recursive call
 *
 * @return array	array containing the trigger expression formula as the first element and an array describing the
 *					expression tree as the second
 */
function buildExpressionHtmlTree(array $expressionTree, array &$next, &$letterNum, $level = 0, $operator = null) {
	$treeList = [];
	$outline = '';

	end($expressionTree);
	$lastKey = key($expressionTree);

	foreach ($expressionTree as $key => $element) {
		switch ($element['type']) {
			case 'operator':
				$next[$level] = ($key != $lastKey);
				$expr = expressionLevelDraw($next, $level);
				$expr[] = SPACE;
				$expr[] = ($element['operator'] === 'and') ? _('And') : _('Or');
				$levelDetails = [
					'list' => $expr,
					'id' => $element['id'],
					'expression' => [
						'value' => $element['expression']
					]
				];

				$levelErrors = expressionHighLevelErrors($element['expression']);
				if (count($levelErrors) > 0) {
					$levelDetails['expression']['levelErrors'] = $levelErrors;
				}
				$treeList[] = $levelDetails;

				list($subOutline, $subTreeList) = buildExpressionHtmlTree($element['elements'], $next, $letterNum,
						$level + 1, $element['operator']);
				$treeList = array_merge($treeList, $subTreeList);

				$outline .= ($level == 0) ? $subOutline : '('.$subOutline.')';
				if ($operator !== null && $next[$level]) {
					$outline .= ' '.$operator.' ';
				}
				break;
			case 'expression':
				$next[$level] = ($key != $lastKey);

				$letter = num2letter($letterNum++);
				$outline .= $letter;
				if ($operator !== null && $next[$level]) {
					$outline .= ' '.$operator.' ';
				}

				if (defined('NO_LINK_IN_TESTING')) {
					$url = $element['expression'];
				}
				else {
					$expressionId = 'expr_'.$element['id'];

					$url = (new CSpan($element['expression']))
						->addClass(BS_STYLE_LINK_ACTION)
						->setId($expressionId)
						->onClick('javascript: copy_expression("'.$expressionId.'");');
				}
				$expr = expressionLevelDraw($next, $level);
				$expr[] = SPACE;
				$expr[] = bold($letter);
				$expr[] = SPACE;
				$expr[] = $url;

				$levelDetails = [
					'list' => $expr,
					'id' => $element['id'],
					'expression' => [
						'value' => $element['expression']
					]
				];

				$levelErrors = expressionHighLevelErrors($element['expression']);
				if (count($levelErrors) > 0) {
					$levelDetails['expression']['levelErrors'] = $levelErrors;
				}
				$treeList[] = $levelDetails;
				break;
		}
	}
	return [$outline, $treeList];
}

function expressionHighLevelErrors($expression) {
	static $errors, $definedErrorPhrases;

	if (!isset($errors)) {
		$definedErrorPhrases = [
			EXPRESSION_HOST_UNKNOWN => _('Unknown host, no such host present in system'),
			EXPRESSION_HOST_ITEM_UNKNOWN => _('Unknown host item, no such item in selected host'),
			EXPRESSION_NOT_A_MACRO_ERROR => _('Given expression is not a macro'),
			EXPRESSION_FUNCTION_UNKNOWN => _('Incorrect function is used')
		];
		$errors = [];
	}

	if (!isset($errors[$expression])) {
		$errors[$expression] = [];
		$expressionData = new CTriggerExpression();
		if ($expressionData->parse($expression)) {
			foreach ($expressionData->expressions as $exprPart) {
				$info = get_item_function_info($exprPart['expression']);

				if (!is_array($info) && isset($definedErrorPhrases[$info])) {
					if (!isset($errors[$expression][$exprPart['expression']])) {
						$errors[$expression][$exprPart['expression']] = $definedErrorPhrases[$info];
					}
				}
			}
		}
	}

	$ret = [];
	if (count($errors[$expression]) == 0) {
		return $ret;
	}

	$expressionData = new CTriggerExpression();
	if ($expressionData->parse($expression)) {
		foreach ($expressionData->expressions as $exprPart) {
			if (isset($errors[$expression][$exprPart['expression']])) {
				$ret[$exprPart['expression']] = $errors[$expression][$exprPart['expression']];
			}
		}
	}
	return $ret;
}

/**
 * Draw level for trigger expression builder tree
 *
 * @param array $next
 * @param int $level
 *
 * @return array
 */
function expressionLevelDraw(array $next, $level) {
	$expr = [];
	for ($i = 1; $i <= $level; $i++) {
		if ($i == $level) {
			$image = $next[$i] ? 'top_right_bottom' : 'top_right';
		}
		else {
			$image = $next[$i] ? 'top_bottom' : 'space';
		}
		$expr[] = new CImg('images/general/tr_'.$image.'.gif', 'tr', 12, 12);
	}
	return $expr;
}

/**
 * Makes tree of expression elements
 *
 * Expression:
 *   "{host1:system.cpu.util[,iowait].last(0)} > 50 and {host2:system.cpu.util[,iowait].last(0)} > 50"
 * Result:
 *   array(
 *     [0] => array(
 *       'id' => '0_94',
 *       'type' => 'operator',
 *       'operator' => 'and',
 *       'elements' => array(
 *         [0] => array(
 *           'id' => '0_44',
 *           'type' => 'expression',
 *           'expression' => '{host1:system.cpu.util[,iowait].last(0)} > 50'
 *         ),
 *         [1] => array(
 *           'id' => '50_94',
 *           'type' => 'expression',
 *           'expression' => '{host2:system.cpu.util[,iowait].last(0)} > 50'
 *         )
 *       )
 *     )
 *   )
 *
 * @param CTriggerExpression $expressionData
 * @param int $start
 * @param int $end
 *
 * @return array
 */
function getExpressionTree(CTriggerExpression $expressionData, $start, $end) {
	$blankSymbols = [' ', "\r", "\n", "\t"];

	$expressionTree = [];
	foreach (['or', 'and'] as $operator) {
		$operatorFound = false;
		$lParentheses = -1;
		$rParentheses = -1;
		$expressions = [];
		$openSymbolNum = $start;
		$operatorPos = 0;
		$operatorToken = '';

		for ($i = $start, $level = 0; $i <= $end; $i++) {
			switch ($expressionData->expression[$i]) {
				case ' ':
				case "\r":
				case "\n":
				case "\t":
					if ($openSymbolNum == $i) {
						$openSymbolNum++;
					}
					break;
				case '(':
					if ($level == 0) {
						$lParentheses = $i;
					}
					$level++;
					break;
				case ')':
					$level--;
					if ($level == 0) {
						$rParentheses = $i;
					}
					break;
				case '{':
					foreach ($expressionData->expressions as $exprPart) {
						if ($exprPart['pos'] == $i) {
							$i += strlen($exprPart['expression']) - 1;
							break;
						}
					}
					break;
				default:
					// try to parse an operator
					if ($operator[$operatorPos] === $expressionData->expression[$i]) {
						$operatorPos++;
						$operatorToken .= $expressionData->expression[$i];

						// operator found
						if ($operatorToken === $operator) {
							// we've reached the end of a complete expression, parse the expression on the left side of
							// the operator
							if ($level == 0) {
								// find the last symbol of the expression before the operator
								$closeSymbolNum = $i - strlen($operator);

								// trim blank symbols after the expression
								while (in_array($expressionData->expression[$closeSymbolNum], $blankSymbols)) {
									$closeSymbolNum--;
								}

								$expressions[] = getExpressionTree($expressionData, $openSymbolNum, $closeSymbolNum);
								$openSymbolNum = $i + 1;
								$operatorFound = true;
							}
							$operatorPos = 0;
							$operatorToken = '';
						}
					}
			}
		}

		// trim blank symbols in the end of the trigger expression
		$closeSymbolNum = $end;
		while (in_array($expressionData->expression[$closeSymbolNum], $blankSymbols)) {
			$closeSymbolNum--;
		}

		// we've found a whole expression and parsed the expression on the left side of the operator,
		// parse the expression on the right
		if ($operatorFound) {
			$expressions[] = getExpressionTree($expressionData, $openSymbolNum, $closeSymbolNum);

			// trim blank symbols in the beginning of the trigger expression
			$openSymbolNum = $start;
			while (in_array($expressionData->expression[$openSymbolNum], $blankSymbols)) {
				$openSymbolNum++;
			}

			// trim blank symbols in the end of the trigger expression
			$closeSymbolNum = $end;
			while (in_array($expressionData->expression[$closeSymbolNum], $blankSymbols)) {
				$closeSymbolNum--;
			}

			$expressionTree = [
				'id' => $openSymbolNum.'_'.$closeSymbolNum,
				'expression' => substr($expressionData->expression, $openSymbolNum, $closeSymbolNum - $openSymbolNum + 1),
				'type' => 'operator',
				'operator' => $operator,
				'elements' => $expressions
			];
			break;
		}
		// if we've tried both operators and didn't find anything, it means there's only one expression
		// return the result
		elseif ($operator === 'and') {
			// trim extra parentheses
			if ($openSymbolNum == $lParentheses && $closeSymbolNum == $rParentheses) {
				$openSymbolNum++;
				$closeSymbolNum--;

				$expressionTree = getExpressionTree($expressionData, $openSymbolNum, $closeSymbolNum);
			}
			// no extra parentheses remain, return the result
			else {
				$expressionTree = [
					'id' => $openSymbolNum.'_'.$closeSymbolNum,
					'expression' => substr($expressionData->expression, $openSymbolNum, $closeSymbolNum - $openSymbolNum + 1),
					'type' => 'expression'
				];
			}
		}
	}

	return $expressionTree;
}

/**
 * Recreate an expression depending on action.
 *
 * Supported action values:
 * - and	- add an expression using "and";
 * - or		- add an expression using "or";
 * - r 		- replace;
 * - R		- remove.
 *
 * @param string $expression
 * @param string $expressionId  element identifier like "0_55"
 * @param string $action        action to perform
 * @param string $newExpression expression for AND, OR or replace actions
 *
 * @return bool                 returns new expression or false if expression is incorrect
 */
function remakeExpression($expression, $expressionId, $action, $newExpression) {
	if (empty($expression)) {
		return false;
	}

	$expressionData = new CTriggerExpression();
	if ($action != 'R' && !$expressionData->parse($newExpression)) {
		error($expressionData->error);
		return false;
	}

	if (!$expressionData->parse($expression)) {
		error($expressionData->error);
		return false;
	}

	$expressionTree[] = getExpressionTree($expressionData, 0, strlen($expressionData->expression) - 1);

	if (rebuildExpressionTree($expressionTree, $expressionId, $action, $newExpression)) {
		$expression = makeExpression($expressionTree);
	}
	return $expression;
}

/**
 * Rebuild expression depending on action.
 *
 * Supported action values:
 * - and	- add an expression using "and";
 * - or		- add an expression using "or";
 * - r 		- replace;
 * - R		- remove.
 *
 * Example:
 *   $expressionTree = array(
 *     [0] => array(
 *       'id' => '0_94',
 *       'type' => 'operator',
 *       'operator' => 'and',
 *       'elements' => array(
 *         [0] => array(
 *           'id' => '0_44',
 *           'type' => 'expression',
 *           'expression' => '{host1:system.cpu.util[,iowait].last(0)} > 50'
 *         ),
 *         [1] => array(
 *           'id' => '50_94',
 *           'type' => 'expression',
 *           'expression' => '{host2:system.cpu.util[,iowait].last(0)} > 50'
 *         )
 *       )
 *     )
 *   )
 *   $action = 'R'
 *   $expressionId = '50_94'
 *
 * Result:
 *   $expressionTree = array(
 *     [0] => array(
 *       'id' => '0_44',
 *       'type' => 'expression',
 *       'expression' => '{host1:system.cpu.util[,iowait].last(0)} > 50'
 *     )
 *   )
 *
 * @param array 	$expressionTree
 * @param string 	$expressionId  		element identifier like "0_55"
 * @param string 	$action        		action to perform
 * @param string 	$newExpression 		expression for AND, OR or replace actions
 * @param string 	$operator       	parameter only for recursive call
 *
 * @return bool                 returns true if element is found, false - otherwise
 */
function rebuildExpressionTree(array &$expressionTree, $expressionId, $action, $newExpression, $operator = null) {
	foreach ($expressionTree as $key => $expression) {
		if ($expressionId == $expressionTree[$key]['id']) {
			switch ($action) {
				case 'and':
				case 'or':
					switch ($expressionTree[$key]['type']) {
						case 'operator':
							if ($expressionTree[$key]['operator'] == $action) {
								$expressionTree[$key]['elements'][] = [
									'expression' => $newExpression,
									'type' => 'expression'
								];
							}
							else {
								$element = [
									'type' => 'operator',
									'operator' => $action,
									'elements' => [
										$expressionTree[$key],
										[
											'expression' => $newExpression,
											'type' => 'expression'
										]
									]
								];
								$expressionTree[$key] = $element;
							}
							break;
						case 'expression':
							if (!$operator || $operator != $action) {
								$element = [
									'type' => 'operator',
									'operator' => $action,
									'elements' => [
										$expressionTree[$key],
										[
											'expression' => $newExpression,
											'type' => 'expression'
										]
									]
								];
								$expressionTree[$key] = $element;
							}
							else {
								$expressionTree[] = [
									'expression' => $newExpression,
									'type' => 'expression'
								];
							}
							break;
					}
					break;
				// replace
				case 'r':
					$expressionTree[$key]['expression'] = $newExpression;
					if ($expressionTree[$key]['type'] == 'operator') {
						$expressionTree[$key]['type'] = 'expression';
						unset($expressionTree[$key]['operator'], $expressionTree[$key]['elements']);
					}
					break;
				// remove
				case 'R':
					unset($expressionTree[$key]);
					break;
			}
			return true;
		}

		if ($expressionTree[$key]['type'] == 'operator') {
			if (rebuildExpressionTree($expressionTree[$key]['elements'], $expressionId, $action, $newExpression,
					$expressionTree[$key]['operator'])) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Makes expression by expression tree
 *
 * Example:
 *   $expressionTree = array(
 *     [0] => array(
 *       'type' => 'operator',
 *       'operator' => 'and',
 *       'elements' => array(
 *         [0] => array(
 *           'type' => 'expression',
 *           'expression' => '{host1:system.cpu.util[,iowait].last(0)} > 50'
 *         ),
 *         [1] => array(
 *           'type' => 'expression',
 *           'expression' => '{host2:system.cpu.util[,iowait].last(0)} > 50'
 *         )
 *       )
 *     )
 *   )
 *
 * Result:
 *   "{host1:system.cpu.util[,iowait].last(0)} > 50 and {host2:system.cpu.util[,iowait].last(0)} > 50"
 *
 * @param array  $expressionTree
 * @param int    $level				parameter only for recursive call
 * @param string $operator			parameter only for recursive call
 *
 * @return string
 */
function makeExpression(array $expressionTree, $level = 0, $operator = null) {
	$expression = '';

	end($expressionTree);
	$lastKey = key($expressionTree);

	foreach ($expressionTree as $key => $element) {
		switch ($element['type']) {
			case 'operator':
				$subExpression = makeExpression($element['elements'], $level + 1, $element['operator']);

				$expression .= ($level == 0) ? $subExpression : '('.$subExpression.')';
				break;
			case 'expression':
				$expression .= $element['expression'];
				break;
		}
		if ($operator !== null && $key != $lastKey) {
			$expression .= ' '.$operator.' ';
		}
	}

	return $expression;
}

function get_item_function_info($expr) {
	$value_type = [
		ITEM_VALUE_TYPE_UINT64	=> _('Numeric (integer 64bit)'),
		ITEM_VALUE_TYPE_FLOAT	=> _('Numeric (float)'),
		ITEM_VALUE_TYPE_STR		=> _('Character'),
		ITEM_VALUE_TYPE_LOG		=> _('Log'),
		ITEM_VALUE_TYPE_TEXT	=> _('Text')
	];

	$type_of_value_type = [
		ITEM_VALUE_TYPE_UINT64	=> T_BS_INT,
		ITEM_VALUE_TYPE_FLOAT	=> T_BS_DBL_BIG,
		ITEM_VALUE_TYPE_STR		=> T_BS_STR,
		ITEM_VALUE_TYPE_LOG		=> T_BS_STR,
		ITEM_VALUE_TYPE_TEXT	=> T_BS_STR
	];

	$function_info = [
		'band' =>		['value_type' => _('Numeric (integer 64bit)'),	'type' => T_BS_INT, 'validation' => NOT_EMPTY],
		'abschange' =>	['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'avg' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'change' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'count' =>		['value_type' => _('Numeric (integer 64bit)'), 'type' => T_BS_INT, 'validation' => NOT_EMPTY],
		'date' =>		['value_type' => 'YYYYMMDD',	'type' => T_BS_INT,			'validation' => '{}>=19700101&&{}<=99991231'],
		'dayofmonth' =>	['value_type' => '1-31',		'type' => T_BS_INT,			'validation' => '{}>=1&&{}<=31'],
		'dayofweek' =>	['value_type' => '1-7',		'type' => T_BS_INT,			'validation' => IN('1,2,3,4,5,6,7')],
		'delta' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'diff' =>		['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'forecast' =>	['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'fuzzytime' =>	['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'iregexp' =>	['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'last' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'logeventid' =>	['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'logseverity' =>['value_type' => _('Numeric (integer 64bit)'), 'type' => T_BS_INT, 'validation' => NOT_EMPTY],
		'logsource' =>	['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'max' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'min' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'nodata' =>		['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'now' =>		['value_type' => _('Numeric (integer 64bit)'), 'type' => T_BS_INT, 'validation' => NOT_EMPTY],
		'percentile' =>	['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'prev' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'regexp' =>		['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'str' =>		['value_type' => _('0 or 1'),	'type' => T_BS_INT,			'validation' => IN('0,1')],
		'strlen' =>		['value_type' => _('Numeric (integer 64bit)'), 'type' => T_BS_INT, 'validation' => NOT_EMPTY],
		'sum' =>		['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY],
		'time' =>		['value_type' => 'HHMMSS',		'type' => T_BS_INT,			'validation' => 'strlen({})==6'],
		'timeleft' =>	['value_type' => $value_type,	'type' => $type_of_value_type,	'validation' => NOT_EMPTY]
	];

	$expressionData = new CTriggerExpression();
	$parseResult = $expressionData->parse($expr);

	if ($parseResult) {
		if ($parseResult->hasTokenOfType(CTriggerExpressionParserResult::TOKEN_TYPE_MACRO)) {
			$result = [
				'value_type' => _('0 or 1'),
				'type' => T_BS_INT,
				'validation' => IN('0,1')
			];
		}
		elseif ($parseResult->hasTokenOfType(CTriggerExpressionParserResult::TOKEN_TYPE_USER_MACRO)
				|| $parseResult->hasTokenOfType(CTriggerExpressionParserResult::TOKEN_TYPE_LLD_MACRO)) {

			$result = [
				'value_type' => $value_type[ITEM_VALUE_TYPE_FLOAT],
				'type' => T_BS_STR,
				'validation' => 'preg_match("/^'.BS_PREG_NUMBER.'$/u", {})'
			];
		}
		elseif ($parseResult->hasTokenOfType(CTriggerExpressionParserResult::TOKEN_TYPE_FUNCTION_MACRO)) {
			$exprPart = reset($expressionData->expressions);

			if (!isset($function_info[$exprPart['functionName']])) {
				return EXPRESSION_FUNCTION_UNKNOWN;
			}

			$hostFound = API::Host()->get([
				'output' => ['hostid'],
				'filter' => ['host' => [$exprPart['host']]],
				'templated_hosts' => true
			]);

			if (!$hostFound) {
				return EXPRESSION_HOST_UNKNOWN;
			}

			$itemFound = API::Item()->get([
				'output' => ['value_type'],
				'hostids' => bs_objectValues($hostFound, 'hostid'),
				'filter' => [
					'key_' => [$exprPart['item']]
				],
				'webitems' => true
			]);

			if (!$itemFound) {
				$itemFound = API::ItemPrototype()->get([
					'output' => ['value_type'],
					'hostids' => bs_objectValues($hostFound, 'hostid'),
					'filter' => [
						'key_' => [$exprPart['item']]
					]
				]);

				if (!$itemFound) {
					return EXPRESSION_HOST_ITEM_UNKNOWN;
				}
			}

			$itemFound = reset($itemFound);
			$result = $function_info[$exprPart['functionName']];

			if (is_array($result['value_type'])) {
				$result['value_type'] = $result['value_type'][$itemFound['value_type']];
				$result['type'] = $result['type'][$itemFound['value_type']];

				if ($result['type'] == T_BS_INT) {
					$result['type'] = T_BS_STR;
					$result['validation'] = 'preg_match("/^'.BS_PREG_NUMBER.'$/u",{})';
				}
			}
		}
		else {
			return EXPRESSION_NOT_A_MACRO_ERROR;
		}
	}

	return $result;
}

/**
 * Substitute macros in the expression with the given values and evaluate its result.
 *
 * @param string $expression                a trigger expression
 * @param array  $replaceFunctionMacros     an array of macro - value pairs
 *
 * @return bool     the calculated value of the expression
 */
function evalExpressionData($expression, $replaceFunctionMacros) {
	// replace function macros with their values
	$expression = str_replace(array_keys($replaceFunctionMacros), array_values($replaceFunctionMacros), $expression);

	$parser = new CTriggerExpression();
	$parseResult = $parser->parse($expression);

	// The $replaceFunctionMacros array may contain string values which after substitution
	// will result in an invalid expression. In such cases we should just return false.
	if (!$parseResult) {
		return false;
	}

	// turn the expression into valid PHP code
	$evStr = '';
	$replaceOperators = ['not' => '!', '=' => '=='];
	foreach ($parseResult->getTokens() as $token) {
		$value = $token['value'];

		switch ($token['type']) {
			case CTriggerExpressionParserResult::TOKEN_TYPE_OPERATOR:
				// replace specific operators with their PHP analogues
				if (isset($replaceOperators[$token['value']])) {
					$value = $replaceOperators[$token['value']];
				}

				break;
			case CTriggerExpressionParserResult::TOKEN_TYPE_NUMBER:
				// convert numeric values with suffixes
				if ($token['data']['suffix'] !== null) {
					$value = convert($value);
				}

				$value = '((float) "'.$value.'")';

				break;
		}

		$evStr .= ' '.$value;
	}

	// execute expression
	eval('$result = ('.trim($evStr).');');

	return $result;
}

function convert($value) {
	$value = trim($value);
	if (!preg_match('/(?P<value>[\-+]?[0-9]+[.]?[0-9]*)(?P<mult>['.BS_BYTE_SUFFIXES.BS_TIME_SUFFIXES.']?)/', $value, $arr)) {
		return $value;
	}

	$value = $arr['value'];
	switch ($arr['mult']) {
		case 'T':
			$value *= 1024 * 1024 * 1024 * 1024;
			break;
		case 'G':
			$value *= 1024 * 1024 * 1024;
			break;
		case 'M':
			$value *= 1024 * 1024;
			break;
		case 'K':
			$value *= 1024;
			break;
		case 'm':
			$value *= 60;
			break;
		case 'h':
			$value *= 60 * 60;
			break;
		case 'd':
			$value *= 60 * 60 * 24;
			break;
		case 'w':
			$value *= 60 * 60 * 24 * 7;
			break;
	}

	return $value;
}

/**
 * Quoting $param if it contains special characters.
 *
 * @param string $param
 * @param bool   $forced
 *
 * @return string
 */
function quoteFunctionParam($param, $forced = false) {
	if (!$forced) {
		if (!isset($param[0]) || ($param[0] != '"' && false === strpbrk($param, ',)'))) {
			return $param;
		}
	}

	return '"'.str_replace('"', '\\"', $param).'"';
}

/**
 * Returns the text indicating the trigger's status and state. If the $state parameter is not given, only the status of
 * the trigger will be taken into account.
 *
 * @param int $status
 * @param int $state
 *
 * @return string
 */
function triggerIndicator($status, $state = null) {
	if ($status == TRIGGER_STATUS_ENABLED) {
		return ($state == TRIGGER_STATE_UNKNOWN) ? _('Unknown') : _('Enabled');
	}
	elseif ($status == TRIGGER_STATUS_DISABLED) {
		return _('Disabled');
	}

	return _('Unknown');
}

/**
 * Returns the CSS class for the trigger's status and state indicator. If the $state parameter is not given, only the
 * status of the trigger will be taken into account.
 *
 * @param int $status
 * @param int $state
 *
 * @return string
 */
function triggerIndicatorStyle($status, $state = null) {
	if ($status == TRIGGER_STATUS_ENABLED) {
		return ($state == TRIGGER_STATE_UNKNOWN) ?
			BS_STYLE_GREY :
			BS_STYLE_GREEN;
	}
	elseif ($status == TRIGGER_STATUS_DISABLED) {
		return BS_STYLE_RED;
	}

	return BS_STYLE_GREY;
}

/**
 * Orders triggers by both status and state. Triggers are sorted in the following order: enabled, disabled, unknown.
 *
 * Keep in sync with orderItemsByStatus().
 *
 * @param array  $triggers
 * @param string $sortorder
 */
function orderTriggersByStatus(array &$triggers, $sortorder = BS_SORT_UP) {
	$sort = [];

	foreach ($triggers as $key => $trigger) {
		if ($trigger['status'] == TRIGGER_STATUS_ENABLED) {
			$statusOrder = ($trigger['state'] == TRIGGER_STATE_UNKNOWN) ? 2 : 0;
		}
		elseif ($trigger['status'] == TRIGGER_STATUS_DISABLED) {
			$statusOrder = 1;
		}

		$sort[$key] = $statusOrder;
	}

	if ($sortorder == BS_SORT_UP) {
		asort($sort);
	}
	else {
		arsort($sort);
	}

	$sortedTriggers = [];
	foreach ($sort as $key => $val) {
		$sortedTriggers[$key] = $triggers[$key];
	}
	$triggers = $sortedTriggers;
}
