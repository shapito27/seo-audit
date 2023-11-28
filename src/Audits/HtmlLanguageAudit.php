<?php

namespace Wizardstool\SeoAudit\Audits;

use DiDom\Exceptions\InvalidSelectorException;
use RuntimeException;
use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\AuditIssues;
use Wizardstool\SeoAudit\Helpers\CountryCodes;
use Wizardstool\SeoAudit\Helpers\LanguageCodes;

class HtmlLanguageAudit extends Audit
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
        $auditResult = $audit->getAuditResult();

        // Initialize issues' category and audit keys
        $this->initializeIssuesWithAuditKeys($auditResult);

        $htmlLang = $auditResult->getHtmlLang();
        if (empty($htmlLang)) {
            $this->addIssue($auditResult, AuditIssues::HTML_LANG_EMPTY, true);

            return;
        }

        //check that html lang valid value
        try {
            $this->validateHtmlLang($htmlLang);
        } catch (\RuntimeException $exception) {
            $this->addIssue($auditResult, AuditIssues::HTML_LANG_NOT_VALID, [$exception->getMessage()]);
            return;
        }

        //@todo can add https://github.com/patrickschur/language-detection for text detection
//            if ($htmlLang !== $audit->getPageUrl()) {
//                $this->addIssue($auditResult,
//                    AuditIssues::CANONICAL_NOT_SAME_AS_CURRENT_URL,
//                    [
//                        'canonical_url' => $htmlLang,
//                        'page_url'      => $audit->getPageUrl(),
//                    ]
//                );
//
//                return;
//            }

    }

    protected function validateHtmlLang(string $htmlLang): void
    {
        $langCode    = '';
        $countryCode = '';
        if (strpos($htmlLang, '-') !== false) {
            $tmp = explode($htmlLang, '-');
            if (!empty($tmp[0]) && !empty($tmp[1]) && !isset($tmp[2])) {
                $langCode    = $tmp[0];
                $countryCode = $tmp[1];
            } else {
                throw new RuntimeException('Not valid html lang');
            }
        } else {
            $langCode = $htmlLang;
        }

        //check if lang valid
        if (!isset(LanguageCodes::CODES[$langCode])) {
            throw new RuntimeException(
                sprintf(
                    'Lang code "%s" from html lang "%s" is not valid according to ISO 639-1',
                    $langCode,
                    $htmlLang
                )
            );
        }
        if (!empty($countryCode)) {
            //check if country code valid
            if (!isset(CountryCodes::COUNTRIES[$countryCode])) {
                throw new RuntimeException(
                    sprintf(
                        'Country code "%s" from html lang "%s" is not valid according to ISO 3166-1 alpha-2',
                        $countryCode,
                        $htmlLang
                    )
                );
            }
        }
    }
}
