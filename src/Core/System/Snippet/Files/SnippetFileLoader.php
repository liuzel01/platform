<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Files;

use Doctrine\DBAL\Connection;
use Shuwei\Core\Framework\Bundle;
use Shuwei\Core\Framework\Log\Package;
use Shuwei\Core\Framework\Plugin;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

#[Package('system-settings')]
class SnippetFileLoader implements SnippetFileLoaderInterface
{
    /**
     * @var array<string, string>
     */
    private array $pluginAuthors = [];

    /**
     * @internal
     */
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly Connection $connection,
    ) {
    }

    public function loadSnippetFilesIntoCollection(SnippetFileCollection $snippetFileCollection): void
    {
        $this->loadPluginSnippets($snippetFileCollection);
    }

    private function loadPluginSnippets(SnippetFileCollection $snippetFileCollection): void
    {
        foreach ($this->kernel->getBundles() as $bundle) {
            if (!$bundle instanceof Bundle) {
                continue;
            }

            $snippetDir = $bundle->getPath() . '/Resources/snippet';

            if (!is_dir($snippetDir)) {
                continue;
            }

            foreach ($this->loadSnippetFilesInDir($snippetDir, $bundle) as $snippetFile) {
                if ($snippetFileCollection->hasFileForPath($snippetFile->getPath())) {
                    continue;
                }

                $snippetFileCollection->add($snippetFile);
            }
        }
    }

    /**
     * @return AbstractSnippetFile[]
     */
    private function loadSnippetFilesInDir(string $snippetDir, Bundle $bundle): array
    {
        $finder = new Finder();
        $finder->in($snippetDir)
            ->files()
            ->name('*.json');

        $snippetFiles = [];

        foreach ($finder->getIterator() as $fileInfo) {
            $nameParts = explode('.', $fileInfo->getFilenameWithoutExtension());

            $snippetFile = null;
            switch (\count($nameParts)) {
                case 2:
                    $snippetFile = new GenericSnippetFile(
                        implode('.', $nameParts),
                        $fileInfo->getPathname(),
                        $nameParts[1],
                        $this->getAuthorFromBundle($bundle),
                        false,
                        $bundle->getName()
                    );

                    break;
                case 3:
                    $snippetFile = new GenericSnippetFile(
                        implode('.', [$nameParts[0], $nameParts[1]]),
                        $fileInfo->getPathname(),
                        $nameParts[1],
                        $this->getAuthorFromBundle($bundle),
                        $nameParts[2] === 'base',
                        $bundle->getName()
                    );

                    break;
            }

            if ($snippetFile) {
                $snippetFiles[] = $snippetFile;
            }
        }

        return $snippetFiles;
    }

    private function getAuthorFromBundle(Bundle $bundle): string
    {
        if (!$bundle instanceof Plugin) {
            return 'Shuwei';
        }

        return $this->getPluginAuthors()[$bundle::class] ?? '';
    }

    /**
     * @return array<string, string>
     */
    private function getPluginAuthors(): array
    {
        if (!$this->pluginAuthors) {
            /** @var array<string, string> $authors */
            $authors = $this->connection->fetchAllKeyValue('
                SELECT `base_class` AS `baseClass`, `author`
                FROM `plugin`
            ');

            $this->pluginAuthors = $authors;
        }

        return $this->pluginAuthors;
    }
}
