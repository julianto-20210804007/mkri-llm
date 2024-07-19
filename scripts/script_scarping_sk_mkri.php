<?php
set_time_limit(0);

//okay lets rock
for($x = 4054; $x <= 4100; $x++)
{
    $curlSession = curl_init();
    $url = 'https://www.mkri.id/index.php?page=download.Putusan&id='.$x;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    $html = curl_exec($ch);

    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

    curl_close($ch);

    //echo "Original URL:   " . $url . "\n";
    //echo "Redirected URL: " . $redirectedUrl . "\n";
    //echo $data_check;    
    echo "$url -> $redirectedUrl";
    
    if('https://www.mkri.id/public/content/persidangan/putusan/' == $redirectedUrl)
    {
        echo ' -> SKIP<br>';
    }
    else
    {
        //https://www.mkri.id/public/content/persidangan/putusan/Ketetapan%20015.pdf
        $namefile = $x.'_'.str_replace('https://www.mkri.id/public/content/persidangan/putusan/','',$redirectedUrl);
        echo ' -> OK<br>';
        file_put_contents("repo/".$namefile, fopen($redirectedUrl, 'r'));           
    }
}  
