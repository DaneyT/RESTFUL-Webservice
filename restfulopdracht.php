<?php

//DB settings localhost
define('DB_HOST', 'localhost');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_NAME', 'restfull');

//DB settings school
//define('DB_HOST', 'localhost');
//define('DB_USER', '0810099');
//define('DB_PASSWORD', 'XXXXXXX');
//define('DB_NAME', '0810099');

$url = "https://stud.hosted.hr.nl/0810099/MediaTechnologie%20Jaar%202/REST/Praktijkopdracht/restfullopdracht";



if(isset($_SERVER['CONTENT TYPE']
)){
    $content= $_SERVER['CONTENT_TYPE'];
}else{
    $content= 'application/json';
}



if(isset($_SERVER['HTTP_ACCEPT']
)){
    $accept= $_SERVER['HTTP_ACCEPT'];
}else{
    $accept= 'application/json';
}




//$content = $_SERVER["CONTENT_TYPE"];
//$accept = $_SERVER["HTTP_ACCEPT"];

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    header("Allow: GET,POST,OPTIONS");
    exit;
}

elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    //$accept = "application/json";

    //$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml';//xml is the default
    //$start = isset($_GET['start']) ? intval($_GET['start']) : 3; //10 is the default
    //$format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default
    //$user_id = intval($_GET['user']); //no default


    /* connect to the db */
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or die('Error connecting to the database');

    /* grab the games from the db */


    //saves total amount of posts
    $total = 0;
    /* create one two arraya to hold the posts and link array*/
    $posts = array();
    $links = array ();
    $selectid = "";

    /*Methode to get ID and limit else get all*/
    if(isset($_GET['id']) && intval($_GET['id'])) {
        $selectid = $_GET['id'];
        $query = "SELECT id, Title, Genre, Description FROM games WHERE id = $selectid";
        $result = mysqli_query($link, $query);

        if (mysqli_num_rows($result)) {
            while ($post = mysqli_fetch_assoc($result)) {
                $id = $post['id'];
                $links = array('links' => array(array("rel" => "self", "href" => $url."/$id"),array("rel" => "collection", "href" => "$url")));
                $posts[] = $post+$links;
                $total ++;
            }

        }
    }else{
        $query = "SELECT id, Title, Genre, Description FROM games";
        $result = mysqli_query($link, $query);
        if (mysqli_num_rows($result)) {
            while ($post = mysqli_fetch_assoc($result)) {
                $id = $post['id'];
                $links = array('links' => array(array("rel" => "self", "href" => $url."/$id"),array("rel" => "collection", "href" => "$url")));
                $posts[] = $post+$links;
                $total ++;

                //WERKT**
//            $id++;
//            $links = array('Links' => array(array("rel" => "self", "href" => $url."/$id"),array("rel" => "collection", "href" => "$url")));
//            $posts[] = array('game' => $post+$links);
            }

        }
    }

    /* output in necessary format */
    if ($content =="application/json"){
    //if ($format == 'json') {
        header('Content-type: application/json');
        //pagination hier -> moet in $posts?
        $finallink = array('link' => array("rel" => "self", "href" => "$url".'/'."$selectid"));
        $pagination = array("pagination" => array(
            "currentPage" => 1,
            "currentItems" => $total,
            "totalPages" => 1,
            "totalItems" => $total,

            "links" => array(                       //links kunnen met een while loop gemaakt worden en de "rel"=> "woorden kunnen in een array 1-4"
                "link" => array(
                        "rel" => "first",
                        "page" => 1,
                        "href" => $url.'/1',
                ),
                "link2" => array(
                        "rel" => "last",
                        "page" => 1,
                        "href" => $url.'/6',
                ),
                "link3" => array(
                        "rel" => "previous",
                        "page" => 1,
                        "href" => $url.'/'.($selectid-1),
                ),
                "link4" => array(
                        "rel" => "next",
                        "page" => 1,
                        "href" => $url.'/'.($selectid+1),
                ),



            )));

        //werkende pagination
//        $pagination = array("pagination" => array(
//            "currentPage" => 1,
//            "currentItems" => 6,
//            "totalPages" => 1,
//            "totalItems" => 6,
//
//            "links" => array(
//                "rel" => "first",
//                "page" => 1,
//                "href" => $url."/1",
//            )));

        //array_push($posts, , );
        echo json_encode(array('items' => $posts)+array('links' =>$finallink)+array('pagination' => $pagination));
    } else {
        header('Content-type: text/xml');       //xml format
        echo '<posts>';
        foreach ($posts as $index => $post) {
            if (is_array($post)) {
                foreach ($post as $key => $value) {
                    echo '<', $key, '>';
                    if (is_array($value)) {
                        foreach ($value as $tag => $val) {
                            echo '<', $tag, '>', htmlentities($val), '</', $tag, '>';
                        }
                    }
                    echo '</', $key, '>';
                }
            }
        }
        echo '</posts>';
    }

    /* disconnect from the db */
    mysqli_close($link);
    exit;

}elseif($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST)){
    exit;
}

else{
        header("http/1.1 405 method not allowed");
}



