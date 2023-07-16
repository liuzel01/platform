<?php declare(strict_types=1);

namespace Shuwei\Core\System\Snippet\Aggregate\SnippetSet;

use Shuwei\Core\Framework\DataAbstractionLayer\Entity;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shuwei\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shuwei\Core\Framework\Log\Package;
use Frontend\Website\Aggregate\WebsiteDomain\WebsiteDomainCollection;
use Shuwei\Core\System\Snippet\SnippetCollection;

#[Package('system-settings')]
class SnippetSetEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $baseFile;

    /**
     * @var string
     */
    protected $iso;

    /**
     * @var SnippetCollection|null
     */
    protected $snippets;

    /**
     * @var WebsiteDomainCollection|null
     */
    protected $websiteDomains;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBaseFile(): string
    {
        return $this->baseFile;
    }

    public function setBaseFile(string $baseFile): void
    {
        $this->baseFile = $baseFile;
    }

    public function getIso(): string
    {
        return $this->iso;
    }

    public function setIso(string $iso): void
    {
        $this->iso = $iso;
    }

    public function getSnippets(): ?SnippetCollection
    {
        return $this->snippets;
    }

    public function setSnippets(SnippetCollection $snippets): void
    {
        $this->snippets = $snippets;
    }

    public function getWebsiteDomains(): ?WebsiteDomainCollection
    {
        return $this->websiteDomains;
    }

    public function setWebsiteDomains(WebsiteDomainCollection $websiteDomains): void
    {
        $this->websiteDomains = $websiteDomains;
    }
}
