<?php

namespace Alms\Testing\Trait;

use Alms\Testing\AssertableJsonString;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

trait JsonAsserts
{
    private function decodeResponseJson(): AssertableJsonString
    {
        $content = $this->getContent();

        $testJson = new AssertableJsonString(
            empty($content) ? [] : $content,
        );

        $decodedResponse = $testJson->json();

        if (is_null($decodedResponse) || $decodedResponse === false)
        {
            Assert::fail('Invalid JSON was returned from the route.');
        }

        return $testJson;
    }

    private function getContent(): false|string
    {
        /** @var Response $response */
        if (!$response = self::getClient()?->getResponse()) {
            static::fail('A client must have an HTTP Response to make assertions. Did you forget to make an HTTP request?');
        }

        return $response->getContent();
    }

    public function assertJsonResponse(): AssertableJsonString
    {
        return $this->decodeResponseJson();
    }
}