<?php
namespace Wizardstool\SeoAudit;

use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use UnexpectedValueException;
use Wizardstool\SeoAudit\Audits\Audit;

class AuditBuilder
{
    /**
     * @var string page url to audit
     */
    private $pageUrl;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Document
     */
    private $document;

    /**
     * @var Audit[]|null
     */
    private $audits;

    /**
     * @var AuditResult
     */
    private $auditResult;

    public const AUDIT_CATEGORY_SEO = 'seo';
    public const AUDIT_CATEGORY_SECURITY = 'security';
    public const AUDIT_CATEGORY_PERFORMANCE = 'performance';

    /**
     * @param  string  $url
     * @param  string  $content  html content
     * @param  array|null  $audits
     */
    public function __construct(string $url, string $content, ?array $audits)
    {
        $this->pageUrl = $url;
        $this->content = $content;
        $this->audits = $audits;
        // parse html
        $this->document = new Document($content, false);
        $this->auditResult = new AuditResult();
    }

    /**
     * @return void
     * @throws InvalidSelectorException
     */
    public function run(): void
    {
        $this->parseTitle();
        $this->parseDescription();
        $this->parseCanonical();

        foreach ($this->audits as $audit) {
            if ( ! $audit instanceof Audit) {
                throw new UnexpectedValueException(
                    sprintf('Audit %s must extend %s class!',
                        get_class($audit),
                        Audit::class)
                );
            }
            $audit->run($this);
        }
    }

    /**
     * @return void
     * @throws InvalidSelectorException
     */
    public function parseTitle(): void
    {
        $title = $this->document->first('title');
        if($title instanceof Element) {
            $this->auditResult->setTitle($title->text());
        }
    }

    public function parseDescription(): void
    {
        $description = $this->getDocument()->first("meta[name='description']");
        if($description instanceof Element) {
            $this->auditResult->setDescription($description->content);
        }
    }

    public function parseCanonical(): void
    {
        $canonical = $this->getDocument()->first("link[rel='canonical']");
        if($canonical instanceof Element) {
            $this->auditResult->setCanonicalUrl($canonical->href);
        }
    }

    /**
     * @return string
     */
    public function getPageUrl(): string
    {
        return $this->pageUrl;
    }

    /**
     * @param  string  $pageUrl
     */
    public function setPageUrl(string $pageUrl): void
    {
        $this->pageUrl = $pageUrl;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param  string  $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @param  Document  $document
     */
    public function setDocument(Document $document): void
    {
        $this->document = $document;
    }

    /**
     * @return array
     */
    public function getAudits(): array
    {
        return $this->audits;
    }

    /**
     * @param  mixed  $audits
     */
    public function setAudits($audits): void
    {
        $this->audits = $audits;
    }

    /**
     * @return AuditResult
     */
    public function getAuditResult(): AuditResult
    {
        return $this->auditResult;
    }

    /**
     * @param  AuditResult  $auditResult
     */
    public function setAuditResult(AuditResult $auditResult): void
    {
        $this->auditResult = $auditResult;
    }
}