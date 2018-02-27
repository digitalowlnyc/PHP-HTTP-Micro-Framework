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
 * Generate an HTML response with some basic styles for error and success messages
 *
 * @see ResponseMakerInterface
 */

class StyledHtmlResponseMaker extends HtmlResponseMaker
{
    const ERROR_MESSAGE_COLOR = 'red';
    const SUCCESS_MESSAGE_COLOR = 'green';

    protected function createResponse($success, $data = null, $message = null) {
        $responseHtml = parent::createResponse($success, $data, $message);

        $styleHtml = "<style>";
        $styleHtml .= ".error { color: " . self::ERROR_MESSAGE_COLOR . " }" . PHP_EOL;
        $styleHtml .= ".success { color: " . self::SUCCESS_MESSAGE_COLOR . " } " . PHP_EOL;
        $styleHtml .= "</style>";

        return $styleHtml . PHP_EOL . $responseHtml;
    }
}