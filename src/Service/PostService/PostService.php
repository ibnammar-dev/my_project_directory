<?php

namespace App\Service\PostService;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\PostRepository;

class PostService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private PostRepository $postRepository;
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PostRepository $postRepository, // Inject the repository to avoid circular dependency
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->postRepository = $postRepository;
    }

    public function getAllPosts(): array
    {
        return $this->postRepository->findAll();
    }

    public function getPostById(int $id): Post
    {
        $post = $this->postRepository->find($id);
        if (!$post) {
            throw new NotFoundHttpException('Post with id ' . $id . ' not found');
        }
        return $post;
    }

    public function createPost(Post $post): void
    {
        $this->validatePost($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function updatePost(Post $post): void
    {
        $this->validatePost($post);
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function deletePost(Post $post): void
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    public function validatePost(Post $post): void
    {
        $errors = $this->validator->validate($post);
        if (count($errors) > 0) {
            throw new ValidationFailedException($post, $errors);
        }
    }
}
