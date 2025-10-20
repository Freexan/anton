<?php

use flight\Engine;
use flight\database\PdoWrapper;
use Tracy\Debugger;

$ds = DIRECTORY_SEPARATOR;

/*********************************************
 *         FlightPHP Service Setup           *
 *********************************************
 * This file registers services and integrations
 * for your FlightPHP application. Edit as needed.
 *
 * @var array  $config  From config.php
 * @var Engine $app     FlightPHP app instance
 **********************************************/



/*********************************************
 *           Tracy Debugger Setup            *
 *********************************************
 * Tracy is a powerful error handler and debugger for PHP.
 * Docs: https://tracy.nette.org/
 *
 * Key Tracy configuration options:
 *   - Debugger::enable([mode], [ip]);
 *       - mode: Debugger::Development or Debugger::Production
 *       - ip: restrict debug bar to specific IP(s)
 *   - Debugger::$logDirectory: where error logs are stored
 *   - Debugger::$strictMode: show all errors (true/E_ALL), or filter out deprecated notices
 *   - Debugger::$showBar: show/hide debug bar (auto-detected, can be forced)
 *   - Debugger::$maxLen: max length of dumped variables
 *   - Debugger::$maxDepth: max depth of dumped structures
 *   - Debugger::$editor: configure clickable file links (see docs)
 *   - Debugger::$email: send error notifications to email
 *
 * Example Tracy setups:
 *   Debugger::enable(); // Auto-detects environment
 *   Debugger::enable(Debugger::Development); // Explicitly set environment
 *   Debugger::enable('23.75.345.200'); // Restrict debug bar to specific IPs
 *
 * For more options, see https://tracy.nette.org/en/configuration
 **********************************************/
Debugger::enable(Debugger::Production); // Explicitly set environment
Debugger::$showBar = false; // Disable debug bar to avoid loading incompatible PdoQueryCapture
Debugger::$logDirectory = __DIR__ . $ds . '..' . $ds . 'log'; // Log directory
Debugger::$strictMode = true; // Show all errors (set to E_ALL & ~E_DEPRECATED for less noise)
// Debugger::$maxLen = 1000; // Max length of dumped variables (default: 150)
// Debugger::$maxDepth = 5; // Max depth of dumped structures (default: 3)
// Debugger::$editor = 'vscode'; // Enable clickable file links in debug bar
// Debugger::$email = 'your@email.com'; // Send error notifications

/**********************************************
 *           Database Service Setup           *
 **********************************************/
$sqlitePath = $config['database']['file_path'] ?? (__DIR__ . $ds . '..' . $ds . 'database.sqlite');
$dsn = 'sqlite:' . $sqlitePath;
$options = [ \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC ];
$app->register('db', PdoWrapper::class, [ $dsn, null, null, $options ]);

/**********************************************
 *         Third-Party Integrations           *
 **********************************************/
// Google OAuth Example:
// $app->register('google_oauth', Google_Client::class, [ $config['google_oauth'] ]);

// Redis Example:
// $app->register('redis', Redis::class, [ $config['redis']['host'], $config['redis']['port'] ]);

// Add more service registrations below as needed
