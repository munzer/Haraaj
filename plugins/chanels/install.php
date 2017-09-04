<?
require_once("../../config.php");
require_once("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$uid = $sessions->id;
include("../../lang/ar.php");

$L = new Lang;
$PluginName      = "Chanels";
$PluginState     = "active";
$PluginDeletable = false;

if(sql_count("plugins","plugin_name='$PluginName'") < 1){
	
	if(insert("plugins",array("plugin_name"=>$PluginName,"plugin_state"=>$PluginState,"plugin_deletable"=>$PluginDeletable))){
		
		// Create a Plugin Table
		$SqlStatment = "CREATE TABLE IF NOT EXISTS ".$PluginName." (
		Chanel_id   INT(6) AUTO_INCREMENT PRIMARY KEY,
		Chanel_cat  VARCHAR(30) not null,
		Chanel_user VARCHAR(30) not null
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