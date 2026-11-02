<?php
// Quick check that error reporting works
echo '<h3>display_errors = ' . var_export(ini_get('display_errors'), true) . '</h3>';
echo '<h3>error_reporting = ' . error_reporting() . ' (E_ALL = ' . E_ALL . ')</h3>';

// This undefined variable should trigger a visible warning when display_errors is On
echo $undefinedVariable;
