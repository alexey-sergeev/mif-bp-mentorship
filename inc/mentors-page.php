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
                                        'screen_function' => 'mentors_page', 
                                        'position' => 10,
                                        'user_has_access' => mentorship_user_can( 'access_to_mentorship_mentors_page' ) 
                                    ) );


    bp_core_new_subnav_item( array(     'name' => __( 'Учащиеся', 'mentorship' ), 
                                        'slug' => 'learners', 
                                        'parent_url' => $profile_link, 
                                        'parent_slug' => $bp->friends->slug, 
                                        'screen_function' => 'learners_page', 
                                        'position' => 15,
                                        'user_has_access' => mentorship_user_can( 'access_to_mentorship_learners_page' ) 
                                    ) );

}

//
// Оформление вкладки учеников
//
// 

function learners_page()
{
    global $bp;

    if ( isset( $_POST['add_mentors_list'] ) &&  wp_verify_nonce( $_POST['_wpnonce'], "mentorship_add_mentors" ) ) 
        mentorship_add_mentors_by_list( $bp->displayed_user->id, $_POST['add_mentors_list'] );

    add_action( 'bp_template_title', 'learners_page_title' );
    add_action( 'bp_template_content', 'learners_page_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

}

function learners_page_title()
{
    echo '<h2>' . __( 'Ученики', 'mentorship' ) . '</h2>';
}

function learners_page_content()
{
    global $bp;
    $mentor = $bp->displayed_user->id;

    $learners = mentorship_get_learners( $mentor );

    $learners_param = array( 'include' => implode( $learners, ',' ) );

    require( dirname( __FILE__ ) . '/../templates/mentors-loop.php' );
    
    echo get_mentors_learner_form ( $mentor, 'learners' );

}




//
// Оформление вкладки наставников
//
// 

function mentors_page()
{
    global $bp;

    if ( isset( $_POST['add_mentors_list'] ) &&  wp_verify_nonce( $_POST['_wpnonce'], "mentorship_add_mentors" ) ) 
        mentorship_add_mentors_by_list( $bp->displayed_user->id, $_POST['add_mentors_list'] );

    add_action( 'bp_template_title', 'mentors_page_title' );
    add_action( 'bp_template_content', 'mentors_page_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

}

function mentors_page_title()
{
    echo '<h2>' . __( 'Наставники', 'mentorship' ) . '</h2>';
}

function mentors_page_content()
{
    global $bp;
    $learner = $bp->displayed_user->id;

    $mentors = mentorship_get_mentors( $learner );

    $mentors_param = array( 'include' => implode( $mentors, ',' ) );

    require( dirname( __FILE__ ) . '/../templates/mentors-loop.php' );
    
    echo get_mentors_learner_form ( $learner, 'mentors' );

}


//
// Создает форму для добавления наставников или учеников
//
//

function get_mentors_learner_form ( $target_user = false, $mode =  'mentors' )
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
