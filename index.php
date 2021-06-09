<pre>
<?php

use Doctrine\DBAL\DriverManager;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

require "vendor/autoload.php";

$reader = new Xlsx();
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load("Schools - USA Public Schools.xlsx");
$worksheet = $spreadsheet->getActiveSheet();
$connectionParams = array(
    'dbname' => 'xlimport',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
try {
    $conn = DriverManager::getConnection($connectionParams);
} catch (\Doctrine\DBAL\Exception $e) {
    echo $e->getMessage();
}
$queryBuilder = $conn->createQueryBuilder();


foreach ($worksheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    foreach ($cellIterator as $cell) {
        $name = $cell->getValue();
        if (!empty($name)) {
            try {
                $queryBuilder
                    ->insert('table_name')
                    ->values(
                        array(
                            'name' => '?',
                        )
                    )
                    ->setParameter(0, $name)
                    ->executeQuery();
            } catch (\Doctrine\DBAL\Exception $e) {
                echo $e->getMessage();
            }
        }

    }
}


