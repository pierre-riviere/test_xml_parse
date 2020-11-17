<?php

/**
 * PHP 7.2.24
 * Mysql 5.7.30
 */

class Article {
    public $name;
    public $sourceName;
    public $content;

    public function __construct(string $name, string $sourceName, string $content) {
        $this->name = $name;
        $this->sourceName = $sourceName;
        $this->content = $content;
    }
}

class ArticleAgregator implements IteratorAggregate {
    // used to store articles before displaying
    private $articles = [];
    // db connection singleton
    private static $con = null;

    public function __construct() {}

    /**
     * Database connection
     */
    private function connectDb(string $host, string $username, string $password, string $dbname): void {
        if (!isset(self::$con)) {
            self::$con = new mysqli($host, $username, $password, $dbname);
        
            if (self::$con->connect_error) {
                throw new Exception(mysqli_error());
            }
        }
    }

    /**
     * Database query helper
     */
    private function dbQuery(string $query) {
        return self::$con->query($query);
    }

    /**
     * Retrieve articles from db
     */
    public function appendDatabase(string $host, string $username, string $password, string $dbname): void {
        $this->connectDb($host, $username, $password, $dbname);

        $res = $this->dbQuery("SELECT s.name AS sourceName, a.name, a.content FROM article a JOIN source s ON a.source_id = s.id ORDER BY a.id");

        while($res && $row = $res->fetch_array(MYSQLI_ASSOC)) {
            array_push($this->articles, new Article($row['name'], $row['sourceName'], $row['content']));
        }
    }

    /**
     * Retrieve articles from specified rss url
     */
    public function appendRss(string $sourceName, string $rssUrl): void {
        $xml = simplexml_load_file($rssUrl);

        foreach($xml->channel->item as $item) {
            array_push($this->articles, new Article($item->title, $sourceName, $item->description));
        }        
    }

    /**
     * Get Iterator from ArrayObject
     */
    public function getIterator() {
        return new ArrayIterator($this->articles);
    }
}

$a = new ArticleAgregator();

/**
 * Récupère les articles de la base de données, avec leur source.
 * host, username, password, database name
 */
$a->appendDatabase('localhost', 'root', 'root', 'lemonde');

/**
 * Récupère les articles d'un flux rss donné
 * source name, feed url
 */
$a->appendRss('Le Monde', 'http://www.lemonde.fr/rss/une.xml');

foreach ($a as $article) {
    echo sprintf('<h2>%s</h2><em>%s</em><p>%s</p>',
        $article->name,
        $article->sourceName,
        $article->content
    );
}