<?php
	$UserAdmin = $this->session->userdata('UserAdmin');
	$Permission = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'JenisBiaya'));
?>

<?php $this->load->view('administrator/header', array('PageTitle' => 'Jenis Biaya'));?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/finance/jenis_biaya.js"></script>

<input type="hidden" id="PermissionRead" name="PermissionRead" value="<?php echo $Permission['Read']; ?>" />
<input type="hidden" id="PermissionWrite" name="PermissionWrite" value="<?php echo $Permission['Write']; ?>" />

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

<?php $this->load->view('administrator/footer');?>