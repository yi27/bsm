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


require_once dirname(__FILE__).'/include/config.inc.php';

$page['title'] = _('Resource');
$page['file'] = 'popup_right.php';

define('BS_PAGE_NO_MENU', 1);

require_once dirname(__FILE__).'/include/page_header.php';

//	VAR					TYPE	OPTIONAL FLAGS	VALIDATION	EXCEPTION
$fields = [
	'dstfrm' =>		[T_BS_STR, O_MAND,P_SYS, NOT_EMPTY,	null],
	'permission' =>	[T_BS_INT, O_MAND,P_SYS, IN(PERM_DENY.','.PERM_READ.','.PERM_READ_WRITE), null]
];
check_fields($fields);

$dstfrm = getRequest('dstfrm', 0);
$permission = getRequest('permission', PERM_DENY);

/*
 * Display
 */

// host groups
$hostGroupForm = (new CForm())->setId('groups');

$hostGroupTable = (new CTableInfo())
	->setHeader([
		(new CColHeader(
			(new CCheckBox('all_groups'))->onClick('checkAll(this.checked)')
		))->addClass(BS_STYLE_CELL_WIDTH),
		_('Name')
	]);

$hostGroups = API::HostGroup()->get([
	'output' => ['groupid', 'name']
]);

order_result($hostGroups, 'name');

foreach ($hostGroups as $hostGroup) {
	$hostGroupTable->addRow([
		(new CCheckBox())
			->setAttribute('data-id', $hostGroup['groupid'])
			->setAttribute('data-name', $hostGroup['name'])
			->setAttribute('data-permission', $permission),
		$hostGroup['name']
	]);
}

$hostGroupTable->setFooter(
	(new CCol(
		(new CButton('select', _('Select')))->onClick('addGroups("'.$dstfrm.'")')
	))
);

$hostGroupForm->addItem($hostGroupTable);

(new CWidget())
	->setTitle(permission2str($permission))
	->addItem($hostGroupForm)
	->show();

?>
<script type="text/javascript">
	function addGroups(formName) {
		var parentDocument = window.opener.document;

		if (!parentDocument) {
			return close_window();
		}

		jQuery('#groups input[type=checkbox]').each(function() {
			var obj = jQuery(this);

			if (obj.attr('name') !== 'all_groups' && obj.prop('checked')) {
				var id = obj.data('id');

				add_variable('input', 'new_right[' + id + '][permission]', obj.data('permission'), formName,
					parentDocument);
				add_variable('input', 'new_right[' + id + '][name]', obj.data('name'), formName, parentDocument);
			}
		});

		parentDocument.forms[formName].submit();

		close_window();
	}

	function checkAll(value) {
		jQuery('#groups input[type=checkbox]').each(function() {
			jQuery(this).prop('checked', value);
		});
	}
</script>
<?php

require_once dirname(__FILE__).'/include/page_footer.php';
