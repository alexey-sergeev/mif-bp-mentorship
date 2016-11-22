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
// Получить список наставников
//
//

function mentorship_get_mentors( $user = false )
{
    if ( $user === false ) $user = get_current_user_id();
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    $arr = get_user_meta( $user_id, 'mentorship' );

    return $arr;
}


//
// Получить список учеников
//
//

function mentorship_get_learners( $user = false )
{
    if ( $user === false ) $user = get_current_user_id();
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    global $wpdb;

    if ( ! $arr = wp_cache_get( $user_id, "mentorship_get_learners") ) {
        
        $table = _get_meta_table( 'user' );
        $arr = $wpdb->get_results( "SELECT user_id FROM $table WHERE meta_key='mentorship' AND meta_value=$user_id", ARRAY_A );
        wp_cache_add( $user_id, $arr, "mentorship_get_learners");

    }

    $arr_out = array();
    foreach ( (array) $arr as $item ) $arr_out[] = $item['user_id'];   

    return $arr_out;
}


//
// Добавить наставника
//
//

function mentorship_add_mentor( $learner = false, $mentor = false )
{
    //
    // Возвращаемые значения:
    //      false - ошибка входных данных (не добавлено)      
    //      100 - наставник добавлен
    //      101 - такой наставник уже есть (второй раз не добавлен)
    //      102 - попытка добавить наставником самому себе (не добавлен)
    //

    if ( !$learner || !$mentor ) return false;
    if ( $learner == $mentor ) return 102;
  
   	$learner_id = is_object( $learner ) ? $learner->ID : absint( $learner );
   	$mentor_id = is_object( $mentor ) ? $mentor->ID : absint( $mentor );

	if ( empty( $learner_id ) || empty( $mentor_id ) ) return false; 

    $arr = get_user_meta( $learner_id, 'mentorship' );

    if ( in_array( $mentor_id, (array) $arr ) ) return 101; 

    add_user_meta( $learner_id, 'mentorship', $mentor_id );
    wp_cache_delete( $mentor_id, "mentorship_get_learners");

    return 100;
}

//
// Удалить наставника
//
//

function mentorship_remove_mentor( $learner = false, $mentor = false )
{
    //
    // Возвращает:
    //      false - проблема входных данных (ничего не изменено)
    //      true - пользователь удален (или такого наставника не было)
    //

    if ( !$learner || !$mentor ) return false;
   	$learner_id = is_object( $learner ) ? $learner->ID : absint( $learner );
   	$mentor_id = is_object( $mentor ) ? $mentor->ID : absint( $mentor );
	if ( empty( $learner_id ) || empty( $mentor_id ) ) return false; 

    delete_user_meta( $learner_id, 'mentorship', $mentor_id );
    wp_cache_delete( $mentor_id, "mentorship_get_learners");

    return true;
}

//
// Добавить наставников списком
//
//

function mentorship_add_mentors_by_list( $learner, $mentor_list )
{

    //
    // Возвращает двумерный массив
    //      строка 0 - те пользователи, которые были успешно добавлены
    //      строка 1 - те пользователи, которые уже были наставниками (не добавлены)
    //      строка 2 - сам себе наставник (не добавлен)
    //      строка 3 - пользователи, которые не существуют (не добавлены)
    //  

    $arr_out = array();
    $mentor_list = preg_replace( '/\@/', ' ', $mentor_list );
    $mentor_list = preg_replace( '/\s/', ',', $mentor_list );
    // $mentor_list = preg_replace( '/\n/', ',', $mentor_list );
    $mentor_list = preg_replace( '/;/', ',', $mentor_list );
    $arr = explode( ",", $mentor_list );
    
    foreach ( (array) $arr as $user_nicename ) {

        $ret = false;

        $user_nicename = sanitize_text_field( $user_nicename );
        $user_nicename = strim( $user_nicename );

        if ( $user_nicename == '' ) continue;

        $user = get_user_by( 'login', $user_nicename ); 
        if ( is_object( $user ) ) $ret = mentorship_add_mentor( $learner, $user->ID );
        
        $index = ( $ret ) ? $ret - 100 : 3;
        $arr_out[$index][] = $user_nicename;

    }

    return $arr_out;
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