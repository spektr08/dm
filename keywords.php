<?php
include 'safemysql.php';


$num_rec_per_page = 10;

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
};
$start_from = ($page - 1) * $num_rec_per_page;
error_reporting(E_ERROR);
?>
<html>

    <head>
        <title>Find keywords</title>



        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="files/css/bootstrap.min.css" >
        <link rel="stylesheet" href="files/css/test.css"  crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="files/css/bootstrap-theme.min.css" >
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="files/js/bootstrap.min.js" ></script>
    </head>

    <body>

        <!-- <header>
                <nav>
                        <ul>
                                <li>Your menu</li>
                        </ul>
                </nav>
        </header>
        -->
        <div id="content">
            <form  method="get" name="test"   >

                <div class="form-group">
                    <label for="exampleInputEmail1">Keyword: </label>
                    <input type="text" class="form-control"  name="keyword" id="exampleInputEmail1" value="<?php echo $_GET['keyword'] ?>" />
                    <input type="hidden" name="page" value="<?php echo $page; ?>" />
                </div>
                <input type="submit" value="Submit" class="btn btn-default" />
            </form>
            <?php
            if (isset($_GET['keyword'])) {
                //$keyord = trim($_GET['keyword']);
                $keyord2 = $_GET['keyword'];
                $db = new SafeMySQL;


                $total_records = $db->getCol("SELECT id FROM articles WHERE   MATCH (title,article) AGAINST ('$keyord2')    group by title ");  //count number of records
                $total_records = count($total_records);
                //$total_records = $total_records[0];
                $total_pages = ceil($total_records / $num_rec_per_page);

                $qpart = " LIMIT $start_from, $num_rec_per_page";
                // echo "SELECT * FROM articles WHERE  MATCH (article) AGAINST ('$keyord2') group by title ".$qpart; exit;
                $data = $db->getAll("SELECT * FROM articles WHERE  MATCH (title,article) AGAINST ('$keyord2') group by title ?p", $qpart);

                if (!empty($data)) {
                    ?>

                    <div class="title">
                        <div class="title1">Title</div>        
                        <div>Found In</div>    
                        <div class="clear" ></div>   
                    </div>
                    <table >
                        <tbody>
                            <?php
                            foreach ($data as $v) {
                                $str = $v['article'];

                                $words = explode(' ', $keyord2);
                                $article = '';
                                if (!empty($words))
                                    foreach ($words as $word) {
                                        $str2 = '';
                                        $num = strpos(strtolower($str), strtolower($word));
                                        if ($num === false) {
                                            continue;
                                        }
                                        if ($num > 100) {
                                            $str2 = substr($str, $num - 30, 230);
                                        } else {
                                            $str2 = substr($str, 0, 200);
                                        }
                                        $article.= str_ireplace($word, "<b>$word</b>", $str2) . '<br/>';
                                    }


                                if ($v['img'] != '') {
                                    $img = "<img height='150' width='150' src=" . $v['img'] . " />";
                                } else {
                                    $img = '';
                                }
                                if(strpos($v['url'],'tvline')){
                                    $logo = '<img height="74" width="309" src="files/img/tvline_logo.png" />';
                                }elseif(strpos($v['url'],'deadline')){
                                     $logo = '<img height="74" width="309" src="files/img/deadline_logo.png" />';
                                }elseif(strpos($v['url'],'variety')){
                                     $logo = '<img height="74" width="309" src="files/img/variety_logo.png" />';
                                }
                                
                                echo "
                                          <tr>
                                            <td class='img_td' ><a href=" . $v['url'] . " target='_blank' >" . $img . "</a></td>
                                            <td class='main_td' ><p class='article_title' >" . strip_tags($v['title']) . "</p>
                                                <p class='date' >" . $v['date'] . "</p>
                                                <p class='main_content' >" . $v['description'] . "</p>
                                                
                                            
                                            </td>
                                            <td>".$logo."</br></br>
                                                <p class='main_content' >".$article."</p>
                                                </td>
                                        </tr>
                                       ";
                            }
                            ?>
                        </tbody>
                    </table>

                    <ul class="pagination">
        <?php
        //echo $total_pages; 
        if ($page != 1) {
            $pervpage = '<li><a href="?page=1&keyword=' . $keyord2 . '"><<</a></li> 
    <li><a href= "?page=' . ($page - 1) . '&keyword=' . $keyord2 . '"><</a> ';
        }

        if ($page != $total_pages)
            $nextpage = ' <li><a href="?page=' . ($page + 1) . '&keyword=' . $keyord2 . '">></a></li> 
                                   <li><a href="?page=' . $total_pages . '&keyword=' . $keyord2 . '">>></a></li>';


        if ($page - 2 > 0)
            $page2left = ' <li><a href="?page=' . ($page - 2) . '&keyword=' . $keyord2 . '">' . ($page - 2) . '</a> </li>';
        if ($page - 1 > 0)
            $page1left = '<li><a href="?page=' . ($page - 1) . '&keyword=' . $keyord2 . '">' . ($page - 1) . '</a> </li>';
        if ($page + 2 <= $total_pages)
            $page2right = ' <li><a href="?page=' . ($page + 2) . '&keyword=' . $keyord2 . '">' . ($page + 2) . '</a></li>';
        if ($page + 1 <= $total_pages)
            $page1right = ' <li><a href="?page=' . ($page + 1) . '&keyword=' . $keyord2 . '">' . ($page + 1) . '</a></li>';


        echo $pervpage . $page2left . $page1left . '<li class="active" ><a >' . $page . '</a></li>' . $page1right . $page2right . $nextpage;
        ?>  </ul>
                <?php
                }
                else {
                    echo '<h2 style="text-align:center;">No mach</h2>';
                }
            }
            ?>



        </div>

    </body>

</html>

