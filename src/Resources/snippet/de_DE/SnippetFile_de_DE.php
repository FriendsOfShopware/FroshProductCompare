<?php declare(strict_types=1);

namespace Justa\FroshProductCompare\Resources\snippet\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{
    public const ISO = 'de-DE';
    public const NAME = 'froshProductCompare';

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
