<script type="text/x-jquery-tmpl" id="user_group_row_tpl">
	<?= (new CRow([
			new CCol([
				(new CTextBox('userGroups[#{usrgrpid}][usrgrpid]', '#{usrgrpid}'))->setAttribute('type', 'hidden'),
				(new CSpan('#{name}')),
			]),
			new CCol(
				(new CTag('ul', false, [
					new CTag('li', false, [
						(new CInput('radio', 'userGroups[#{usrgrpid}][permission]', PERM_READ))
							->setId('user_group_#{usrgrpid}_permission_'.PERM_READ),
						(new CTag('label', false, _('Read-only')))
							->setAttribute('for', 'user_group_#{usrgrpid}_permission_'.PERM_READ)
					]),
					new CTag('li', false, [
						(new CInput('radio', 'userGroups[#{usrgrpid}][permission]', PERM_READ_WRITE))
							->setId('user_group_#{usrgrpid}_permission_'.PERM_READ_WRITE),
						(new CTag('label', false, _('Read-write')))
							->setAttribute('for', 'user_group_#{usrgrpid}_permission_'.PERM_READ_WRITE)
					])
				]))->addClass('radio-segmented')
			),
			(new CCol(
				(new CButton('remove', _('Remove')))
					->addClass(BS_STYLE_BTN_LINK)
					->onClick('removeUserGroupShares("#{usrgrpid}");')
			))->addClass(BS_STYLE_NOWRAP)
		]))
			->setId('user_group_shares_#{usrgrpid}')
			->toString()
	?>
</script>

<script; type="text/x-jquery-tmpl"; id="user_row_tpl">
	<?= (new CRow([
			new CCol([
				(new CTextBox('users[#{id}][userid]', '#{id}'))->setAttribute('type', 'hidden'),
				(new CSpan('#{name}')),
			]),
			new CCol(
				(new CTag('ul', false, [
					new CTag('li', false, [
						(new CInput('radio', 'users[#{id}][permission]', PERM_READ))
							->setId('user_#{id}_permission_'.PERM_READ),
						(new CTag('label', false, _('Read-only')))
							->setAttribute('for', 'user_#{id}_permission_'.PERM_READ)
					]),
					new CTag('li', false, [
						(new CInput('radio', 'users[#{id}][permission]', PERM_READ_WRITE))
							->setId('user_#{id}_permission_'.PERM_READ_WRITE),
						(new CTag('label', false, _('Read-write')))
							->setAttribute('for', 'user_#{id}_permission_'.PERM_READ_WRITE)
					])
				]))->addClass('radio-segmented')
			),
			(new CCol(
				(new CButton('remove', _('Remove')))
					->addClass(BS_STYLE_BTN_LINK)
					->onClick('removeUserShares("#{id}");')
			))->addClass(BS_STYLE_NOWRAP)
		]))
			->setId('user_shares_#{id}')
			->toString()
	?>
</script>

<script; type="text/javascript">
	jQuery(function($) {
		// clone button
		$('#clone').click(function() {
			// Remove buttons, sharing options and inaccessible user message.
			$('#screenid, #delete, #clone, [id^=user_group_shares_], [id^=user_shares_], #inaccessible_user').remove();
			$('#update')
				.text(<?= CJs::encodeJson(_('Add')) ?>)
				.attr({id: 'add', name: 'add'});
			$('#name').focus();

			$('#form').val('clone');

			// Set screen to private.
			$('input[name=private][value=' + <?= PRIVATE_SHARING ?> + ']').prop('checked', true);

			// Switch to first tab so multiselect is visible and only then add data and resize.
			$('#tab_screen_tab').trigger('click');

			$('#multiselect_userid_wrapper').show();

			// Set current user as owner.
			$('#userid').multiSelect('addData', {
				'id': $('#current_user_userid').val(),
				'name': $('#current_user_fullname').val()
			});
		});
	});

	/**
	 * @see init.js add.popup event
	 */
	function addPopupValues(list) {
		var i,
			value,
			tpl,
			container;

		for (i = 0; i < list.values.length; i++) {
			if (empty(list.values[i])) {
				continue;
			}

			value = list.values[i];
			if (typeof value.permission === 'undefined') {
				if (jQuery('input[name=private]:checked').val() == <?= PRIVATE_SHARING ?>) {
					value.permission = <?= PERM_READ ?>;
				}
				else {
					value.permission = <?= PERM_READ_WRITE ?>;
				}
			}

			switch (list.object) {
				case 'usrgrpid':
					if (jQuery('#user_group_shares_' + value.usrgrpid).length) {
						continue;
					}

					tpl = new Template(jQuery('#user_group_row_tpl').html());

					container = jQuery('#user_group_list_footer');
					container.before(tpl.evaluate(value));

					jQuery('#user_group_' + value.usrgrpid + '_permission_' + value.permission + '')
						.prop('checked', true);
					break;

				case 'userid':
					if (jQuery('#user_shares_' + value.id).length) {
						continue;
					}

					tpl = new Template(jQuery('#user_row_tpl').html());

					container = jQuery('#user_list_footer');
					container.before(tpl.evaluate(value));

					jQuery('#user_' + value.id + '_permission_' + value.permission + '')
						.prop('checked', true);
					break;
			}
		}
	}

	function removeUserGroupShares(usrgrpid) {
		jQuery('#user_group_shares_' + usrgrpid).remove();
	}

	function removeUserShares(userid) {
		jQuery('#user_shares_' + userid).remove();
	}
</script>
