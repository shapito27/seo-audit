<?php

namespace Wizardstool\SeoAudit\Audits;

use Wizardstool\SeoAudit\AuditBuilder;

interface AuditInterface
{
    public function run(AuditBuilder $audit);
}