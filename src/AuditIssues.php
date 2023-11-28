<?php
namespace Wizardstool\SeoAudit;
class AuditIssues
{
    public const TITLE_NOT_FOUND = 1;
    public const MANY_TITLES = 2;
    public const TITLE_EMPTY = 3;
    public const TITLE_TOO_SHORT = 4;
    public const TITLE_TOO_LONG = 5;
//    public const TITLE_ALMOST_EXCEEDS_LENGTH = 6;
//    public const TITLE_CORRECT = 7;
    public const TITLE_HAS_WORD_REPETITIONS = 8;
    public const TITLE_NOT_WITHIN_HEAD = 9;

    public const DESCRIPTION_NOT_FOUND = 1;
    public const MANY_DESCRIPTIONS = 2;
    public const DESCRIPTION_EMPTY = 3;
    public const DESCRIPTION_TOO_SHORT = 4;
    public const DESCRIPTION_TOO_LONG = 5;
//    public const DESCRIPTION_ALMOST_EXCEEDS_LENGTH = 6;
//    public const DESCRIPTION_CORRECT = 7;
//    public const DESCRIPTION_HAS_WORD_REPETITIONS = 8;
    public const DESCRIPTION_NOT_WITHIN_HEAD = 9;
    public const DESCRIPTION_SAME_AS_TITLE = 10;

    public const CANONICAL_NOT_FOUND = 1;
    public const MANY_CANONICALS = 2;
    public const CANONICAL_EMPTY = 3;
    public const CANONICAL_NOT_VALID_URL = 4;
    public const CANONICAL_CORRECT = 5;
    public const CANONICAL_NOT_WITHIN_HEAD = 6;
    public const CANONICAL_NOT_SAME_AS_CURRENT_URL = 7;

    public const HTML_LANG_EMPTY = 3;
    public const HTML_LANG_NOT_VALID = 4;
    public const HTML_LANG_NOT_SAME_AS_ON_PAGE = 7;

    public const VIEWPORT_NOT_FOUND = 1;
    public const MANY_VIEWPORTS = 2;
    public const VIEWPORT_EMPTY = 3;
    public const VIEWPORT_NOT_VALID = 4;
    public const VIEWPORT_NOT_WITHIN_HEAD = 9;
}
