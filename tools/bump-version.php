<?php
/**
 * Usage: php tools/bump-version.php <version> <slug>
 * Example: php tools/bump-version.php 0.9.1 mailhealth-lite
 */
if ($argc < 3) {
    fwrite(STDERR, "Usage: php tools/bump-version.php <version> <slug>\n");
    exit(1);
}
$version = $argv[1];
$slug = $argv[2];

$pluginFile = "$slug.php";
$readmeFile = "readme.txt";

if (!file_exists($pluginFile)) {
    fwrite(STDERR, "Plugin file not found: $pluginFile\n");
    exit(1);
}
if (!file_exists($readmeFile)) {
    fwrite(STDERR, "readme.txt not found\n");
    exit(1);
}

$plugin = file_get_contents($pluginFile);
$plugin = preg_replace('/^\s*\*?\s*Version:\s*.*$/mi', " * Version: $version", $plugin);
file_put_contents($pluginFile, $plugin);

$readme = file_get_contents($readmeFile);
$readme = preg_replace('/^\s*Stable tag:\s*.*$/mi', "Stable tag: $version", $readme);
file_put_contents($readmeFile, $readme);

echo "Bumped to $version\n";
