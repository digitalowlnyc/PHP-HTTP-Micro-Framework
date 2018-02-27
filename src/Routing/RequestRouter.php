<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * License: (Blue Nest Digital LLC, All rights reserved)
 * Copyright: Copyright 2018 Blue Nest Digital LLC
 */

/**
 * Class Documentation
 *
 * Description:
 * An HTTP request router that provides for registering URI patterns to look for and what callbacks to run
 * if one of those URI patterns matches an HTTP request.
 *
 * Basic Usage:
 *
 * $requestRouter = new RequestRouter();
 *
 * $requestRouter->registerRoute("post/{post_id}/{comment_id}", "GET", function($data) {
 *  return "This route was matched";
 * });
 *
 * $requestRouter->handle();
 *
 */

class RequestRouter {
    /**
     * Stores any registered routes. Each route consists of an array entry that looks like the following:
     *
     *  [
     *      "callback" => ...,
     *      "regex" => ...,
     *      "request-method" => ...
     *  ]
     *
     * @var array
     */
    private $routes = [];

    /**
     * Take a regex-like pattern and turn it into a usable regex for matching against potential URIs.
     * The pattern format provides for using "{" "}" enclosing characters to define a route parameter,
     *      for example: http://www.test.dev/{post_id}/{comment_id}
     *
     * @param $routePattern string A regex-like pattern to match against a URI
     * @return string Regex
     */
    private function parseRoutePatternIntoRegex($routePattern) {
        preg_match_all( "#{([^/]+)}#", $routePattern, $matches, PREG_PATTERN_ORDER);

        array_shift($matches); // get rid of unneeded full match

        foreach($matches as $match) {
            foreach($match as $matchGroup) {
                $routePattern = str_replace("{" . $matchGroup . "}", "(?P<$matchGroup>" . "[^/]+" . ")", $routePattern);
            }
        }

        return $routePattern;
    }

    /**
     * Look at the current HTTP request and try to match it to a registered route based on 1) HTTP method and 2) the route
     * url pattern that was registered.
     * If a matching route is found, call the registered callback function with the appropriate data.
     * Only one route can match per request. Matches will be attempted in the order the routes were registered.
     *
     * @return array An array containing the data passed back from the route callback, as well as a flag indicating if the
     * uri matched any registered routes.
     */
    function handle() {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        if($requestMethod === 'GET') {
            $data = $_GET;
        } else {
            $data = $_POST;
        }

        $uri = $_SERVER['REQUEST_URI'];

        $routeHandled = false; // whether a matching route handler was found/executed
        $routeResponse = null; // the response data from the route callback

        /**
         * Look through registered routes for a pattern that matches the current tuple of
         * (uri, HTTP method).
         */
        foreach($this->routes as $routePatternData) {
            if(strtoupper($requestMethod) !== $routePatternData['request-method']) {
                continue;
            }

            $routePattern = $routePatternData['regex'];

            if(!preg_match("#" . $routePattern . "#", $uri, $matches)) {
                continue;
            }

            // Note: preg_match with named capture groups will always include both the name and the index,
            // so filter out the numeric indices since we won't use them
            $matches = array_filter($matches, function($item, $key) {
                return !is_int($key);
            }, ARRAY_FILTER_USE_BOTH);

            $routeCallback = $routePatternData['callback'];
            $routeResponse = $routeCallback(array_merge($data, $matches));

            $routeHandled = true;
            break; // Stop after first matched route
        }

        return [
            "handled" => $routeHandled,
            "data" => $routeResponse
        ];
    }

    /**
     * Register a route pattern. Use "{" "}" to enclose any path segments you want to be parsed and passed
     * as parameters to your route callback.
     *
     * @param $routePattern string
     * @param $routeHttpRequestMethod string HTTP request method, e.g. POST, GET, PUT
     * @param $routeCallback callable A callback that takes one parameter (an array of data consisting of GET params, POST data and the parsed url)
     *
     */
    function registerRoute($routePattern, $routeHttpRequestMethod, $routeCallback) {
        $routeRegexPattern = $this->parseRoutePatternIntoRegex($routePattern);
        $this->routes[] = [
            "callback" => $routeCallback,
            "regex" => $routeRegexPattern,
            "request-method" => $routeHttpRequestMethod
        ];
    }
}