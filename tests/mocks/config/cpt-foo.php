<?php

return array(
	'args'              => array(
		'label'     	=> 'Foo',
		'labels'        => array(
			'menu_name' => 'Foo',
			'add_new'   => 'Add a New Foo',
		),
		'description'   => 'Foo Mock CPT',
		'public'        => true,
		'menu_position' => 50,
		'hierarchical'	=> true,
		'rewrite'       => array(
			'slug' => 'faq'
		),
	),
	'columns_filter' => array(
		'cb'    	=> true,
		'title' 	=> 'Foo Title',
		'author' 	=> 'Author',
		'date'		=> 'Date',
	),
	'columns_data' => array(
		'date'	=> array(
			'callback'      => 'the_date',
			'echo'          => false,
			'args'          => array(),
		),
	),
);
