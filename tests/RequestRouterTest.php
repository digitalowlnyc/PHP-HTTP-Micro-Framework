<?php

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (Blue Nest Digital LLC, All rights reserved)
 * Copyright: Copyright 2018 Blue Nest Digital LLC
 */

class RequestRouterTest extends TestCase
{
    /**
     * Test GET route matching
     */
    function testGetRouteMatching() {
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = "15/test-framework.php";

        $requestRouter = new RequestRouter();

        $requestRouter->registerRoute("{comment_id}/test-framework.php", "GET", function($data) {
            return (new HtmlResponseMaker)->response($data);
        });

        $requestRouter->registerRoute("{comment_id}/test-framework.php", "POST", function($data) {
            return "We did not expect to call this route!";
        });

        $response = $requestRouter->handle();

        $this->assertTrue($response['handled']);
        $this->assertEquals("<div class='content'><div>comment_id: 15</div></div>", $response['data']);
    }

    /**
     * Test POST route matching
     */
    function testPostRouteMatching() {
        $_SERVER['REQUEST_METHOD'] = "POST";
        $_SERVER['REQUEST_URI'] = "15/test-framework.php";

        $requestRouter = new RequestRouter();

        $requestRouter->registerRoute("{comment_id}/test-framework.php", "GET", function($data) {
            return "We did not expect to call this route!";
        });

        $requestRouter->registerRoute("{comment_id}/test-framework.php", "POST", function($data) {
            return (new HtmlResponseMaker)->response($data);
        });

        $response = $requestRouter->handle();

        $this->assertTrue($response['handled']);
        $this->assertEquals("<div class='content'><div>comment_id: 15</div></div>", $response['data']);
    }

    /**
     * Test that URL parameters are parsed correctly
     */
    function testRouteUrlParameterParsing() {
        $_SERVER['REQUEST_METHOD'] = "GET";
        $_SERVER['REQUEST_URI'] = "/post/123/comment/456";

        $requestRouter = new RequestRouter();

        $requestRouter->registerRoute("/post/{post_id}/comment/{comment_id}", "GET", function($data) {
            return (new ApiJsonResponseMaker())->response($data);
        });

        $response = $requestRouter->handle();

        $this->assertTrue($response['handled']);
        $this->assertJson(json_encode(["post_id" => 123, "comment_id" => 456], JSON_PRETTY_PRINT), $response['data']);
    }

    /**
     * PHPUnit helper to determine if two arrays contain the same keys and values (order does not matter)
     *
     * @param $arr1
     * @param $arr2
     */
    private function assertArraysEqual($arr1, $arr2) {
        foreach($arr1 as $key=>$val) {
            if(!array_key_exists($key, $arr2)) {
                throw new AssertionFailedError("Array 2 missing key: " . $key);
            }
            if($arr2[$key] !== $val) {
                throw new AssertionFailedError("Array values do not match for key '" . $key . "': " . $val . " vs ". $arr2[$key]);
            }
        }
        foreach($arr2 as $key2=>$val2) {
            if(!array_key_exists($key2, $arr1)) {
                throw new AssertionFailedError("Array 1 missing key: " . $key2);
            }
            if($arr1[$key2] !== $val2) {
                throw new AssertionFailedError("Array values do not match for key '" . $key2 . "': " . $val2 . " vs ". $arr1[$key2]);
            }
        }
    }
}