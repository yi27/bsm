<script type="text/x-jquery-tmpl" id="dcheckRowTPL">
	<?= (new CRow([
			(new CCol(
				new CSpan('#{name}')
			))->setId('dcheckCell_#{dcheckid}'),
			new CHorList([
				(new CButton(null, _('Edit')))
					->addClass(BS_STYLE_BTN_LINK)
					->onClick("javascript: showNewCheckForm(null, '#{dcheckid}');"),
				(new CButton(null, _('Remove')))
					->addClass(BS_STYLE_BTN_LINK)
					->onClick("javascript: removeDCheckRow('#{dcheckid}');")
			])
		]))
			->setId('dcheckRow_#{dcheckid}')
			->toString()
	?>
</script>
<script; type="text/x-jquery-tmpl"; id="uniqRowTPL">
	<?=	(new CListItem(
			(new CLabel(
				[
					(new CInput('radio', 'uniqueness_criteria', '#{dcheckid}'))
						->setId('uniqueness_criteria_#{dcheckid}'),
					'#{name}'
				],
				'uniqueness_criteria_#{dcheckid}'
			))
		))
			->setId('uniqueness_criteria_row_#{dcheckid}')
			->toString()
	?>
</script>
<script; type="text/x-jquery-tmpl"; id="newDCheckTPL">
	<div; id="new_check_form">
		<div; class="<?= BS_STYLE_TABLE_FORMS_SEPARATOR ?>">
			<table>
				<tbody>
				<tr>
					<td><label; for="type"><?= _('Check type') ?></label></td>
					<td><select id="type" name="type"></select></td>
				</tr>
				<tr; id="newCheckPortsRow">
					<td><label; for="ports"><?= _('Port range') ?></label></td>
					<td>
						<input type="text" id="ports"; name="ports"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="255">
					</td>
				</tr>
				<tr; id="newCheckCommunityRow">
					<td><label; for="snmp_community"><?= _('SNMP community') ?></label></td>
					<td><input type="text" id="snmp_community"; name="snmp_community"; value="";
							style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="255"></td>
				</tr>
				<tr; id="newCheckKeyRow">
					<td><label; for="key_"><?= _('SNMP Key') ?></label></td>
					<td>
						<input type="text" id="key_"; name="key_"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="255">
					</td>
				</tr>
				<tr; id="newCheckContextRow">
					<td><label; for="snmpv3_contextname"><?= _('Context name') ?></label></td>
					<td>
						<input type="text" id="snmpv3_contextname"; name="snmpv3_contextname"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="255">
					</td>
				</tr>
				<tr; id="newCheckSecNameRow">
					<td><label; for="snmpv3_securityname"><?= _('Security name') ?></label></td>
					<td><input type="text" id="snmpv3_securityname"; name="snmpv3_securityname"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="64"></td>
				</tr>
				<tr; id="newCheckSecLevRow">
					<td><label; for="snmpv3_securitylevel"><?= _('Security level') ?></label></td>
					<td>
						<select id="snmpv3_securitylevel" name="snmpv3_securitylevel">
							<option; value="0"><?= 'noAuthNoPriv' ?> </option>
							<option; value="1"><?= 'authNoPriv' ?> </option>
							<option; value="2"><?= 'authPriv' ?> </option>
						</select>
					</td>
				</tr>
				<?= (new CRow([
						_('Authentication protocol'),
						(new CRadioButtonList('snmpv3_authprotocol', ITEM_AUTHPROTOCOL_MD5))
							->addValue(_('MD5'), ITEM_AUTHPROTOCOL_MD5, 'snmpv3_authprotocol_'.ITEM_AUTHPROTOCOL_MD5)
							->addValue(_('SHA'), ITEM_AUTHPROTOCOL_SHA, 'snmpv3_authprotocol_'.ITEM_AUTHPROTOCOL_SHA)
							->setModern(true)
					]))
						->setId('newCheckAuthProtocolRow')
						->toString()
				?>
				<tr; id="newCheckAuthPassRow">
					<td><label; for="snmpv3_authpassphrase"><?= _('Authentication passphrase') ?></label></td>
					<td><input type="text" id="snmpv3_authpassphrase"; name="snmpv3_authpassphrase"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="64"></td>
				</tr>
				<?= (new CRow([
						_('Privacy protocol'),
						(new CRadioButtonList('snmpv3_privprotocol', ITEM_PRIVPROTOCOL_DES))
							->addValue(_('DES'), ITEM_PRIVPROTOCOL_DES, 'snmpv3_privprotocol_'.ITEM_PRIVPROTOCOL_DES)
							->addValue(_('AES'), ITEM_PRIVPROTOCOL_AES, 'snmpv3_privprotocol_'.ITEM_PRIVPROTOCOL_AES)
							->setModern(true)
					]))
						->setId('newCheckPrivProtocolRow')
						->toString()
				?>
				<tr; id="newCheckPrivPassRow">
					<td><label; for="snmpv3_privpassphrase"><?= _('Privacy passphrase') ?></label></td>
					<td><input type="text" id="snmpv3_privpassphrase"; name="snmpv3_privpassphrase"; value=""; style="width: <?= BS_TEXTAREA_STANDARD_WIDTH ?>px"; maxlength="64"></td>
				</tr>
				</tbody>
			</table>
			<?= (new CHorList([
					(new CButton('add_new_dcheck', _('Add')))->addClass(BS_STYLE_BTN_LINK),
					(new CButton('cancel_new_dcheck', _('Cancel')))->addClass(BS_STYLE_BTN_LINK)
				]))->toString()
			?>
		</div>
	</div>
</script>
<script; type="text/javascript">;
	var BS_SVC = {
		ssh: <?= SVC_SSH ?>,
		ldap: <?= SVC_LDAP ?>,
		smtp: <?= SVC_SMTP ?>,
		ftp: <?= SVC_FTP ?>,
		http: <?= SVC_HTTP ?>,
		pop: <?= SVC_POP ?>,
		nntp: <?= SVC_NNTP ?>,
		imap: <?= SVC_IMAP ?>,
		tcp: <?= SVC_TCP ?>,
		agent: <?= SVC_AGENT ?>,
		snmpv1: <?= SVC_SNMPv1 ?>,
		snmpv2: <?= SVC_SNMPv2c ?>,
		snmpv3: <?= SVC_SNMPv3 ?>,
		icmp: <?= SVC_ICMPPING ?>,
		https: <?= SVC_HTTPS ?>,
		telnet: <?= SVC_TELNET ?>
	};

	var BS_CHECKLIST = {};

	function discoveryCheckDefaultPort(service) {
		var defPorts = {};
		defPorts[BS_SVC.ssh] = '22';
		defPorts[BS_SVC.ldap] = '389';
		defPorts[BS_SVC.smtp] = '25';
		defPorts[BS_SVC.ftp] = '21';
		defPorts[BS_SVC.http] = '80';
		defPorts[BS_SVC.pop] = '110';
		defPorts[BS_SVC.nntp] = '119';
		defPorts[BS_SVC.imap] = '143';
		defPorts[BS_SVC.tcp] = '0';
		defPorts[BS_SVC.icmp] = '0';
		defPorts[BS_SVC.agent] = '10050';
		defPorts[BS_SVC.snmpv1] = '161';
		defPorts[BS_SVC.snmpv2] = '161';
		defPorts[BS_SVC.snmpv3] = '161';
		defPorts[BS_SVC.https] = '443';
		defPorts[BS_SVC.telnet] = '23';

		service = service.toString();

		return isset(service, defPorts) ? defPorts[service] : 0;
	}

	function discoveryCheckTypeToString(svcPort) {
		var defPorts = {};
		defPorts[BS_SVC.ftp] = <?= CJs::encodeJson(_('FTP')) ?>;
		defPorts[BS_SVC.http] = <?= CJs::encodeJson(_('HTTP')) ?>;
		defPorts[BS_SVC.https] = <?= CJs::encodeJson(_('HTTPS')) ?>;
		defPorts[BS_SVC.icmp] = <?= CJs::encodeJson(_('ICMP ping')) ?>;
		defPorts[BS_SVC.imap] = <?= CJs::encodeJson(_('IMAP')) ?>;
		defPorts[BS_SVC.tcp] = <?= CJs::encodeJson(_('TCP')) ?>;
		defPorts[BS_SVC.ldap] = <?= CJs::encodeJson(_('LDAP')) ?>;
		defPorts[BS_SVC.nntp] = <?= CJs::encodeJson(_('NNTP')) ?>;
		defPorts[BS_SVC.pop] = <?= CJs::encodeJson(_('POP')) ?>;
		defPorts[BS_SVC.snmpv1] = <?= CJs::encodeJson(_('SNMPv1 agent')) ?>;
		defPorts[BS_SVC.snmpv2] = <?= CJs::encodeJson(_('SNMPv2 agent')) ?>;
		defPorts[BS_SVC.snmpv3] = <?= CJs::encodeJson(_('SNMPv3 agent')) ?>;
		defPorts[BS_SVC.smtp] = <?= CJs::encodeJson(_('SMTP')) ?>;
		defPorts[BS_SVC.ssh] = <?= CJs::encodeJson(_('SSH')) ?>;
		defPorts[BS_SVC.telnet] = <?= CJs::encodeJson(_('Telnet')) ?>;
		defPorts[BS_SVC.agent] = <?= CJs::encodeJson(_('Bsm agent')) ?>;

		if (typeof svcPort === 'undefined') {
			return defPorts;
		}

		svcPort = parseInt(svcPort, 10);

		return isset(svcPort, defPorts) ? defPorts[svcPort] : <?= CJs::encodeJson(_('Unknown')) ?>;
	}

	function toggleInputs(id, state) {
		jQuery('#' + id).toggle(state);

		if (state) {
			jQuery('#' + id + ' :input').prop('disabled', false);
		}
		else {
			jQuery('#' + id + ' :input').prop('disabled', true);
		}
	}

	/**
	 * @see init.js add.popup event
	 */
	function addPopupValues(list) {
		// templates
		var dcheckRowTpl = new Template(jQuery('#dcheckRowTPL').html()),
			uniqRowTpl = new Template(jQuery('#uniqRowTPL').html());

		for (var i = 0; i < list.length; i++) {
			if (empty(list[i])) {
				continue;
			}

			var value = list[i];

			if (typeof value.dcheckid === 'undefined') {
				value.dcheckid = getUniqueId();
			}

			// add
			if (typeof BS_CHECKLIST[value.dcheckid] === 'undefined') {
				BS_CHECKLIST[value.dcheckid] = value;

				jQuery('#dcheckListFooter').before(dcheckRowTpl.evaluate(value));

				for (var fieldName in value) {
					if (typeof value[fieldName] === 'string') {
						var input = jQuery('<input>', {
							name: 'dchecks[' + value.dcheckid + '][' + fieldName + ']',
							type: 'hidden',
							value: value[fieldName]
						});

						jQuery('#dcheckCell_' + value.dcheckid).append(input);
					}
				}
			}

			// update
			else {
				BS_CHECKLIST[value.dcheckid] = value;

				var ignoreNames = ['druleid', 'dcheckid', 'name', 'ports', 'type', 'uniq'];

				// clean values
				jQuery('#dcheckCell_' + value.dcheckid + ' input').each(function(i, item) {
					var itemObj = jQuery(item);

					var name = itemObj.attr('name').replace('dchecks[' + value.dcheckid + '][', '');
					name = name.substring(0, name.length - 1);

					if (jQuery.inArray(name, ignoreNames) == -1) {
						itemObj.remove();
					}
				});

				// set values
				for (var fieldName in value) {
					if (typeof value[fieldName] === 'string') {
						var obj = jQuery('input[name="dchecks[' + value.dcheckid + '][' + fieldName + ']"]');

						if (obj.length) {
							obj.val(value[fieldName]);
						}
						else {
							var input = jQuery('<input>', {
								name: 'dchecks[' + value.dcheckid + '][' + fieldName + ']',
								type: 'hidden',
								value: value[fieldName]
							});

							jQuery('#dcheckCell_' + value.dcheckid).append(input);
						}
					}
				}

				// update check name
				jQuery('#dcheckCell_' + value.dcheckid + ' span').text(value['name']);
			}

			// update device uniqueness criteria
			var availableDeviceTypes = [BS_SVC.agent, BS_SVC.snmpv1, BS_SVC.snmpv2, BS_SVC.snmpv3],
				uniquenessCriteria = jQuery('#uniqueness_criteria_row_' + value.dcheckid);

			if (jQuery.inArray(parseInt(value.type, 10), availableDeviceTypes) > -1) {
				var new_uniqueness_criteria = uniqRowTpl.evaluate(value);
				if (uniquenessCriteria.length) {
					var checked_id = jQuery('input:radio[name=uniqueness_criteria]:checked').attr('id');
					uniquenessCriteria.replaceWith(new_uniqueness_criteria);
					jQuery('#' + checked_id).prop('checked', true);
				}
				else {
					jQuery('#uniqueness_criteria').append(new_uniqueness_criteria);
				}
			}
			else {
				if (uniquenessCriteria.length) {
					uniquenessCriteria.remove();

					selectUniquenessCriteriaDefault();
				}
			}
		}
	}

	function removeDCheckRow(dcheckid) {
		jQuery('#dcheckRow_' + dcheckid).remove();

		delete(BS_CHECKLIST[dcheckid]);

		// remove uniqueness criteria
		var obj = jQuery('#uniqueness_criteria_' + dcheckid);

		if (obj.length) {
			if (obj.is(':checked')) {
				selectUniquenessCriteriaDefault();
			}

			jQuery('#uniqueness_criteria_row_' + dcheckid).remove();
		}
	}

	function showNewCheckForm(e, dcheckId) {
		var isUpdate = (typeof dcheckId !== 'undefined');

		// remove existing form
		jQuery('#new_check_form').remove();

		if (jQuery('#new_check_form').length == 0) {
			var tpl = new Template(jQuery('#newDCheckTPL').html());

			jQuery('#dcheckList').after(tpl.evaluate());

			// display fields dependent from type
			jQuery('#type').change(function() {
				updateNewDCheckType(dcheckId);
			});

			// display addition snmpv3 security level fields dependent from snmpv3 security level
			jQuery('#snmpv3_securitylevel').change(updateNewDCheckSNMPType);

			// button "add"
			jQuery('#add_new_dcheck').click(function() {
				saveNewDCheckForm(dcheckId);
			});

			// rename button to "update"
			if (isUpdate) {
				jQuery('#add_new_dcheck').text(<?= CJs::encodeJson(_('Update')) ?>);
			}

			// button "remove" form
			jQuery('#cancel_new_dcheck').click(function() {
				jQuery('#new_check_form').remove();
			});

			// port name sorting
			var svcPorts = discoveryCheckTypeToString(),
				portNameSvcValue = {},
				portNameOrder = [];

			for (var key in svcPorts) {
				portNameOrder.push(svcPorts[key]);
				portNameSvcValue[svcPorts[key]] = key;
			}

			portNameOrder.sort();

			for (var i = 0; i < portNameOrder.length; i++) {
				var portName = portNameOrder[i];

				jQuery('#type').append(jQuery('<option>', {
					value: portNameSvcValue[portName],
					text: portName
				}));
			}
		}

		// restore form values
		if (isUpdate) {
			jQuery('#dcheckCell_' + dcheckId + ' input').each(function(i, item) {
				var itemObj = jQuery(item);

				var name = itemObj.attr('name').replace('dchecks[' + dcheckId + '][', '');
				name = name.substring(0, name.length - 1);

				// ignore "name" value because it is virtual
				if (name !== 'name') {
					jQuery('#' + name).val(itemObj.val());

					// set radio button value
					var radioObj = jQuery('input[name=' + name + ']');

					if (radioObj.attr('type') == 'radio') {
						radioObj.prop('checked', false);

						jQuery('#' + name + '_' + itemObj.val()).prop('checked', true);
					}
				}
			});
		}

		updateNewDCheckType(dcheckId);
	}

	function updateNewDCheckType(dcheckId) {
		var dcheckType = parseInt(jQuery('#type').val(), 10);

		var keyRowTypes = {};
		keyRowTypes[BS_SVC.agent] = true;
		keyRowTypes[BS_SVC.snmpv1] = true;
		keyRowTypes[BS_SVC.snmpv2] = true;
		keyRowTypes[BS_SVC.snmpv3] = true;

		var comRowTypes = {};
		comRowTypes[BS_SVC.snmpv1] = true;
		comRowTypes[BS_SVC.snmpv2] = true;

		var secNameRowTypes = {};
		secNameRowTypes[BS_SVC.snmpv3] = true;

		toggleInputs('newCheckPortsRow', (BS_SVC.icmp != dcheckType));
		toggleInputs('newCheckKeyRow', isset(dcheckType, keyRowTypes));

		if (isset(dcheckType, keyRowTypes)) {
			var caption = (dcheckType == BS_SVC.agent)
				? <?= CJs::encodeJson(_('Key')) ?>
				: <?= CJs::encodeJson(_('SNMP OID')) ?>;

			jQuery('#newCheckKeyRow label').text(caption);
		}

		toggleInputs('newCheckCommunityRow', isset(dcheckType, comRowTypes));
		toggleInputs('newCheckSecNameRow', isset(dcheckType, secNameRowTypes));
		toggleInputs('newCheckSecLevRow', isset(dcheckType, secNameRowTypes));
		toggleInputs('newCheckContextRow', isset(dcheckType, secNameRowTypes));

		// get old type
		var oldType = jQuery('#type').data('oldType');

		jQuery('#type').data('oldType', dcheckType);

		// type is changed
		if (BS_SVC.icmp != dcheckType && typeof oldType !== 'undefined' && dcheckType != oldType) {
			// reset values
			var snmpTypes = [BS_SVC.snmpv1, BS_SVC.snmpv2, BS_SVC.snmpv3],
				ignoreNames = ['druleid', 'name', 'ports', 'type'];

			if (jQuery.inArray(dcheckType, snmpTypes) !== -1 && jQuery.inArray(oldType, snmpTypes) !== -1) {
				// ignore value reset when changing type from snmp's
			}
			else {
				jQuery('#new_check_form input[type="text"]').each(function(i, item) {
					var itemObj = jQuery(item);

					if (jQuery.inArray(itemObj.attr('id'), ignoreNames) < 0) {
						itemObj.val('');
					}
				});

				// reset port to default
				jQuery('#ports').val(discoveryCheckDefaultPort(dcheckType));
			}
		}

		// set default port
		if (jQuery('#ports').val() == '') {
			jQuery('#ports').val(discoveryCheckDefaultPort(dcheckType));
		}

		updateNewDCheckSNMPType();
	}

	function updateNewDCheckSNMPType() {
		var dcheckType = parseInt(jQuery('#type').val(), 10),
			dcheckSecLevType = parseInt(jQuery('#snmpv3_securitylevel').val(), 10);

		var secNameRowTypes = {};
		secNameRowTypes[BS_SVC.snmpv3] = true;

		var showAuthProtocol = (isset(dcheckType, secNameRowTypes)
			&& (dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV ?>
				|| dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV ?>));
		var showAuthPass = (isset(dcheckType, secNameRowTypes)
			&& (dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHNOPRIV ?>
				|| dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV ?>));
		var showPrivProtocol = (isset(dcheckType, secNameRowTypes)
			&& dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV ?>);
		var showPrivPass = (isset(dcheckType, secNameRowTypes)
			&& dcheckSecLevType == <?= ITEM_SNMPV3_SECURITYLEVEL_AUTHPRIV ?>);

		toggleInputs('newCheckAuthProtocolRow', showAuthProtocol);
		toggleInputs('newCheckAuthPassRow', showAuthPass);
		toggleInputs('newCheckPrivProtocolRow', showPrivProtocol);
		toggleInputs('newCheckPrivPassRow', showPrivPass);
	}

	function saveNewDCheckForm(dcheckId) {
		var dCheck = jQuery('#new_check_form :input:enabled').serializeJSON();

		// get check id
		dCheck.dcheckid = (typeof dcheckId === 'undefined') ? getUniqueId() : dcheckId;

		// check for duplicates
		for (var bsDcheckId in BS_CHECKLIST) {
			if (typeof dcheckId === 'undefined' || (typeof dcheckId !== 'undefined') && dcheckId != bsDcheckId) {
				if ((typeof dCheck['key_'] === 'undefined' || BS_CHECKLIST[bsDcheckId]['key_'] === dCheck['key_'])
						&& (typeof dCheck['type'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['type'] === dCheck['type'])
						&& (typeof dCheck['ports'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['ports'] === dCheck['ports'])
						&& (typeof dCheck['snmp_community'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmp_community'] === dCheck['snmp_community'])
						&& (typeof dCheck['snmpv3_authprotocol'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_authprotocol'] === dCheck['snmpv3_authprotocol'])
						&& (typeof dCheck['snmpv3_authpassphrase'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_authpassphrase'] === dCheck['snmpv3_authpassphrase'])
						&& (typeof dCheck['snmpv3_privprotocol'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_privprotocol'] === dCheck['snmpv3_privprotocol'])
						&& (typeof dCheck['snmpv3_privpassphrase'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_privpassphrase'] === dCheck['snmpv3_privpassphrase'])
						&& (typeof dCheck['snmpv3_securitylevel'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_securitylevel'] === dCheck['snmpv3_securitylevel'])
						&& (typeof dCheck['snmpv3_securityname'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_securityname'] === dCheck['snmpv3_securityname'])
						&& (typeof dCheck['snmpv3_contextname'] === 'undefined'
							|| BS_CHECKLIST[bsDcheckId]['snmpv3_contextname'] === dCheck['snmpv3_contextname'])) {

					overlayDialogue({
						'title': '<?= _('Discovery check error') ?>',
						'content': jQuery('<span>').text('<?= _('Check already exists.') ?>'),
						'buttons': [
							{
								'title': '<?= _('Cancel') ?>',
								'cancel': true,
								'focused': true,
								'action': function() {}
							}
						]
					});

					return null;
				}
			}
		}

		// validate
		var validationErrors = [],
			ajaxChecks = {
				ajaxaction: 'validate',
				ajaxdata: []
			};

		switch (parseInt(dCheck.type, 10)) {
			case BS_SVC.agent:
				ajaxChecks.ajaxdata.push({
					field: 'itemKey',
					value: dCheck.key_
				});
				break;
			case BS_SVC.snmpv1:
			case BS_SVC.snmpv2:
				if (dCheck.snmp_community == '') {
					validationErrors.push(<?= CJs::encodeJson(_('Incorrect SNMP community.')) ?>);
				}
			case BS_SVC.snmpv3:
				if (dCheck.key_ == '') {
					validationErrors.push(<?= CJs::encodeJson(_('Incorrect SNMP OID.')) ?>);
				}
				break;
		}

		if (dCheck.type != BS_SVC.icmp) {
			ajaxChecks.ajaxdata.push({
				field: 'port',
				value: dCheck.ports
			});
		}

		var jqxhr;

		if (ajaxChecks.ajaxdata.length > 0) {
			jQuery('#add_new_dcheck').prop('disabled', true);

			var url = new Curl();
			jqxhr = jQuery.ajax({
				url: url.getPath() + '?output=ajax&sid=' + url.getArgument('sid'),
				data: ajaxChecks,
				dataType: 'json',
				success: function(result) {
					if (!result.result) {
						jQuery.each(result.errors, function(i, val) {
							validationErrors.push(val.error);
						});
					}
				},
				error: function() {
					overlayDialogue({
						'title': '<?= _('Discovery check error') ?>',
						'content': jQuery('<span>').text('<?= _('Cannot validate discovery check: invalid request or connection to Bsm server failed.') ?>'),
						'buttons': [
							{
								'title': '<?= _('Cancel') ?>',
								'cancel': true,
								'focused': true,
								'action': function() {}
							}
						]
					});
					jQuery('#add_new_dcheck').prop('disabled', false);
				}
			});
		}

		jQuery.when(jqxhr).done(function() {
			jQuery('#add_new_dcheck').prop('disabled', false);

			if (validationErrors.length) {
				var content = jQuery('<span>');

				for (var i = 0; i < validationErrors.length; i++) {
					if (content.html() !== '') {
						content.append(jQuery('<br>'));
					}
					content.append(jQuery('<span>').text(validationErrors[i]));
				}

				overlayDialogue({
					'title': '<?= _('Discovery check error') ?>',
					'content': content,
					'buttons': [
						{
							'title': '<?= _('Cancel') ?>',
							'cancel': true,
							'focused': true,
							'action': function() {}
						}
					]
				});
			}
			else {
				dCheck.name = jQuery('#type :selected').text();

				if (typeof dCheck.ports !== 'undefined' && dCheck.ports != discoveryCheckDefaultPort(dCheck.type)) {
					dCheck.name += ' (' + dCheck.ports + ')';
				}
				if (dCheck.key_) {
					dCheck.name += ' "' + dCheck.key_ + '"';
				}

				addPopupValues([dCheck]);

				jQuery('#new_check_form').remove();
			}
		});
	}

	function selectUniquenessCriteriaDefault() {
		jQuery('#uniqueness_criteria_ip').prop('checked', true);
	}

	jQuery(document).ready(function() {
		addPopupValues(<?= bs_jsvalue(array_values($this->data['drule']['dchecks'])) ?>);

		jQuery("input:radio[name='uniqueness_criteria'][value=<?= bs_jsvalue($this->data['drule']['uniqueness_criteria']) ?>]").attr('checked', 'checked');

		jQuery('#newCheck').click(showNewCheckForm);
		jQuery('#clone').click(function() {
			jQuery('#update')
				.text(<?= CJs::encodeJson(_('Add')) ?>)
				.attr({id: 'add', name: 'add'});
			jQuery('#druleid, #delete, #clone').remove();
			jQuery('#form').val('clone');
			jQuery('#name').focus();
		});
	});

	(function($) {
		$.fn.serializeJSON = function() {
			var json = {};

			jQuery.map($(this).serializeArray(), function(n, i) {
				json[n['name']] = n['value'];
			});

			return json;
		};
	})(jQuery);
</script>
