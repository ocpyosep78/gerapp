<?php
	$IsValid = 1;
	$ArrayRaw = GetArrayFromFileUpload($this->config->item('base_path') . '/images/other/' . $_POST['FileUpload']);
?>
<div style="padding: 10px;">
    <table cellspacing="0" id="ConvertionGrid">
        <thead>
			<tr style="background: #EEEEEE;">
                <th style="width: 150px;">Nama</th>
                <th style="width: 80px;">Tanggal Lahir</th>
				<th style="width: 90px;">Tempat Lahir</th>
				<th style="width: 150px;">Alamat</th>
				<th style="width: 80px;" class="hidden">Telepon</th>
				<th style="width: 90px;">HP</th>
                <th style="width: 150px;">Email</th>
				<th style="width: 75px;">Status</th></tr>
        </thead>
        <tbody>
			<?php foreach($ArrayRaw as $Key => $Data) { ?>
				<?php $Status = 1; ?>
				<?php $Status = (empty($Data[0])) ? 0 : $Status; ?>
				<?php $IsValid = ($Status == 0) ? 0 : $IsValid; ?>
				<?php $Data[0] = (isset($Data[0])) ? $Data[0] : '&nbsp;'; ?>
				<?php $Data[1] = (isset($Data[1])) ? $Data[1] : '&nbsp;'; ?>
				<?php $Data[2] = (isset($Data[2])) ? $Data[2] : '&nbsp;'; ?>
				<?php $Data[3] = (isset($Data[3])) ? $Data[3] : '&nbsp;'; ?>
				<?php $Data[4] = (isset($Data[4])) ? $Data[4] : '&nbsp;'; ?>
				<?php $Data[5] = (isset($Data[5])) ? $Data[5] : '&nbsp;'; ?>
				<?php $Data[6] = (isset($Data[6])) ? $Data[6] : '&nbsp;'; ?>
				<tr>
					<td><?php echo $Data[0]; ?></td>
					<td><?php echo $Data[1]; ?></td>
					<td><?php echo $Data[2]; ?></td>
					<td><?php echo $Data[3]; ?></td>
					<td><?php echo $Data[4]; ?></td>
					<td><?php echo $Data[5]; ?></td>
					<td><?php echo $Data[6]; ?></td>
					<td><?php echo ($Status == 1) ? '<span style="color: #0000FF;">Valid</span>' : '<span style="color: #FF0000;">Tidak Valid</span>'; ?></td></tr>
			<?php } ?>
        </tbody>
    </table>
	
	<input type="hidden" name="IsValid" id="IsValid" value="<?php echo $IsValid; ?>" />
</div>