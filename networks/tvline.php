<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class tvline extends parser{
    public $url = 'http://tvline.com/';    
    public $articles_html = '#main a.title';
    public $article = '.entry-content';
    public $title = '.entry-title';
    public $next = '.nav-previous a';
    public $img = '.wp-post-image';
    public $date = '.date-published';
       
}