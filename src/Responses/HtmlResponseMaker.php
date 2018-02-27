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
 * Generate an HTML response with a content div and message div
 *
 * @see ResponseMakerInterface
 */

class HtmlResponseMaker implements ResponseMakerInterface {
    const JSON_RESPONSE_OPTIONS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
    private $defaultErrorMessage = "An error occurred";

    protected function createResponse($success, $data = null, $message = null) {
        $responseHtml = "";

        if($success === 0) {
            if($message === null) {
                $message = $this->defaultErrorMessage;
            }
        }

        if($message !== null) {
            $messageClass = $success ? 'success' : 'error';
            $responseHtml .= "<div class='message " . $messageClass . "'>" . $message . "</div>";
        }

        $responseHtml .=  "<div class='content'>";
        if($data !== null) {
            if(is_array($data)) {
                foreach($data as $key => $val) {
                    $responseHtml .= "<div>" . $key . ": " . $val . "</div>";
                }
            } else {
                $responseHtml .= $data;
            }
        }
        $responseHtml .=  "</div>";

        return $responseHtml;
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