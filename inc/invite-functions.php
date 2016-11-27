<?php

//
// MIF BP Mentorship invite functions
// 
//

defined( 'ABSPATH' ) || exit;


//
// Регистрируем тип записей invite
//
//

add_action( 'init', 'mentorship_register_invites_post_type' );

function mentorship_register_invites_post_type()
{

    // --- Новый тип записей - приглашения --- //

	register_post_type( 'invite', array(
		'label'  => null,
		'labels' => array(
			'name'               => __( 'Приглашения', 'mentorship' ),                  // основное название для типа записи
			'singular_name'      => __( 'Приглашение', 'mentorship' ),                  // название для одной записи этого типа
			'add_new'            => __( 'Добавить приглашение', 'mentorship' ),         // для добавления новой записи
			'add_new_item'       => __( 'Добавление приглашение', 'mentorship' ),       // заголовка у вновь создаваемой записи в админ-панели.
			'edit_item'          => __( 'Редактирование приглашения', 'mentorship' ),   // для редактирования типа записи
			'new_item'           => __( 'Новое приглашение', 'mentorship' ),            // текст новой записи
			'view_item'          => __( 'Смотреть приглашение', 'mentorship' ),         // для просмотра записи этого типа.
			'search_items'       => __( 'Искать приглашения', 'mentorship' ),           // для поиска по этим типам записи
			'not_found'          => __( 'Не найдено', 'mentorship' ),                   // если в результате поиска ничего не было найдено
			'not_found_in_trash' => __( 'Не найдено в корзине', 'mentorship' ),         // если не было найдено в корзине
		),
		'description'         => '',
		'public'              => true,      // Глобальная настройка публичности
		'publicly_queryable'  => true,      // Запросы относящиеся к этому типу записей будут работать во фронтэнде (в шаблоне сайта)
		'exclude_from_search' => true,      // Исключить этот тип записей из поиска по сайту
		'show_ui'             => true,      // Показывать ли меню в админ-панели 
		'show_in_menu'        => true,      // Показывать ли меню в админ-панели администратора
		'show_in_admin_bar'   => false,     // Сделать доступным из админ бара
		'show_in_nav_menus'   => false,     // Возможность выбирать этот тип записи в меню навигации 
		'menu_position'       => 30,        // Позиция в меню      
		'menu_icon'           => 'dashicons-tickets',       
		//'capability_type'   => 'post',
		//'capabilities'      => 'post',
		//'map_meta_cap'      => null,
		'hierarchical'        => false,     // Является ли иерархическим типом
		'supports'            => array( 'title', 'editor', 'custom-fields' ), 
                                            // Список полей, доступных в админ-панели для редактирования
                                            // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
		'taxonomies'          => array(),
		'has_archive'         => false,     // Поддвержка архивов
		'rewrite'             => true,      // Использовать ЧПУ
		'query_var'           => true,      // Возможность делать запросы
	) );


    // --- Рубрики для типа записей - приглашения --- //

	register_taxonomy( 'invite_categories', array( 'invite' ), array(
		// 'label'                 => '', // определяется параметром $labels->name
		'labels'                => array(
            'name'              => __( 'Типы приглашений', 'mentorship' ),
            'singular_name'     => __( 'Типы приглашений', 'mentorship' ),
            'search_items'      => __( 'Типы приглашений', 'mentorship' ),
            'all_items'         => __( 'Все типы', 'mentorship' ),
            'parent_item'       => __( 'Родительский тип приглашения', 'mentorship' ),
            'parent_item_colon' => __( 'Родительские типы приглашений:', 'mentorship' ),
            'edit_item'         => __( 'Редактировать типы приглашений', 'mentorship' ),
            'update_item'       => __( 'Обновить типы приглашений', 'mentorship' ),
            'add_new_item'      => __( 'Добавить новый тип', 'mentorship' ),
            'new_item_name'     => __( 'Новое имя типа приглашений', 'mentorship' ),
            'menu_name'         => __( 'Типы приглашений', 'mentorship' ),
        ),
		// 'description'           => '',   // описание таксономии
		'public'                => true,
		'publicly_queryable'    => true,    // Есть ли доступ к элементам таксономии во внешней части сайта
		'show_in_nav_menus'     => true,    // Возможность выбора в навигационном меню
		'show_ui'               => true,    // Показывать блок управления этой таксономией в админке.
		'show_tagcloud'         => false,   // Создать облако элементов этой таксономии
		'hierarchical'          => true,    // Таксономия будет древовидная
		// 'update_count_callback' => '',
		// 'rewrite'               => true,
		//'query_var'             => $taxonomy, // название параметра запроса
		// 'capabilities'          => array(),
		'meta_box_cb'           => 'post_categories_meta_box', // post_categories_meta_box или post_tags_meta_box
		// 'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
		// '_builtin'              => false,
		'show_in_quick_edit'    => true,    // Показывать ли таксономию в панели быстрого редактирования записи 
	) );


    // --- Метки для типа записей - приглашения --- //

	register_taxonomy( 'invite_tags', array( 'invite' ), array(
		// 'label'                 => '', // определяется параметром $labels->name
		'labels'                => array(
            'name'              => __( 'Метки приглашений', 'mentorship' ),
            'singular_name'     => __( 'Метки приглашений', 'mentorship' ),
            'search_items'      => __( 'Метки приглашений', 'mentorship' ),
            'all_items'         => __( 'Все метки приглашений', 'mentorship' ),
            'parent_item'       => __( 'Родительские метки приглашений', 'mentorship' ),
            'parent_item_colon' => __( 'Родительские метки приглашений:', 'mentorship' ),
            'edit_item'         => __( 'Редактировать метки приглашений', 'mentorship' ),
            'update_item'       => __( 'Обновить метки приглашений', 'mentorship' ),
            'add_new_item'      => __( 'Добавить новые метки приглашений', 'mentorship' ),
            'new_item_name'     => __( 'Новое имя метки приглашения', 'mentorship' ),
            'menu_name'         => __( 'Метки приглашений', 'mentorship' ),
        ),
		// 'description'           => '',   // описание таксономии
		'public'                => true,
		'publicly_queryable'    => true,    // Есть ли доступ к элементам таксономии во внешней части сайта
		'show_in_nav_menus'     => true,    // Возможность выбора в навигационном меню
		'show_ui'               => true,    // Показывать блок управления этой таксономией в админке.
		'show_tagcloud'         => true,   // Создать облако элементов этой таксономии
		'hierarchical'          => false,    // Таксономия будет древовидная
		// 'update_count_callback' => '',
		// 'rewrite'               => true,
		//'query_var'             => $taxonomy, // название параметра запроса
		// 'capabilities'          => array(),
		'meta_box_cb'           => 'post_tags_meta_box', // post_categories_meta_box или post_tags_meta_box
		// 'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
		// '_builtin'              => false,
		'show_in_quick_edit'    => true,    // Показывать ли таксономию в панели быстрого редактирования записи 
	) );

    mentorship_set_default_invite_categories(); // ## Запускать это каждый раз здесь, или только при инициализации плагина?

}



//
// Проверяет и при необходимости устанавливает категории по умолчанию
//
//

function mentorship_set_default_invite_categories()
{

    $default_cats = mentorship_get_default_invite_categories();
    $cats = get_terms( array( 'taxonomy' => 'invite_categories', 'hide_empty' => false ) );

    $cats_index = array();
    foreach ( (array) $cats as $item ) $cats_index[$item->slug] = array( 'name' => $item->name, 'parent' => $item->parent, 'term_id' => $item->term_id );

    foreach ( (array) $default_cats as $item ) {

        if ( isset( $cats_index[$item['slug']] ) ) continue; 

        $parent_term_id = null;
        if ( isset( $item['parent'] ) ) {
            $parent_term = term_exists( $item['parent'], 'invite_categories' );
            $parent_term_id = $parent_term['term_id'];
        } 
        wp_insert_term( $item['name'], 'invite_categories', array( 'slug' => $item['slug'], 'parent'=> $parent_term_id ) );

    }

}



//
// Возвращает список обязательных категорий
//
//

function mentorship_get_default_invite_categories()
{

    $cats = array();
    $cats[] = array( 'slug' => 'levels', 'name' => __( 'Статус пользователя', 'mentorship' ) );
    $cats[] = array( 'slug' => 'reusable', 'name' => __( 'Повторность использования', 'mentorship' ) );
    $cats[] = array( 'slug' => 'single', 'name' => __( 'Однократный', 'mentorship' ), 'parent' => 'reusable' );
    $cats[] = array( 'slug' => 'multiple', 'name' => __( 'Многократный', 'mentorship' ), 'parent' => 'reusable' );
    
    $levels = mentorship_get_mprofile_levels();    

    foreach ( (array) $levels as $key => $item )
        $cats[] = array( 'slug' => $key, 'name' => $item['descr'], 'parent' => 'levels' );

    return apply_filters( 'mif_bp_mentorship_get_default_invite_categories', $cats );

}









?>