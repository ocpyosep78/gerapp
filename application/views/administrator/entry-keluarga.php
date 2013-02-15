<?php
	$UserAdmin = $this->session->userdata('UserAdmin');
	$Permission = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Keluarga'));
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/entry.keluarga.js"></script>

<input type="hidden" id="KeluargaRead" name="KeluargaRead" value="<?php echo $Permission['Read']; ?>" />
<input type="hidden" id="KeluargaWrite" name="KeluargaWrite" value="<?php echo $Permission['Write']; ?>" />
<input type="hidden" id="AccessGerejaID" name="AccessGerejaID" value="<?php echo $AccessGerejaID; ?>" />

<div id="loading_mask">
    <div class="loading">
        <p><img src="<?php echo $this->config->item('base_url').'/images/loading.gif'?>"></p>
        <p>Loading...</p>
    </div>
</div>

<div class="wi">
	<div id="x-cnt">
		<div id="grid-member"></div>
	</div>
</div>