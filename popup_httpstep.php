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
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
**/


require_once dirname(__FILE__).'/include/config.inc.php';

$page['title'] = _('Step of web scenario');
$page['file'] = 'popup_httpstep.php';

define('BS_PAGE_NO_MENU', 1);

require_once dirname(__FILE__).'/include/page_header.php';

// VAR	TYPE	OPTIONAL	FLAGS	VALIDATION	EXCEPTION
$fields = [
	'dstfrm' =>			[T_BS_STR, O_MAND, P_SYS,	NOT_EMPTY,			null],
	'stepid' =>			[T_BS_INT, O_OPT, P_SYS,	BETWEEN(0,65535),	null],
	'list_name' =>		[T_BS_STR, O_OPT, P_SYS,	NOT_EMPTY,			'(isset({add}) || isset({update})) && isset({stepid})'],
	'name' =>			[T_BS_STR, O_OPT, null,	NOT_EMPTY,			'isset({add}) || isset({update})', _('Name')],
	'url' =>			[T_BS_STR, O_OPT, null,	NOT_EMPTY,			'isset({add}) || isset({update})', _('URL')],
	'posts' =>			[T_BS_STR, O_OPT, null,	null,				null],
	'variables' =>		[T_BS_STR, O_OPT, null,	null,				'isset({add}) || isset({update})'],
	'headers' =>		[T_BS_STR, O_OPT, null,	null,				'isset({add}) || isset({update})'],
	'retrieve_mode' =>	[T_BS_STR, O_OPT, null,	null,				null],
	'follow_redirects' => [T_BS_STR, O_OPT, null,	null,				null],
	'timeout' =>		[T_BS_INT, O_OPT, null,	BETWEEN(0,65535),	'isset({add}) || isset({update})', _('Timeout')],
	'required' =>		[T_BS_STR, O_OPT, null,	null,				null],
	'status_codes' =>	[T_BS_STR, O_OPT, null,	null,				'isset({add}) || isset({update})'],
	'templated' =>		[T_BS_STR, O_OPT, null, 	null, null],
	'old_name'=>		[T_BS_STR, O_OPT, null, 	null, null],
	'steps_names'=>		[T_BS_STR, O_OPT, null, 	null, null],
	// actions
	'add' =>			[T_BS_STR, O_OPT, P_SYS|P_ACT, null,			null],
	'update' =>			[T_BS_STR, O_OPT, P_SYS|P_ACT, null,			null],
	'form' =>			[T_BS_STR, O_OPT, P_SYS,	null,				null],
	'form_refresh' =>	[T_BS_INT, O_OPT, null,	null,				null]
];
check_fields($fields);


// render view
$httpPopupView = new CView('configuration.httpconf.popup');
$httpPopupView->render();
$httpPopupView->show();

require_once dirname(__FILE__).'/include/page_footer.php';
