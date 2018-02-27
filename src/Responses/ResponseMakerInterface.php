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
 * Interface for response makers, which are classes that take in an HTTP request and generate a response of some
 * sort, such as HTML or JSON data
 *
 */

interface ResponseMakerInterface {
    /**
     * Perform an actual response. For example, for JSON data, echo a JSON string and then die().
     *
     * @param $data
     * @param $message
     * @return mixed
     */
    public function doResponse($responseData);

    /**
     * Generate data to respond with (html, json, etc)
     * @param $data
     * @param $message
     * @return mixed
     */
    public function response($data, $message);

    /**
     * Same as respond(), but for error scenarios
     *
     * @param $data
     * @param $message
     * @return mixed
     */
    public function error($data, $message);
}