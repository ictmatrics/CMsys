<?php
require_once __DIR__ . '/../APP/System/bootstrap.php';
$db = new \System\Config\QueryBuilder();

$cpts = [
    'portfolio' => [
        'label' => 'Portfolio',
        'description' => 'Portfolio items and projects.',
        'fields' => [
            ['name' => 'client_name', 'label' => 'Client Name', 'type' => 'text'],
            ['name' => 'project_url', 'label' => 'Project URL', 'type' => 'url']
        ]
    ],
    'service' => [
        'label' => 'Services',
        'description' => 'Services provided by the company.',
        'fields' => [
            ['name' => 'icon_class', 'label' => 'Icon Class (e.g. fa fa-star)', 'type' => 'text'],
            ['name' => 'base_price', 'label' => 'Base Price', 'type' => 'text']
        ]
    ],
    'testimonial' => [
        'label' => 'Testimonials',
        'description' => 'Customer reviews and testimonials.',
        'fields' => [
            ['name' => 'reviewer_name', 'label' => 'Reviewer Name', 'type' => 'text'],
            ['name' => 'reviewer_role', 'label' => 'Reviewer Role / Company', 'type' => 'text'],
            ['name' => 'rating', 'label' => 'Rating (1-5)', 'type' => 'number']
        ]
    ]
];

// We should merge with existing
$existingQuery = $db->query("SELECT option_value FROM options WHERE option_key = 'custom_post_types' LIMIT 1");
$existingCpts = [];
if (!empty($existingQuery)) {
    $existingCpts = json_decode($existingQuery[0]['option_value'], true) ?: [];
}

$mergedCpts = array_merge($existingCpts, $cpts);
$json = json_encode($mergedCpts);

if (!empty($existingQuery)) {
    $db->query("UPDATE options SET option_value = ? WHERE option_key = 'custom_post_types'", [$json]);
} else {
    $db->query("INSERT INTO options (option_key, option_value) VALUES ('custom_post_types', ?)", [$json]);
}

echo "CPTs added successfully!";
