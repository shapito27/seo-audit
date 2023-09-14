<?php

namespace Wizardstool\SeoAudit\Audits;

use DiDom\Exceptions\InvalidSelectorException;
use RuntimeException;
use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\AuditIssues;

class CanonicalUrlAudit extends Audit
{
    protected $category = AuditBuilder::AUDIT_CATEGORY_SEO;

    /**
     * @param  AuditBuilder  $audit
     *
     * @return void
     * @throws InvalidSelectorException
     */
    public function run(AuditBuilder $audit): void
    {
        $canonicalUrls = $audit->getDocument()->find("link[rel='canonical']");

        $numberOfElements = count($canonicalUrls);

        $auditResult = $audit->getAuditResult();

        // Initialize issues' category and audit keys
        $this->initializeIssuesWithAuditKeys($auditResult);

        if ($numberOfElements > 1) {
            $canonicalUrlsContent = [];
            foreach ($canonicalUrls as $canonicalUrl) {
                $canonicalUrlsContent[] = $canonicalUrl->content;
            }
            $this->addIssue($auditResult, AuditIssues::MANY_CANONICALS, $canonicalUrlsContent);
        } elseif ($numberOfElements === 1) {
            $parent = $canonicalUrls[0]->parent();
            if ($parent === null) {
                throw new RuntimeException('Can\'t parse parent of canonicalUrl');
            }
            $canonicalUrl = $auditResult->getcanonicalUrl();

            // check canonicalUrl within tag head
            if ($canonicalUrls[0]->parent()->getNode()->tagName !== 'head') {
                $this->addIssue($auditResult, AuditIssues::CANONICAL_NOT_WITHIN_HEAD, $canonicalUrl);

                return;
            }

            if (empty($canonicalUrl)) {
                $this->addIssue($auditResult, AuditIssues::CANONICAL_EMPTY, true);

                return;
            }

            if(!filter_var($canonicalUrl, FILTER_VALIDATE_URL)) {
                $this->addIssue($auditResult, AuditIssues::CANONICAL_NOT_VALID_URL, [$canonicalUrl]);

                return;
            }

            if ( $canonicalUrl !== $audit->getPageUrl()) {
                $this->addIssue($auditResult, AuditIssues::CANONICAL_NOT_SAME_AS_CURRENT_URL, ['canonical_url' => $canonicalUrl, 'page_url' => $audit->getPageUrl()]);

                return;
            }
        } else {
            $this->addIssue($auditResult, AuditIssues::CANONICAL_NOT_FOUND, true);
        }
    }
}