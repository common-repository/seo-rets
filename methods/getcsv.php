<?php

$users = get_option('sr_users');
//echo "<pre>";
//print_r($users);
//echo "</pre>";

function array_to_csv_download($array, $filename = "export.csv", $delimiter = ";")
{
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');

    fputcsv($f, 'name,mail,phone');
    foreach ($array as $key => $user) {
        fputcsv($f, $user['name'] . "," . $user['email'] . "," . $user['u_mobile'] . ",\n");
    }
}

array_to_csv_download($users,'con.csv');

?>