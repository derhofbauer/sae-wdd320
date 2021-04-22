<?php

namespace App\Models;

use Core\Models\AbstractModel;

/**
 * Class Post
 *
 * @package App\Models
 * @todo    : comment
 */
class Post extends AbstractModel
{
    public int $id;
    public string $title;
    public string $slug;
    public string $content;
    public int $author;
    public string $crdate;
    public string $tstamp;
    public mixed $deleted_at;


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
}
