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


define('BS_PAGE_NO_HEADER', 1);
define('BS_PAGE_NO_FOOTER', 1);

$request = CHtml::encode(getRequest('request', ''));
$message = CHtml::encode(getRequest('message', '')) ;
// remove debug code for login form message, trimming not in regex to relay only on [ ] in debug message.
$message = trim(preg_replace('/\[.*\]/', '', $message));

require_once dirname(__FILE__).'/../page_header.php';
$pageHeader->addCssFile('styles/'.CHtml::encode($bootstrap).'.css');


$error = ($message !== '') ? (new CDiv($message))->addClass(BS_STYLE_RED) : null;
$guest = (CWebUser::$data['userid'] > 0)
	? (new CListItem([' ', new CLink('', BS_DEFAULT_URL)]))
		->addClass(BS_STYLE_SIGN_IN_TXT)
	: null;

global $BS_SERVER_NAME;

(new CDiv([
	(isset($BS_SERVER_NAME) && $BS_SERVER_NAME !== '')
		? (new CDiv($BS_SERVER_NAME))->addClass(BS_STYLE_SERVER_NAME)
		: null,

	(new CDiv([
		(new CDiv([
			(new CDiv())->addClass(BS_STYLE_SIGNIN_OVER_H1)
		]))->addClass(BS_STYLE_SIGNIN_OVER),
		(new CDiv([
			(new CDiv())->addClass(BS_STYLE_SIGNIN_LEFT)->addClass('col-md-7'),

			(new CDiv([
				(new CDiv([

					(new CForm())
						->addItem(
							(new CList())
								->addItem([
									new CLabel(_('用户名'), 'name'),
									(new CTextBox('name'))->setAttribute('autofocus', 'autofocus'),
									$error
								])
								->addItem([new CLabel(_('密码'), 'password'), (new CTextBox('password'))->setType('password')])
								->addItem(
									new CLabel([
										(new CCheckBox('autologin'))->setChecked(getRequest('autologin', 1) == 1),
										_('记住我')
									], 'autologin')
								)
								->addItem(new CSubmit('enter', _('登陆')))
								->addItem($guest)
						)
				]))->addClass(BS_STYLE_SIGNIN_INNER_CONTAINER)
			]))->addClass(BS_STYLE_SIGNIN_CONTAINER)->addClass('col-md-4')
		]))->addClass(BS_STYLE_SIGNIN_DOWN)->addClass('col-md-12')



	]))->addClass(BS_STYLE_SIGNIN_BLACKGROUND)
]))


	->addClass('container')
	->show();

makePageFooter(false)->show();
?>

</body>

