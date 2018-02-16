<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12/30/2017
 * Time: 11:26 PM
 */

namespace MyBlogBundle\Controller;


use MyBlogBundle\Entity\ArticleEntity;
use MyBlogBundle\Entity\Category;
use MyBlogBundle\Form\ArticleEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @Route("/articles", name="articles_all")
     */
    public function ArticlesShowAction()
    {
        $artcilesRepo = $this->getDoctrine()->getRepository(ArticleEntity::class);
        $articles = $artcilesRepo->findAll();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render("articles/ArticlesAll.html.twig", ['articles' => $articles,
                                                           'categories' => $categories]);
    }

    /**
     * @Route("/articles/show/{id}", name="article_id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ArticleShowById(int $id)
    {
        $article = $this->getDoctrine()->getRepository(ArticleEntity::class)->findArticlesByIdWithCategories($id);
        if ($article == null)
        {
            throw $this->createNotFoundException();
        }
        else
        {
            return $this->render("articles/ArticleById.html.twig", ['article' => $article]);
        }
    }

    /**
     * @Route("article/create", name="create_article")
     * @param $request
     * @Security("has_role('ROLE_USER')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ArticleCreate(Request $request)
    {
        $article = new ArticleEntity();
        $article->setDate(new \DateTime());
        $article->setAuthor(($this->getUser()->getUsername()));
        $form = $this->createForm(ArticleEntityType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute("articles_all");
        }
        return $this->render("articles/ArticleCreate.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("article/{id}/delete", name="delete_article")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ArticleDelete(int $id)
    {
        $article = $this->getDoctrine()->getRepository(ArticleEntity::class)->find($id);
        if($article == null)
        {
            throw $this->createNotFoundException();
        }
        elseif ($this->getUser() == null){
            return $this->redirectToRoute("article_id", ["article" => $article, "id" => $id]);
        }
        elseif ($this->getUser()->getUsername() != $article->getAuthor()){
            return $this->redirectToRoute("article_id", ["article" => $article, "id" => $id]);
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();
            return $this->redirectToRoute("articles_all");
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/article/{id}/edit", name="article_edit")
     */
    public function ArticleEdit(int $id, Request $request)
    {
        $article = $this->getDoctrine()->getRepository(ArticleEntity::class)->find($id);
        if ($article == null)
        {
            throw $this->createNotFoundException();
        }
        elseif ($this->getUser() == null){
            return $this->redirectToRoute("article_id", ["article" => $article, "id" => $id]);
        }
        elseif ($this->getUser()->getUsername() != $article->getAuthor()){
            return $this->redirectToRoute("article_id", ["article" => $article, "id" => $id]);
        }
        else
        {
            $form = $this->createForm(ArticleEntityType::class, $article);
            $form->handleRequest($request);
            if ($form->isValid() && $form->isSubmitted())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
                return $this->redirectToRoute("articles_all");
            }
            return $this->render("articles/ArticleUpdate.html.twig", ["form" => $form->createView(), "article" => $article]);
        }
    }

    /**
     * @Route("/articles/showByCategs/{id}", name="articles_category")
     * @param int $id
     * @return null
     */
    public function ArticlesShowByCategories(int $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $articles = $category->getArticles();
        $allCategories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $category = $category->getName();
        if(count($articles) == 0){
            return $this->render("articles/CleanCategory.html.twig", ['categories' => $allCategories,
                                                                           'category' => $category]);
        }
        else{
        return $this->render("articles/ArticlesByCategory.html.twig", ['articles' => $articles,
                                                                            'category' => $category,
                                                                            'categories' => $allCategories]);
        }
    }
}