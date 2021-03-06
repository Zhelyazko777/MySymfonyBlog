<?php

namespace MyBlogBundle\Repository;

/**
 * ArticleEntityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleEntityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findArticlesByIdWithCategories(int $id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("SELECT a, c FROM MyBlogBundle:ArticleEntity a 
                                      JOIN a.category c WHERE a.id = $id");
        $article = $query->getResult();
        return $article[0];
    }

}
