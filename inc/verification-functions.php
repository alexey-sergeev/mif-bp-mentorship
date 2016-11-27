<?php

//
// MIF BP Mentorship mprofile functions
// Функции системы верификации пользователей
//

defined( 'ABSPATH' ) || exit;


//
// Определить список полей верификации пользователя 
// 
//

function mentorship_get_verification_fields()
{

    $arr = array(

        'verification' =>   array( 'descr' => __( 'Статус подтверждения', 'mentorship' ),          'comment' => '',   'visible' => true ),
        'verifier' =>       array( 'descr' => __( 'Кем назначен статус', 'mentorship' ),           'comment' => '',                                     'visible' => false ),
        'date' =>           array( 'descr' => __( 'Дата получения статуса', 'mentorship' ),        'comment' => '',                                     'visible' => true ),
        'comment' =>        array( 'descr' => __( 'Комментарий', 'mentorship' ),                   'comment' => __( 'Вы можете оставить комментарий, чтобы пояснить проблемы, из-за которых учётную запись не удаётся подтвердить (отсутствие достоверных сведений о имени, фамилии, месте работы, учёбы и др.)', 'mentorship' ), 'visible' => true ),
        'log' =>            array( 'descr' => __( 'История подтверждений', 'mentorship' ),         'comment' => '',                                     'visible' => false ),

    );

    return apply_filters( 'mif_bp_mentorship_get_verification_fields', $arr );

}


//
// Определить список возможных статусов подтверждения
// 
//

function mentorship_get_verification_verifications()
{

    $arr = array(

        'absent' =>     array( 'descr' => __( 'Запись не подтверждена', 'mentorship' ),         'visible' => true ),
        'confirmed' =>  array( 'descr' => __( 'Запись подтверждена', 'mentorship' ),            'visible' => true ),
        'request' =>    array( 'descr' => __( 'Ожидается подтверждение заявки', 'mentorship' ), 'visible' => false ),
        'rejected' =>   array( 'descr' => __( 'Заявка отклонена', 'mentorship' ),               'visible' => true ),
        'revoked' =>    array( 'descr' => __( 'Подтверждение отозвано', 'mentorship' ),         'visible' => false ),

    );

    return apply_filters( 'mif_bp_mentorship_get_verification_verifications', $arr );

}


//
// Сохранить данные о подтверждении пользователя
//
//

function mentorship_set_verification_data( $verification_date = false, $user = false, $show_msg = true )
{
    //
    // $verification_date - массив с данными, см. mentorship_get_verification_verifications()
    //
    // $user - пользователь, для которого надо сохранить данные (id или сам пользователь)
    // Если не указывается, то данные сохраняются для отображаемого пользователя
    //
    // $show_msg - true или false (выводить сообщения на экран, или нет)
    //
    // Возвращает:  - массив фактически сохранненых данных
    //              - false, если сохранить данные не удалось
    //

    if ( $verification_date === false ) return false;

    global $bp;
    if ( $user === false ) $user = $bp->displayed_user->id;
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    $arr = array();
    $arr_old = mentorship_get_verification_data( $user_id ); 

    $verification_fields = mentorship_get_verification_fields();

    foreach ( (array) $verification_fields as $key => $item ) {

        $arr[$key] = ( isset( $verification_date[$key] ) ) ? strim( $verification_date[$key] ) : '';  
    
    }

    $arr['verifier'] = get_current_user_id(); 

    $arr['date'] = (int) current_time('timestamp'); 
    
    $vs = mentorship_get_verification_verifications();
    
    $log_old_arr = explode( "\n", $arr_old['log'] );
    array_splice( $log_old_arr, 9);
    $log_old = implode( "\n", (array) $log_old_arr );
    $log_new = ( $arr_old ) ? '<p>' . $arr_old['date'] . ' - ' . get_userdata( $arr_old['verifier'] )->user_nicename . ' - ' . $vs[$arr_old['verification']]['descr'] . ' - ' . $arr_old['comment'] . "\n" : ''; 

    $arr['log'] = $log_new . $log_old; 

    $ret = update_user_meta( $user_id, 'mentorship_verification_data', $arr );

    if ( !$ret ) {

        if ( $show_msg ) mentorship_add_message( __( 'Новый статус подтверждения сохранить не удалось', 'mentorship' ), 'error' );
        return false;

    }

    $cmnt = '';
    if ( $show_msg && $arr['verification'] == 'confirmed' ) $cmnt = ': ' . __( 'установлен статус подтверждённого пользователя', 'mentorship' );
    if ( $show_msg && $arr['verification'] == 'request' ) $cmnt = ': ' . __( 'отправлена заявка на подтверждение пользователя', 'mentorship' );
    if ( $show_msg && $arr['verification'] == 'rejected' ) $cmnt = ': ' . __( 'заявка отклонена', 'mentorship' );
    if ( $show_msg && $arr['verification'] == 'absent' ) $cmnt = ': ' . __( 'установлен статус неподтверждённого пользователя', 'mentorship' );
    if ( $show_msg && $arr['verification'] == 'revoked' ) $cmnt = ': ' . __( 'подтверждение пользователя отозвано', 'mentorship' );
    if ( $show_msg ) mentorship_add_message( __( 'Статус подтверждения успешно изменён', 'mentorship' ) . $cmnt );

    return $arr;
}


//
// Подтвердить пользователя
//
//

function mentorship_set_verification_confirmed( $user = false, $show_msg = true )
{

    return mentorship_set_verification_data( array( 'verification' => 'confirmed' ), $user, $show_msg );

}


//
// Отозвать подтверждение пользователя
//
//

function mentorship_set_verification_revoked( $user = false, $show_msg = true )
{

    return mentorship_set_verification_data( array( 'verification' => 'revoked' ), $user, $show_msg );

}


//
// Отклонить заявку пользователя на получения статуса подтвержденного
//
//

function mentorship_set_verification_rejected( $user = false, $show_msg = true )
{

    return mentorship_set_verification_data( array( 'verification' => 'rejected' ), $user, $show_msg );

}


//
// Отправить заявку на подтверждение пользователя
//
//

function mentorship_verification_requesting( $comment = '', $user = false, $show_msg = true )
{
    //
    // $comment - комментарий пользователя с дополнительной информацией наставнику
    //
    // $user - пользователь, для которого отправляется заявка (id или сам пользователь)
    // Если не указывается, то заявка отправляется для отображаемого пользователя
    //
    // $show_msg - true или false (выводить сообщения на экран, или нет)
    //
    // Возвращает:  - массив фактически сохранненых данных
    //              - false, если сохранить данные не удалось
    //

    $comment = strim( $comment );

    $ret = mentorship_set_verification_data( array( 'verification' => 'request', 'comment' => $comment ), $user, $show_msg );
    
    do_action( 'mif_bp_mentorship_verification_requesting', $ret, $show_msg );

    return $ret;

}


//
// Получить данные о подтверждении пользователя
//
//

function mentorship_get_verification_data( $user = false )
{
    //
    // $user - пользователь, для которого надо получить данные (id или сам пользователь)
    // Если не указывается, то данные берутся для отображаемого пользователя
    //
    // Возвращает:  - массив с данными
    //              - false, если получить данные не удалось
    //

    global $bp;
    if ( $user === false ) $user = $bp->displayed_user->id;
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    $arr = get_user_meta( $user_id, 'mentorship_verification_data', true );

    if ( isset( $arr['date'] ) ) $arr['date'] = mprofile_date_format( $arr['date'] );

    $ret = ( is_array( $arr ) ) ? $arr : false;

    return $ret;
}


//
// Проверить наличие статуса подтвержденного пользователя
//
//

function mentorship_check_verification( $user = false )
{

    $data = mentorship_get_verification_data( $user );


    $ret = false;

    if ( isset( $data['verification'] ) )
        if ( $data['verification'] == 'confirmed' ) 
            $ret = true;

    return $ret;

}

?>