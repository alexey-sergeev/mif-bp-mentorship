<?php

//
// MIF BP Mentorship functions
//
//

defined( 'ABSPATH' ) || exit;



//
// Проверка прав пользователя
//
//

function mentorship_user_can()
{
    //
    // access_to_mentorship_profile_page

    return true;
}







//
// Формирует блок сообщения пользователю
//
//

function mentorship_message( $msg = '', $type = false )
{

    if ( $msg == '' ) return;

    $classes = array( 'message' );
    if ( $type ) $classes[] = $type;

    return '<div class="' . implode( ' ', $classes ) . '">' . $msg . '</div>';

}


//
// Добавляет сообщение к глобальному массиву 
//
//

function mentorship_add_message( $msg = '', $type = '', $rank = 0 )
{
    global $mentorship_messages;

    if ( $msg == '' ) return false;

    $mentorship_messages[] = array( 'msg' => $msg, 'type' => $type, 'rank' => $rank );

    return true;
}


//
// Показывает все сообщения
//
//

function mentorship_show_messages()
{
    global $mentorship_messages;

    if ( !is_array( $mentorship_messages ) ) return;

    $out = '';

    // p($mentorship_messages);
    // ksort( $mentorship_messages );
    // p($mentorship_messages);

    $index = array();

    foreach ( $mentorship_messages as $key => $item ) {

        $index[$item['rank']][] = $key;

    } 

    ksort( $index );

    foreach ( $index as $ranks ) 
        foreach ( (array) $ranks as $key ) { 

        $out .= mentorship_message( $mentorship_messages[$key]['msg'], $mentorship_messages[$key]['type'] ) . "\n";

    }

    return $out;
}



//
// Получить аватар для указания пользователя
//
//

function mentorship_get_avatar( $user = false, $size = 50, $nicename_show = true )
{
    global $bp;
    if ( $user === false ) $user = $bp->displayed_user->id;
    if ( !is_object( $user ) ) $user = get_userdata( $user ); 
    if ( empty( $user ) ) return false; 

    $out = '';
    
    $out .= '<a href="' . bp_core_get_user_domain( $user->ID ) . '">';

    $out .= get_avatar( $user->ID, $size );

    if ( $nicename_show ) $out .= ' ' . $user->user_nicename;

    $out .= '</a>';

    return $out;
}





//
// Удаляет двойные пробелы, а также пробелы в начале и в конце строки
//
//

if ( ! function_exists( 'strim' ) ) {

    function strim( $st = '' )
    {
        $st = preg_replace( '/\s+/', ' ', $st );
        $st = trim( $st );
        
        return $st;
    }

}

//
// Вывод отладочной информации
//
//

function p( $data )
{
    print_r( '<pre>' );
    print_r( $data );
    print_r( '</pre>' );
}


?>