<?
require_once("../../config.php");
require_once("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$uid = $sessions->id;
include("../../lang/ar.php");

$L = new Lang;
$PluginName      = "pirceList";
$PluginState     = "active";
$PluginDeletable = false;

if(sql_count("plugins","plugin_name='$PluginName'") < 1){
	
	if(insert("plugins",array("plugin_name"=>$PluginName,"plugin_state"=>$PluginState,"plugin_deletable"=>$PluginDeletable))){
		
		// Create a Plugin Table
		$SqlStatment = "CREATE TABLE IF NOT EXISTS ".$PluginName." (
		pirce_list_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		pirce_list_cat VARCHAR(30) not null,
		pirce_list_type VARCHAR(30) not null,
		pirce_list_price VARCHAR(50) not null
		)";

			if ($condb->query($SqlStatment) === TRUE) {
			    echo "plugin table created successfully";
			} else {
			    echo "Error creating table: " . $condb->error;
			}

	}
}else{
		echo "Plugin Is Ready EXIST";
}
?>