<?php
set_time_limit(0);
// Create connection
$conn = mysqli_connect('localhost', 'root', '', 'mk_qa');
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


for($x = 1; $x <= 79; $x++)
{
    echo 'Checking... '.$x.'<br>';
    //https://www.mkri.id/index.php?page=web.Pengaduan&id=79&kat=1&cari=&menu=7
    $curlSession = curl_init();
    $url = 'https://www.mkri.id/index.php?page=web.Pengaduan&id='.$x.'&kat=1&cari=&menu=7';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    $html = curl_exec($ch);

    //$redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $output = curl_exec($ch); 

    curl_close($ch);

    //echo $output;
    $exp = explode('<div id="content-pertanyaan">',$output);
    $output = $exp[1];
    $exp = explode('<div class="content-persidangan">',$output);

    //print_r($exp);

    foreach($exp as $val)
    {
        $exp_sub = explode('<div',$val);
        //print_r($exp_sub);
        $payload = array();
        foreach($exp_sub as $index => $value)
        {
            $exp_x = explode('">',$value);
            $exp_y = explode('</div>',$exp_x[1]);
            //echo $index.' -> '.$exp_y[0].'<br>'; 
            
            if($index == 2)
            {
                $exp_z = explode('Nomor ',$exp_y[0]);
                $payload['id'] = ($exp_z[1]*1);    
            }
            elseif($index == 3)
            {
                $payload['question_date'] = trim($exp_y[0]);    
            }
            elseif($index == 6)
            {
                $payload['question_by'] = trim($exp_y[0]);    
            }
            elseif($index == 8)
            {
                
                $based = trim($exp_y[0]);
                
                 
                
                $based = strip_tags(trim($based));
                //$based = utf8_decode($based);
                $based = trim(preg_replace('/\s+/', ' ', $based));
                //$based = htmlspecialchars($based);
                $based = htmlentities($based, null, 'utf-8');
                $based = str_replace('nbsp;','##',$based);
                $based = str_replace('&','',$based);
                $based = str_replace('amp;##','',$based);
                //$based = preg_replace("/\[(.*?)\]\s*(.*?)\s*\[\/(.*?)\]/", "[$1]$2[/$3]", html_entity_decode($based));
                $based = str_replace("'","`",$based);
                $payload['question'] = str_replace("'","`",$based);  
            }
            elseif($index == 10)
            {
                $exp_z = explode('Di Jawaban Pada Tanggal : ',$exp_y[0]);
                $exp_z = explode('</b>',$exp_z[1]);
                $payload['answer_date'] = trim($exp_z[0]);    
            }
            elseif($index == 11)
            {                                 
                $based = strip_tags(trim($exp_y[0]));
                //$based = utf8_decode($based);
                $based = trim(preg_replace('/\s+/', ' ', $based));
                //$based = htmlspecialchars($based);
                $based = htmlentities($based, null, 'utf-8');
                $based = str_replace('nbsp;','##',$based);
                $based = str_replace('&','',$based);
                $based = str_replace('amp;##','',$based);
                //$based = preg_replace("/\[(.*?)\]\s*(.*?)\s*\[\/(.*?)\]/", "[$1]$2[/$3]", html_entity_decode($based));
                $based = str_replace("'","`",$based);
                $payload['answer'] = $based;    
            }   
        }

        //print_r($payload);

        if(count($payload) > 0)
        {
            $query = "
                insert into
                    dataset
                    (
                        question_id,
                        question,
                        question_date,
                        question_by,
                        answer_date,
                        answer
                    )
                VALUES
                    (
                        '".$payload['id']."',    
                        '".$payload['question']."',    
                        '".$payload['question_date']."',    
                        '".$payload['question_by']."',    
                        '".$payload['answer_date']."',    
                        '".$payload['answer']."'    
                    )
            ";

            mysqli_query($conn, $query) or print('ERROR!!!');    
        }
                
    }
            
}


