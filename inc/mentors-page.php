<?php

//
// MIF BP Mentorship 
// Работа с вкладкой Montors в профиле пользователя
//

defined( 'ABSPATH' ) || exit;


//
// Новые вкладки на странице друзей
//
// 

add_action( 'bp_friends_setup_nav', 'mentorship_profile_page' );

function mentorship_profile_page()
{
    global $bp;
 
    $profile_link = $bp->displayed_user->domain . $bp->friends->slug . '/';

    bp_core_new_subnav_item( array(     'name' => __( 'Наставники', 'mentorship' ), 
                                        'slug' => 'mentors', 
                                        'parent_url' => $profile_link, 
                                        'parent_slug' => $bp->friends->slug, 
                                        'screen_function' => 'mentors_learners_page', 
                                        'position' => 10,
                                        'user_has_access' => mentorship_user_can( 'access_to_mentorship_mentors_page' ) 
                                    ) );


    bp_core_new_subnav_item( array(     'name' => __( 'Учащиеся', 'mentorship' ), 
                                        'slug' => 'learners', 
                                        'parent_url' => $profile_link, 
                                        'parent_slug' => $bp->friends->slug, 
                                        'screen_function' => 'mentors_learners_page', 
                                        'position' => 15,
                                        'user_has_access' => mentorship_user_can( 'access_to_mentorship_learners_page' ) 
                                    ) );

}


//
// Оформление вкладки учащихся или наставников
//
// 

function mentors_learners_page()
{
    global $bp;

    if ( ! ( $bp->current_action == 'mentors' || $bp->current_action == 'learners' ) ) return false; 

    $mode = $bp->current_action;

    // if ( isset( $_POST['add_members_list'] ) &&  wp_verify_nonce( $_POST['_wpnonce'], "mentorship_add_members" ) ) 
    //     mentorship_add_members_by_list( $bp->displayed_user->id, $_POST['add_members_list'], $mode );

    if ( $mode == 'mentors' ) {

        add_action( 'bp_template_title', function() { echo '<h2>' . __( 'Наставники', 'mentorship' ) . '</h2>'; }  );
        add_action( 'bp_template_content', function() { mentors_learners_page_content( 'mentors' ); }  );
        
    } elseif ( $mode == 'learners' ) {

        add_action( 'bp_template_title', function() { echo '<h2>' . __( 'Учащиеся', 'mentorship' ) . '</h2>'; }  );
        add_action( 'bp_template_content', function() { mentors_learners_page_content( 'learners' ); }  );

    }

    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

}

function mentors_learners_page_content( $mode )
{
    global $bp;

    $target_member = $bp->displayed_user->id;

    if ( $mode == 'mentors' ) {

        $members = mentorship_get_mentors( $target_member );

    } elseif ( $mode == 'learners' ) {

        $members = mentorship_get_learners( $target_member );

    }
    
    $members_param = array( 'include' => implode( $members, ',' ), 'user_id' => false );

    require( dirname( __FILE__ ) . '/../templates/members-loop.php' );
    
    echo get_mentors_learners_form ( $target_member, $mode );
}


//
// Создает форму для добавления наставников или учеников
//
//

function get_mentors_learners_form ( $target_user = false, $mode =  'mentors' )
{

    if ( $target_user === false ) return;

    $mode = ( $mode == 'mentors' ) ? 'mentors' : 'learners';

    $out = '';

    $btn1 = ( $mode == 'mentors' ) ? __( 'Добавить наставника', 'mentorship' ) : __( 'Добавить учащегося', 'mentorship' );
    $btn2 = __( 'Сохранить изменения', 'mentorship' );

    $msg1 = ( $mode == 'mentors' ) ? __( 'Укажите имена пользователей, которых вы хотите добавить в наставники', 'mentorship' ) : __( 'Укажите имена пользователей, которых вы хотите добавить в список учеников', 'mentorship' ); 

    $out .= '<div class="add-new-mentor-learner">
    <span class="center btn1"><a href="" class="button">' . $btn1 . '</a></span>
    <form method="POST">
    ';

    $out .= '<p>' . $msg1 . '</p>';
    $out .= '<div class="response-box"></div>';
    $out .= '<span><textarea name="add_list" placeholder="username"></textarea></span>
    <span><input type="submit" value="' . $btn2 . '"><span>
    ';
    
    $out .= wp_nonce_field( "mentorship-add-mentor-learner-nonce", "_wpnonce", true, false ); 
    $out .= '<input type="hidden" name="mode" value="' . $mode . '">'; 
    $out .= '<input type="hidden" name="target_user" value="' . $target_user . '">'; 

    $out .= '</form>
    </div>
    ';


    return $out;

}



//
// Добавляем кнопки наставников в списках пользователей
//
//

add_action( 'mentorship_members_actions', 'mentorship_add_members_button' );

function mentorship_add_members_button() 
{
    global $bp;

    $displayed_user_id = $bp->displayed_user->id;
    $list_user_id = bp_get_member_user_id();

    if ( ! $list_user_id || ! $displayed_user_id ) return;

    echo get_remove_mentor_button( $list_user_id, $displayed_user_id );

}

function get_remove_mentor_button( $mentor = false, $learner = false )
{

    if ( ! $mentor || ! $learner ) return '';

    $button = array(
        'id'                => 'remove_mentor',
        'component'         => 'activity',
        'must_be_logged_in' => true,
        'block_self'        => false,
        'button_attr'         => array( 'data-mentor' => $mentor, 'data-learner' => $learner, 'data-mode' => 'remove' ),        
        'wrapper_class'     => 'mentorship-button remove-mentor',
       'link_href'         => wp_nonce_url( bp_loggedin_user_domain() , 'mentorship-remove-mentor-nonce' ),
        'link_text'         => __( 'Удалить из наставников', 'mentorship' ),
        'link_class'        => 'mentorship-button remove-mentor'
    );
    
    return bp_get_button( $button );

}

function get_add_mentor_button( $mentor = false, $learner = false )
{

    if ( ! $mentor || ! $learner ) return '';
    if ( $mentor == $learner ) return '';

    $button = array(
        'id'                => 'add_mentor',
        'component'         => 'activity',
        'must_be_logged_in' => true,
        'block_self'        => false,
        'button_attr'         => array( 'data-mentor' => $mentor, 'data-learner' => $learner, 'data-mode' => 'add' ),        
        'wrapper_class'     => 'mentorship-button add-mentor',
       'link_href'         => wp_nonce_url( bp_loggedin_user_domain() , 'mentorship-add-mentor-nonce' ),
        'link_text'         => __( 'Добавить наставником', 'mentorship' ),
        'link_class'        => 'mentorship-button add-mentor'
    );
    
    return bp_get_button( $button );

}





?>
