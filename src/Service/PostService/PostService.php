<?php

namespace App\Service\PostService;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly PostRepository $postRepository
    ) {}

    /**
     * Get all posts from the database.
     *
     * @return Post[]
     */
    public function getAllPosts(): array
    {
        return $this->postRepository->findAll();
    }

    /**
     * Get a post by its ID.
     *
     * @param int $id The post ID
     * @return Post The post entity
     * @throws NotFoundHttpException When post is not found
     */
    public function getPostById(int $id): Post
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            throw new NotFoundHttpException(sprintf('Post with id %d not found', $id));
        }

        return $post;
    }

    /**
     * Create a new post.
     *
     * @param Post $post The post entity to create
     * @return Post The created post entity
     * @throws ValidationFailedException When validation fails
     * @throws DBALException When database operation fails
     */
    public function createPost(Post $post): Post
    {
        $this->validatePost($post);

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $post;
        } catch (DBALException $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Update an existing post.
     *
     * @param Post $post The post entity to update
     * @return Post The updated post entity
     * @throws ValidationFailedException When validation fails
     * @throws DBALException When database operation fails
     */
    public function updatePost(Post $post): Post
    {
        $this->validatePost($post);

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $post;
        } catch (DBALException $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Delete a post.
     *
     * @param Post $post The post entity to delete
     * @throws DBALException When database operation fails
     */
    public function deletePost(Post $post): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->remove($post);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (DBALException $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * Validate a post entity.
     *
     * @param Post $post The post entity to validate
     * @throws ValidationFailedException When validation fails
     */
    private function validatePost(Post $post): void
    {
        $errors = $this->validator->validate($post);

        if (count($errors) > 0) {
            throw new ValidationFailedException($post, $errors);
        }
    }
}
