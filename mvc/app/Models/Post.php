<?php

namespace App\Models;

use Core\Models\AbstractModel;
use Core\Traits\HasSlug;

/**
 * Class Post
 *
 * @package App\Models
 */
class Post extends AbstractModel
{
    /**
     * @todo: comment
     */
    use HasSlug;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $title;
    public string $slug;
    public string $content;
    public int $author;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $crdate;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public string $tstamp;
    /**
     * @var string Nachdem wir hier den ISO-8601 Zeit verwenden in der Datenbank, handelt es sich um einen String.
     */
    public mixed $deleted_at;

    /**
     * Diese Methode ermöglicht es uns, die Daten aus einem Datenbankergebnis in nur einer Zeile direkt in ein Objekt
     * zu füllen. Bei der Instanziierung kann über den Konstruktor auch diese Methode verwendet werden.
     *
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->slug = $data['slug'];
        $this->content = $data['content'];
        $this->author = $data['author'];
        $this->crdate = $data['crdate'];
        $this->tstamp = $data['tstamp'];
        $this->deleted_at = $data['deleted_at'];
    }

    /**
     * @todo: comment
     */
    public function teaser ($length = 240): string
    {
        return substr($this->content, 0, $length);
    }

    /**
     * @param int $length
     *
     * @return string
     * @todo: comment
     */
    public function teaserSentence ($length = 240): string
    {
        $indexOfNextPeriod = strpos($this->content, '.', $length);
        return substr($this->content, 0, $indexOfNextPeriod + 1);
    }

    public function categories (): array
    {
        return Category::findByPost($this->id);
    }
}
