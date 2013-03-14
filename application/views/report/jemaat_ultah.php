<?php
	$Param = $_GET;
	$Param['date_start'] = (isset($Param['date_start'])) ? $Param['date_start'] : '';
	$Param['date_end'] = (isset($Param['date_end'])) ? $Param['date_end'] : '';
	
	$ArrayDateStart = ConvertDateToArray($Param['date_start']);
	$ArrayDateEnd = ConvertDateToArray($Param['date_end']);
	$StringCustom  = "AND (MONTH(tgllahir) >= '" . $ArrayDateStart['Month'] . "' AND DAY(tgllahir) >= '" . $ArrayDateStart['Day'] . "') ";
	$StringCustom .= "AND (MONTH(tgllahir) <= '" . $ArrayDateEnd['Month'] . "' AND DAY(tgllahir) <= '" . $ArrayDateEnd['Day'] . "') ";
	
	$ParamJemaat['idgereja'] = $this->M_User->GetGerejaID();
	$ParamJemaat['StringCustom'] = $StringCustom;
	$ParamJemaat['limit'] = 100;
	$ParamJemaat['sort'] = '[{"property":"tgllahir_monthday","direction":"ASC"}]';
	$ArrayJemaat = $this->M_Jemaat->GetArray($ParamJemaat);
?>

<?php if (count($ArrayJemaat) > 0) { ?>
	<style>
		td { border: 1px solid #000000; }
		.center { text-align: center; }
	</style>
	
	<div style="padding: 0 0 15px 0;">
		<div class="center">Data Ulang Tahuan Jemaat</div>
		<div class="center"><?php echo GetFormatDateCommon($Param['date_start'], array('FormatDate' => "d F Y")) . ' - ' . GetFormatDateCommon($Param['date_end'], array('FormatDate' => "d F Y")); ?></div>
	</div>
	
	<table style="border-collapse: collapse; width: 100%;">
		<tr>
			<td class="center" style="width: 15%;">Nama Jemaat</td>
			<td class="center" style="width: 15%;">Nomor Anggota</td>
			<td class="center" style="width: 15%;">No KK</td>
			<td class="center" style="width: 10%;">No HP</td>
			<td class="center" style="width: 10%;">Ultah Jemaat</td>
		</tr>
		<?php foreach ($ArrayJemaat as $Key => $Record) { ?>
			<tr>
				<td><?php echo $Record['nama']; ?></td>
				<td><?php echo $Record['nomor']; ?></td>
				<td><?php echo $Record['no_kk']; ?></td>
				<td><?php echo $Record['no_hp']; ?></td>
				<td class="center"><?php echo GetFormatDateCommon($Record['tgllahir'], array('FormatDate' => "d F")); ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>