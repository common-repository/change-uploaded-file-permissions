<?php
/*
Plugin Name: Change Uploaded File Permissions
Plugin URI: https://wordpress.org/plugins/change-uploaded-file-permissions/
Description: Changes the permissions of uploaded files, pictures and thumbnails after the upload.
Version: 5.0.0
Author: Sven Kubiak
Author URI: https://svenkubiak.de

Copyright 2007-20022 Sven Kubiak

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

global $wp_version;
define('UFPWP26', version_compare($wp_version, '2.6', '>='));

Class UploadedFilePermissions
{
	var $uploadfolder;	
	var $logging;

	function UploadedFilePermissions()
	{
		//load language file
		if (function_exists('load_plugin_textdomain'))
			load_plugin_textdomain('change-uploaded-file-permissions', PLUGINDIR.'/change-uploaded-file-permissions');
	
		//add wp actions
		add_action('deactivate_change-uploaded-file-permissions/change-uploaded-file-permissions.php', array(&$this, 'deactivate'));
		add_action('activate_change-uploaded-file-permissions/change-uploaded-file-permissions.php', array(&$this, 'activate'));
		add_action('admin_menu', array(&$this, 'setAdminPage'));
		add_action('wp_handle_upload', array(&$this, 'uploadHook'));
		
		//add wp filter
		add_filter('wp_generate_attachment_metadata', array(&$this, 'thumbHook'));
			
		//set logging
		$this->logging = get_option('sk_enable_log');
		
		//set folder
		$folder = get_option('sk_wproot_folder')."/".get_option('upload_path')."/";
		
		//check if year and month based folders are active and crate them if they are not created already
		if (get_option('uploads_use_yearmonth_folders') == 1){
			if (file_exists($folder) && !file_exists($folder.date("Y/m")."/")){
				if (function_exists('mkdir')){					
					@mkdir($folder.date("Y"));
					@mkdir($folder.date("Y/m"));
				}
			}	
			$this->uploadfolder = $folder.date("Y/m")."/";		
		}
		else{ 
			$this->uploadfolder = $folder;
		}
	}
	
	function activate()
	{		
		add_option('sk_wproot_folder', $_SERVER['DOCUMENT_ROOT'], '', 'yes');
		add_option('sk_file_log', '', '', 'yes');
		add_option('sk_enable_log', 'false', '', 'yes');	
	}
	
	function deactivate()
	{
		delete_option('sk_wproot_folder');
		delete_option('sk_file_log');	
		delete_option('sk_enable_log');
	}
	
	function setAdminPage()
	{
		add_options_page(__('Upload file permissions','change-uploaded-file-permissions'), __('Upload file permissions','change-uploaded-file-permissions'), 8, 'uploadedfilepermissions', array(&$this, 'setOptionPage'));	
	}
	
	function setOptionPage()
	{
		if (!current_user_can('manage_options'))
			wp_die(__('Sorry, but you have no permissions to change settings.','change-uploaded-file-permissions'));
		
		?>
		<div class="wrap">
		<h2><?php echo __('Change Uploaded File Permissions Settings','change-uploaded-file-permissions'); ?></h2>
	    <h3><?php echo __('Path','change-uploaded-file-permissions'); ?></h3>
	    <table class="form-table">
	    <tr>
	     <?php
			if (file_exists(get_option('sk_wproot_folder')) && is_dir(get_option('sk_wproot_folder'))){
				echo "<td colspan='2'><strong>".__('Path test','change-uploaded-file-permissions').": </strong> <font color='green'>".__('The upload folder has been successfully found!','change-uploaded-file-permissions')."</font></td>";
			}
			else{
				echo "<td colspan='2'><strong>".__('Path test','change-uploaded-file-permissions').": <font color='red'>".__('Error','change-uploaded-file-permissions')."</strong> - ".__('The folder wp-content/uploads was not found!')."</font></td>";
			}	
		 ?> 
	    </tr>      
	    </table>   
		</div>	
		
		<?php	
	}	
	
	function uploadHook($filedata)
	{		
		$this->changePermission($filedata['file'],false,0);
		return $filedata;
	}
	
	function thumbHook($metadata)
	{
		if (!UFPWP26)
			return $metadata;		
		
		$folder = explode("/",$metadata['file']);
	
		$thumbnail = $metadata ['sizes'] ['thumbnail'] ['file'];
		$medium = $metadata ['sizes'] ['medium'] ['file'];
		$large = $metadata ['sizes'] ['large'] ['file'];			

		if (!empty($thumbnail)){
			$this->changePermission($thumbnail,true,$folder);	
		}
		if (!empty($medium)){
			$this->changePermission($medium,true,$folder);	
		}	
		if (!empty($large)){
			$this->changePermission($large,true,$folder);	
		}		

		return $metadata;
	}
	
	function changePermission($filename,$usefolder,$folder)
	{
		if ($usefolder === true){
			if (get_option('uploads_use_yearmonth_folders') == 1){
				$currentfile = get_option('sk_wproot_folder')."/".get_option('upload_path')."/".$folder[0]."/".$folder[1]."/".$filename;
			}
			else{
				$currentfile = $this->uploadfolder.$filename;
			}
		}
		else{
			$currentfile = $filename;
			$filename = str_replace("/","",strrchr($currentfile, "/"));
		}
	
		if (file_exists($currentfile) && !is_dir($currentfile)){		
			if (chmod($currentfile, 0640)){
				$this->logAction($filename, __('Filepermissions have been changed succsesfully','change-uploaded-file-permissions'));
			}
			else{
				$this->logAction($filename, __('Failed to change filepermissions','change-uploaded-file-permissions'));		
			}		
		}	
		else{
			$this->logAction($filename, __('File does not exist or is a folder','change-uploaded-file-permissions'));
		}
		return;	
	}
	
	function displayLog()
	{
		$log = get_option('sk_file_log');
		(!is_array($log)) ? $log = unserialize($log) : false;

		$countlog = count($log);
			
		echo "<h3>Log Ausgabe</h3>";
		echo '<table class="form-table">';		
			
		if (!empty($log[0])){
			echo "<tr>";
			echo '<th scope="row" valign="top">'.__('Filename','change-uploaded-file-permissions').'</b></td>';
			echo '<th scope="row" valign="top">'.__('Date','change-uploaded-file-permissions').'</b></td>';
			echo '<th scope="row" valign="top">'.__('Log','change-uploaded-file-permissions').'</b></td>';
			echo "</tr>";
			for ($i=$countlog;$i >= 0;$i--){
				if ($log[$i]!= null){
					$current = $log[$i];
					echo "<tr>";
					echo "<td>".$current['filename']."</td>";
					echo "<td>".date("d.m.y - H:i:s", $current['timestamp'])."</td>";
					echo "<td>".$current['log']."</td>";
					echo "</tr>";
				}
			}
		}
		else{
			echo '<tr><th scope="row" valign="top">'.__('No log yet.','change-uploaded-file-permissions').'</b></td></tr>';
		}
	
		echo "</table>";
		echo '<p class="submit" />';
	}
	
	function logAction($filename, $message)
	{
		if (get_option('sk_enable_log') == 'true'){
			$log = get_option('sk_file_log');
			(!is_array($log)) ? $log = unserialize($log) : false;			

			$log [] = array(
				'filename' => $filename,
				'timestamp' => time(),
				'log' => $message
			);
	
			$log = serialize($log);
			update_option('sk_file_log',$log);		
		}	
	}
}
//initialize class
if (class_exists('UploadedFilePermissions'))
	$fileperms = new UploadedFilePermissions();
?>