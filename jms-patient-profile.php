<?php
/* 
Plugin Name: JMS Patient Profile
Plugin URI: http://www.jmsliu.com/products/jms-rss-feed
Description: Create profile for each patient. This will help doctors to keep patient record for each patient.
Author: James Liu
Version: 2.0.0
Author URI: http://jmsliu.com/
License: GPL2

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//ali sms
//include dirname(__FILE__)."/lib/TopSdk.php";
date_default_timezone_set('Asia/Shanghai');

global $jms_patient_profile_db_version;
$jms_patient_profile_db_version = '1.0';
    
//install database
register_activation_hook( __FILE__, 'installJMSPatientProfile' );
add_shortcode( 'jms-patient-profile-manager', 'jmsPatientProfileManager');

add_action( 'admin_init', 'jms_patient_profile_admin_init' );
add_action( 'admin_menu', 'jmsPatientProfileAdminPage' );

add_action( 'init', 'jms_patient_profile_load_textdomain' );

function jms_patient_profile_load_textdomain() {
  load_plugin_textdomain( 'jms-patient-profile', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function jmsPatientProfileManager($atts) {
    global $wpdb;
    if ( is_user_logged_in() ) {
        $userid = get_current_user_id();
        $table_name = $wpdb->prefix . 'jms_patient_profile';
        $wpdb->show_errors( true );
        wp_enqueue_style( 'jms_patient_profile_style', plugins_url('css/profile.css?version=1.1.2', __FILE__));
        wp_enqueue_script( 'jms_patient_profile_d3_js', plugins_url('js/chart.bundle.min.js?version=4.7.1', __FILE__));
        wp_enqueue_script('jquery');
        if(isset($_GET[ "jms_patient_profile_id" ]) && !empty(trim($_GET[ "jms_patient_profile_id" ]))) {
            $profileID = trim($_GET[ "jms_patient_profile_id" ]);
            $result = $wpdb->get_results("SELECT * FROM $table_name WHERE userid=$userid AND id=".(int)$profileID, ARRAY_A);
            if($wpdb->num_rows > 0) {
                require_once(dirname(__FILE__)."/template/profile_details_user.php");
            } else {
                echo __('Sorry, we cannot find your profile!', 'jms-patient-profile');
            }
        } else if(isset($_GET[ "current_profile_id" ]) && isset($_GET[ "track_slug" ])) {
            $current_profile_id = $_GET[ "current_profile_id" ];
            $slug = $_GET[ "track_slug" ];
            $slug_desc = "";
            $fieldTablename = $wpdb->prefix . 'jms_profile_field';
            $result = $wpdb->get_results("SELECT * FROM $fieldTablename WHERE slug=\"$slug\"", ARRAY_A);
            if($wpdb->num_rows > 0) {
                $slug_desc = $result[0]["description"];
            }

            $result = $wpdb->get_results("SELECT * FROM $table_name WHERE userid=$userid", ARRAY_A);
            $history = array();
            foreach ($result as $profile) {
                $fields = json_decode($profile["content"], true);
                foreach($fields as $field) {
                    foreach($field as $item) {
                        if($item["slug"] == $_GET[ "track_slug" ]) {
                            $history[] = array("id"=>$profile["id"], "title"=>$profile["title"], "name"=>$item["name"], "value"=>$item["data"], "description"=>$slug_desc);
                        }
                    }
                }
            }
            
            require_once(dirname(__FILE__)."/template/profile_history_chart.php");
        } else {
            $result = $wpdb->get_results("SELECT * FROM $table_name WHERE userid=$userid", ARRAY_A);
            if($wpdb->num_rows > 0) {
                require_once(dirname(__FILE__)."/template/profile_list_user.php");
            } else {
                echo __('You don\'t have any profile yet!', 'jms-patient-profile');
            }
        }
    } else {
        $loginMsg = sprintf(__("Please <a href=\"%s\">sign in</a> to access your profile.", "jms-patient-profile"), site_url("/login/"));
        echo $loginMsg;
    }
}

function installJMSPatientProfile() {
    global $jms_patient_profile_db_version;
    global $wpdb;
    
    $jms_patient_profile_db_version = get_option( "jms_patient_profile_db_version", null );
    if ( $jms_patient_profile_db_version == null ) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . "jms_profile_field";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `type` INT UNSIGNED NOT NULL COMMENT '1: text\n2: number\n3: image for upload\n4: other file for upload\n5: text area',
                `weight` INT UNSIGNED NOT NULL,
                `create_date` VARCHAR(255) NOT NULL,
                `update_date` VARCHAR(255) NOT NULL,
                `published` TINYINT(1) NOT NULL,
                `slug` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `id_UNIQUE` (`id` ASC))
                ENGINE = InnoDB ".$charset_collate.";";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
                
        $table_name = $wpdb->prefix . "jms_profile_template";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `field_set` TEXT(65535) NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `id_UNIQUE` (`id` ASC))
                ENGINE = InnoDB ".$charset_collate.";";
        dbDelta( $sql );
                
        $table_name = $wpdb->prefix . "jms_patient_profile";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `userid` INT UNSIGNED NOT NULL,
                `content` MEDIUMTEXT NULL,
                `create_by` INT NULL,
                `update_by` INT NULL,
                `create_date` VARCHAR(255) NULL,
                `update_date` VARCHAR(255) NULL,
                `title` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `id_UNIQUE` (`id` ASC))
                ENGINE = InnoDB ".$charset_collate.";";
        dbDelta( $sql );
        
        add_option( "jms_patient_profile_db_version", $jms_patient_profile_db_version );
    }
}

function jms_patient_profile_admin_init() {
    wp_enqueue_style( 'jms_patient_profile_style', plugins_url('css/my.css?version=1.1.1', __FILE__));
}

function jmsPatientProfileAdminPage() {
    
    add_menu_page(
        __("Patient Profile", 'jms-patient-profile' ),
        __('Patient Profile','jms-patient-profile'),
        'manage_options',
        'jms-patient-profile-top',
        'jmsPatientProfileAdminPageOptions' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page(
        'jms-patient-profile-top',
        __('Profile Fields','jms-patient-profile'),
        __('Profile Fields','jms-patient-profile'),
        'manage_options',
        'jms-patient-profile-sub1',
        'jmsPatientProfileAdminPageSub1');
}

function jmsPatientProfileAdminPageOptions() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    if( isset($_POST["action"]) ) {
        if($_POST[ "action" ] == "new-save") {
            if(check_admin_referer( 'new_profile' )) {
                if(isset($_POST[ "fields" ]) && !empty(trim($_POST[ "fields" ]))
                    && isset($_POST[ "profile_title" ]) && !empty(trim($_POST[ "profile_title" ]))
                    && isset($_POST[ "userid" ]) && !empty(trim($_POST[ "userid" ]))
                ) {
                    $fieldString = urldecode(trim($_POST[ "fields" ]));
                    $fields = json_decode($fieldString, true);
                    if($fields != NULL) {
                        $fieldLength = count($fields["fields"]);
                        for($i = 0; $i < $fieldLength; $i++) {
                            $field = &$fields["fields"][$i];
                            switch($field["type"]) {
                                case 1:
                                case 2:
                                case 5:
                                    $data = trim($_POST[$field["slug"]]);
                                    $field["data"] = $data;
                                    break;
                                case 3:
                                case 4:
                                    $data = trim($_POST[$field["slug"]]);
                                    $field["data"] = $data;
                                    break;
                            }
                        }
                        
                        $userid = trim($_POST[ "userid" ]);
                        $title = trim($_POST[ "profile_title" ]);
                        
                        //save to database
                        $wpdb->show_errors( true );
                        $table_name = $wpdb->prefix . "jms_patient_profile";
                        $result = $wpdb->query($wpdb->prepare( 
                            "
                                INSERT INTO $table_name
                                ( userid, content, create_by, update_by, create_date, update_date, title )
                                VALUES ( %d, %s, %d, %d, %s, %s, %s)
                            ",
                            array(
                                $userid,
                                json_encode($fields),
                                get_current_user_id(),
                                get_current_user_id(),
                                current_time('mysql', 1),
                                current_time('mysql', 1),
                                $title
                            )
                        ));
                        
                        if($result !== false) {
                            $message = sprintf(__('Create a new profile successfully! <a href="%s">Back to List</a>','jms-patient-profile'), $wp->request."admin.php?page=jms-patient-profile-top");
                            echo "<h1>".$message."</h1>";
                        } else {
                            echo __('Insert New Profile, DB operation failed!','jms-patient-profile');
                        }
                        //print_r($fields);
                    } else {
                        echo __('Cannot find profile field data','jms-patient-profile');
                    }
                } else {
                    echo __('Cannot find necessary data when creating new profile','jms-patient-profile');
                }
            } else {
                echo __( 'You do not have sufficient permissions to access this page.' );
            }
        } else if($_POST[ "action" ] == "update-save") {
            if(check_admin_referer( 'update_profile' )) {
                if(isset($_POST[ "fields" ]) && !empty(trim($_POST[ "fields" ]))
                    && isset($_POST[ "profile_title" ]) && !empty(trim($_POST[ "profile_title" ]))
                    && isset($_POST[ "userid" ]) && !empty(trim($_POST[ "userid" ]))
                    && isset($_POST[ "id" ]) && !empty(trim($_POST[ "id" ]))
                ) {
                    $fieldString = urldecode(trim($_POST[ "fields" ]));
                    $fields = json_decode($fieldString, true);
                    if($fields != NULL) {
                        $fieldLength = count($fields["fields"]);
                        for($i = 0; $i < $fieldLength; $i++) {
                            $field = &$fields["fields"][$i];
                            switch($field["type"]) {
                                case 1:
                                case 2:
                                case 5:
                                    $data = trim($_POST[$field["slug"]]);
                                    $field["data"] = $data;
                                    break;
                                case 3:
                                case 4:
                                    $data = trim($_POST[$field["slug"]]);
                                    $field["data"] = $data;
                                    break;
                            }
                        }
                        
                        $userid = trim($_POST[ "userid" ]);
                        $title = trim($_POST[ "profile_title" ]);
                        
                        //save to database
                        $wpdb->show_errors( true );
                        $table_name = $wpdb->prefix . "jms_patient_profile";
                        $result = $wpdb->query( $wpdb->prepare( 
                            "UPDATE $table_name SET `userid`=\"%d\", `content`=%s, `update_by`=%d, `update_date`=\"%s\", `title`=\"%s\" WHERE `id`=%d",
                            array(
                                $userid,
                                json_encode($fields),
                                get_current_user_id(),
                                current_time('mysql', 1),
                                $title,
                                (int)$_POST["id"]
                            )
                        ));
                        
                        if($result !== false) {
                            $message = sprintf(__('Update profile successfully! <a href="%s">Back to List</a>','jms-patient-profile'), $wp->request."admin.php?page=jms-patient-profile-top");
                            echo "<h1>".$message."</h1>";
                        } else {
                            echo __('Update Profile, DB operation failed!','jms-patient-profile');
                        }
                        //print_r($fields);
                    } else {
                        echo __('Cannot find profile field data','jms-patient-profile');
                    }
                } else {
                    echo __('Cannot find necessary data when updating new profile','jms-patient-profile');
                }
            } else {
                echo __( 'You do not have sufficient permissions to access this page.' );
            }
        } else {
            echo __( 'You do not have sufficient permissions to access this page.' );
        }
    } else if( isset($_GET[ "action" ]) ) {
        //show new form
        if($_GET[ "action" ] == 'new') {
            $blogusers = get_users( 'blog_id=1&orderby=login&role=subscriber' );
            if(count($blogusers) > 0) {
                $table_name = $wpdb->prefix . 'jms_profile_field';
                $result = $wpdb->get_results("SELECT * FROM $table_name WHERE published=true ORDER BY weight DESC", ARRAY_A);
                if($wpdb->num_rows > 0) {
                    require_once(dirname(__FILE__)."/template/profile_new.php");
                } else {
                    echo "<div class=\"wrap\"><h1><a href=\"".$wp->request."admin.php?page=jms-patient-profile-sub1\" class=\"page-title-action\">".__('Please create profile field first!','jms-patient-profile')."</a></h1>";
                }
            } else {
                echo __('There is no user yet. Please create user before you can add profile.','jms-patient-profile');
            }
        } else if($_GET[ "action" ] == 'edit') {
            //show update form
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                $profileID = trim($_GET["id"]);
                $table_name = $wpdb->prefix . 'jms_patient_profile';
                $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id=".(int)$profileID, ARRAY_A);
                $blogusers = get_users( 'blog_id=1&orderby=login&role=subscriber' );
                $table_name = $wpdb->prefix . 'jms_profile_field';
                $fieldsInDB = $wpdb->get_results("SELECT * FROM $table_name WHERE published=true ORDER BY weight DESC", ARRAY_A);
                require_once(dirname(__FILE__)."/template/profile_edit.php");
            } else {
                echo __('Edit Profile, Something Wrong','jms-patient-profile');
            }
        } else if($_GET[ "action" ] == 'delete') {
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                if(isset($_GET["_wpnonce"]) && !empty(trim($_GET["_wpnonce"])) && wp_verify_nonce( trim($_GET["_wpnonce"]), 'delete-profile_'.trim($_GET["id"]) )) {
                    $profileID = trim($_GET["id"]);
                    $table_name = $wpdb->prefix . 'jms_patient_profile';
                    
                    $result = $wpdb->query( $wpdb->prepare( 
						"DELETE FROM $table_name WHERE `id` = %d",
                        array(
                            $profileID
                        )
					));
                    
                    if($result !== false) {
                        echo __('Delete profile successfully!','jms-patient-profile');
                    } else {
                        echo __('Delete Profile, DB operation failed!','jms-patient-profile');
                    }
                } else {
                    echo __('Delete, Something Wrong','jms-patient-profile');
                }
            } else {
                echo __('Delete, Something Wrong','jms-patient-profile');
            }
        } else {
            echo __( 'You do not have sufficient permissions to access this page.' );
        }
    } else {
        //show list
        $paged = 1;
        $numberOfRecord = 10;
        $searchTerm = "";
        if( isset($_GET[ "s" ]) ) {
            $searchTerm = trim($_GET["s"]);
        }

        $table_name = $wpdb->prefix . 'jms_patient_profile';
        $wpdb->show_errors( true );
        $sql = "SELECT count(*) AS total FROM $table_name";
        if($searchTerm != "") {
            $sql = "SELECT count(*) AS total FROM $table_name WHERE `title` LIKE '%".$searchTerm."%'";
        }
        $totalNumber = $wpdb->get_results($sql, OBJECT);
        $totalRecord = $totalNumber[0]->total;
        $totalPage = ceil($totalRecord / $numberOfRecord) ;

        if( isset($_GET[ "paged" ]) ) {
            $paged = (int)trim($_GET["paged"]);
            if($paged > $totalPage) {
                $paged = $totalPage > 0 ? $totalPage : 1;
            } else if($paged < 1) {
                $paged = 1;
            }
        }
        $startIndex = ($paged - 1) * $numberOfRecord;
        $sql = "SELECT * FROM $table_name ORDER BY `id` ASC LIMIT $startIndex,$numberOfRecord";
        if($searchTerm != "") {
            $sql = "SELECT * FROM $table_name WHERE `title` LIKE '%".$searchTerm."%' ORDER BY `id` ASC LIMIT $startIndex,$numberOfRecord";
        }
        $result = $wpdb->get_results($sql, ARRAY_A);
        //if($wpdb->num_rows > 0) {
            require_once(dirname(__FILE__)."/template/profile_list.php");
        //} else {
        //    echo "<div class=\"wrap\"><h1>".__('Profiles','jms-patient-profile')."<a href=\"".$wp->request."admin.php?page=jms-patient-profile-top&action=new\" class=\"page-title-action\">".__('New Profile','jms-patient-profile')."</a></h1>";
        //}
    }
}

function jmsPatientProfileAdminPageSub1() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    if( isset($_POST[ "action" ]) ) {
        //POST to create new
        if($_POST[ "action" ] == "new-save") {
            if(check_admin_referer( 'new_profile_field' )) {
                if(isset($_POST[ "itemname" ]) && !empty(trim($_POST[ "itemname" ]))
                    && isset($_POST[ "itemtype" ]) && !empty(trim($_POST[ "itemtype" ]))) {
                    
                    $itemName = trim($_POST[ "itemname" ]);
                    $itemDesc = trim($_POST[ "itemdesc" ]);
                    $itemType = (int)trim($_POST[ "itemtype" ]);
                    $itemOrder = 0;
                    if(isset($_POST[ "itemorder" ]) && !empty(trim($_POST[ "itemorder" ]))) {
                        $itemOrder = (int) trim($_POST[ "itemorder" ]);
                    }
                    $slug = createSlug($itemName);
                    
                    $table_name = $wpdb->prefix . 'jms_profile_field';
                    $sql = "SELECT * FROM $table_name WHERE slug=\"$slug\"";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    if($wpdb->num_rows > 0) {
                        if($result[0]["published"]) {
                            echo __('Profile field name already exists!','jms-patient-profile');
                        } else {
                            $result = $wpdb->query( $wpdb->prepare( 
                                "UPDATE $table_name SET `name`=\"%s\", `type`=%d, `weight`=%d, `update_date`=\"%s\", `published`=%d, `slug`=\"%s\", `description`=\"%s\" WHERE `id`=%d",
                                array(
                                    $itemName,
                                    $itemType,
                                    $itemOrder,
                                    current_time('mysql', 1),
                                    1,
                                    $slug,
                                    $itemDesc,
                                    (int)$result[0]["id"]
                                )
                            ));
                            
                            if($result !== false) {
                                echo "<h1>".__('Update profile field successfully!','jms-patient-profile')."</h1>";
                                $linkMsg = sprintf(__('<a href="%s">Back to Field List</a>', 'jms-patient-profile'),  $wp->request."admin.php?page=jms-patient-profile-sub1");
                                echo "<div>$linkMsg</div>";
                            } else {
                                echo "<h1>".__('DB operation failed!','jms-patient-profile')."</h1>";
                            }
                        }
                    } else {
                        $result = $wpdb->query($wpdb->prepare( 
                            "
                                INSERT INTO $table_name
                                ( name, type, weight, create_date, update_date, published, slug, description )
                                VALUES ( %s, %d, %d, %s, %s, %d, %s, %s)
                            ",
                            array(
                                $itemName,
                                $itemType,
                                $itemOrder,
                                current_time('mysql', 1),
                                current_time('mysql', 1),
                                true,
                                $slug,
                                $itemDesc
                            )
                        ));
                        
                        if($result !== false) {
                            $message = sprintf(__('Create a new profile field successfully! <a href="%s">Add New Field</a>','jms-patient-profile'), $wp->request."admin.php?page=jms-patient-profile-sub1&action=new");
                            echo "<h1>$message</h1>";
                        } else {
                            echo "<h1>".__('DB operation failed!','jms-patient-profile')."</h1>";
                        }
                    }
                } else {
                    echo __('Please make sure all necessary data is not empty!','jms-patient-profile');
                }
            } else {
                echo __( 'You do not have sufficient permissions to access this page.' );
            }
        } else if( $_POST[ "action" ] == "update-save" ){
            //post to update
            if(check_admin_referer( 'new_profile_field' )) {
                if(isset($_POST[ "itemname" ]) && !empty(trim($_POST[ "itemname" ]))
                    && isset($_POST[ "itemtype" ]) && !empty(trim($_POST[ "itemtype" ]))
                    && isset($_POST[ "id"]) && !empty(trim($_POST[ "id"]))) {
                    
                    $itemId = trim($_POST[ "id" ]);
                    $itemName = trim($_POST[ "itemname" ]);
                    $itemDesc = trim($_POST[ "itemdesc" ]);
                    $itemType = (int)trim($_POST[ "itemtype" ]);
                    $itemOrder = 0;
                    if(isset($_POST[ "itemorder" ]) && !empty(trim($_POST[ "itemorder" ]))) {
                        $itemOrder = (int) trim($_POST[ "itemorder" ]);
                    }

                    //update name will not update slug
                    /*
                    $slug = createSlug($itemName);
                    $table_name = $wpdb->prefix . 'jms_profile_field';
					$sql = "SELECT * FROM $table_name WHERE slug=\"$slug\"";
                    $result = $wpdb->get_results($sql, ARRAY_A);
                    if($wpdb->num_rows > 0 ) {
                        foreach ($result as $data) {
                            if($data["id"] != $itemId
                                && $result[0]["published"]) {
                                echo __('Profile field name already exists!','jms-patient-profile');
                                exit;   
                            }
                        }
                    }
                    */

                    $table_name = $wpdb->prefix . 'jms_profile_field';
                    $wpdb->show_errors(true);
					$result = $wpdb->query( $wpdb->prepare(
						"UPDATE $table_name SET `name`=\"%s\", `type`=%d, `weight`=%d, `update_date`=\"%s\", `published`=%d, `description`=\"%s\"  WHERE `id`=%d",
                        array(
                            $itemName, 
                            $itemType,
                            $itemOrder,
                            current_time('mysql', 1),
                            true,
                            $itemDesc,
                            $itemId
                        )
					));
					
					if($result !== false) {
                            echo "<h1>".__('Update profile field successfully!','jms-patient-profile')."</h1>";
                            $linkMsg = sprintf(__('<a href="%s">Back to Field List</a>', 'jms-patient-profile'),  $wp->request."admin.php?page=jms-patient-profile-sub1");
                            echo "<div>$linkMsg</div>";
                    } else {
                        echo __('DB operation failed!','jms-patient-profile');
                    }
                } else {
                    echo __('Please make sure all necessary data is not empty!','jms-patient-profile');
                }
            } else {
                echo __( 'You do not have sufficient permissions to access this page.' );
            }
        } else {
            echo __( 'You do not have sufficient permissions to access this page.' );
        }
    } else if( isset($_GET[ "action" ]) ) {
        //show new form
        if($_GET[ "action" ] == 'new') {
            require_once(dirname(__FILE__)."/template/profile_field_new.php");
        } else if($_GET[ "action" ] == 'edit') {
            //show update form
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                $fieldID = trim($_GET["id"]);
                $table_name = $wpdb->prefix . 'jms_profile_field';
                $result = $wpdb->get_results("SELECT * FROM $table_name WHERE published=true and id=".(int)$fieldID, ARRAY_A);
                require_once(dirname(__FILE__)."/template/profile_field_new.php");
            } else {
                echo __('Edit, Something Wrong','jms-patient-profile');
            }
        } else if($_GET[ "action" ] == 'delete') {
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
                if(isset($_GET["_wpnonce"]) && !empty(trim($_GET["_wpnonce"])) && wp_verify_nonce( trim($_GET["_wpnonce"]), 'delete-action_'.trim($_GET["id"]) )) {
                    $fieldID = trim($_GET["id"]);
                    $table_name = $wpdb->prefix . 'jms_profile_field';
                    $result = $wpdb->query( $wpdb->prepare( 
						"UPDATE $table_name SET `published`=false WHERE `id`=%d",
                        array(
                            $fieldID
                        )
					));
                    
                    if($result !== false) {
                        echo __('Delete field successfully!','jms-patient-profile');
                    } else {
                        echo __('DB operation failed!','jms-patient-profile');
                    }
                } else {
                    echo __('Delete, Something Wrong','jms-patient-profile');
                }
            } else {
                echo __('Delete, Something Wrong','jms-patient-profile');
            }
        } else {
            echo __( 'You do not have sufficient permissions to access this page.' );
        }
    } else {
        //show list
        $table_name = $wpdb->prefix . 'jms_profile_field';
        $wpdb->show_errors( true );
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE published=true ORDER BY weight DESC", ARRAY_A);
        
        if($wpdb->num_rows > 0) {
            require_once(dirname(__FILE__)."/template/profile_field_list.php");
        } else {
            echo "<div class=\"wrap\"><h1>".__('Profile Options','jms-patient-profile')."<a href=\"".$wp->request."admin.php?page=jms-patient-profile-sub1&action=new\" class=\"page-title-action\">".__('New Option','jms-patient-profile')."</a></h1>";
        }
    }
}

function createSlug($str) {
    //1. lower case
    $str = strtolower(trim($str));
    $subStr = explode(" ", $str);
    $slug = implode("_", $subStr);
    return $slug;
}

function showUnit($slug) {
    global $wpdb;
    $fieldTablename = $wpdb->prefix . 'jms_profile_field';
    $result = $wpdb->get_results("SELECT * FROM $fieldTablename WHERE slug=\"$slug\"", ARRAY_A);
    if($wpdb->num_rows > 0) {
        return $result[0]["description"];
    }
}
?>