<?php

include 'simple_html_dom.php';

class parser {

    public $url = '';
    public $cont = '';
    public $db = '';
    public $table = 'articles';

    public function __construct() {
        $this->db = new SafeMySQL();
    }

    public function go($site, $url) {
        if ($url != '') {
            $this->url = $url;
        }
        $resp = $this->get_html($this->url);
        $this->cont = $resp['content'];
        $html = str_get_html($this->cont);
        $href = $html->find($this->next);
        $urls = $this->get_article_urls();
        if (!empty($href)) {
            $href = $href[0]->href;
            $this->url = $href;
            $urls['href'] = $href;
        } else {
            $urls['href'] = '';
        }
        $urls['site'] = $site;

        echo json_encode($urls);
        exit;
    }

    public function start() {
        $resp = $this->get_html($this->url);
        $this->cont = $resp['content'];
        $urls = $this->get_article_urls();
        foreach ($urls as $url) {
            $this->get_article($url);
            sleep(5);
        }
        //exit;

        $this->move_next();
    }

    public function get_article($url) {

        $db = $this->db;

        $html = $this->get_html($url);
        $meta = $this->getMetaTags($html['content']);
        if (!empty($meta) and isset($meta['og:description'])) {
            $description = $meta['og:description'];
        } else {
            $description = '';
        }

        $html = str_get_html($html['content']);
        $article = $html->find($this->article);
        $article = strip_tags($article[0]->innertext);
        $title = $html->find($this->title);
        $title = $title[0]->innertext;
        
        
         if (!empty($meta) and isset($meta['og:description'])) {
             $date = $meta['published_at'];
         }else{
             $date = '';
         }
        //$date = $html->find($this->date);
        //$date = $date[0]->innertext;
        //$date = trim(preg_replace("/\s{2,}/", " ", $date));
        $img = $html->find($this->img);
        if (!empty($img)) {
            $img = $img[0]->src;
        } else {
            $img = '';
        }
        if (!is_null($article) && !is_null($title)) {
            $data = array('title' => $title, 'article' => $article, 'url' => $url, 'page' => $this->url, 'img' => $img, 'description' => $description, 'date_str' => $date);
            $table = $this->table;
            $result = $db->getRow("SELECT * FROM " . $table . " WHERE url= '" . $url . "' ");
            if (is_null($result)) {
                $sql = "INSERT INTO ?n SET ?u";
                $db->query($sql, $table, $data);
                return true;
            } else {
                return false;
            }
        } else {
            $this->get_article($url);
        }
    }

    public function move_next() {
        $html = str_get_html($this->cont);
        $href = $html->find($this->next);
        if (!empty($href)) {
            $href = $href[0]->href;
            $this->url = $href;
            var_dump($this->url);
            $this->start();
        }
    }

    public function get_article_urls() {
        $html = str_get_html($this->cont);
        $html = $html->find($this->articles_html);
        $urls = array();
        foreach ($html as $url) {
            $urls[] = $url->href;
        }
        return $urls;
    }

    public function get_html($url) {

        $uagent = "Opera/9.80 (Windows NT 6.1; WOW64) Presto/2.12.388 Version/12.14";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
        curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
        curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        return $header;
    }

    public function getMetaTags($str) {
        $pattern = '
      ~<\s*meta\s

      # using lookahead to capture type to $1
        (?=[^>]*?
        \b(?:name|property|http-equiv)\s*=\s*
        (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
        ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
      )

      # capture content to $2
      [^>]*?\bcontent\s*=\s*
        (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
        ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
      [^>]*>

      ~ix';

        if (preg_match_all($pattern, $str, $out))
            return array_combine($out[1], $out[2]);
        return array();
    }

}
