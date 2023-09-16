<?php

namespace Wizardstool\SeoAudit\Audits;

use RuntimeException;
use Wizardstool\SeoAudit\AuditResult;

abstract class Audit implements AuditInterface
{
    /**
     * @var string
     */
    protected $category;

    public function __construct()
    {
        if(empty($this->category)) {
            throw new RuntimeException('Category must be filled!');
        }
    }

    /**
     * Initialize issues' category and audit keys
     *
     * @param  AuditResult  $auditResult
     *
     * @return void
     */
    public function initializeIssuesWithAuditKeys(AuditResult $auditResult): void
    {
        $issues = $auditResult->getIssues();
        $issues[$this->category][get_class($this)] = [];
        $auditResult->setIssues($issues);
    }

    public function addIssue(AuditResult $auditResult, string $key, $value): void
    {
        $auditResult->addIssue(get_class($this), $this->category, $key, $value);
    }

    /**
     * @return mixed
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param  string  $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }
}