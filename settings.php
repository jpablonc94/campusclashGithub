<?php
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_campusclash'),
            get_string('descconfig', 'block_campusclash')
        ));
 
$settings->add(new admin_setting_configcheckbox(
            'campusclash/Allow_CampusClash',
            get_string('labelallowhtml', 'block_campusclash'),
            get_string('descallowhtml', 'block_campusclash'),
            '0'
        ));
