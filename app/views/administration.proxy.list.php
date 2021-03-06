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


if ($data['uncheck']) {
	uncheckTableRows();
}

$widget = (new CWidget())
	->setTitle(_('Proxies'))
	->setControls((new CForm())
		->cleanItems()
		->addItem((new CList())->addItem(new CRedirectButton(_('Create proxy'), 'bsm.php?action=proxy.edit')))
	);

// create form
$proxyForm = (new CForm('get'))->setName('proxyForm');

// create table
$proxyTable = (new CTableInfo())
	->setHeader([
		(new CColHeader(
			(new CCheckBox('all_hosts'))
				->onClick("checkAll('".$proxyForm->getName()."', 'all_hosts', 'proxyids');")
		))->addClass(BS_STYLE_CELL_WIDTH),
		make_sorting_header(_('Name'), 'host', $data['sort'], $data['sortorder']),
		_('Mode'),
		_('Encryption'),
		_('Last seen (age)'),
		_('Host count'),
		_('Item count'),
		_('Required performance (vps)'),
		_('Hosts')
	]);

foreach ($data['proxies'] as $proxy) {
	$hosts = [];
	$i = 0;

	foreach ($proxy['hosts'] as $host) {
		if (++$i > $data['config']['max_in_table']) {
			$hosts[] = ' &hellip;';

			break;
		}

		switch ($host['status']) {
			case HOST_STATUS_MONITORED:
				$style = null;
				break;
			case HOST_STATUS_TEMPLATE:
				$style = BS_STYLE_GREY;
				break;
			default:
				$style = BS_STYLE_RED;
		}

		if ($hosts) {
			$hosts[] = ', ';
		}

		$hosts[] = (new CLink($host['name'], 'hosts.php?form=update&hostid='.$host['hostid']))->addClass($style);
	}

	$name = new CLink($proxy['host'], 'bsm.php?action=proxy.edit&proxyid='.$proxy['proxyid']);

	// encryption
	$in_encryption = '';
	$out_encryption = '';

	if ($proxy['status'] == HOST_STATUS_PROXY_PASSIVE) {
		// input encryption
		if ($proxy['tls_connect'] == HOST_ENCRYPTION_NONE) {
			$in_encryption = (new CSpan(_('None')))->addClass(BS_STYLE_STATUS_GREEN);
		}
		elseif ($proxy['tls_connect'] == HOST_ENCRYPTION_PSK) {
			$in_encryption = (new CSpan(_('PSK')))->addClass(BS_STYLE_STATUS_GREEN);
		}
		else {
			$in_encryption = (new CSpan(_('CERT')))->addClass(BS_STYLE_STATUS_GREEN);
		}
	}
	else {
		// output encryption
		$out_encryption_array = [];
		if (($proxy['tls_accept'] & HOST_ENCRYPTION_NONE) == HOST_ENCRYPTION_NONE) {
			$out_encryption_array[] = (new CSpan(_('None')))->addClass(BS_STYLE_STATUS_GREEN);
		}
		if (($proxy['tls_accept'] & HOST_ENCRYPTION_PSK) == HOST_ENCRYPTION_PSK) {
			$out_encryption_array[] = (new CSpan(_('PSK')))->addClass(BS_STYLE_STATUS_GREEN);
		}
		if (($proxy['tls_accept'] & HOST_ENCRYPTION_CERTIFICATE) == HOST_ENCRYPTION_CERTIFICATE) {
			$out_encryption_array[] = (new CSpan(_('CERT')))->addClass(BS_STYLE_STATUS_GREEN);
		}

		$out_encryption = (new CDiv($out_encryption_array))->addClass(BS_STYLE_STATUS_CONTAINER);
	}

	$proxyTable->addRow([
		new CCheckBox('proxyids['.$proxy['proxyid'].']', $proxy['proxyid']),
		(new CCol($name))->addClass(BS_STYLE_NOWRAP),
		$proxy['status'] == HOST_STATUS_PROXY_ACTIVE ? _('Active') : _('Passive'),
		$proxy['status'] == HOST_STATUS_PROXY_ACTIVE ? $out_encryption : $in_encryption,
		$proxy['lastaccess'] == 0
			? (new CSpan(_('Never')))->addClass(BS_STYLE_RED)
			: bs_date2age($proxy['lastaccess']),
		count($proxy['hosts']),
		array_key_exists('item_count', $proxy) ? $proxy['item_count'] : 0,
		array_key_exists('perf', $proxy) ? $proxy['perf'] : '',
		$hosts ? $hosts : ''
	]);
}

// append table to form
$proxyForm->addItem([
	$proxyTable,
	$data['paging'],
	new CActionButtonList('action', 'proxyids', [
		'proxy.hostenable' => ['name' => _('Enable hosts'),
			'confirm' => _('Enable hosts monitored by selected proxies?')
		],
		'proxy.hostdisable' => ['name' => _('Disable hosts'),
			'confirm' => _('Disable hosts monitored by selected proxies?')
		],
		'proxy.delete' => ['name' => _('Delete'), 'confirm' => _('Delete selected proxies?')]
	])
]);

// append form to widget
$widget->addItem($proxyForm)->show();
