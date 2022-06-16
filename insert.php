<?php
// Use ls command to shell_exec function

$output = shell_exec('git');

// Display the list of all files and directories

echo "<pre>$output</pre>";

?>
