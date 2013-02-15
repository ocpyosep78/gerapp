<?php
	$UserAdmin = $this->M_User->GetCurrentUser();
	$Permission = $this->M_Permission->GetCollection(array('GroupID' => $UserAdmin['group_id'], 'SingleData' => 'Log'));
?>

<?php $this->load->view('administrator/header', array('PageTitle' => 'Log'));?>
<script type="text/javascript" src="<?php echo $this->config->item('base_url'); ?>/js/site/log.js"></script>

<div id="loading_mask">
    <div class="loading">
        <p><img src="<?php echo $this->config->item('base_url'); ?>/images/loading.gif"></p>
        <p>Loading...</p>
    </div>
</div>

<input type="hidden" id="PermissionRead" name="PermissionRead" value="<?php echo $Permission['Read']; ?>" />
<input type="hidden" id="PermissionWrite" name="PermissionWrite" value="<?php echo $Permission['Write']; ?>" />
<div class="wi">
	<div id="x-cnt">
		<div id="grid-member"></div>
	</div>
</div>
<?php $this->load->view('administrator/footer');?>