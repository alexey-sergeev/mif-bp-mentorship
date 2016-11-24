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
    //
    // Возвращает массив с id наставников для пользователя $user
    // (сырые данные, могут быть несуществующие пользователи)
    //

    if ( $user === false ) $user = get_current_user_id();
   	$user_id = is_object( $user ) ? $user->ID : absint( $user );
	if ( empty( $user_id ) ) return false; 

    $arr = get_user_meta( $user_id, 'mentorship' );

    return $arr;
}


//
// Получить список учащихся
//
//

function mentorship_get_learners( $user = false )
{
    //
    // Возвращает массив с id учащихся для пользователя $user
    // (сырые данные, могут быть несуществующие пользователи)
    //

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
// Проверить, является ли $user наставником у $target
//
//

function mentorship_is_mentor( $target = false, $user = false )
{
    //
    // Возвращает true или false
    //
    // Если $target не указан, то ничего не возвращает
    // Если не указан $user - берется текущий пользователь 
    //
    
    if ( $terget === false ) return;
    if ( $user === false ) $user = get_current_user_id();

    $arr = mentorship_get_mentors( $target );

    $ret = ( in_array( $user, (array)$arr ) ) ? true : false;

    return $ret;
}


//
// Проверить, является ли $user учащимся у $target
//
//

function mentorship_is_learner( $target = false, $user = false )
{
    //
    // Возвращает true или false
    //
    // Если $target не указан, то ничего не возвращает
    // Если не указан $user - берется текущий пользователь 
    //

    if ( $terget === false ) return;
    if ( $user === false ) $user = get_current_user_id();

    $arr = mentorship_get_learners( $target );

    $ret = ( in_array( $user, (array)$arr ) ) ? true : false;

    return $ret;
}


//
// Добавить наставника
//
//

function mentorship_add_mentor( $learner = false, $mentor = false )
{
    //
    // Возвращаемые значения: см. описание mentorship_add_member
    //

    return mentorship_add_member( $mentor, $learner );
}


//
// Удалить наставника
//
//

function mentorship_remove_mentor( $learner = false, $mentor = false )
{
    //
    // Возвращает: см. описание mentorship_remove_member
    //

     return mentorship_remove_member( $mentor, $learner, 'mentor' );
}


//
// Добавить учащегося
//
//

function mentorship_add_learner( $mentor = false, $learner = false )
{
    //
    // Возвращаемые значения: см. описание mentorship_add_member
    //

    return mentorship_add_member( $mentor, $learner );
}

//
// Удалить учащегося
//
//

function mentorship_remove_learner( $mentor = false, $learner = false )
{
    //
    // Возвращает: см. описание mentorship_remove_member
    //

    return mentorship_remove_member( $mentor, $learner );
}


//
// Добавить связь "наставник - учащийся"
//
//

function mentorship_add_member( $mentor = false, $learner = false )
{
    //
    // Возвращаемые значения:
    //      false - ошибка входных данных (не добавлено)      
    //      101 - учащийся добавлен
    //      102 - такой учащийся уже есть (второй раз не добавлен)
    //      103 - попытка добавить учащимся самому себе (не добавлен)
    //      104 - существует обратная связь (запрашиваемая не добавлена)
    //

    if ( !$mentor || !$learner ) return false;
    if ( $mentor == $learner ) return 103;
  
   	$learner_id = is_object( $learner ) ? $learner->ID : absint( $learner );
   	$mentor_id = is_object( $mentor ) ? $mentor->ID : absint( $mentor );

	if ( empty( $mentor_id ) || empty( $learner_id ) ) return false; 

    if ( mentorship_is_learner( $learner_id, $mentor_id ) ) return 104;
    if ( mentorship_is_mentor( $mentor_id, $learner_id ) ) return 104;

    $arr = mentorship_get_mentors( $learner_id ); 
    if ( in_array( $mentor_id, (array) $arr ) ) return 102;

    $arr = mentorship_get_learners( $mentor_id ); 
    if ( in_array( $learner_id, (array) $arr ) ) return 102; 

    $ret = add_user_meta( $learner_id, 'mentorship', $mentor_id );

    if ( !$ret ) return false;

    wp_cache_delete( $mentor_id, "mentorship_get_learners" );

    return 101;
}


//
// Удалить связь "наставник - учащийся"
//
//

function mentorship_remove_member( $mentor = false, $learner = false )
{

    //
    // Возвращает:
    //      false - проблема входных данных (ничего не изменено)
    //      true - пользователь удален (или такого наставника не было)
    //

    if (  !$mentor || !$learner ) return false;
   	$mentor_id = is_object( $mentor ) ? $mentor->ID : absint( $mentor );
   	$learner_id = is_object( $learner ) ? $learner->ID : absint( $learner );
	if ( empty( $mentor_id ) || empty( $learner_id ) ) return false; 

    $ret = delete_user_meta( $learner_id, 'mentorship', $mentor_id );
    wp_cache_delete( $mentor_id, "mentorship_get_learners" );

    return $ret;

}


//
// Добавить наставников или учеников списком
//
//

function mentorship_add_members_by_list( $target_member, $member_list, $mode = 'mentors' )
{
    //
    // Входные данные
    //      $target_member - кому добавляем
    //      $member_list - список добавляемых (логины через пробелы, запятые, точки с запятой, переводы строк)
    //      $mode - 'mentors' или 'learners' (добавляемые - наставники или учащиеся)
    //
    // Возвращает двумерный массив
    //      строка 0 - те пользователи, которые были успешно добавлены
    //      строка 1 - те пользователи, которые уже были в списке (не добавлены)
    //      строка 2 - сам себе наставник (сам себе учащийся) (не добавлен)
    //      строка 3 - пользователи, которые не существуют (не добавлены)
    //  

    $arr_out = array();
    $member_list = preg_replace( '/\@/', ' ', $member_list );
    $member_list = preg_replace( '/\s/', ',', $member_list );
    $member_list = preg_replace( '/;/', ',', $member_list );
    $arr = explode( ",", $member_list );
    
    foreach ( (array) $arr as $user_nicename ) {

        $ret = false;

        $user_nicename = sanitize_text_field( $user_nicename );
        $user_nicename = strim( $user_nicename );

        if ( $user_nicename == '' ) continue;

        $user = get_user_by( 'login', $user_nicename ); 

        if ( is_object( $user ) ) {  

            if ( $mode == 'mentors' ) {

                $ret = mentorship_add_mentor( $target_member, $user->ID );

            } elseif ( $mode == 'learners' ) {

                $ret = mentorship_add_learner( $target_member, $user->ID );

            }

        }
        
        $index = ( $ret ) ? $ret - 100 : 0;
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