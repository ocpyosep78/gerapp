<?php
	$UserAdmin = $this->M_User->GetCurrentUser();
	$Permission = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Profesi'));
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
?>

<?php $this->load->view('administrator/header', array('PageTitle' => 'Site Config'));?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/site/config.js"></script>

<div id="loading_mask">
    <div class="loading">
        <p><img src="<?php echo $this->config->item('base_url'); ?>/images/loading.gif"></p>
        <p>Loading...</p>
    </div>
</div>

<input type="hidden" id="PermissionRead" name="PermissionRead" value="<?php echo $Permission['Read']; ?>" />
<input type="hidden" id="PermissionWrite" name="PermissionWrite" value="<?php echo $Permission['Write']; ?>" />
<input type="hidden" id="AccessGerejaID" name="AccessGerejaID" value="<?php echo $AccessGerejaID; ?>" />

<div class="wi">
	<div id="x-cnt">
		<div id="grid-member"></div>
	</div>
</div>
<?php $this->load->view('administrator/footer');?>