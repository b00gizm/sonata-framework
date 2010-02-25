# Sonata

A lightweight PHP framework for building RESTful webservices.

## Intro.

We all love frameworks like [Cappuccino](http://cappuccino.org) and [SproutCore](http://www.sproutcore.com) for building desktop class apps that run in your browser. Even developers with less to none knowledge in HTML/CSS can create great looking web apps in an easy and elegant way. At their backends they make extensive use of RESTful web services for fetching, creating and modifying data.

## Wanna build your own RESTful web services in PHP?

In the PHP world, if you want to build a fully-featured web app, you have frameworks like [Zend](http://framework.zend.com), [CakePHP](http://cakephp.org) and, off course, the great [Symfony Project](http://www.symfony-project.org) by [Fabien Potentcier](http://github.com/fabpot). But what about services that don't need stuff like fancy layouts/templates, HTML forms or user session management?

Wouldn't it be great to have a stripped-down mini framework for building RESTful web services with just a minimum of overhead but with all the comfort of a "real" framework?

## Introducing Sonata.

Sonata is a lightweight framework for building RESTful webservices in PHP >= 5.2.4. Sonata was mainly inspired by the concepts of the [symfony 1.x](http://www.symfony-project.org) and [Ruby On Rails](http://rubyonrails.org). It's fast, RESTful by design and is build with / on top of some great open source PHP projects like

* [symfony Dependency Injection](http://components.symfony-project.org/dependency-injection/)
* [symfony YAML](http://components.symfony-project.org/yaml/)
* [Lime2 Testing Framework](http://github.com/bschussek/lime)
* [Phing Build Tool](http://phing.info/trac/)



## Sonata at a glance.

Lets say you want to build an web service for fetching artices depending on their unique IDs and display them in XML format.

    GET /articles/123.xml HTTP/1.1

Doing it the Sonata way is as easy as ...

1, ...

    # in config/routing.yml
    
    route_map:
      articles:
        resources: articles
    

2, ...

    // in controllers/ArticleController.class.php
    
    class ArticleController extends Sonata_Controller_Action
    {
      protected function showAction()
      {
        // Access request (and response) from within the action
        $id = $request = $this->getRequest()->getParameter('id');
        
        // Fetch the article data from a DB, 
        // e.g. by using the Doctrine ORM (http://www.doctrine-project.org)
        $article = Doctrine::getTable('Article')->find($id);
        
        $this->article = $article;
      }
    }
    

and 3.

    <!-- in templates/article/ShowSuccess.xml.php -->
    
    <?php echo '<?' ?>xml version="1.0" encoding="utf-8" ?>
    <rsp stat="ok">
      <articles>
        <article id="<?php echo $article->getId() ?>">
          <title><?php echo $article->title ?></title>
          <body><?php echo $article->body ?></body>
          <created_at><?php echo $article->created_at ?></created_at>
        </article>
      </articles>
    </rsp>
    
That's it! Check out the response:

    HTTP/1.1 200 OK
    Date: Mon, 18 Jan 2010 14:41:16 GMT
    Server: Apache/2.2.13 (Unix) mod_ssl/2.2.13 OpenSSL/0.9.8k DAV/2 PHP/5.2.10
    X-Powered-By: PHP/5.2.10
    Transfer-Encoding: chunked
    Content-Type: text/xml
     
    <?xml version="1.0" encoding="utf-8" ?>
    <rsp stat="ok">
      <articles>
        <article id="123">
          <title>My great article</title>
          <body>Lorem ipsum dolor sit amet ...</body>
          <created_at>Mon, 18 Jan 2010 14:41:16 GMT</created_at>
        </article>
      </articles>
    </rsp>
    

## Sonata sandbox, docs and more ...

...will be released real soon. Follow me at Twitter ([@b00giZm](http://www.twitter.com/b00giZm)) to receive updates about Sonata.

Disclaimer: Please notice that Sonata is still in an early stage of development. Some features / components might not be fully implemented yet. **DON'T USE SONATA IN A PRODUCTION ENVIRONMENT!**