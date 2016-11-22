<?php

//
// MIF BP Mentorship 
// Устранение проблем BuddyPress
//

defined( 'ABSPATH' ) || exit;


//
// Решение проблемы отсутсвия дочерних вкладок на странице друзей других пользователей портала
//
//

add_filter( 'bp_is_my_profile', 'mentorship_friends_nav_fix_filter', 10, 1 );

function mentorship_friends_nav_fix_filter( $ret )
{
    global $friends_nav_fix;

    if ( $friends_nav_fix ) {
        $ret = true;
        $friends_nav_fix = false;
    };

    return $ret;
}

add_action( 'get_template_part_members/single/friends', 'mentorship_friends_nav_fix_action' );

function mentorship_friends_nav_fix_action()
{
    global $friends_nav_fix;
    $friends_nav_fix= true;
}

?>