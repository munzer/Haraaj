<?
require_once("../../config.php");
require_once("../../functions/master.php");
$sessions = new sessions();
$sessions->me();
$uid = $sessions->id;
include("../../lang/ar.php");

$L = new Lang;
$PluginName      = "reservation";
$PluginState     = "active";
$PluginDeletable = false;

if(sql_count("plugins","plugin_name='$PluginName'") < 1){
	
	if(insert("plugins",array("plugin_name"=>$PluginName,"plugin_state"=>$PluginState,"plugin_deletable"=>$PluginDeletable))){
		
		// Create a Plugin Table
		$SqlStatment = "CREATE TABLE IF NOT EXISTS ".$PluginName." (
		reservation_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		reservation_type VARCHAR(30),
		reservation_day VARCHAR(30),
		reservation_time VARCHAR(50),
		reservation_count VARCHAR(50),
		reservation_requirements VARCHAR(50)
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