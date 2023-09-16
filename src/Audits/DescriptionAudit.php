<?php

namespace Wizardstool\SeoAudit\Audits;

use DiDom\Exceptions\InvalidSelectorException;
use RuntimeException;
use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\AuditIssues;

class DescriptionAudit extends Audit
{
    protected $category = AuditBuilder::AUDIT_CATEGORY_SEO;
    protected $descriptionMinLength = 150;
    protected $descriptionMaxLength = 170;

    /**
     * @param  AuditBuilder  $audit
     *
     * @return void
     * @throws InvalidSelectorException
     */
    public function run(AuditBuilder $audit): void
    {
        $descriptions = $audit->getDocument()->find("meta[name='description']");

        $numberOfElements = count($descriptions);

        $auditResult = $audit->getAuditResult();

        // Initialize issues' category and audit keys
        $this->initializeIssuesWithAuditKeys($auditResult);

        if ($numberOfElements > 1) {
            $descriptionsContent = [];
            foreach ($descriptions as $description) {
                $descriptionsContent[] = $description->content;
            }
            $this->addIssue($auditResult, AuditIssues::MANY_DESCRIPTIONS, $descriptionsContent);
        } elseif ($numberOfElements === 1) {
            $parent = $descriptions[0]->parent();
            if ($parent === null) {
                throw new RuntimeException('Can\'t parse parent of description');
            }
            $description = $auditResult->getDescription();

            // check description within tag head
            if ($descriptions[0]->parent()->getNode()->tagName !== 'head') {
                $this->addIssue($auditResult, AuditIssues::DESCRIPTION_NOT_WITHIN_HEAD, $description);

                return;
            }


            $descriptionLength = mb_strlen($description);
            if (empty($description)) {
                $this->addIssue($auditResult, AuditIssues::DESCRIPTION_EMPTY, true);

                return;
            }

            if ($descriptionLength < $this->descriptionMinLength) {
                $this->addIssue($auditResult, AuditIssues::DESCRIPTION_TOO_SHORT, ['length' => $descriptionLength]);

                return;
            }
            if ($descriptionLength > $this->descriptionMaxLength) {
                $this->addIssue($auditResult, AuditIssues::DESCRIPTION_TOO_LONG, ['length' => $descriptionLength]);

                return;
            }
            if ( $description === $auditResult->getTitle()) {
                $this->addIssue($auditResult, AuditIssues::DESCRIPTION_SAME_AS_TITLE, [$description]);

                return;
            }
        } else {
            $this->addIssue($auditResult, AuditIssues::DESCRIPTION_NOT_FOUND, true);
        }
    }

    /**
     * @param  int  $descriptionMinLength
     */
    public function setDescriptionMinLength(int $descriptionMinLength): void
    {
        $this->descriptionMinLength = $descriptionMinLength;
    }

    /**
     * @param  int  $descriptionMaxLength
     */
    public function setDescriptionMaxLength(int $descriptionMaxLength): void
    {
        $this->descriptionMaxLength = $descriptionMaxLength;
    }

    /**
     * @return int
     */
    public function getDescriptionMinLength(): int
    {
        return $this->descriptionMinLength;
    }

    /**
     * @return int
     */
    public function getDescriptionMaxLength(): int
    {
        return $this->descriptionMaxLength;
    }
}