<?php
//
// Author: Alejandro Landini
// mpi_AJAX.php 4/6/18
// toDo:
// revision:
// * 05/06/18 add Mpi.php class
// * 17/01/21 add remove default
//

$mpi_dir = dirname(__FILE__);
if (!function_exists('PrepareUploadedFile')) {
    include "$mpi_dir/../../lib.php";
}
include "$mpi_dir/mpi.php";

$mi = getMemberInfo();
$image_folder ="$mpi_dir/../../images/mpi/";

$name = PrepareUploadedFile(
    'mpi',
    4198400,
    'jpg|jpeg|gif|png',
    false,
    $image_folder
);

if ($name) {
    $specs['width'] = 80;
    $specs['height'] = 80;
    $specs['identifier'] = '_mpi';
    $thumb = createThumbnail($image_folder . '/' . $name, $specs);
    preg_match('/\.[a-zA-Z]{3,4}$/U', $name, $matches);
    $ext = strtolower($matches[0]);
    $t =
        substr($name, 0, -5) .
        str_replace($ext, $specs['identifier'] . $ext, substr($name, -5));
}

$mpi = new Mpi($mi['username'], $image_folder, $name, $t);

$remove_default = Request::val('remove-default');
if ($remove_default === "1" || $remove_default === "0"){
    $remove_default = $remove_default === "1" ? true : false ;
    $mpi->remove_default($remove_default);
}

header('Content-Type: application/json');
$res = [
    'image' => $mpi->image,
    'thumb' => $mpi->thumb,
    'version' => $mpi->Version,
    'message' => $mpi->message,
    'default'=>$mpi->remove_default()
];
echo json_encode($res);
