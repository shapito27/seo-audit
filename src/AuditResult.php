<?php

namespace Wizardstool\SeoAudit;

/**
 * AuditResult is used as result of AuditBuilder.
 */
class AuditResult
{
    private $title = '';
    private $description = '';
    private $canonicalUrl = '';
    private $htmlLang = '';
    private $viewport = '';
    private $issues = [];

    /**
     * @param  string  $auditTitle
     * @param  string  $category
     * @param  string  $key
     * @param $value
     *
     * @return void
     */
    public function addIssue(string $auditTitle, string $category, string $key, $value): void
    {
        $this->issues[$category][$auditTitle][$key] = $value;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param  string  $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param  string  $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    /**
     * @param  array  $issues
     */
    public function setIssues(array $issues): void
    {
        $this->issues = $issues;
    }

    /**
     * @return string
     */
    public function getCanonicalUrl(): string
    {
        return $this->canonicalUrl;
    }

    /**
     * @param  string  $canonicalUrl
     */
    public function setCanonicalUrl(string $canonicalUrl): void
    {
        $this->canonicalUrl = $canonicalUrl;
    }

    public function getHtmlLang(): string
    {
        return $this->htmlLang;
    }

    public function setHtmlLang(string $htmlLang): void
    {
        $this->htmlLang = $htmlLang;
    }

    public function getViewport(): string
    {
        return $this->viewport;
    }

    public function setViewport(string $viewport): void
    {
        $this->viewport = $viewport;
    }
}
