<?php

// Usage (from the project folder): php vendor/wai-blue/hubleto-ohs-core/src/Cli/setup-home-dashboards.php
// Gives every active user the Ohs home dashboard with the panels of their roles. Safe to run repeatedly.

// The app is a composer package, so the project is the folder the script is started from.
$projectFolder = getcwd();
if (!is_file($projectFolder . '/boot.php')) {
  fwrite(STDERR, "Run this from the Hubleto project folder.\n");
  exit(1);
}
require_once($projectFolder . '/boot.php');

$hubleto->getService(\Hubleto\App\External\WaiBlue\OhsCore\Services\HomeDashboard::class)->ensureForAllUsers();
echo "Home dashboards are set up.\n";
