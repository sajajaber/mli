<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

final class SafeHtml
{
    /**
     * Sanitize trusted-admin HTML before rendering it publicly.
     */
    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="mli-safe-html">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $root = $document->getElementById('mli-safe-html');

        if (! $root) {
            return '';
        }

        self::sanitizeNode($root);

        $output = '';

        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output;
    }

    private static function sanitizeNode(DOMElement $element): void
    {
        $allowedTags = [
            'a', 'b', 'blockquote', 'br', 'code', 'em', 'h2', 'h3', 'h4',
            'i', 'li', 'ol', 'p', 'strong', 'ul', 'span', 'sub', 'sup',
        ];

        $allowedAttributes = [
            'a' => ['href', 'title', 'target', 'rel'],
            'span' => [],
            'code' => [],
        ];

        for ($node = $element->firstChild; $node !== null;) {
            $next = $node->nextSibling;

            if ($node instanceof DOMElement) {
                $tag = strtolower($node->tagName);

                if (! in_array($tag, $allowedTags, true)) {
                    $text = $node->textContent ?? '';
                    $replacement = $element->ownerDocument->createTextNode($text);
                    $element->replaceChild($replacement, $node);
                    $node = $replacement;
                } else {
                    $allowed = $allowedAttributes[$tag] ?? [];

                    for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
                        $attribute = $node->attributes->item($i);

                        if (! $attribute || ! in_array(strtolower($attribute->name), $allowed, true)) {
                            $node->removeAttributeNode($attribute);
                        }
                    }

                    if ($tag === 'a') {
                        $href = trim($node->getAttribute('href'));

                        if ($href !== '' && ! preg_match('/^(https?:|mailto:|tel:)/i', $href)) {
                            $node->removeAttribute('href');
                        }

                        if ($node->hasAttribute('target')) {
                            $node->setAttribute('rel', 'noopener noreferrer');
                        }
                    }

                    self::sanitizeNode($node);
                }
            }

            $node = $next;
        }
    }

    private function __construct()
    {
    }
}
