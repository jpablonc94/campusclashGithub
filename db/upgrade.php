<?php
 
function xmldb_block_campusclash_upgrade($oldversion) {
    global $CFG; 

    if ($oldversion < 2015030300) {
	// Drop the mirror table.
        $dbman = $DB->get_manager();

        // Define table to be dropped.
        $table = new xmldb_table('campusclash_points');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

	// Campusclash savepoint reached.
        upgrade_block_savepoint(true, 2015030300, 'campusclash');
    }   

    if ($oldversion > 2015030300 && $oldversion < 2015070100) {
        $criteria = array(
            'plugin' => 'block_campusclash',
            'name' => 'lastcomputedid'
        );

        $DB->delete_records('config_plugins', $criteria);
    }

    return true;    
}
?>
