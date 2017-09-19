<?php declare(strict_types=1);

namespace Symplify\Statie\SimilarPosts;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\PostFile;

final class SimilarPostsResolver
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return PostFile[]
     */
    public function resolveForPost(PostFile $postFile): array
    {
        if (! $postFile->getRelatedPostIds()) {
            return [];
        }

        $relatedPosts = [];

        foreach ($this->getPosts() as $post) {
            if (in_array($post->getId(), $postFile->getRelatedPostIds(), true)) {
                $relatedPosts[] = $post;
            }
        }

        return $relatedPosts;
    }

    /**
     * @return PostFile[]
     */
    private function getPosts(): array
    {
        return $this->configuration->getOptions()['posts'];
    }
}
