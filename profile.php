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
require_once dirname(__FILE__).'/include/users.inc.php';
require_once dirname(__FILE__).'/include/forms.inc.php';
require_once dirname(__FILE__).'/include/media.inc.php';

$page['title'] = _('User profile');
$page['file'] = 'profile.php';
$page['scripts'] = ['class.cviewswitcher.js'];

ob_start();

if (CWebUser::isGuest() || !CWebUser::isLoggedIn()) {
	access_deny(ACCESS_DENY_PAGE);
}

require_once dirname(__FILE__).'/include/page_header.php';

$themes = array_keys(Z::getThemes());
$themes[] = THEME_DEFAULT;

//	VAR			TYPE	OPTIONAL FLAGS	VALIDATION	EXCEPTION
$fields = [
	'password1' =>			[T_BS_STR, O_OPT, null, null, 'isset({update}) && isset({form}) && ({form} != "update") && isset({change_password})'],
	'password2' =>			[T_BS_STR, O_OPT, null, null, 'isset({update}) && isset({form}) && ({form} != "update") && isset({change_password})'],
	'lang' =>				[T_BS_STR, O_OPT, null, null, null],
	'theme' =>				[T_BS_STR, O_OPT, null, IN('"'.implode('","', $themes).'"'), 'isset({update})'],
	'autologin' =>			[T_BS_INT, O_OPT, null, IN('1'), null],
	'autologout' =>	[T_BS_INT, O_OPT, null, BETWEEN(90, 10000), null, _('Auto-logout (min 90 seconds)')],
	'autologout_visible' =>	[T_BS_STR, O_OPT, null, IN('1'), null],
	'url' =>				[T_BS_STR, O_OPT, null, null, 'isset({update})'],
	'refresh' => [T_BS_INT, O_OPT, null, BETWEEN(0, SEC_PER_HOUR), 'isset({update})', _('Refresh (in seconds)')],
	'rows_per_page' => [T_BS_INT, O_OPT, null, BETWEEN(1, 999999), 'isset({update})', _('Rows per page')],
	'change_password' =>	[T_BS_STR, O_OPT, null, null, null],
	'user_medias' =>		[T_BS_STR, O_OPT, null, NOT_EMPTY, null],
	'user_medias_to_del' =>	[T_BS_STR, O_OPT, null, null, null],
	'new_media' =>			[T_BS_STR, O_OPT, null, null, null],
	'enable_media' =>		[T_BS_INT, O_OPT, null, null, null],
	'disable_media' =>		[T_BS_INT, O_OPT, null, null, null],
	'messages' =>			[T_BS_STR, O_OPT, null, null, null],
	// actions
	'update'=>				[T_BS_STR, O_OPT, P_SYS|P_ACT, null, null],
	'cancel'=>				[T_BS_STR, O_OPT, P_SYS, null, null],
	'del_user_media'=>		[T_BS_STR, O_OPT, P_SYS|P_ACT, null, null],
	// form
	'form'=>				[T_BS_STR, O_OPT, P_SYS, null, null],
	'form_refresh'=>		[T_BS_INT, O_OPT, null, null, null]
];
check_fields($fields);

$_REQUEST['autologin'] = getRequest('autologin', 0);

// secondary actions
if (isset($_REQUEST['new_media'])) {
	$_REQUEST['user_medias'] = getRequest('user_medias', []);
	array_push($_REQUEST['user_medias'], $_REQUEST['new_media']);
}
elseif (isset($_REQUEST['user_medias']) && isset($_REQUEST['enable_media'])) {
	if (isset($_REQUEST['user_medias'][$_REQUEST['enable_media']])) {
		$_REQUEST['user_medias'][$_REQUEST['enable_media']]['active'] = 0;
	}
}
elseif (isset($_REQUEST['user_medias']) && isset($_REQUEST['disable_media'])) {
	if (isset($_REQUEST['user_medias'][$_REQUEST['disable_media']])) {
		$_REQUEST['user_medias'][$_REQUEST['disable_media']]['active'] = 1;
	}
}
elseif (isset($_REQUEST['del_user_media'])) {
	$user_medias_to_del = getRequest('user_medias_to_del', []);
	foreach ($user_medias_to_del as $mediaid) {
		if (isset($_REQUEST['user_medias'][$mediaid])) {
			unset($_REQUEST['user_medias'][$mediaid]);
		}
	}
}
// primary actions
elseif (isset($_REQUEST['cancel'])) {
	ob_end_clean();
	redirect(BS_DEFAULT_URL);
}
elseif (hasRequest('update')) {
	$auth_type = getUserAuthenticationType(CWebUser::$data['userid']);

	if ($auth_type != BS_AUTH_INTERNAL) {
		$_REQUEST['password1'] = $_REQUEST['password2'] = null;
	}
	else {
		$_REQUEST['password1'] = getRequest('password1');
		$_REQUEST['password2'] = getRequest('password2');
	}

	if ($_REQUEST['password1'] != $_REQUEST['password2']) {
		show_error_message(_('Cannot update user. Both passwords must be equal.'));
	}
	elseif (isset($_REQUEST['password1']) && CWebUser::$data['alias'] == BS_GUEST_USER && !bs_empty($_REQUEST['password1'])) {
		show_error_message(_('For guest, password must be empty'));
	}
	elseif (isset($_REQUEST['password1']) && CWebUser::$data['alias'] != BS_GUEST_USER && bs_empty($_REQUEST['password1'])) {
		show_error_message(_('Password should not be empty'));
	}
	else {
		$user = [];
		$user['userid'] = CWebUser::$data['userid'];
		$user['alias'] = CWebUser::$data['alias'];
		$user['passwd'] = getRequest('password1');
		$user['url'] = getRequest('url');
		$user['autologin'] = getRequest('autologin', 0);
		$user['autologout'] = hasRequest('autologout_visible') ? getRequest('autologout') : 0;
		$user['theme'] = getRequest('theme');
		$user['refresh'] = getRequest('refresh');
		$user['rows_per_page'] = getRequest('rows_per_page');
		$user['user_groups'] = null;
		$user['user_medias'] = getRequest('user_medias', []);

		if (hasRequest('lang')) {
			$user['lang'] = getRequest('lang');
		}

		$messages = getRequest('messages', []);
		if (!isset($messages['enabled'])) {
			$messages['enabled'] = 0;
		}
		if (!isset($messages['triggers.recovery'])) {
			$messages['triggers.recovery'] = 0;
		}
		if (!isset($messages['triggers.severities'])) {
			$messages['triggers.severities'] = [];
		}

		DBstart();
		updateMessageSettings($messages);

		$result = API::User()->updateProfile($user);

		if ($result && CwebUser::$data['type'] > USER_TYPE_BSM_USER) {
			$result = API::User()->updateMedia([
				'users' => $user,
				'medias' => $user['user_medias']
			]);
		}

		$result = DBend($result);
		if (!$result) {
			error(API::User()->resetErrors());
		}

		if ($result) {
			DBstart();
			add_audit(AUDIT_ACTION_UPDATE, AUDIT_RESOURCE_USER,
				'User alias ['.CWebUser::$data['alias'].'] Name ['.CWebUser::$data['name'].']'.
				' Surname ['.CWebUser::$data['surname'].'] profile id ['.CWebUser::$data['userid'].']'
			);
			DBend(true);
			ob_end_clean();

			redirect(BS_DEFAULT_URL);
		}
		else {
			show_messages($result, _('User updated'), _('Cannot update user'));
		}
	}
}

ob_end_flush();

/*
 * Display
 */
$config = select_config();

$data = getUserFormData(CWebUser::$data['userid'], $config, true);
$data['userid'] = CWebUser::$data['userid'];
$data['form'] = getRequest('form');
$data['form_refresh'] = getRequest('form_refresh', 0);
$data['autologout'] = getRequest('autologout');

// render view
$usersView = new CView('administration.users.edit', $data);
$usersView->render();
$usersView->show();

require_once dirname(__FILE__).'/include/page_footer.php';
