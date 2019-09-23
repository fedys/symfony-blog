<?php

namespace App\Tests\Functional\Traits;

use Symfony\Component\DomCrawler\Crawler;

trait FormTrait
{
    /**
     * @param Crawler $formCrawler
     * @param string  $name
     * @param array   $expectedErrors
     */
    private function assertFormElementErrors(Crawler $formCrawler, string $name, array $expectedErrors): void
    {
        $errorCrawler = $formCrawler->filterXPath(sprintf('//*[@name="%s"]/ancestor::div[contains(concat(" ",normalize-space(@class)," ")," form-group ")][1]//span[contains(concat(" ",normalize-space(@class)," ")," form-error-message ")]', $name));
        $actualErrors = $errorCrawler->each(function (Crawler $errorElement) {
            return trim($errorElement->text());
        });
        $this->assertSame($expectedErrors, $actualErrors);
    }
}
