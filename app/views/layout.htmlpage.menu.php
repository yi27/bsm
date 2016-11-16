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

$icons = (new CList())
	->addClass(BS_STYLE_TOP_NAV_ICONS)
/*	->addItem(
		(new CForm('get', 'search.php'))
			->addItem([
				(new CTextBox('search', '', false, 255))
					->setAttribute('autocomplete', 'off')
					->addClass(BS_STYLE_SEARCH),
				(new CSubmitButton(SPACE))->addClass(BS_STYLE_BTN_SEARCH)
			])
	)*/;

if (!$data['user']['is_guest']) {
	$icons->addItem(
		(new CLink(SPACE, 'profile.php'))
			->addClass(BS_STYLE_TOP_NAV_PROFILE)
			->setAttribute('title', getUserFullname($data['user']))
	);
}

$icons->addItem(
	(new CLink(SPACE, 'index.php?reconnect=1'))
		->addClass(BS_STYLE_TOP_NAV_SIGNOUT)
		->setAttribute('title', _('Sign out'))
		->addSID()
);

// 1st level menu
$top_menu = (new CDiv())
	->addItem(new CLink((new CDiv())->addClass(BS_STYLE_LOGO), 'bsm.php?action=dashboard.view'))
	->addItem(
		(new CList($data['menu']['main_menu']))->addClass(BS_STYLE_TOP_NAV)
	)
	->addItem($icons)
	->addClass(BS_STYLE_TOP_NAV_CONTAINER)
	->setId('mmenu');

$sub_menu_div = (new CDiv())
	->addClass(BS_STYLE_TOP_SUBNAV_CONTAINER)
	->onMouseover('javascript: MMenu.submenu_mouseOver();')
	->onMouseout('javascript: MMenu.mouseOut();');

// 2nd level menu
foreach ($data['menu']['sub_menus'] as $label => $sub_menu) {
	$sub_menu_row = (new CList())
		->addClass(BS_STYLE_TOP_SUBNAV)
		->setId('sub_'.$label);

	foreach ($sub_menu as $id => $sub_page) {
		$url = new CUrl($sub_page['menu_url']);
		if ($sub_page['menu_action'] !== null) {
			$url->setArgument('action', $sub_page['menu_action']);
		}

		$url
			->setArgument('ddreset', 1)
			->removeArgument('sid');

		$sub_menu_item = new CLink($sub_page['menu_text'], $url->getUrl());
		if ($sub_page['selected']) {
			$sub_menu_item->addClass(BS_STYLE_SELECTED);
		}

		$sub_menu_row->addItem($sub_menu_item);
	}

	if ($data['menu']['selected'] === $label) {
		$sub_menu_row->setAttribute('style', 'display: block;');
		insert_js('MMenu.def_label = '.bs_jsvalue($label));
	}
	else {
		$sub_menu_row->setAttribute('style', 'display: none;');
	}
	$sub_menu_div->addItem($sub_menu_row);
}

if ($data['server_name'] !== '') {
	$sub_menu_div->addItem(
		(new CDiv($data['server_name']))->addClass(BS_STYLE_SERVER_NAME)
	);
}

(new CTag('header', true))
	->setAttribute('role', 'banner')
	->addItem(
		(new CDiv())
			->addItem($top_menu)
			->addItem($sub_menu_div)
			->addClass(BS_STYLE_NAV)
			->setAttribute('role', 'navigation')
	)
	->show();
