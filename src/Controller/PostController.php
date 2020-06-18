<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\PostLikeRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private $likeRepository;
    private $postRepository;

    public function __construct(PostRepository $postRepository, PostLikeRepository $likeRepository)
    {
        $this->postRepository = $postRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * @Route("/", name="post")
     */
    public function index()
    {
        return $this->render('post/index.html.twig', ['posts' => $this->postRepository->findAll()]);
    }

    /**
     * @Route("/post/{id}/like", name="post_like")
     */
    public function like(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login');
        }

        if($post->isLikedByUser($user)){
            $like = $this->likeRepository->findOneBy(['post' => $post, 'user' => $user]);
            $em->remove($like);
            $em->flush();
            return $this->redirectToRoute('post');
        }
        
        $like = new PostLike();
        $like->setPost($post)->setUser($user);
        $em->persist($like);
        $em->flush();

        return $this->redirectToRoute('post');
    }
}
