<?php
require_once 'db_config.php';

function inputJsonToDatabase($url_file_json, $id_kota)
{
    global $pdo_con;
    $data = file_get_contents($url_file_json);
    $data_decode = json_decode($data, true);
    $data_encode = json_encode($data_decode["statuses"][0]);
    $hitung = 0;

    foreach ($data_decode as $index => $row) {
        if ($index == 'statuses') {
            echo "uwu";
            foreach ($row as $index_a => $a) {
                $year = substr($a['created_at'], -5);
                $month = substr($a['created_at'], 4, 3);
                $date = substr($a['created_at'], 8, 2);

                $created_at = "STR_TO_DATE('$date-$month-$year', '%d-%M-%Y')";
                $id = $a['id'];
                $text = $a['text'];
                $screen_name = $a['user']['screen_name'];
                $id_kota = "1";
                $retweet_count = $a['retweet_count'];
                $favourite_count = $a['favorite_count'];
                $url = null;
                if (!empty($a['entities']['urls'])) {
                    $url = $a['entities']['urls'][0]['url'];
                }

                $pdo_statement_check = $pdo_con->prepare("SELECT * FROM tweets WHERE id=:id");
                $pdo_statement_check->bindParam(":id", $id);
                $pdo_statement_check->execute();

                if (empty($result_check = $pdo_statement_check->fetchAll())) {
                    $sql = "INSERT INTO tweets(id, created_at, text, screen_name, id_kota, retweet_count, favorite_count, url) VALUES(:id, STR_TO_DATE('$date-$month-$year', '%d-%M-%Y'), :text, :screen_name, :id_kota, :retweet_count, :favorite_count, :url)";
                    $pdo_statement = $pdo_con->prepare($sql);

                    $result = $pdo_statement->execute(array(':id' => $id, ':text' => $text, ':screen_name' => $screen_name, ':id_kota' => $id_kota, ':retweet_count' => $retweet_count, ':favorite_count' => $favourite_count, ':url' => $url));
                    if (!empty($result)) {
                        echo ++$hitung;
                    }
                } else {
                    echo "data sudah ada";
                }
            }
        }
    }
}

function inputCrawlToDatabase($json_file, $id_kota)
{
    try {
        global $pdo_con;
        $data_decode = json_decode($json_file, true);
        $data_encode = json_encode($data_decode["statuses"][0]);
        $hitung = 0;

        foreach ($data_decode as $index => $row) {
            if ($index == 'statuses') {
                foreach ($row as $index_a => $a) {

                    $year = substr($a['created_at'], -5);
                    $month = substr($a['created_at'], 4, 3);
                    $date = substr($a['created_at'], 8, 2);

                    $created_at = "STR_TO_DATE('$date-$month-$year', '%d-%M-%Y')";
                    $id = $a['id'];
                    $text = $a['text'];
                    $screen_name = $a['user']['screen_name'];
                    $id_kota = $id_kota;
                    $retweet_count = $a['retweet_count'];
                    $favourite_count = $a['favorite_count'];
                    $url = null;
                    if (!empty($a['entities']['urls'])) {
                        $url = $a['entities']['urls'][0]['url'];
                    }

                    $pdo_statement_check = $pdo_con->prepare("SELECT * FROM tweets WHERE id=:id");
                    $pdo_statement_check->bindParam(":id", $id);
                    $pdo_statement_check->execute();

                    if (empty($result_check = $pdo_statement_check->fetchAll())) {
                        var_dump($a);
                        echo "Berhasil dimasukkan ";
                        $sql = "INSERT INTO tweets(id, created_at, text, screen_name, id_kota, retweet_count, favorite_count, url) VALUES(:id, STR_TO_DATE('$date-$month-$year', '%d-%M-%Y'), :text, :screen_name, :id_kota, :retweet_count, :favorite_count, :url)";
                        $pdo_statement = $pdo_con->prepare($sql);

                        $result = $pdo_statement->execute(array(':id' => $id, ':text' => $text, ':screen_name' => $screen_name, ':id_kota' => $id_kota, ':retweet_count' => $retweet_count, ':favorite_count' => $favourite_count, ':url' => $url));
                        if (!empty($result)) {
                            echo ++$hitung;
                        }
                    } else {
                        echo "$id - data sudah ada\n";
                    }
                }
            }
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// inputJsonToDatabase('ehe3.json');
