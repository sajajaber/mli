<?php

namespace Tests\Unit;

use App\Models\Show;
use App\Support\SafeHtml;
use PHPUnit\Framework\TestCase;

class SecurityAndMediaTest extends TestCase
{
    public function test_vimeo_urls_are_converted_to_player_embeds(): void
    {
        $show = new Show([
            'vimeo_url' => 'https://vimeo.com/123456789',
        ]);

        $this->assertSame(
            'https://player.vimeo.com/video/123456789',
            $show->vimeo_embed_url,
        );

        $showWithPrivacyHash = new Show([
            'vimeo_url' => 'https://vimeo.com/355320399/1b05615f96',
        ]);

        $this->assertSame(
            'https://player.vimeo.com/video/355320399',
            $showWithPrivacyHash->vimeo_embed_url,
        );
    }

    public function test_invalid_vimeo_urls_do_not_produce_an_embed(): void
    {
        $show = new Show([
            'vimeo_url' => 'https://example.com/video/123456789',
        ]);

        $this->assertNull($show->vimeo_embed_url);
    }

    public function test_public_html_is_sanitized(): void
    {
        $html = '<p>Hello <strong>world</strong></p><script>alert(1)</script>'
            . '<img src=x onerror="alert(2)">'
            . '<a href="javascript:alert(3)" onclick="alert(4)">link</a>';

        $clean = SafeHtml::clean($html);

        $this->assertStringContainsString('<strong>world</strong>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
    }
}
