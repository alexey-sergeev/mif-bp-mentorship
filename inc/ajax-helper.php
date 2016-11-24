<?php

//
// MIF BP Mentorship AJAX helper
//
//

defined( 'ABSPATH' ) || exit;


//
// Обработка запросов удаления или добавления наставника или учащегося
// add_remove_member
//

add_action( 'wp_ajax_add_remove_member', 'mentorship_ajax_add_remove_member' );

function mentorship_ajax_add_remove_member()
{
    $nonce = $_POST['nonce'];
    $mentor = $_POST['mentor'];
    $learner = $_POST['learner'];
    $action_do = $_POST['action_do'];
    $mode = $_POST['mode'];

    if ( $action_do == 'remove' ) {

        if ( !wp_verify_nonce( $nonce, 'mentorship-remove-' . $mode . '-nonce' ) ) wp_die();

        if ( mentorship_remove_member( $mentor, $learner ) ) {
            echo get_member_button( $mentor, $learner, $mode, 'add' );
        } 

    } elseif ( $action_do == 'add' ) {

        if ( !wp_verify_nonce( $nonce, 'mentorship-add-' . $mode . '-nonce' ) ) wp_die();

        if ( mentorship_add_member( $mentor, $learner ) ) {
            echo get_member_button( $mentor, $learner, $mode, 'remove' );
        } 
    } 

    wp_die(); 
}


//
// Обработка запросов добавления учащихся или наставников списком
// add_mentors_learners_list
//

add_action( 'wp_ajax_add_mentors_learners_list', 'mentorship_ajax_add_mentors_learners_list' );

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

        $result = mentorship_add_members_by_list( $target_user, $add_list, 'mentors' );

        echo '<div id="message" class="info">';
        if ( $result[0] ) echo '<p>' . __( 'Пользователи не существуют', 'mentorship' ) . ': <strong>' . implode( ', ', $result[0] ) . '</strong></p>';
        if ( $result[1] ) echo '<p>' . __( 'Успешно добавлены', 'mentorship' ) . ': <strong>' . implode( ', ', $result[1] ) . '</strong></p>';
        if ( $result[2] ) echo '<p>' . __( 'Уже были наставниками (не добавлены)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[2] ) . '</strong></p>';
        if ( $result[3] ) echo '<p>' . __( 'Сам себе наставник (не добавлен)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[3] ) . '</strong></p>';
        if ( $result[4] ) echo '<p>' . __( 'Уже являются учащимися (не добавлены)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[4] ) . '</strong></p>';
        echo '</div>';

    } elseif ( $mode == 'learners' ) {

        $result = mentorship_add_members_by_list( $target_user, $add_list, 'learners' );

        echo '<div id="message" class="info">';
        if ( $result[0] ) echo '<p>' . __( 'Пользователи не существуют', 'mentorship' ) . ': <strong>' . implode( ', ', $result[0] ) . '</strong></p>';
        if ( $result[1] ) echo '<p>' . __( 'Успешно добавлены', 'mentorship' ) . ': <strong>' . implode( ', ', $result[1] ) . '</strong></p>';
        if ( $result[2] ) echo '<p>' . __( 'Уже были учащимися (не добавлены)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[2] ) . '</strong></p>';
        if ( $result[3] ) echo '<p>' . __( 'Сам себе учащийся (не добавлен)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[3] ) . '</strong></p>';
        if ( $result[4] ) echo '<p>' . __( 'Уже являются наставниками (не добавлены)', 'mentorship' ) . ': <strong>' . implode( ', ', $result[4] ) . '</strong></p>';
        echo '</div>';

    }

    wp_die(); 
}




?>