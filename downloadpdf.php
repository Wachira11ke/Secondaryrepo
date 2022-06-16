<?php

$file = $_GET["file"] .".pdf";

// We will be outputting a PDF
header('Content-Type: application/pdf');

// It will be called Delft.pdf
header('Content-Disposition: attachment; filename="Delft.pdf"');

$imagpdf = file_put_contents($image, file_get_contents($file));

echo $imagepdf;
?>
