<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class variety extends parser{
    public $url = 'http://variety.com/';    
    public $articles_html = '#content .col1 .thumb';
    public $article = '.article-body';
    public $title = 'header h1';
    public $next = '.more-news a, .next a';
    public $img = '.attachment-featured-carousel';   
    public $date = '.timestamp';  
       
}