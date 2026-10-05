<?php

declare(strict_types=1);

namespace ShopStack\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testEscapesHtmlCharacters(): void
    {
        self::assertSame('&lt;script&gt;&quot;alert&lt;/script&gt;', e('<script>"alert</script>'));
    }

    public function testFormatsMoneyInPolishFormat(): void
    {
        self::assertSame('1 234,50 zł', money('1234.5'));
    }

    public function testCsrfTokenIsStableAndValidTokenIsAccepted(): void
    {
        $_SESSION = [];
        $token = csrf_token();
        self::assertSame($token, csrf_token());
        $_POST['_csrf'] = $token;
        verify_csrf();
    }
}
