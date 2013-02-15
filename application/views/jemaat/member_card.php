<?php
	$JemaatID = (isset($_GET['JemaatID'])) ? $_GET['JemaatID'] : 0;
	$Jemaat = $this->M_Jemaat->GetByID(array('JemaatID' => $JemaatID));
?>

<div style="width: 400px; border: 1px solid #000000;">
	<div style="padding: 10px 0 5px 0; text-align: center;">
		<img src="<?php echo $Jemaat['FotoLink']; ?>" style="width: 100px; height: 150px; "/>
	</div>
	<div style="text-align: center;">
		<div style="font-size: 24px;"><?php echo $Jemaat['nama']; ?></div>
		<div style="font-size: 20px;"><?php echo $Jemaat['tempatlahir']; ?></div>
		<div style="font-size: 20px;">Keluarga <?php echo $Jemaat['KeluargaNama']; ?></div>
		<div style="font-size: 20px;">Gereja <?php echo $Jemaat['GerejaNama']; ?></div>
	</div>
</div>