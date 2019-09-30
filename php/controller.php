<?php
require 'php/config.php';
$url_page = isset($_GET["page"]);
$page = $url_page ? htmlspecialchars($_GET["page"]) : false;
$date_start = isset($_GET["start"]);
$start = $date_start ? htmlspecialchars($_GET["start"]) : false;
$date_end = isset($_GET["end"]);
$end = $date_end ? htmlspecialchars($_GET["end"]) : false;
$limit_param = isset($_GET["limit"]);
$limit = $limit_param ? htmlspecialchars($_GET["limit"]) : false;
$pb_param = isset($_GET["pb"]);
$pb = $pb_param ? htmlspecialchars($_GET["pb"]) : false;
$num_param = isset($_GET["number"]);
$num = $num_param ? htmlspecialchars($_GET["number"]) : false;
switch($page){
    case 'updateNumbers':
        echo update_numbers();
        break;
    case 'topNumbers':
        echo list_top_numbers($start,$end,$pb);

        break;
    case 'listDraws':
        echo list_draws($start,$end,$limit);
        break;
    case 'luckyNumbers':
        echo lucky_numbers();
        break;
    case 'getNumberDetails':
        echo get_number_details($num,$pb);
        break;
}


function update_numbers(){
    _execute("TRUNCATE numbers;",'insert');
    _execute("TRUNCATE draws;",'insert');
    _execute("TRUNCATE number_details",'insert');

    $limit = 100;
    $x = 1;
    $sql = array();
    $rows = "INSERT INTO `numbers` (`num`, `date`,`powerball`) VALUES \n";
    $today = date('Y-m-d');
    // $dates = array('start'=>'min=2000-09-01%2000:00:00','end'=>'max='.$today.'%2023:59:59');
    $dates = array('start'=>'min=2015-08-07%2000:00:00','end'=>'max='.$today.'%2023:59:59');
    $more = true;

    while($more == true){
        $obj = file_get_contents("https://www.powerball.com/api/v1/numbers/powerball?_format=json&".$dates['start']."&".$dates['end']);
        $results = json_decode($obj,true);
        array_pop($results);
        $last_row = end($results);
        $dates['end'] = "max=".$last_row['field_draw_date']."%2023:59:59";

        if(count($results) < 99) $more = false;

        foreach($results as $r){
            $date = $r['field_draw_date'];
            $draw = $r['field_winning_numbers'];
            $numbers_ary = explode(",",$r['field_winning_numbers']);

            //Insert into draws table
            $sq = "SELECT * FROM draws WHERE `date` = '".$date."';";
            $test = _execute($sq,'read');
            if(isset($test)) continue; //Skipping any rows already added
            $sq = "INSERT INTO draws (`draw`,`date`) VALUES ('".$draw."','".$date."');";
            _execute($sq,'insert');

            foreach($numbers_ary as $key=>$val){
                //print_r("v = $key and x = $x\n");
                $pb = '0';
                if($key == 5) $pb = '1';
                if($x >= $limit && $key == '0'){
                    $rows = rtrim($rows,",");
                    $rows .= ";";
                    array_push($sql,$rows);
                    $rows = "INSERT INTO `numbers` (`num`, `date`,`powerball`) VALUES \n";
                    $x = 0;
                }
                $rows .= "('$val','$date','$pb'),";
                $x++;

            }
        }


    }

    foreach($sql as $q){
        _execute($q,'insert');
    }

    // Update the number details table
    _execute("INSERT into numbers.number_details (number,pb) select num, powerball from numbers.numbers where powerball = false group by num;",'insert');
    _execute("INSERT into numbers.number_details (number,pb) select num, powerball from numbers.numbers where powerball = true group by num;",'insert');
    $numbers = _execute("SELECT distinct `number` FROM number_details WHERE pb = false;",'read');
    $pbs = _execute("SELECT distinct `number` FROM number_details WHERE pb = true;",'read');

    foreach($numbers as $n){
        if(!_number_details($n['number'],'false')) echo "Something went wrong when updating details for number: ".$n['number'];
    }
    foreach($pbs as $n){
        if(!_number_details($n['number'],'true')) echo "Something went wrong when updating details for number: ".$n['number'];
    }

    if(!score_numbers('false')) echo "Something went wrong";
    if(!score_numbers('true')) echo "Something went wrong";

    return "Everything is now up to date!";

}

function _execute($query,$type){
    $servername = $DB_HOST;
    $username = $DB_USER;
    $password = $DB_PASS;
    $dbname = $DB_NAME;
    // Create connection
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    /*$sql = "INSERT INTO numbers (firstname, lastname, email)
    VALUES ('John', 'Doe', 'john@example.com')";
    */
    switch($type){
        case 'read':
            $x=0;
            if ($result = $mysqli->query($query)) {

                /* fetch associative array */
                if($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;
                        $x++;
                    }
                } else {

                        //print_r("No results were returned for the following query: \n\t$query\n");
                        break;

                }
                /* free result set */
                $result->free();
            } else {
                print_r($mysqli->error);
            }
            return $data;
            break;
        case 'insert':
            if ($mysqli->query($query) === TRUE) {
                // echo "New records created successfully";
            } else {
                echo "Error: " . $query . "<br>" . $mysqli->error;
            }
            break;
    }

    $mysqli->close();
}

function list_draws($start="now()-interval 30 day",$end="now()"){
    $limit = '60';
    $q = "SELECT * FROM numbers WHERE `date` BETWEEN '$start' AND '$end' limit $limit;";
    $results = _execute($q,'read');
    $output = array();
    foreach($results as $r){
        if($r['powerball'] == '1') $r['num'] .= "(pb)";
        if(isset($output[$r['date']])){
            array_push($output[$r['date']]['num'],$r['num']);
        } else {
            $output[$r['date']] = array('num'=>array($r['num']),'date'=>$r['date']);
        }
    }

    return json_encode($output);
}

function list_top_numbers($start="now()-interval 30 day",$end="now()",$pb='false'){
    $q = "SELECT * FROM numbers WHERE powerball = $pb AND `date` BETWEEN '$start' and '$end';";
    //print_r($q);die;
    $results = _execute($q,'read');
    $newAry = array();
    foreach($results as $r){
        if(!isset($newAry[$r['num']])){
            $newAry[$r['num']] = array(
                'num' => $r['num'],
                'dates' => array($r['date']),
                'count' => 1
            );
        } else {
            array_push($newAry[$r['num']]['dates'],$r['date']);
            $newAry[$r['num']]['count'] = $newAry[$r['num']]['count'] + 1;
        }

    }
    //print_r($newAry);die;
    $output = array_values($newAry);
    return json_encode($output);
}

function _number_details($number,$pb){
    //Get the Avg Frequency, and the high and low
    $q = "SELECT `date` FROM numbers WHERE num = '".$number."' AND `date` >= '2015-08-07' AND powerball = $pb ORDER BY `date` DESC;";
    $dates = _execute($q,'read');
    $date_freq = array();

    foreach($dates as $key=>$value){
        if(!isset($dates[$key+1])) break;
        $cur = $value['date'];
        $next =  $dates[$key+1]['date'];

        $q = "SELECT DISTINCT `date` from draws WHERE `date` <= '$cur' AND `date` >= '$next';";
        $count = count(_execute($q,'read'));

        array_push($date_freq,$count);
    }


    asort($date_freq);
    $num_of_draws = count($dates);
    $avg_freq = floor(array_sum($date_freq) / count($date_freq));
    $min_freq = reset($date_freq);
    $max_freq = end($date_freq);

    $freq_var = ($num_of_draws == 1) ? 100 : $max_freq - $min_freq;
    $freq_var =floor($num_of_draws / $freq_var * 100);

    $last_draw = $dates[0]['date'];

    $status = 'Everything updated';
    $q = "UPDATE number_details SET avg_freq = '$avg_freq', freq_var = '$freq_var', high_freq = '$max_freq', low_freq = '$min_freq', last_draw = '$last_draw', num_of_draws = '$num_of_draws', status = '$status' WHERE `number` = $number and pb = $pb;";
    // $q = "INSERT INTO number_details (avg_freq, high_freq, low_freq, last_draw, num_of_draws,status) VALUES ('$avg_freq','$max_freq','$min_freq','$last_draw','$num_of_draws','$status') WHERE number = $number;";
    _execute($q,'insert');
    // echo "the Avg freq = $avg_freq, the max freq = $max_freq, the min freq = $min_freq";
    return true;
}

function score_numbers($pb){
    $numbers = _execute("SELECT distinct `number` FROM number_details WHERE pb = $pb;",'read');
    $avg_freqs = _execute("select * from number_details where pb = $pb order by avg_freq asc;",'read');
    $last_draws = _execute("select * from number_details where pb = $pb order by last_draw asc;",'read');
    $num_of_draws = _execute("select * from number_details where pb = $pb order by num_of_draws desc;",'read');
    $freq_vars = _execute("select * from number_details where pb = $pb order by freq_var desc",'read');

    foreach($numbers as $num){
        $score = 0;
        $n = $num['number'];
        $score = $score + array_search($n,array_column($avg_freqs,'number')); //Avg Freq Score
        $score = $score + array_search($n,array_column($last_draws,'number'));
        $score = $score + array_search($n,array_column($num_of_draws,'number'));
        $score = $score + array_search($n,array_column($freq_vars,'number'));
        _execute("UPDATE number_details SET score = $score WHERE `number` = $n;",'insert');
    }
    return true;
}

function lucky_numbers(){
    $nums  = _execute("select * from numbers.number_details where pb = false order by score asc limit 5;",'read');
    $pb = _execute("select * from numbers.number_details where pb = true order by score asc limit 1;",'read');
    $lucky_nums = array();
    foreach($nums as $n){
        array_push($lucky_nums,$n['number']);
    }
    sort($lucky_nums);
    array_push($lucky_nums,$pb[0]['number']."(pb)");
    return json_encode($lucky_nums);
}

function get_number_details($num,$pb=false){
    return json_encode(_execute("SELECT * FROM number_details WHERE `number` = $num and pb = $pb",'read'));
}

?>
