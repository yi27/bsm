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


require_once 'include/menu.inc.php';

function local_generateHeader($data) {
	// only needed for bs_construct_menu
	global $page;

	header('Content-Type: text/html; charset=UTF-8');

	// construct menu
	$main_menu = [];
	$sub_menus = [];

	bs_construct_menu($main_menu, $sub_menus, $page, $data['controller']['action']);

	$pageHeader = new CView('layout.htmlpage.header', [
		'javascript' => [
			'files' => $data['javascript']['files']
		],
		'page' => [
			'title' => $data['page']['title']
		],
		'user' => [
			'lang' => CWebUser::$data['lang'],
			'theme' => CWebUser::$data['theme']
		]
	]);
	echo $pageHeader->getOutput();

	if ($data['fullscreen'] == 0) {
		global $BS_SERVER_NAME;

		$pageMenu = new CView('layout.htmlpage.menu', [
			'server_name' => isset($BS_SERVER_NAME) ? $BS_SERVER_NAME : '',
			'menu' => [
				'main_menu' => $main_menu,
				'sub_menus' => $sub_menus,
				'selected' => $page['menu']
			],
			'user' => [
				'is_guest' => CWebUser::isGuest(),
				'alias' => CWebUser::$data['alias'],
				'name' => CWebUser::$data['name'],
				'surname' => CWebUser::$data['surname']
			]
		]);
		echo $pageMenu->getOutput();
	}

	echo '<div class="'.BS_STYLE_ARTICLE.'">';

	// should be replaced with addPostJS() at some point
	bs_add_post_js('initMessages({});');

	// if a user logs in after several unsuccessful attempts, display a warning
	if ($failedAttempts = CProfile::get('web.login.attempt.failed', 0)) {
		$attempt_ip = CProfile::get('web.login.attempt.ip', '');
		$attempt_date = CProfile::get('web.login.attempt.clock', 0);

		$error_msg = _n('%4$s failed login attempt logged. Last failed attempt was from %1$s on %2$s at %3$s.',
			'%4$s failed login attempts logged. Last failed attempt was from %1$s on %2$s at %3$s.',
			$attempt_ip,
			bs_date2str(DATE_FORMAT, $attempt_date),
			bs_date2str(TIME_FORMAT, $attempt_date),
			$failedAttempts
		);
		error($error_msg);

		CProfile::update('web.login.attempt.failed', 0, PROFILE_TYPE_INT);
	}

	show_messages();
}

function local_generateFooter($fullscreen) {
	$pageFooter = new CView('layout.htmlpage.footer', [
		'fullscreen' => $fullscreen,
		'user' => [
			'alias' => CWebUser::$data['alias'],
			'debug_mode' => CWebUser::$data['debug_mode']
		]
	]);
	echo $pageFooter->getOutput();
}

function local_showMessage() {
	global $BS_MESSAGES;

	if (CSession::keyExists('messageOk') || CSession::keyExists('messageError')) {
		if (CSession::keyExists('messages')) {
			$BS_MESSAGES = CSession::getValue('messages');
			CSession::unsetValue(['messages']);
		}

		if (CSession::keyExists('messageOk')) {
			show_messages(true, CSession::getValue('messageOk'));
		}
		else {
			show_messages(false, null, CSession::getValue('messageError'));
		}

		CSession::unsetValue(['messageOk', 'messageError']);
	}
}

local_generateHeader($data);
local_showMessage();
echo $data['javascript']['pre'];
echo $data['main_block'];
echo $data['javascript']['post'];

local_generateFooter($data['fullscreen']);

show_messages();

echo '</body></html>';
