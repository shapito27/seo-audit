Html document seo audit (WIP)
==========

This tool applies different SEO audits to HTML page. 

How it works:
----------

1. Provide Html document as input
2. Tool will parse, extract important data and do some tests.
3. Output:
   - extracted seo data
   - audit results


Features
---------

* Extracting main SEO data: title, description, keywords, canonical etc.
* Title audits:
  * Title exist
  * Title not empty
  * Title not too short and not too long
  * No title duplicates
  * Title within head
* Description audits:
  * Description exist
  * Description not empty
  * Description not too short and not too long
  * No Description duplicates
  * Description within head
* Canonical URL audits:
  * Canonical exist
  * Canonical not empty
  * Canonical not equal page URL
  * No Canonical duplicates
  * Canonical within head
* It's possible to create your own custom Audit and pass to `AuditBuilder`


Quick Start
-----------

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use Wizardstool\SeoAudit\AuditBuilder;
use Wizardstool\SeoAudit\Audits\DescriptionAudit;
use Wizardstool\SeoAudit\Audits\CanonicalUrlAudit;
use Wizardstool\SeoAudit\Audits\TitleAudit;
use Wizardstool\SeoAudit\AuditIssues;

$subAudits = [
    new TitleAudit(),
    new DescriptionAudit(),
    new CanonicalUrlAudit(),
];

$html = <<<'HTML'
<html>
    <head>
        <title>This is title</title>
        <meta name="description" content="This is document with description." />
        <link rel="canonical" href="https://my-second-url-for-test.com">
    </head>
</html>
HTML;

$audit     = new AuditBuilder('https://my-url-for-test.com', $html, $subAudits);
$audit->run();
$result = $audit->getAuditResult();
var_dump($result);
```

This dump is Audit result:

```php
object(Wizardstool\SeoAudit\AuditResult)#6 (4) {
  ["title":"Wizardstool\SeoAudit\AuditResult":private]=>
  string(13) "This is title"
  ["description":"Wizardstool\SeoAudit\AuditResult":private]=>
  string(34) "This is document with description."
  ["canonicalUrl":"Wizardstool\SeoAudit\AuditResult":private]=>
  string(34) "https://my-second-url-for-test.com"
  ["issues":"Wizardstool\SeoAudit\AuditResult":private]=>
  array(1) {
    ["seo"]=>
    array(3) {
      ["Wizardstool\SeoAudit\Audits\TitleAudit"]=>
      array(1) {
        [4]=>
        array(1) {
          ["length"]=>
          int(13)
        }
      }
      ["Wizardstool\SeoAudit\Audits\DescriptionAudit"]=>
      array(1) {
        [4]=>
        array(1) {
          ["length"]=>
          int(34)
        }
      }
      ["Wizardstool\SeoAudit\Audits\CanonicalUrlAudit"]=>
      array(1) {
        [7]=>
        array(2) {
          ["canonical_url"]=>
          string(34) "https://my-second-url-for-test.com"
          ["page_url"]=>
          string(27) "https://my-url-for-test.com"
        }
      }
    }
  }
}
```
The first level Keys in array issues are audit type (seo, security, performance and etc.). The second level array keys are 
Audit classes e.g. `Wizardstool\SeoAudit\Audits\TitleAudit` and its keys are comes from `\Wizardstool\SeoAudit\AuditIssues`. 
For example 4 is from const `\Wizardstool\SeoAudit\AuditIssues::TITLE_TOO_SHORT`.
So you can handle too short title like this:
```php
$result = $audit->getAuditResult();

$titleTooShortResult = $result->getIssues()['seo'][TitleAudit::class][AuditIssues::TITLE_TOO_SHORT];
if (isset($titleTooShortResult['length'])) {
    echo sprintf('Title "%s" is too short, its length %d. It should be atleast %d characters.', $result->getTitle(),
        $titleTooShortResult['length'], (new TitleAudit())->getTitleMinLength());
}
```
Output:
```shell
Title "This is title" is too short, its length 13. It should be atleast 50 characters.
```
Customize audit
---------------
```php
$titleAudit = new TitleAudit();
// setup custom title limits
$titleAudit->setTitleMinLength(45);
$titleAudit->setTitleMaxLength(70);

$subAudits = [
    $titleAudit,
    new DescriptionAudit(),
    new CanonicalUrlAudit(),
];
```

Tests
---------
To execute tests:
```shell
make test
```