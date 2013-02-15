<?php
	$UserAdmin = $this->session->userdata('UserAdmin');
	$PermissionJemaat = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Jemaat'));
	$PermissionKeluarga = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Keluarga'));
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/entry.jemaat.js"></script>

<input type="hidden" id="PermissionRead" name="PermissionRead" value="<?php echo $PermissionJemaat['Read']; ?>" />
<input type="hidden" id="PermissionWrite" name="PermissionWrite" value="<?php echo $PermissionJemaat['Write']; ?>" />
<input type="hidden" id="KeluargaWrite" name="KeluargaWrite" value="<?php echo $PermissionKeluarga['Write']; ?>" />
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