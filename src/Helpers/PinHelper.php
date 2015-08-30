<?php

namespace seregazhuk\PinterestBot\Helpers;

class PinHelper
{
    /**
     * Create Pinterest API request form commenting pin
     *
     * @param int    $pinId
     * @param string $text
     * @return array
     */
    public static function createCommentRequest($pinId, $text)
    {
        $dataJson                    = self::createPinRequestData($pinId);
        $dataJson["options"]["text"] = $text;

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Create Pinterest API request form commenting pin
     *
     * @param int $pinId
     * @param int $commentId
     * @return array
     */
    public static function createCommentDeleteRequest($pinId, $commentId)
    {
        $dataJson                          = self::createPinRequestData($pinId);
        $dataJson["options"]["comment_id"] = $commentId;

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Checks result of PIN-methods
     *
     * @param array $res
     * @return bool
     */
    public static function checkMethodCallResult($res)
    {
        if ($res !== null && isset($res['resource_response'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Creates Pinterest API request for Pin creation
     *
     * @param string $description
     * @param string $imageUrl
     * @param int    $boardId
     * @return array
     */
    public static function createPinCreationRequest($imageUrl, $boardId, $description = "")
    {
        $dataJson = [
            "options" => [
                "method"      => "scraped",
                "description" => $description,
                "link"        => $imageUrl,
                "image_url"   => $imageUrl,
                "board_id"    => $boardId,
            ],
            "context" => new \stdClass(),
        ];

        return [
            "source_url" => "/pin/create/bookmarklet/?url=" . urlencode($imageUrl),
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }


    /**
     * Creates Pinterest API request for Pin repin
     *
     * @param string $description
     * @param int    $repinId
     * @param int    $boardId
     * @return array
     */
    public static function createRepinRequest($repinId, $boardId, $description)
    {
        $dataJson = [
            "options" => [
                "board_id"    => $boardId,
                "description" => stripslashes($description),
                "link"        => stripslashes($repinId),
                "is_video"    => null,
                "pin_id"      => $repinId,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/pin/{$repinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }


    /**
     * Parses pin create response
     *
     * @param $response
     * @return bool
     */
    public static function parsePinCreateResponse($response)
    {
        if (isset($response['resource_response']['data']['id'])) {
            return $response['resource_response']['data']['id'];
        }

        return false;
    }

    /**
     * Creates Pinterest API request to get Pin info
     *
     * @param int $pinId
     * @return array
     */
    public static function createInfoRequest($pinId)
    {
        $dataJson = [

            "options" => [
                "field_set_key" => "detailed",
                "fetch_visualsearchCall_objects" => true,
                "id"            => $pinId,
                "allow_stale"   => true,
            ],
            "context" => new \StdClass(),
        ];

        return [
            "source_url" => "/pin/$pinId/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Parses Pinterest API response with pin information
     *
     * @param array $res
     * @return null|array
     */
    public static function parsePinInfoResponse($res)
    {
        if ( ! empty($res)) {
            if (isset($res['resource_response']['data'])) {
                return $res['resource_response']['data'];
            }
        }

        return null;
    }

    /**
     * Creates common pin request data by PinId
     *
     * @param $pinId
     * @return array
     */
    public static function createPinRequestData($pinId)
    {
        return [
            "options" => [
                "pin_id" => $pinId,
            ],
            "context" => [],
        ];
    }

    /**
     * Creates simple Pin request by PinId (used by delete and like requests)
     *
     * @param $pinId
     * @return array
     */
    public static function createSimplePinRequest($pinId)
    {
        $dataJson = self::createPinRequestData($pinId);

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }
}
