<?php

// Default values — overridden by the Settings table in the admin panel.
return [
    'groom'        => 'Raahif',
    'bride'        => 'Hadhu',
    'date'         => 'May 15, 2026',
    'date_short'   => '15 | 05 | 26',
    'date_day'     => 'Friday',
    'venue'        => 'Medhu Bageecha, Lonuziyaarai Park',
    'city'         => "Male'",
    'country'      => 'Maldives',
    'venue_map_url' => 'https://maps.app.goo.gl/E1cDqkyRUWAF5iM26',
    'dress_code'         => 'Smart Formal',
    'dress_note'         => 'Modest & elegant attire',
    'dress_code_ladies'  => 'Evening Gown',
    'dress_note_ladies'  => 'Modest & elegant attire',
    'dress_code_gents'   => 'Smart Formal',
    'dress_note_gents'   => 'White Shirt & Navy Pants',
    'rsvp_by'      => 'March 26, 2026',
    'datetime_iso' => '2026-05-15T17:00:00+05:00',

    // Raw picker values — derived/stored by admin settings form
    'event_date'          => '2026-05-15',
    'reception_time_raw'  => '17:30',
    'reception_time'      => '5:30 pm',
    'reception_time_words' => "at five-thirty in the evening",
    'timezone'            => '+05:00',
    'rsvp_by_raw'         => '2026-03-26',

    // Wedding window (ceremony guests)
    'wedding_start'       => '4:45 pm',
    'wedding_end'         => '7:30 pm',
    'wedding_start_raw'   => '16:45',
    'wedding_end_raw'     => '19:30',
    'wedding_time_words'  => 'from four forty-five until seven thirty in the evening',

    // Default guest categories
    'categories' => [
        ['name' => 'Bridesmaid'],
        ['name' => 'Groomsman'],
    ],

    // Color palette (JSON array of {color, name} objects)
    'palette' => json_encode([
        ['color' => '#152f4a', 'name' => 'Deep Navy'],
        ['color' => '#22304a', 'name' => 'Navy'],
        ['color' => '#194569', 'name' => 'Deep Sea'],
        ['color' => '#3f6594', 'name' => 'Ocean Blue'],
        ['color' => '#718db5', 'name' => 'Steel Blue'],
        ['color' => '#a0c2e8', 'name' => 'Powder Blue'],
        ['color' => '#c2dcf7', 'name' => 'Sky Mist'],
        ['color' => '#cadeed', 'name' => 'Sky Blue'],
        ['color' => '#e1e5e8', 'name' => 'Silver Grey'],
    ]),

    // Reception sessions
    'session1_start'      => '5:30 pm',
    'session1_end'        => '6:30 pm',
    'session1_start_raw'  => '17:30',
    'session1_end_raw'    => '18:30',
    'session1_time_words' => 'at five thirty in the evening',
    'session2_start'      => '6:30 pm',
    'session2_end'        => '7:30 pm',
    'session2_start_raw'  => '18:30',
    'session2_end_raw'    => '19:30',
    'session2_time_words' => 'at six thirty in the evening',
];
