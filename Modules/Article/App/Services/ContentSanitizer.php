<?php

namespace Modules\Article\App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class ContentSanitizer
{
    public function sanitize(string $html): string
    {
        return (new HtmlSanitizer((new HtmlSanitizerConfig)->allowSafeElements()->allowRelativeLinks()->allowRelativeMedias()->withMaxInputLength(2000000)))->sanitize($html);
    }
}
