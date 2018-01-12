<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12/30/2017
 * Time: 11:26 PM
 */

namespace MyBlogBundle\Controller;


use MyBlogBundle\Entity\ArticleEntity;
use MyBlogBundle\Form\ArticleEntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @Route("/articles", name="articles_all")
     */
    public function ArticlesShowAction()
    {
        $repo = $this->getDoctrine()->getRepository(ArticleEntity::class);
        $articles = $repo->findAll();
        return $this->render("articles/ArticlesAll.html.twig", ['articles' => $articles]);
    }

    /**
     * @Route("/articles/show/{id}", name="article_id")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ArticleShowById(int $id)
    {
        $article = $this->getDoctrine()->getRepository(ArticleEntity::class)->find($id);
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ArticleCreate(Request $request)
    {
        $article = new ArticleEntity();
        $article->setDate(new \DateTime());
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
            return $this->render("articles/ArticleUpdate.html.twig", ["form" => $form->createView()]);
        }
    }
}