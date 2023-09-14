<?php

namespace Wizardstool\SeoAudit\Audits;

use DiDom\Exceptions\InvalidSelectorException;
use RuntimeException;
use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\AuditIssues;

class TitleAudit extends Audit
{
    public const TITLE_MIN_LENGTH = 50;
    public const TITLE_MAX_LENGTH = 60;
    protected $category = AuditBuilder::AUDIT_CATEGORY_SEO;

    protected $titleMinLength = 50;
    protected $titleMaxLength = 60;

    /**
     * @param  AuditBuilder  $audit
     *
     * @return void
     * @throws InvalidSelectorException
     */
    public function run(AuditBuilder $audit): void
    {
        $titles = $audit->getDocument()->find('title');

        $numberOfElements = count($titles);

        $auditResult = $audit->getAuditResult();

        // Initialize issues' category and audit keys
        $this->initializeIssuesWithAuditKeys($auditResult);

        if ($numberOfElements > 1) {
            $titlesText = [];
            foreach ($titles as $title) {
                $titlesText[] = $title->text();
            }
            $this->addIssue($auditResult, AuditIssues::MANY_TITLES, $titlesText);
        } elseif ($numberOfElements === 1) {
            $parent = $titles[0]->parent();
            if ($parent === null) {
                throw new RuntimeException('Can\'t parse title parent');
            }
            $title = $auditResult->getTitle();

            // check title within tag head
            if ($titles[0]->parent()->getNode()->tagName !== 'head') {
                $this->addIssue($auditResult, AuditIssues::TITLE_NOT_WITHIN_HEAD, $title);

                return;
            }

            $titleLength = mb_strlen($title);
            if (empty($title)) {
                $this->addIssue($auditResult, AuditIssues::TITLE_EMPTY, true);

                return;
            }

            if ($titleLength < $this->titleMinLength) {
                $this->addIssue($auditResult, AuditIssues::TITLE_TOO_SHORT, ['length' => $titleLength]);

                return;
            }
            if ($titleLength > $this->titleMaxLength) {
                $this->addIssue($auditResult, AuditIssues::TITLE_TOO_LONG, ['length' => $titleLength]);

                return;
            }
            $duplicates = $this->findTitleWordDuplicates($title);
            if ( ! empty($duplicates)) {
                $this->addIssue($auditResult, AuditIssues::TITLE_HAS_WORD_REPETITIONS, ['duplicates' => $duplicates]);

                return;
            }
        } else {
            $this->addIssue($auditResult, AuditIssues::TITLE_NOT_FOUND, true);
        }
    }


    /**
     * @param  string  $str
     *
     * @return array|false|string[]
     */
    private function strWordCountUtf8(string $str)
    {
        return preg_split('~[^\p{L}\p{N}\']+~u', $str);
    }

    /**
     * @param  string  $title
     *
     * @return array
     */
    private function findTitleWordDuplicates(string $title): array
    {
        $duplicates = [];
        $words      = $this->strWordCountUtf8(strtolower($title));
        $words      = array_count_values($words);

        foreach ($words as $word => $number) {
            // if word is longer than 3 characters and repeats more or equal to 2 times
            if ($number >= 2 && mb_strlen($word) > 3) {
                $duplicates[] = $word;
            }
        }

        return $duplicates;
    }
}