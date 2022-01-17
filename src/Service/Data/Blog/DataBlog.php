<?php

namespace App\Service\Data\Blog;

use App\Entity\Blog\BoArticle;
use App\Entity\Blog\BoCategory;
use App\Service\Data\DataConstructor;

class DataBlog extends DataConstructor
{
    public function setDataCategory(BoCategory $obj, $data): BoCategory
    {
        return ($obj)
            ->setSlug(null)
            ->setName(trim($data->name))
        ;
    }

    public function setDataArticle(BoArticle $obj, $data): BoArticle
    {
        return ($obj)

        ;
    }
}