<?php

//
// MIF BP Mentorship AJAX helper
//
//

defined( 'ABSPATH' ) || exit;

//
// Обработка запросов удаления или добавления наставника  
// remove_mentor
//

add_action('wp_ajax_add_remove_mentor', 'mentorship_ajax_add_remove_mentor');

function mentorship_ajax_add_remove_mentor()
{
    $nonce = $_POST['nonce'];
    $mentor = $_POST['mentor'];
    $learner = $_POST['learner'];
    $mode = $_POST['mode'];


    if ( $mode == 'remove' ) {

        if ( !wp_verify_nonce( $nonce, 'mentorship-remove-mentor-nonce' ) ) die ();

        if ( mentorship_remove_mentor( $learner, $mentor ) ) {
            echo get_add_mentor_button( $mentor, $learner );
        } 

    } elseif ( $mode == 'add' ) {

        if ( !wp_verify_nonce( $nonce, 'mentorship-add-mentor-nonce' ) ) die ();

        if ( mentorship_add_mentor( $learner, $mentor ) ) {
            echo get_remove_mentor_button( $mentor, $learner );
        } 

    } 

    wp_die(); 
}



//
// Обработка запросов добавления наставников
// add_mentors_learners_list
//

add_action('wp_ajax_add_mentors_learners_list', 'mentorship_ajax_add_mentors_learners_list');

function mentorship_ajax_add_mentors_learners_list()
{
    $nonce = $_POST['nonce'];
    $target_user = $_POST['target_user'];
    $add_list = $_POST['add_list'];
    $mode = $_POST['mode'];

    if ( !wp_verify_nonce( $nonce, 'mentorship-add-mentor-learner-nonce' ) ) {

        echo '<div id="message" class="error">';
        echo '<p>' . __( 'Ошибка', 'mentorship' ) . '</p>';
        echo '</div>';

        die ();
    }

    if ( $mode == 'mentors' ) {

        $result = mentorship_add_mentors_by_list( $target_user, $add_list );

        echo '<div id="message" class="info">';
        if ( $result[0] ) echo '<p>' . __( 'Успешно добавлены', 'mentorship' ) . ': <strong>' . implode( ', ', $result[0] ) . '</strong></p>';
        if ( $result[1] ) echo '<p>' . __( 'Уже были наставниками (не добавлены)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[1] ) . '</strong></p>';
        if ( $result[2] ) echo '<p>' . __( 'Сам себе наставник (не добавлен)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[2] ) . '</strong></p>';
        if ( $result[3] ) echo '<p>' . __( 'Пользователи не существуют', 'mentorship' ) . ': <strong>' . implode( ', ', $result[3] ) . '</strong></p>';
        echo '</div>';

    } elseif ( $mode == 'learners' ) {


    }

    wp_die(); 
}




?>