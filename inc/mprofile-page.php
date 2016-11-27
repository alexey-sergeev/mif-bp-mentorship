<?php

//
// MIF BP Mentorship 
// Работа с вкладкой дополнительных полей в профиле пользователя
//

defined( 'ABSPATH' ) || exit;


//
// Вкладка mprofile в профиле пользователя
//
// 

add_action( 'bp_setup_nav', 'mentorship_mprofile_page' );

function mentorship_mprofile_page()
{
    global $bp;

    $profile_link = $bp->displayed_user->domain . $bp->profile->slug . '/';

    bp_core_new_subnav_item( array(     'name' => __( 'М-профиль', 'mentorship' ), 
                                        'slug' => 'mprofile', 
                                        'parent_url' => $profile_link, 
                                        'parent_slug' => $bp->profile->slug, 
                                        'screen_function' => 'mprofile_page', 
                                        'position' => 10,
                                        'user_has_access' => mentorship_user_can( 'access_to_mprofile_page' ) 
                                    ) );



}

//
// Оформление вкладки mprofile
//
//

function mprofile_page()
{
    add_action( 'bp_template_title', function() { echo '<h2>' . __( 'М-профиль', 'mentorship' ) . '</h2>'; } );
    add_action( 'bp_template_content', 'mprofile_page_content' );

    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function mprofile_page_content()
{

    $out = '';

    if ( isset( $_REQUEST['action'] ) ) {  

        mprofile_page_content_helper();

        if ( $_REQUEST['action'] == 'mprofile_edit' ) 
        {
            echo get_mprofile_mprofile_edit_form();
            return;
        } 

        if ( $_REQUEST['action'] == 'verification_edit' ) 
        {
            echo get_mprofile_verification_edit_form();
            return;
        } 

        if ( $_REQUEST['action'] == 'verification_request' ) 
        {
            echo get_mprofile_verification_request_form();
            return;
        } 


    }

    $out .= '<div class="mentor-mprofile">';

    $out .= '<p>' . __( 'На этой странице представлены данные, необходимые для подтверждения вашей учетной записи. Информация доступна только вам и группе администраторов портала.', 'mentorship' ) . '</p>';

    $out .= mentorship_show_messages();    


    // --- Информация о пользователе --- //

    $out .= '<h3>' . __( 'Пользователь', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $mprofile_fields = mentorship_get_mprofile_fields();
    $mprofile_data = mentorship_get_mprofile_data();

    $n = 0;
    foreach ( (array) $mprofile_fields as $key => $item ) {

        if ( !$item['visible'] ) continue; // добавить проверку прав наставника - ему отображать

        $class_arr = array();
        $class_arr[] = ( $n % 2 == 1 ) ? 'alt' : '';
        $class = ( $class_arr ) ? ' class="' . implode( ' ', $class_arr ) . '"' : '';

        $value = ( isset( $mprofile_data[$key] ) ) ? $mprofile_data[$key] : '';  

        if ( $key == 'level' ) {

            $levels = mentorship_get_mprofile_levels();
            $value = ( isset( $levels[$value] ) ) ? $value : 'none';
            $value = $levels[$value]['descr'];

        }

        $out .= '<tr' . $class . '><td class="label">' . $item['descr'] . '</td><td class="data">' . $value . '</td></tr>'; 

        $n++;
    }

    $out .= '</tbody></table>';

    if ( true ) { // ## Кто должен видеть эту кнопку??
        
        $out .= '<p><a href="' . wp_nonce_url( '?action=mprofile_edit', 'mentorship_mprofile_edit' ) . '" class="button">' . __( 'Редактировать', 'mentorship' ) . '</a></p>';

    } 


    // --- Информация учётной записи --- //

    $out .= '<h3>' . __( 'Учётная запись', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $ud = bp_get_displayed_user()->userdata;

    $out .= '<tr><td class="label">' . __( 'Имя', 'mentorship' ) . '</td><td class="data">' . $ud->display_name . '</td></tr>';
    $out .= '<tr class="alt"><td class="label">' . __( 'Email', 'mentorship' ) . '</td><td class="data">' . $ud->user_email . '</td></tr>';
    $out .= '<tr><td class="label">' . __( 'Логин', 'mentorship' ) . '</td><td class="data">' . $ud->user_login . '</td></tr>';
    $out .= '<tr class="alt"><td class="label">' . __( 'Никнейм', 'mentorship' ) . '</td><td class="data">@' . $ud->user_nicename . '</td></tr>';
    $out .= '<tr><td class="label">' . __( 'Дата регистрации', 'mentorship' ) . '</td><td class="data">' . $ud->user_registered . '</td></tr>';

    $out .= '</tbody></table>';


    // --- Информация о приглашении --- //

    $out .= '<h3>' . __( 'Приглашение', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $invite_fields = mentorship_get_invite_fields();

    $n = 0;
    foreach ( (array) $invite_fields as $key => $item ) {

        $class_arr = array();
        $class_arr[] = ( $n % 2 == 1 ) ? 'alt' : '';
        $class = ( $class_arr ) ? ' class="' . implode( ' ', $class_arr ) . '"' : '';

        $out .= '<tr' . $class . '><td class="label">' . $item['descr'] . '</td><td class="data">' . $key . '</td></tr>'; 

        $n++;
    }

    $out .= '</tbody></table>';


    // --- Подтверждение учетной записи --- //

    $out .= '<h3>' . __( 'Подтверждение учётной записи', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $verification_fields = mentorship_get_verification_fields();
    $verification_data = mentorship_get_verification_data();
    $verifications = mentorship_get_verification_verifications();

    $n = 0;
    foreach ( (array) $verification_fields as $key => $item ) {

        $class_arr = array();
        $class_arr[] = ( $n % 2 == 1 ) ? 'alt' : '';
        $class = ( $class_arr ) ? ' class="' . implode( ' ', $class_arr ) . '"' : '';

        $value = $verification_data[$key];

        if ( $key == 'verification' ) $value = ( isset( $verifications[$value] ) ) ? $value : 'absent';
        if ( $key == 'verification' ) $value = $verifications[$value]['descr'];
        if ( $key == 'verifier' ) $value = ( isset( $value ) ) ? mentorship_get_avatar( $value, 25 ) : ''; 

        $out .= '<tr' . $class . '><td class="label">' . $item['descr'] . '</td><td class="data">' . $value . '</td></tr>'; 

        $n++;
    }

    $out .= '</tbody></table>';

    if ( true ) { // ## Кто должен видеть эту кнопку??
        
        $out .= '<p><a href="' . wp_nonce_url( '?action=verification_edit', 'mentorship_verification_edit' ) . '" class="button">' . __( 'Редактировать', 'mentorship' ) . '</a> ';

    } 

    if ( true ) { // ## Кто должен видеть эту кнопку??
        
        $out .= '<a href="' . wp_nonce_url( '?action=verification_request', 'mentorship_verification_request' ) . '" class="button">' . __( 'Заявка на подтверждение', 'mentorship' ) . '</a></p>';

    } 

    // ---  //



    $out .= '</div>';


    echo $out;

}


//
// Получить форму редактирования данных пользователя (mprofile)
//
//

function get_mprofile_mprofile_edit_form()
{
    $out = '';

    if ( false ) show_mprofile_page(); // ### Если нет прав, то не показывать форму

    $out .= '<div class="mentor-mprofile mprofilt-edit">
                <form action="' . get_mprofile_page_permalink() . '" method="post">
                <p></p><h3>' . __( 'Редактирование данных пользователя', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $mprofile_fields = mentorship_get_mprofile_fields();
    $mprofile_data = mentorship_get_mprofile_data();

    $n = 0;
    foreach ( (array) $mprofile_fields as $key => $item ) {

        // if ( $key == 'date' ) continue;
        if ( false && $key == 'comment' ) continue; // ### добавить проверку на наличие статуса подтвержденного пользователя

        $class_arr = array();
        $class_arr[] = ( $n % 2 == 1 ) ? 'alt' : '';
        $class = ( $class_arr ) ? ' class="' . implode( ' ', $class_arr ) . '"' : '';

        $ro = '';
        if ( $key == 'date' ) {

            $item['comment'] = __( 'Если сведения были актуальны на указанную дату, то нет необходимости их менять.', 'mentorship' );    
            $ro = ' readonly';
        
        }

        $value = ( isset( $mprofile_data[$key] ) ) ? $mprofile_data[$key] : '';  
        $input = '<input type="text" name="' . $key . '" value="' . $value . '"' . $ro . '>';

        if ( $key == 'level' ) {

            $levels = mentorship_get_mprofile_levels();

            foreach ( (array) $levels as $k => $o ) {
                $selected = ( $mprofile_data[$key] == $k ) ? ' selected' : ''; 
                $options[] = '<option value="' . $k . '"' . $selected . '>' . $o['descr'] . '</option>';
            }

            $input = '<select name="' . $key . '">' . implode( '', $options ) . '</select>';    
           
        }
    
        if ( $key == 'comment' ) {

            $input = '<textarea name="' . $key . '">' . $value . '</textarea>';    
           
        }
    
        // if ( $key == 'date' ) {

        //     $input = '<p class="field">' . $value . '</p><p class="comment">' . __( 'Если сведения были актуальны на указанную дату, то нет необходимости их менять.', 'mentorship' ) . '</p>';    
           
        // }
    
        $tr = '<tr' . $class . '><td class="label">' . $item['descr'] . '</td><td class="data"><p>' . $input . '</p><p class="comment">' . $item['comment'] . '</p></td></tr>'; 
        $out .= apply_filters( 'mif_bp_mentorship_get_mprofile_mprofile_edit_form_tr', $tr, $item, $value, $class );

        $n++;
    }

    $out = apply_filters( 'mif_bp_mentorship_get_mprofile_mprofile_edit_form_table', $out );

    $out .= '</tbody></table>';

    $out .= wp_nonce_field( "mentorship-mprofile-save", "_wpnonce", true, false );

    if ( mentorship_check_verification() ) $out .= '<p>' . __( 'Изменение фамилии, имени и других ваших сведений ведёт к потере статуса подтверждённого пользователя. Изменяйте эти сведения, если это действительно необходимо. Для повторного получения статуса подтвержденного пользователя вам потребуется отправить заявку.', 'mentorship' ) . '</p>';

    $out .= '<p>
                <input type="hidden" name="action" value="mprofile_save">
                <input type="submit" value="' . __( 'Сохранить', 'mentorship' ) . '">
                <input type="button" value="' . __( 'Вернуться не сохраняя', 'mentorship' ) . '" onclick="document.location.href = \'' . get_mprofile_page_permalink() . '\'">
            </p>';

    $out .= '</form></div>';

    return apply_filters( 'mif_bp_mentorship_get_mprofile_mprofile_edit_form', $out );
}



//
// Обработать и сохранить новые данные mprofile
//
//

function mprofile_page_content_helper()
{

    if ( !isset( $_REQUEST['action'] ) ) return false;

    if ( $_REQUEST['action'] == 'mprofile_save' ) {

        if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], "mentorship-mprofile-save" ) ) return false;

        return mentorship_set_mprofile_data( $_REQUEST );

    }

    if ( $_REQUEST['action'] == 'verification_save' ) {

        if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], "mentorship-verification-save" ) ) return false;

        return mentorship_set_verification_data( $_REQUEST );

    }

    if ( $_REQUEST['action'] == 'verification_send_request' ) {

        if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], "mentorship-verification-request" ) ) return false;

        return mentorship_verification_requesting( $_REQUEST['request_comment'] );

    }



}


?>
