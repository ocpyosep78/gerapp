<?php
    $ext = $this->config->item('base_url') . '/extjs';
    $stiki = $this->config->item('base_url');
    $app = $stiki . '/app';
    
    $ArrayUserAdmin = $this->session->userdata('UserAdmin');
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($ArrayUserAdmin);
    if ($ArrayUserAdmin) {
        unset($ArrayUserAdmin['AdminPassWord']); 
        unset($ArrayUserAdmin['password']); 
    }
	
	$SiteHeader = $this->M_Config->GetByID(array('config_name' => 'Site Header', 'gereja_id' => $AccessGerejaID));
	$LogoGereja = $this->M_Config->GetByID(array('config_name' => 'Logo Gereja', 'gereja_id' => $AccessGerejaID));
	$LogoGerejaLink = (isset($LogoGereja['config'])) ? $LogoGereja['config'] : LOGO_GEREJA;
?>
		<h1 id="logo">
			<div style="height: 50px;">
				<img src="<?php echo $this->config->item('base_url') . '/images/logo/' . $LogoGerejaLink; ?>" style="float: left; height: 50px; margin: 0 10px 0 0;" />
				<?php echo (isset($SiteHeader['config'])) ? $SiteHeader['config'] : SITE_HEADER; ?>
			</div>
		</h1>
		<div class="navigation"><ul>
		<?php if (isset($ArrayUserAdmin['name'])) { ?>
			<li class="welcome">Selamat datang <b><?php echo $ArrayUserAdmin['name']; ?></b></li>
		<?php } ?>
			<li><a href="<?php echo $this->config->item('base_url'); ?>/administrator/logout"><div>Logout</div></a></li>
			<li><img src="<?php echo $ArrayUserAdmin['FotoLink']; ?>" style="width: 48px; height: 48px; margin: -18px 0 0 0;"/></li>
		</ul></div>
