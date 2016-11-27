<?php

//
// MIF BP Mentorship 
// Работа со страницами верификации пользователей в профиле пользователя
// Отдельные средства верификации, объединенные с mprofile - см. mprofile-page.php
//

defined( 'ABSPATH' ) || exit;



//
// Получить форму изменения статуса подтверждения пользователя
//
//

function get_mprofile_verification_edit_form()
{
    $out = '';

    if ( false ) show_mprofile_page(); // ### Если нет прав, то не показывать форму

    $out .= '<div class="mentor-mprofile verification-edit">
                <form action="' . get_mprofile_page_permalink() . '" method="post">
                <p></p><h3>' . __( 'Изменение статуса подтверждения пользователя', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';

    $verification_fields = mentorship_get_verification_fields();
    $verification_data = mentorship_get_verification_data();

    // --- Выпадающий список статусов --- // 

    $item = $verification_fields['verification'];
    $v_status = ( isset( $verification_data['verification'] ) ) ? $verification_data['verification'] : 'absent';
    
    $verifications = mentorship_get_verification_verifications();

    $input = '<select name="verification">';

    foreach ( (array) $verifications as $key => $v ) {

        if ( !$v['visible'] ) continue;

        $selected = ( $key == $v_status ) ? ' selected' : ''; 
        $input .= '<option value="' . $key . '"' . $selected . '>' . $v['descr'] . '</option>';

    }

    $input .= '</select>';

    $out .= '<tr><td class="label">' . $item['descr'] . '</td><td class="data"><p>' . $input . '</p><p class="comment">' . $item['comment'] . '</p></td></tr>';

    // --- Комментарий --- // 

    $item = $verification_fields['comment'];

    $input = '<textarea name="comment"></textarea>';

    $out .= '<tr><td class="label">' . $item['descr'] . '</td><td class="data"><p>' . $input . '</p><p class="comment">' . $item['comment'] . '</p></td></tr>';

    // --- //

    $out = apply_filters( 'mif_bp_mentorship_get_mprofile_verification_edit_form_table', $out );

    $out .= '</tbody></table>';

    $out .= wp_nonce_field( "mentorship-verification-save", "_wpnonce", true, false );

    $out .= '<p>
                <input type="hidden" name="action" value="verification_save">
                <input type="submit" value="' . __( 'Сохранить', 'mentorship' ) . '">
                <input type="button" value="' . __( 'Вернуться не сохраняя', 'mentorship' ) . '" onclick="document.location.href = \'' . get_mprofile_page_permalink() . '\'">
            </p>';

    $out .= '</form></div>';

    return apply_filters( 'mif_bp_mentorship_get_mprofile_verification_edit_form', $out );;
}


//
// Получить форму отправки заявки на подтверждение пользователя
//
//

function get_mprofile_verification_request_form()
{
    $out = '';

    if ( false ) show_mprofile_page(); // ### Если нет прав, то не показывать форму

    $out .= '<div class="mentor-mprofile verification-edit">
                <form action="' . get_mprofile_page_permalink() . '" method="post">
                <p></p><h3>' . __( 'Заявка на подтверждение пользователя', 'mentorship' ) . '</h3>
                <table class="profile-fields">
    			<tbody>';


    $ud = bp_get_displayed_user()->userdata;
    $mf = mentorship_get_mprofile_fields();
    $md = mentorship_get_mprofile_data();
    $levels = mentorship_get_mprofile_levels();
    $vf = mentorship_get_verification_fields();
    $vd = mentorship_get_verification_data();
    $verifications = mentorship_get_verification_verifications();
    $v_status = ( isset( $vd['verification'] ) ) ? $vd['verification'] : 'absent';

    $out .= '<tr><td class="label">' . $mf['surname']['descr'] . '</td><td class="data">' . $md['surname'] . '</td></tr>';
    $out .= '<tr class="alt"><td class="label">' . $mf['name']['descr'] . '</td><td class="data">' . $md['name'] . '</td></tr>';
    $out .= '<tr><td class="label">' . $mf['middle_name']['descr'] . '</td><td class="data">' . $md['middle_name'] . '</td></tr>';

    $out .= '<tr class="alt"><td class="label">' . __( 'Логин', 'mentorship' ) . '</td><td class="data">' . $ud->user_login . '</td></tr>';
    $out .= '<tr><td class="label">' . __( 'Email', 'mentorship' ) . '</td><td class="data">' . $ud->user_email . '</td></tr>';

    $out .= '<tr class="alt"><td class="label">' . $mf['institution']['descr'] . '</td><td class="data">' . $md['institution'] . '</td></tr>';
    $out .= '<tr><td class="label">' . $mf['level']['descr'] . '</td><td class="data">' . $levels[$md['level']]['descr'] . '</td></tr>';

    $out .= '<tr class="alt"><td class="label">' . $vf['verification']['descr'] . '</td><td class="data"><p>' . $verifications[$v_status]['descr'] . '</p></td></tr>';
    $out .= '<tr><td class="label">' . $vf['comment']['descr'] . '</td><td class="data"><p>' . $vd['comment'] . '</p></td></tr>';


    $input = '<textarea name="request_comment"></textarea>';
    $comment = __( 'Вы можете оставить комментарий администраторам портала. Сообщите дополнительную информацию, которая поможет назначить вам статус подтверждённого пользователя', 'mentorship' );
    $out .= '<tr class="alt"><td class="label"></td><td class="data"><p>' . $input . '</p><p class="comment">' . $comment . '</p></td></tr>';

    $out = apply_filters( 'mif_bp_mentorship_get_mprofile_verification_request_form_table', $out );

    $out .= '</tbody></table>';

    $out .= wp_nonce_field( "mentorship-verification-request", "_wpnonce", true, false );

    $out .= '<p>
                <input type="hidden" name="action" value="verification_send_request">
                <input type="submit" value="' . __( 'Отправить заявку', 'mentorship' ) . '">
                <input type="button" value="' . __( 'Вернуться не отправляя', 'mentorship' ) . '" onclick="document.location.href = \'' . get_mprofile_page_permalink() . '\'">
            </p>';

    $out .= '</form></div>';

    return apply_filters( 'mif_bp_mentorship_get_mprofile_verification_request_form', $out );;
}


?>
