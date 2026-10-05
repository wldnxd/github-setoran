<?php

$nama = "Wildan Ramadhnai Akbar";
$nim = "E41250425";
$waktu_server = date("Y-m-d H:i:s");
$versi_php = phpversion();
$os_server = PHP_OS;
?>

<!DOCTYPE html>
<html>

<head>
    <title>Informasi</title>
</head>

<body>
    <h1>Informasi</h1>

    <table border="1">
        <tr>
            <td>Nama</td>
            <td><?php echo $nama; ?></td>
        </tr>
        <tr>
            <td>NIM</td>
            <td><?php echo $nim; ?></td>
        </tr>
        <tr>
            <td>Waktu Server</td>
            <td><?php echo $waktu_server; ?></td>
        </tr>
        <tr>
            <td>Versi PHP</td>
            <td><?php echo $versi_php; ?></td>
        </tr>
        <tr>
            <td>Sistem Operasi Server</td>
            <td><?php echo $os_server; ?></td>
        </tr>
    </table>

</body>

</html>