<?php

return [

    'name' => 'Event',

    'slug'=> 'event',

    'description' => 'Event module',

    'service_provider' => 'EventServiceProvider',

    'admin_menus' => [
    			'group'=> [
	    					'name'=>'Events',
	    					'url'=>'admin/events*',
			    			'icon'=>'fas fa-calendar-alt'
			    		],
    			'index'=> [
    			 			'name'=>'Events',
    			 			'url'=>'admin/events',
			    			'icon'=>'fas fa-calendar-alt'
		    			],
    			'new'=> [
    						'name'=>'Add New',
			    			'url'=>'admin/events/create',
			    			'icon'=>'fas fa-plus'
		    			],
                'related'=> [
                            'name'=>'Registrations',
                            'url'=>'admin/events/registrations',
                            'icon'=>'fas fa-file-invoice-dollar'
                        ]
    ],
    
    'permissions' => [
    			'manage-events'
    ]

];