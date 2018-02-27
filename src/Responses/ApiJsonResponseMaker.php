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
 * Generate a JSON response for API usage with top level keys as follows:
 * - "response" - The actual response data
 * - "success" - 0/1 or other api specific status code
 * - "message" - A message with some information for the user
 *
 * @see ResponseMakerInterface
 */

class ApiJsonResponseMaker implements ResponseMakerInterface {
    const JSON_RESPONSE_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;

    private function createResponse($success, $data = null, $message = null) {
        $responseData = [
            "response" => $data,
            "success" => $success,
        ];

        if($data !== null) {
            $responseData['response'] = $data;
        }

        if($message !== null) {
            $responseData['message'] = $message;
        }

        return json_encode($responseData, static::JSON_RESPONSE_OPTIONS);
    }

    public function doResponse($responseData) {
        echo $responseData;
        return $responseData;
    }

    public function response($data = null, $message = null) {
        $responseData = $this->createResponse(1, $data, $message);
        return $this->doResponse($responseData);
    }

    public function error($data = null, $message = null) {
        $responseData = $this->createResponse(0, $data, $message);
        return $this->doResponse($responseData);
    }
}