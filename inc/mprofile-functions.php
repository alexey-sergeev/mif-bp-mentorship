<?php

//
// MIF BP Mentorship mprofile functions
// Функции системы mprofile
//

defined( 'ABSPATH' ) || exit;


//
// Определить список полей mprofile
// 
//

function mentorship_get_mprofile_fields()
{

    $arr = array(

        'surname' =>        array( 'descr' => __( 'Фамилия', 'mentorship' ),                 'comment' => __( 'Фамилия (обязательно)', 'mentorship' ),                              'visible' => true ),
        'name' =>           array( 'descr' => __( 'Имя', 'mentorship' ),                     'comment' => __( 'Имя (обязательно)', 'mentorship' ),                                  'visible' => true ),
        'middle_name' =>    array( 'descr' => __( 'Отчество', 'mentorship' ),                'comment' => __( 'Отчество', 'mentorship' ),                                           'visible' => true ),
        'institution' =>    array( 'descr' => __( 'Место учёбы (работы)', 'mentorship' ),    'comment' => __( 'Школа, вуз, факультет, группа и др. (обязательно)', 'mentorship' ),  'visible' => true ),
        'level' =>          array( 'descr' => __( 'Статус', 'mentorship' ),                  'comment' => '',                                                                       'visible' => true ),
        'date' =>           array( 'descr' => __( 'Дата заполнения', 'mentorship' ),         'comment' => '',                                                                       'visible' => true ),
        // 'comment' =>        array( 'descr' => __( 'Комментарий', 'mentorship' ),             'comment' => __( 'Изменение фамилии, имени и других ваших данных ведёт к потере статуса подтверждённого пользователя. Вы можете оставить пояснения администратору, почему информацию пришлось поменять. Ваш комментарий упростит повторное получение статуса подтвержденного пользователя.', 'mentorship' ),                      'visible' => true ), // false

    );

    return apply_filters( 'mif_bp_mentorship_get_mprofile_fields', $arr );

}


//
// Определить список возможных статусов пользователя
// 
//

function mentorship_get_mprofile_levels()
{

    $arr = array(

        'none' =>       array( 'descr' => __( 'Статус не указан', 'mentorship' ) ),
        'preschool' =>  array( 'descr' => __( 'Дошкольник', 'mentorship' ) ),
        'junior' =>     array( 'descr' => __( 'Младшая школа', 'mentorship' ) ),
        'basic' =>      array( 'descr' => __( 'Средняя школа', 'mentorship' ) ),
        'high' =>       array( 'descr' => __( 'Старшая школа', 'mentorship' ) ),
        'student' =>    array( 'descr' => __( 'Студент', 'mentorship' ) ),
        'graduate' =>   array( 'descr' => __( 'Выпускник', 'mentorship' ) )

    );

    return apply_filters( 'mif_bp_mentorship_get_mprofile_levels', $arr );

}


//
// Определить список полей приглашения 
// 
//

function mentorship_get_invite_fields()
{

    $arr = array(

        'invite' =>     array( 'descr' => __( 'Код приглашения', 'mentorship' ),               'visible' => true ),
        'issuer' =>     array( 'descr' => __( 'Кем выдано', 'mentorship' ),                    'visible' => false ),
        'date' =>       array( 'descr' => __( 'Дата выдачи', 'mentorship' ),                   'visible' => false ),
        'type' =>       array( 'descr' => __( 'Тип приглашения', 'mentorship' ),               'visible' => false ),  // персональное или групповое
        'level' =>      array( 'descr' => __( 'Статус по приглашению', 'mentorship' ),         'visible' => false )

    );

    return apply_filters( 'mif_bp_mentorship_get_invite_fields', $arr );

}


//
// Сохранить данные mprofile
//
//

function mentorship_set_mprofile_data( $mprofile_date = false, $user = false, $show_msg = true )
{
    //
    // $mprofile_date - массив с данными. См. mentorship_get_mprofile_fields()
    //
    // $user - пользователь, для которого надо сохранить данные (id или сам пользователь)
    // Если не указывается, то данные сохраняются для отображаемого пользователя
    //
    // $show_msg - true или false (выводить сообщения на экран, или нет)
    //
    // Возвращает:  - массив фактически сохранненых данных
    //              - false, если сохранить данные не удалось
    //

    if ( $mprofile_date === false ) return false;

    global $bp;
    if ( $user === false ) $user = $bp->displayed_user->id;
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    $arr = array();
    $arr_old = mentorship_get_mprofile_data( $user_id ); 
    $mprofile_fields = mentorship_get_mprofile_fields();
    $update_flag = false;

    foreach ( (array) $mprofile_fields as $key => $item ) {

        $arr[$key] = ( isset( $mprofile_date[$key] ) ) ? strim( $mprofile_date[$key] ) : '';  

        if ( !isset( $arr_old[$key] )) $arr_old[$key] = ''; 
        
        if ( in_array( $key, array( 'date', 'comment' ) ) ) continue;
        
        if ( $arr[$key] != $arr_old[$key] ) $update_flag = true;

    }

    $arr['date'] = (int) current_time('timestamp'); 
    $ret = update_user_meta( $user_id, 'mentorship_mprofile_data', $arr );

    if ( !$ret ) {

        if ( $show_msg ) mentorship_add_message( __( 'Информацию сохранить не удалось', 'mentorship' ), 'error' );
        return false;

    }

    if ( $update_flag ) {

        if ( mentorship_check_verification( $user ) ) {

            mentorship_set_verification_revoked( $user_id, false );
            if ( $show_msg ) mentorship_add_message( __( 'Снят статус подтверждённого пользователя', 'mentorship' ), 'warning', 10 );
            
        }

    } else {

        if ( $show_msg ) mentorship_add_message( __( 'Статус подтверждённого пользователя не изменился', 'mentorship' ), '', 10 );

    }

    if ( $show_msg ) mentorship_add_message( __( 'Информация успешно сохранена', 'mentorship' ) );

    return $arr;
}


//
// Получить данные mprofile
//
//

function mentorship_get_mprofile_data( $user = false )
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

    $arr = get_user_meta( $user_id, 'mentorship_mprofile_data', true );

    if ( isset( $arr['date'] ) ) $arr['date'] = mprofile_date_format( $arr['date'] );

    $ret = ( is_array( $arr ) ) ? $arr : false;

    return $ret;
}










//
// Переадресация на страницу mprofile пользователя
// 
//

function show_mprofile_page( $user = false )
{
    $profile_link = get_mprofile_page_permalink( $user );
    echo '<script>document.location.href = "' . $profile_link . '"</script>';
}


//
// Получить адрес страницы mprofile пользователя
// 
//

function get_mprofile_page_permalink( $user = false )
{
    global $bp;

    if ( $user === false )  {

        $profile_link = $bp->displayed_user->domain . $bp->profile->slug . '/mprofile/';

    } else {

        // Здесь ссылку на указанного пользователя. Но надо ли?

    }

    return $profile_link;
}


//
// Получить понятный формат даты для mprofile
// 
//

function mprofile_date_format( $timestamp )
{

    $format = 'Y-m-d H:i:s';
    return date( $format, (int) $timestamp );
    // return date_default_timezone_get();

}



?>