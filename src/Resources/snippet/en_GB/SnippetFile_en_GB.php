<?php declare(strict_types=1);

namespace Justa\SimpleProductCompare\Resources\snippet\en_GB;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_en_GB implements SnippetFileInterface
{
    public const ISO = 'en-GB';
    public const NAME = 'simpleProductCompare';

    public function getName(): string
    {
        return sprintf('%s.%s', self::NAME, self::ISO);
    }

    public function getPath(): string
    {
        return sprintf('%s/%s.json', __DIR__, $this->getName());
    }

    public function getIso(): string
    {
        return self::ISO;
    }

    public function getAuthor(): string
    {
        return 'Justa';
    }

    public function isBase(): bool
    {
        return false;
    }
}
