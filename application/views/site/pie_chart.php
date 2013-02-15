<?php
	$ArrayJemaatCount = $this->M_Jemaat_Rekap->GetChartLine();
	$ArraySex = $this->M_Jemaat->GetArrayJenisKelamin(array());
	$ArrayLengkap = $this->M_Jemaat->GetArrayLengkap(array());
	$ArrayProfesi = $this->M_Jemaat->GetArrayProfesi(array());
?>
<div style="padding: 5px; background: #FFFFFF;">
	<div style="overflow: auto; height: 540px;">
	<div style="float: left; width: 385px;">
		<div id="PChart1" style="margin: 0 0 15px 0;"></div>
		<div id="PChart3" style="margin: 0 0 15px 0;"></div>
	</div>
	<div style="float: right; width: 385px;">
		<div id="PChart2" style="margin: 0 0 15px 0;"></div>
		<div id="PChart4" style="margin: 0 0 15px 0;"></div>
	</div>
	<div class="clear" style="margin: 0 0 10px 0;"></div>
	
	<div class="hidden">
		<div id="DChart1"><?php echo json_encode($ArrayJemaatCount); ?></div>
		<div id="DChart2"><?php echo json_encode($ArraySex); ?></div>
		<div id="DChart3"><?php echo json_encode($ArrayLengkap); ?></div>
		<div id="DChart4"><?php echo json_encode($ArrayProfesi); ?></div>
	</div>
</div>