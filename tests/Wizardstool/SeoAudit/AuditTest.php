<?php

namespace Wizardstool\SeoAudit;

use PHPUnit\Framework\TestCase;
use Wizardstool\SeoAudit\Audits\CanonicalUrlAudit;
use Wizardstool\SeoAudit\Audits\DescriptionAudit;
use Wizardstool\SeoAudit\Audits\TitleAudit;

class AuditTest extends TestCase
{
    public function testAuditTitleParsed(): void
    {
        $title = 'This is document with correct title';
        $subAudits = [];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            sprintf('<html><head><title>%s</title></head></html>', $title),
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEquals($title, $auditResult->getTitle(), 'Title parsed not correctly');
    }

    public function testTitleAuditNoIssue(): void
    {
        $subAudits = [new TitleAudit()];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            '<html><head><title>This is small html document with correct title ....</title></head></html>',
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEmpty($auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][TitleAudit::class]);
    }

    /**
     * @param  string  $html
     * @param  int  $issueId
     *
     * @return void
     * @dataProvider provideTitleAuditData
     */
    public function testTitleAudit(string $html, int $issueId): void
    {
        $subAudits = [new TitleAudit()];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            $html,
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertArrayHasKey($issueId, $auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][TitleAudit::class]);
    }

    public static function provideTitleAuditData(): array
    {
        return [
            'short title' => [
                '<html><head><title>This is small html document short title</title></head></html>',
                AuditIssues::TITLE_TOO_SHORT
            ],
            'long title' => [
                '<html><head><title>This is small html document and it has too long title .........</title></head></html>',
                AuditIssues::TITLE_TOO_LONG
            ],
            'title not found' => [
                '<html><head></head></html>',
                AuditIssues::TITLE_NOT_FOUND
            ],
            'many title' => [
                '<html><head><title>This is small html document and it is first title</title><title>This is small html document and it is second title</title></head></html>',
                AuditIssues::MANY_TITLES
            ],
            'title empty' => [
                '<html><head><title></title></head></html>',
                AuditIssues::TITLE_EMPTY
            ],
            'title has word repetitions' => [
                '<html><head><title>This is small html document short title short title</title></head></html>',
                AuditIssues::TITLE_HAS_WORD_REPETITIONS
            ],
            'title not within head' => [
                '<html><head></head><title>This is small html document short title</title></html>',
                AuditIssues::TITLE_NOT_WITHIN_HEAD
            ],
        ];
    }

    public function testAuditDescriptionParsed(): void
    {
        $description = 'This is document with description';
        $subAudits = [];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            sprintf('<html><head><meta name="description" content="%s" /></head></html>', $description),
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEquals($description, $auditResult->getDescription(), 'description parsed not correctly');
    }

    public function testDescriptionAuditNoIssue(): void
    {
        $subAudits = [new DescriptionAudit()];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            '<html><head><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /></head></html>',
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEmpty($auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][DescriptionAudit::class]);
    }

    /**
     * @param  string  $html
     * @param  int  $issueId
     *
     * @return void
     * @dataProvider provideDescriptionAuditData
     */
    public function testDescriptionAudit(string $html, int $issueId): void
    {
        $subAudits = [new DescriptionAudit()];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            $html,
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertArrayHasKey($issueId, $auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][DescriptionAudit::class]);
    }

    public static function provideDescriptionAuditData(): array
    {
        return [
            'short description' => [
                '<html><head><meta name="description" content="This is document with description." /></head></html>',
                AuditIssues::DESCRIPTION_TOO_SHORT
            ],
            'long description' => [
                '<html><head><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /></head></html>',
                AuditIssues::DESCRIPTION_TOO_LONG
            ],
            'description not found' => [
                '<html><head></head></html>',
                AuditIssues::DESCRIPTION_NOT_FOUND
            ],
            'many descriptions' => [
                '<html><head><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /></head></html>',
                AuditIssues::MANY_DESCRIPTIONS
            ],
            'description empty' => [
                '<html><head><meta name="description" content="" /></head></html>',
                AuditIssues::DESCRIPTION_EMPTY
            ],
            'description same as title' => [
                '<html><head><title>This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</title><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /></head></html>',
                AuditIssues::DESCRIPTION_SAME_AS_TITLE
            ],
            'description not within head' => [
                '<html><head></head><meta name="description" content="This is document with description. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." /></html>',
                AuditIssues::DESCRIPTION_NOT_WITHIN_HEAD
            ],
        ];
    }

    public function testAuditCanonicalUrlParsed(): void
    {
        $canonicalUrl = 'https://my-url-for-test.com';
        $subAudits = [];
        $audit     = new AuditBuilder(
            $canonicalUrl,
            sprintf('<html><head><link rel="canonical" href="%s"></head></html>', $canonicalUrl),
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEquals($canonicalUrl, $auditResult->getCanonicalUrl(), 'CanonicalUrl parsed not correctly');
    }

    public function testCanonicalUrlAuditNoIssue(): void
    {
        $canonicalUrl = 'https://my-url-for-test.com';
        $subAudits = [new CanonicalUrlAudit()];
        $audit     = new AuditBuilder(
            $canonicalUrl,
            sprintf('<html><head><link rel="canonical" href="%s"></head></html>', $canonicalUrl),
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertEmpty($auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][CanonicalUrlAudit::class]);
    }

    /**
     * @param  string  $html
     * @param  int  $issueId
     *
     * @return void
     * @dataProvider provideCanonicalUrlAuditData
     */
    public function testCanonicalUrlAudit(string $html, int $issueId): void
    {
        $subAudits = [new CanonicalUrlAudit()];
        $audit     = new AuditBuilder(
            'https://my-url-for-test.com',
            $html,
            $subAudits
        );
        $audit->run();
        $auditResult = $audit->getAuditResult();
        $this->assertArrayHasKey($issueId, $auditResult->getIssues()[AuditBuilder::AUDIT_CATEGORY_SEO][CanonicalUrlAudit::class]);
    }

    public static function provideCanonicalUrlAuditData(): array
    {
        return [
            'CanonicalUrl not found' => [
                '<html><head></head></html>',
                AuditIssues::CANONICAL_NOT_FOUND
            ],
            'many CanonicalUrls' => [
                '<html><head><link rel="canonical" href="https://my-url-for-test.com"><link rel="canonical" href="https://my-url-for-test.com"></head></html>',
                AuditIssues::MANY_CANONICALS
            ],
            'CanonicalUrl empty' => [
                '<html><head><link rel="canonical" href=""></head></html>',
                AuditIssues::CANONICAL_EMPTY
            ],
            'CanonicalUrl not same as page url' => [
                '<html><head><link rel="canonical" href="https://my-second-url-for-test.com"></head></html>',
                AuditIssues::CANONICAL_NOT_SAME_AS_CURRENT_URL
            ],
            'CanonicalUrl not within head' => [
                '<html><head></head><link rel="canonical" href="https://my-url-for-test.com"></html>',
                AuditIssues::CANONICAL_NOT_WITHIN_HEAD
            ],
            'not valid CanonicalUrls' => [
                '<html><head><link rel="canonical" href="https:/my-url-for-test.com"></head></html>',
                AuditIssues::CANONICAL_NOT_VALID_URL
            ],
        ];
    }
}
