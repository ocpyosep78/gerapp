<?php
	$UserAdmin = $this->session->userdata('UserAdmin');
	$PermissionTagihan = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Tagihan'));
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
?>

<?php $this->load->view('administrator/header', array('PageTitle' => 'Tagihan'));?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/finance/bill.js"></script>

<input type="hidden" id="PermissionRead" name="PermissionRead" value="<?php echo $PermissionTagihan['Read']; ?>" />
<input type="hidden" id="PermissionWrite" name="PermissionWrite" value="<?php echo $PermissionTagihan['Write']; ?>" />
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

<?php $this->load->view('administrator/footer');?>