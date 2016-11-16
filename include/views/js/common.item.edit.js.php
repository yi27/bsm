<script type="text/x-jquery-tmpl" id="delayFlexRow">
	<tr class="form_row">
		<td>
			<ul class="radio-segmented" id="delay_flex_#{rowNum}_type">
				<li>
					<input type="radio" id="delay_flex_#{rowNum}_type_0" name="delay_flex[#{rowNum}][type]" value="0" checked="checked">
					<label for="delay_flex_#{rowNum}_type_0"><?= _('Flexible') ?></label>
				</li><li>
					<input type="radio" id="delay_flex_#{rowNum}_type_1" name="delay_flex[#{rowNum}][type]" value="1">
					<label for="delay_flex_#{rowNum}_type_1"><?= _('Scheduling') ?></label>
				</li>
			</ul>
		</td>
		<td>
			<input type="text" id="delay_flex_#{rowNum}_delay" name="delay_flex[#{rowNum}][delay]" maxlength="5" onchange="validateNumericBox(this, true, false);" placeholder="50" style="text-align: right;">
			<input type="text" id="delay_flex_#{rowNum}_schedule" name="delay_flex[#{rowNum}][schedule]" maxlength="255" placeholder="wd1-5h9-18" style="display: none;">
		</td>
		<td>
			<input type="text" id="delay_flex_#{rowNum}_period" name="delay_flex[#{rowNum}][period]" maxlength="255" placeholder="<?= BS_DEFAULT_INTERVAL ?>">
		</td>
		<td>
			<button type="button" id="delay_flex_#{rowNum}_remove" name="delay_flex[#{rowNum}][remove]" class="<?= BS_STYLE_BTN_LINK ?> element-table-remove"><?= _('Remove') ?></button>
		</td>
	</tr>
</script>
<script type="text/javascript">
	jQuery(function($) {
		$('#delayFlexTable').on('click', 'input[type="radio"]', function() {
			var rowNum = $(this).attr('id').split('_')[2];

			if ($(this).val() == <?= ITEM_DELAY_FLEX_TYPE_FLEXIBLE; ?>) {
				$('#delay_flex_' + rowNum + '_schedule').hide();
				$('#delay_flex_' + rowNum + '_delay').show();
				$('#delay_flex_' + rowNum + '_period').show();
			}
			else {
				$('#delay_flex_' + rowNum + '_delay').hide();
				$('#delay_flex_' + rowNum + '_period').hide();
				$('#delay_flex_' + rowNum + '_schedule').show();
			}
		});

		$('#delayFlexTable').dynamicRows({
			template: '#delayFlexRow'
		});
	});
</script>
<?php

/*
 * Visibility
 */
$this->data['typeVisibility'] = [];
$i = 0;
foreach ($this->data['delay_flex'] as $delayFlex) {
	if (!isset($delayFlex['delay']) && !isset($delayFlex['period'])) {
		continue;
	}
	foreach ($this->data['types'] as $type => $label) {
		if ($type == ITEM_TYPE_TRAPPER || $type == ITEM_TYPE_BSM_ACTIVE || $type == ITEM_TYPE_SNMPTRAP) {
			continue;
		}
		bs_subarray_push($this->data['typeVisibility'], $type, 'delay_flex['.$i.'][delay]');
		bs_subarray_push($this->data['typeVisibility'], $type, 'delay_flex['.$i.'][period]');
	}
	$i++;
	if ($i == 7) {
		break;
	}
}
if (!empty($this->data['interfaces'])) {
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_BSM, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_BSM, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_EXTERNAL, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_EXTERNAL, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_IPMI, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_IPMI, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'interfaceid');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPTRAP, 'interface_row');
	bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPTRAP, 'interfaceid');
}
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'row_username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'row_password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SIMPLE, 'password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'row_snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'row_snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'row_snmp_oid');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'snmp_community');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'snmp_community');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'row_snmp_community');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'row_snmp_community');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'snmpv3_contextname');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'row_snmpv3_contextname');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'snmpv3_securityname');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'row_snmpv3_securityname');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'snmpv3_securitylevel');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'row_snmpv3_securitylevel');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV1, 'row_port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV2C, 'row_port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SNMPV3, 'row_port');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_IPMI, 'ipmi_sensor');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_IPMI, 'row_ipmi_sensor');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'authtype');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'row_authtype');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'row_username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'row_username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'row_username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'row_username');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'row_password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'row_password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'row_password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_JMX, 'row_password');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'label_executed_script');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'label_executed_script');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'label_params');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_CALCULATED, 'label_formula');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'params_script');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_SSH, 'row_params');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'params_script');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TELNET, 'row_params');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'params_dbmonitor');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_DB_MONITOR, 'row_params');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_CALCULATED, 'params_calculted');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_CALCULATED, 'row_params');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TRAPPER, 'trapper_hosts');
bs_subarray_push($this->data['typeVisibility'], ITEM_TYPE_TRAPPER, 'row_trapper_hosts');
foreach ($this->data['types'] as $type => $label) {
	switch ($type) {
		case ITEM_TYPE_DB_MONITOR:
			$defaultKey = $this->data['is_discovery_rule']
				? BS_DEFAULT_KEY_DB_MONITOR_DISCOVERY
				: BS_DEFAULT_KEY_DB_MONITOR;
			bs_subarray_push($this->data['typeVisibility'], $type,
				['id' => 'key', 'defaultValue' => $defaultKey]
			);
			break;
		case ITEM_TYPE_SSH:
			bs_subarray_push($this->data['typeVisibility'], $type,
				['id' => 'key', 'defaultValue' => BS_DEFAULT_KEY_SSH]
			);
			break;
		case ITEM_TYPE_TELNET:
			bs_subarray_push($this->data['typeVisibility'], $type,
				['id' => 'key', 'defaultValue' => BS_DEFAULT_KEY_TELNET]
			);
			break;
		case ITEM_TYPE_JMX:
			bs_subarray_push($this->data['typeVisibility'], $type,
				['id' => 'key', 'defaultValue' => BS_DEFAULT_KEY_JMX]
			);
			break;
		default:
			bs_subarray_push($this->data['typeVisibility'], $type, ['id' => 'key', 'defaultValue' => '']);
	}
}
foreach ($this->data['types'] as $type => $label) {
	if ($type == ITEM_TYPE_TRAPPER || $type == ITEM_TYPE_BSM_ACTIVE || $type == ITEM_TYPE_SNMPTRAP) {
		continue;
	}
	bs_subarray_push($this->data['typeVisibility'], $type, 'row_flex_intervals');
	bs_subarray_push($this->data['typeVisibility'], $type, 'row_new_delay_flex');
	bs_subarray_push($this->data['typeVisibility'], $type, 'new_delay_flex[delay]');
	bs_subarray_push($this->data['typeVisibility'], $type, 'new_delay_flex[period]');
	bs_subarray_push($this->data['typeVisibility'], $type, 'add_delay_flex');
}
foreach ($this->data['types'] as $type => $label) {
	if ($type == ITEM_TYPE_TRAPPER || $type == ITEM_TYPE_SNMPTRAP) {
		continue;
	}
	bs_subarray_push($this->data['typeVisibility'], $type, 'delay');
	bs_subarray_push($this->data['typeVisibility'], $type, 'row_delay');
}

// disable dropdown items for calculated and aggregate items
foreach ([ITEM_TYPE_CALCULATED, ITEM_TYPE_AGGREGATE] as $type) {
	// set to disable character, log and text items in value type
	bs_subarray_push($this->data['typeDisable'], $type, [ITEM_VALUE_TYPE_STR, ITEM_VALUE_TYPE_LOG, ITEM_VALUE_TYPE_TEXT], 'value_type');

	// disable octal, hexadecimal and boolean items in data_type; Necessary for Numeric (unsigned) value type only
	bs_subarray_push($this->data['typeDisable'], $type, [ITEM_DATA_TYPE_OCTAL, ITEM_DATA_TYPE_HEXADECIMAL, ITEM_DATA_TYPE_BOOLEAN], 'data_type');
}

$this->data['securityLevelVisibility'] = [];
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV, 'snmpv3_authprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV, 'row_snmpv3_authprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV, 'snmpv3_authpassphrase');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV, 'row_snmpv3_authpassphrase');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'snmpv3_authprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'row_snmpv3_authprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'snmpv3_authpassphrase');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'row_snmpv3_authpassphrase');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'snmpv3_privprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'row_snmpv3_privprotocol');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'snmpv3_privpassphrase');
bs_subarray_push($this->data['securityLevelVisibility'], ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV, 'row_snmpv3_privpassphrase');

$this->data['authTypeVisibility'] = [];
bs_subarray_push($this->data['authTypeVisibility'], ITEM_AUTHTYPE_PUBLICKEY, 'publickey');
bs_subarray_push($this->data['authTypeVisibility'], ITEM_AUTHTYPE_PUBLICKEY, 'row_publickey');
bs_subarray_push($this->data['authTypeVisibility'], ITEM_AUTHTYPE_PUBLICKEY, 'privatekey');
bs_subarray_push($this->data['authTypeVisibility'], ITEM_AUTHTYPE_PUBLICKEY, 'row_privatekey');

?>

<script type="text/javascript">
	function setAuthTypeLabel() {
		if (jQuery('#authtype').val() == <?php echo CJs::encodeJson(ITEM_AUTHTYPE_PUBLICKEY); ?>
				&& jQuery('#type').val() == <?php echo CJs::encodeJson(ITEM_TYPE_SSH); ?>) {
			jQuery('#row_password label').html(<?php echo CJs::encodeJson(_('Key passphrase')); ?>);
		}
		else {
			jQuery('#row_password label').html(<?php echo CJs::encodeJson(_('Password')); ?>);
		}
	}

	jQuery(document).ready(function() {
		<?php
		if (!empty($this->data['authTypeVisibility'])) { ?>
			var authTypeSwitcher = new CViewSwitcher('authtype', 'change',
				<?php echo bs_jsvalue($this->data['authTypeVisibility'], true); ?>);
		<?php }
		if (!empty($this->data['typeVisibility'])) { ?>
			var typeSwitcher = new CViewSwitcher('type', 'change',
				<?php echo bs_jsvalue($this->data['typeVisibility'], true); ?>,
				<?php echo bs_jsvalue($this->data['typeDisable'], true); ?>);
		<?php }
		if (!empty($this->data['securityLevelVisibility'])) { ?>
			var securityLevelSwitcher = new CViewSwitcher('snmpv3_securitylevel', 'change',
				<?php echo bs_jsvalue($this->data['securityLevelVisibility'], true); ?>);
		<?php } ?>

		jQuery('#type')
			.change(function() {
				// update the interface select with each item type change
				var itemInterfaceTypes = <?php echo CJs::encodeJson(itemTypeInterface()); ?>;
				organizeInterfaces(itemInterfaceTypes[parseInt(jQuery(this).val())]);

				setAuthTypeLabel();
			})
			.trigger('change');

		jQuery('#authtype').bind('change', function() {
			setAuthTypeLabel();
		});
	});
</script>
