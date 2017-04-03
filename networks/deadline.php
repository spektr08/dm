<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class deadline extends parser{
    public $url = 'http://deadline.com/';    
    public $articles_html = '.article-list-wrapper .article-inner .article-thumb';
    public $article = '#content-wrapper article';
    public $title = '.entry-title';
    public $next = '.navigation .alignright a';
    public $img = '.size-main-article-thumb';   
    public $date = '.date-published';    
}