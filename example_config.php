<?php
// Add you bot's API key and name
$bot_api_key  = '';
$bot_username = 'fl_ru_projects_bot';

// Define all paths for your custom commands in this array (leave as empty array if not used)
$commands_paths = [
  __DIR__ . '/Commands/',
];

// Enter your MySQL database credentials
$mysql_credentials = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '',
    'database' => 'examplebot',
];

?>