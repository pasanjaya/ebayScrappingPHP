<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Scrapper</title>
    <style>
        h1 {
            text-align: center;
        }

        table, td, th {  
            border: 1px solid #ddd;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 10px;
        }
    </style>

</head>
<body>

<?php

echo "


";

$curl = curl_init(); // initilization of curl

$serchString = "pc video games in 2018"; // string to search
$serchString = rawurlencode($serchString); // encode as a url search string eg: movies in 2018  => movies%20in%20%2018

$url = "https://www.ebay.com/sch/i.html?_from=R40&_trksid=m570.l1313&_nkw=$serchString"; // ebay searching string; can be change according to the region

curl_setopt($curl, CURLOPT_URL, $url); //set curl url option to load
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // set access to https protocols
curl_setopt($curl, CURLOPT_CAINFO, getcwd()."/CAcerts/cacert.pem"); // get certification (*optional)
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // set the return option

$result = curl_exec($curl); // execute and return a result as a string


$movies = array();

preg_match_all('!src=\"https://i.ebayimg.com/thumbs/images/(g|m)/[^\s]*?/s-l225.jpg!', $result, $matches);
$movies['imgs'] = str_replace("src=\"", "", $matches[0]);

preg_match_all('!<h3 class=\"s-item__title\" role=\"text\">(.*?)<\/h3>!', $result, $matches);
$movies['names'] = $matches[1];

preg_match_all('!<div class=\"s-item__detail s-item__detail--primary\">(.*?)<\/div>!', $result, $matches);

// preg_match_all("!<span class=\"s-item__price\">(.*?)<\/span>!", $result, $matches); // (\$\d+\.\d{1,2})
// <span class=\"s-item__price\">\$(\d+\.\d{1,2})<\/span>
// print_r($matches[1]);

for($i = 0; $i <count($matches[1]); $i++){

    if(preg_match('!<span class=\"s-item__price\">(\$\d+\.\d{1,2})<\/span>!', $matches[1][$i], $priceMatches)){
        $movies['price'][$i] = $priceMatches[1];
    }

    else if (preg_match('!<span class=\"s-item__price\">(\$\d+\.\d{1,2})<span class="DEFAULT"> to <\/span>(\$\d+\.\d{1,2})<\/span>!', $matches[1][$i], $priceRangeMatch)){
        $movies['price'][$i] = $priceRangeMatch[1]." to ".$priceRangeMatch[2];
    }
}
$movies['price'] = array_values($movies['price']);


// print_r($movies['imgs']);die;


echo "<h1> Search string: ".rawurldecode($serchString)."</h1>";
echo "<br/>";
echo "<table>";
echo "<tr>";
echo "<th>Image</th>";
echo "<th>Name</th>";
echo "<th>Price</th>";
echo "</tr>";
for($i = 0; $i < count($movies['imgs']); $i++){
    echo "<tr>";
    echo "<td><img src=".$movies['imgs'][$i]."></td>";
    echo "<td>".$movies['names'][$i]."</td>";
    echo "<td>".$movies['price'][$i]."</td>";
    echo "</tr>";
}
echo "</table>";



curl_close($curl); // close curl

?>
</body>
</html>