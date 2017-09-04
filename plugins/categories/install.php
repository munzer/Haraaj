<?
require_once("../../config.php");
require_once("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$uid = $sessions->id;
include("../../lang/ar.php");

$L = new Lang;
$PluginName      = "categories";
$PluginState     = "active";
$PluginDeletable = false;

if(sql_count("plugins","plugin_name='$PluginName'") < 1){
	
	if(insert("plugins",array("plugin_name"=>$PluginName,"plugin_state"=>$PluginState,"plugin_deletable"=>$PluginDeletable))){
		
		// Create a Plugin Table
		$SqlStatment = "CREATE TABLE IF NOT EXISTS ".$PluginName." (
		categories_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		categories_date   VARCHAR(130),
		categories_type  VARCHAR(190),
		categories_title  VARCHAR(190),
		categories_parent VARCHAR(150)
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