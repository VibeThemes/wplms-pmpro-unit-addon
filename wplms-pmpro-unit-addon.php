<?php
/*
Plugin Name: Wplms pmpro unit addon
Plugin URI: http://www.VibeThemes.com
Description: This is the unit addon  by VibeThemes
Version: 1.0
License: (Themeforest License : http://themeforest.net/licenses)
Author: Mr.Vibe 
Author URI: http://www.VibeThemes.com
Network: true
*/

add_filter('wplms_unit_metabox','wplms_unit_show_pmpro_memberships');
function wplms_unit_show_pmpro_memberships($metabox_settings){
    $prefix = 'vibe_';
    if ( in_array( 'paid-memberships-pro/paid-memberships-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && function_exists('pmpro_getAllLevels')) { 
                $levels=pmpro_getAllLevels();
                foreach($levels as $level){
                    $level_array[]= array('value' =>$level->id,'label'=>$level->name);
                }
                $metabox_settings[$prefix.'pmpro_membership'] =array(
                        'label' => __('PMPro Membership','vibe-customtypes'), // <label>
                        'desc'  => __('Required Membership levls for this unit','wplms-pmpro-unit-addon'), // description
                        'id'    => $prefix.'pmpro_membership', // field id and name
                        'type'  => 'multiselect', // type of field
                        'options' => $level_array,
                    );
            }
            return $metabox_settings;
}
add_filter('the_content','wplms_unit_check_pmpro_membership',999);
function wplms_unit_check_pmpro_membership($content){
    global $post;

    if($post->post_type != 'unit' || !is_user_logged_in()){
        return $content;
    }

    $unit_id = $post->ID;
    $user_id = get_current_user_id();

    if ( in_array( 'paid-memberships-pro/paid-memberships-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && function_exists('pmpro_getAllLevels')) { 

        $membership_ids = get_post_meta($unit_id,'vibe_pmpro_membership',true);
            if(!empty($membership_ids) && count($membership_ids) >= 1){
                if(pmpro_hasMembershipLevel($membership_ids,$user_id)){
                    return $content;
                }else{
                    $levels=pmpro_getAllLevels($membership_ids);
                    foreach($levels as $level){
                        $level_array[$level->id]=$level->name;
                    }
                    $content = 'Please purchase membership plan ';
                }
            }
    }
    return $content;
}