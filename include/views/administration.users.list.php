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

$userGroupComboBox = (new CComboBox('filter_usrgrpid', $_REQUEST['filter_usrgrpid'], 'submit()'))
	->addItem(0, _('All'));

foreach ($this->data['userGroups'] as $userGroup) {
	$userGroupComboBox->addItem($userGroup['usrgrpid'], $userGroup['name']);
}

$widget = (new CWidget())
	->setTitle(_('Users'))
	->setControls((new CForm('get'))
		->cleanItems()
		->addItem((new CList())
			->addItem([_('User group'), SPACE, $userGroupComboBox])
			->addItem(new CSubmit('form', _('Create user')))
		)
	);

// create form
$usersForm = (new CForm())->setName('userForm');

// create users table
$usersTable = (new CTableInfo())
	->setHeader([
		(new CColHeader(
			(new CCheckBox('all_users'))->onClick("checkAll('".$usersForm->getName()."', 'all_users', 'group_userid');")
		))->addClass(BS_STYLE_CELL_WIDTH),
		make_sorting_header(_('Alias'), 'alias', $this->data['sort'], $this->data['sortorder']),
		make_sorting_header(_x('Name', 'user first name'), 'name', $this->data['sort'], $this->data['sortorder']),
		make_sorting_header(_('Surname'), 'surname', $this->data['sort'], $this->data['sortorder']),
		make_sorting_header(_('User type'), 'type', $this->data['sort'], $this->data['sortorder']),
		_('Groups'),
		_('Is online?'),
		_('Login'),
		_('Frontend access'),
		_('Debug mode'),
		_('Status')
	]);

foreach ($this->data['users'] as $user) {
	$userId = $user['userid'];
	$session = $this->data['usersSessions'][$userId];

	// online time
	if ($session['lastaccess']) {
		$online_time = ($user['autologout'] == 0 || BS_USER_ONLINE_TIME < $user['autologout'])
			? BS_USER_ONLINE_TIME
			: $user['autologout'];

		$online = ($session['status'] == BS_SESSION_ACTIVE && $user['users_status'] == GROUP_STATUS_ENABLED
				&& ($session['lastaccess'] + $online_time) >= time())
			? (new CCol(_('Yes').' ('.bs_date2str(DATE_TIME_FORMAT_SECONDS, $session['lastaccess']).')'))
				->addClass(BS_STYLE_GREEN)
			: (new CCol(_('No').' ('.bs_date2str(DATE_TIME_FORMAT_SECONDS, $session['lastaccess']).')'))
				->addClass(BS_STYLE_RED);
	}
	else {
		$online = (new CCol(_('No')))->addClass(BS_STYLE_RED);
	}

	// blocked
	$blocked = ($user['attempt_failed'] >= BS_LOGIN_ATTEMPTS)
		? (new CLink(_('Blocked'), 'users.php?action=user.massunblock&group_userid[]='.$userId))
			->addClass(BS_STYLE_LINK_ACTION)
			->addClass(BS_STYLE_RED)
			->addSID()
		: (new CSpan(_('Ok')))->addClass(BS_STYLE_GREEN);

	// user groups
	order_result($user['usrgrps'], 'name');

	$usersGroups = [];
	$i = 0;

	foreach ($user['usrgrps'] as $userGroup) {
		$i++;

		if ($i > $this->data['config']['max_in_table']) {
			$usersGroups[] = ' &hellip;';

			break;
		}

		if ($usersGroups) {
			$usersGroups[] = ', ';
		}

		$usersGroups[] = (new CLink(
			$userGroup['name'],
			'usergrps.php?form=update&usrgrpid='.$userGroup['usrgrpid']))
			->addClass($userGroup['gui_access'] == GROUP_GUI_ACCESS_DISABLED
					|| $userGroup['users_status'] == GROUP_STATUS_DISABLED
				? BS_STYLE_LINK_ALT . ' ' . BS_STYLE_RED
				: BS_STYLE_LINK_ALT . ' ' . BS_STYLE_GREEN);
	}

	// gui access style
	$guiAccessStyle = BS_STYLE_GREEN;
	if ($user['gui_access'] == GROUP_GUI_ACCESS_INTERNAL) {
		$guiAccessStyle = BS_STYLE_ORANGE;
	}
	if ($user['gui_access'] == GROUP_GUI_ACCESS_DISABLED) {
		$guiAccessStyle = BS_STYLE_GREY;
	}

	$alias = new CLink($user['alias'], 'users.php?form=update&userid='.$userId);

	// append user to table
	$usersTable->addRow([
		new CCheckBox('group_userid['.$userId.']', $userId),
		(new CCol($alias))->addClass(BS_STYLE_NOWRAP),
		$user['name'],
		$user['surname'],
		user_type2str($user['type']),
		$usersGroups,
		$online,
		$blocked,
		(new CSpan(user_auth_type2str($user['gui_access'])))->addClass($guiAccessStyle),
		($user['debug_mode'] == GROUP_DEBUG_MODE_ENABLED)
			? (new CSpan(_('Enabled')))->addClass(BS_STYLE_ORANGE)
			: (new CSpan(_('Disabled')))->addClass(BS_STYLE_GREEN),
		($user['users_status'] == GROUP_STATUS_DISABLED)
			? (new CSpan(_('Disabled')))->addClass(BS_STYLE_RED)
			: (new CSpan(_('Enabled')))->addClass(BS_STYLE_GREEN)
	]);
}

// append table to form
$usersForm->addItem([
	$usersTable,
	$this->data['paging'],
	new CActionButtonList('action', 'group_userid', [
		'user.massunblock' => ['name' => _('Unblock'), 'confirm' => _('Unblock selected users?')],
		'user.massdelete' => ['name' => _('Delete'), 'confirm' => _('Delete selected users?')]
	])
]);

// append form to widget
$widget->addItem($usersForm);

return $widget;
