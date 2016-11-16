<?php

include dirname(__FILE__).'/common.item.edit.js.php';

$this->data['valueTypeVisibility'] = [];
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'data_type');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'row_data_type');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'units');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'row_units');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'multiplier');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'row_multiplier');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'delta');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'row_delta');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'trends');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'row_trends');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'trends');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'row_trends');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_LOG, 'logtimefmt');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_LOG, 'row_logtimefmt');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'valuemapid');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_STR, 'valuemapid');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_STR, 'row_valuemap');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_STR, 'valuemap_name');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'row_valuemap');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_FLOAT, 'valuemap_name');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'valuemapid');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'row_valuemap');
bs_subarray_push($this->data['valueTypeVisibility'], ITEM_VALUE_TYPE_UINT64, 'valuemap_name');

$this->data['dataTypeVisibility'] = [];
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'row_units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'row_units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'row_units');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'row_multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'row_multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'row_multiplier');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'delta');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_DECIMAL, 'row_delta');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'delta');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_OCTAL, 'row_delta');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'delta');
bs_subarray_push($this->data['dataTypeVisibility'], ITEM_DATA_TYPE_HEXADECIMAL, 'row_delta');

?>
<script type="text/javascript">
	function displayKeyButton() {
		// selected item type
		var type = parseInt(jQuery('#type').val());

		jQuery('#keyButton').prop('disabled',
			type != <?php echo ITEM_TYPE_BSM; ?>
				&& type != <?php echo ITEM_TYPE_BSM_ACTIVE; ?>
				&& type != <?php echo ITEM_TYPE_SIMPLE; ?>
				&& type != <?php echo ITEM_TYPE_INTERNAL; ?>
				&& type != <?php echo ITEM_TYPE_AGGREGATE; ?>
				&& type != <?php echo ITEM_TYPE_DB_MONITOR; ?>
				&& type != <?php echo ITEM_TYPE_SNMPTRAP; ?>
		)
	}

	jQuery(document).ready(function() {
		// field switchers
		<?php if (!empty($this->data['dataTypeVisibility'])) { ?>
		var dataTypeSwitcher = new CViewSwitcher('data_type', 'change',
			<?php echo bs_jsvalue($this->data['dataTypeVisibility'], true); ?>);
		<?php } ?>
		<?php
		if (!empty($this->data['valueTypeVisibility'])) { ?>
			var valueTypeSwitcher = new CViewSwitcher('value_type', 'change',
				<?php echo bs_jsvalue($this->data['valueTypeVisibility'], true); ?>);
		<?php } ?>

		// multiplier
		var multpStat = document.getElementById('multiplier');

		if (multpStat && multpStat.onclick) {
			multpStat.onclick();
		}

		jQuery('#type').change(function() {
				displayKeyButton();
			})
			.trigger('change');
	});
</script>
