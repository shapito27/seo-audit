<?php

namespace Wizardstool\SeoAudit\Audits;

use DiDom\Exceptions\InvalidSelectorException;
use RuntimeException;
use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\AuditIssues;

class ViewportAudit extends Audit
{
    protected $category = AuditBuilder::AUDIT_CATEGORY_SEO;

    /**
     * @param AuditBuilder $audit
     *
     * @return void
     * @throws InvalidSelectorException
     */
    public function run(AuditBuilder $audit): void
    {
        $viewports = $audit->getDocument()->find("meta[name='viewport']");
//meta name="viewport"
        $numberOfElements = count($viewports);
        $auditResult = $audit->getAuditResult();

        // Initialize issues' category and audit keys
        $this->initializeIssuesWithAuditKeys($auditResult);

        if ($numberOfElements > 1) {
            $viewportsValues = [];
            foreach ($viewports as $viewport) {
                $viewportsValues[] = $viewport->content();
            }
            $this->addIssue($auditResult, AuditIssues::MANY_VIEWPORTS, $viewportsValues);
        } elseif ($numberOfElements === 1) {
            $parent = $viewports[0]->parent();
            if ($parent === null) {
                throw new RuntimeException('Can\'t parse viewport parent tag');
            }
            $viewport = $auditResult->getViewport();

            // check title within tag head
            if ($viewports[0]->parent()->getNode()->tagName !== 'head') {
                $this->addIssue($auditResult, AuditIssues::VIEWPORT_NOT_WITHIN_HEAD, $viewport);

                return;
            }

            if (empty($viewport)) {
                $this->addIssue($auditResult, AuditIssues::VIEWPORT_EMPTY, true);

                return;
            }
            //check that viewport valid value
            try {
                $this->validateViewport($viewport);
            } catch (\RuntimeException $exception) {
                $this->addIssue($auditResult, AuditIssues::VIEWPORT_NOT_VALID, [$exception->getMessage()]);
                return;
            }
        } else {
            $this->addIssue($auditResult, AuditIssues::VIEWPORT_NOT_FOUND, true);
        }
    }

    protected function validateViewport(string $viewport): void
    {
        /**
         * @todo check that content has width=, initial-scale=
         * what are possible values?
         */
    }
}
