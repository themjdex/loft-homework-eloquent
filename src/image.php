<?php

header('Content-type: image/png');
$field = (int)$_GET['id'];
$data = file_get_contents('../images/' . $field . '.png');

echo $data;