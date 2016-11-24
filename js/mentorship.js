jq( document ).ready( function() {

	//
	// Меняем кнопку "Добавить наставника (ученика)" на форму добавления
	//

	jq( '.add-new-mentor-learner .btn1 .button' ).on( 'click', function() {

		jq( '.add-new-mentor-learner .btn1' ).slideUp( 200, function() { jq( '.add-new-mentor-learner form' ).slideDown( 200 ) } );		

		return false;
	} );


	//
	// Отправляем данные о новых наставниках (учениках)
	//

	jq( '.add-new-mentor-learner input[type=submit]' ).on('click', function() {

        // nonce = jq( '.add-new-mentor-learner #_wpnonce' ).attr('value');
        // target_user = jq( '.add-new-mentor-learner #target_user' ).attr('value');
        // // add_list = jq( '.add-new-mentor-learner #add_list' ).val();
		nonce = jq( this ).parent().parent().find( "[name='_wpnonce']" ).attr('value');
		target_user = jq( this ).parent().parent().find( "[name='target_user']" ).attr('value');
		mode = jq( this ).parent().parent().find( "[name='mode']" ).attr('value');
		add_list = jq( this ).parent().parent().find( "[name='add_list']" ).val();
		response_box = jq( this ).parent().parent().find( ".response-box" );
		time = 0;

		jq.post( ajaxurl, {
			action: 'add_mentors_learners_list',
			nonce: nonce,
			target_user: target_user,
			mode: mode,
			add_list: add_list
			// mentor: mentor,
			// learner: learner
		},
		function( response ) {

			response_box.slideUp( time, function() { response_box.slideDown(200).html( response ) } );
			time = 200;

			// response_box.slideDown(200).html( response );
			// alert( response );

		} );	

		return false;
	} );


	//
	// Удаляем или добавляем наставника в списке наставников в профиле ученика
	//

	jq( '#mentors-dir-list' ).on('click', '.mentorship-button a', function() {

        nonce = jq( this ).attr( 'href' );
		mentor = jq( this ).attr( 'data-mentor' );
		learner = jq( this ).attr( 'data-learner' );
		action_do = jq( this ).attr( 'data-action-do' );
		mode = jq( this ).attr( 'data-mode' );
		button = jq( this ).parent().parent();

		nonce = nonce.split('?_wpnonce=');
		nonce = nonce[1].split('&');
		nonce = nonce[0];

		jq.post( ajaxurl, {
			action: 'add_remove_member',
			action_do: action_do,
			nonce: nonce,
			mentor: mentor,
			learner: learner,
			mode: mode,
		},
		function( response ) {
			button.fadeOut( 200, function() { button.fadeIn(200).html( response ) } );
		});
		return false;
	} );
});
