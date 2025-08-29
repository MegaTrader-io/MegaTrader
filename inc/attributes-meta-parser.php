<?php
/**
 * Attribute Meta Parser
 *
 * Provides a global helper function to parse product attribute_meta arrays
 * separating plain text strings and config directives (starting with @).
 *
 * Usage:
 *   $parsed = parse_attribute_array($input_array);
 *
 *   $parsed['data']   → array of strings
 *   $parsed['config'] → array of structured config options
 *
 * ------------------------------------------------------------
 * Supported Config Types
 * ------------------------------------------------------------
 *
 * 1. @status
 *    Marks an element with a status string (e.g., "hidden", "disabled").
 *    Example:
 *      @status "hidden"
 *    Output:
 *      [ 'status' => 'hidden' ]
 *
 * 2. @badge
 *    Adds a badge with text and a status.
 *    Example:
 *      @badge {"text": "Coming Soon", "style": "neutral"}
 *    Output:
 *      [ 'badge' => [ 'text' => 'Coming Soon', 'style' => 'neutral' ] ]
 *
 * 3. @link
 *    Defines a list of links with icon, text, and URL.
 *    Example:
 *      @link [{"icon":"path_to_icon","text":"Web App","url":"url…"}]"
 *    Output:
 *      [ 'link' => [
 *          [ 'icon' => 'path_to_icon', 'text' => 'Web App', 'url' => 'url…' ]
 *        ]
 *      ]
 *
 * ------------------------------------------------------------
 * Notes
 * ------------------------------------------------------------
 * - Config strings must start with "@" followed by a type name.
 * - Config payloads should be JSON-compatible for parsing.
 * - Plain strings without "@" are collected under "data".
 *
 * ------------------------------------------------------------
 */

if (!function_exists('parse_attribute_meta')) {
    /**
     * Parse array of mixed text/config strings into structured object
     *
     * @param array $input
     * @return array { data: [...], config: [...] }
     */
    function parse_attribute_meta(array $input): array {
        $result = [
            'data' => [],
            'config' => []
        ];

        foreach ($input as $item) {
            $item = trim($item);

            if (strpos($item, '@') === 0) {
                if (preg_match('/^@([a-zA-Z0-9_-]+)\s*(.*)$/', $item, $matches)) {
                    $key = $matches[1];
                    $payload = trim($matches[2]);
                    $payload = str_replace(['“','”'], '"', $payload);

                    $value = $payload;

                    if ($payload !== '') {
                        $decoded = json_decode($payload, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $value = $decoded;
                        }else {
                            error_log(sprintf(
                                '[parse_attribute_meta] JSON decode error "%s" on payload: %s',
                                json_last_error_msg(),
                                $payload
                            ));
                        }
                    }

                    $result['config'][$key] = $value;
                }
            } else {
                $result['data'][] = $item;
            }
        }

        return $result;
    }
}

if (!function_exists('mt_is_coming_soon')) {
    function mt_is_coming_soon($badge_text = ''): bool
    {
        return strtolower(str_replace(' ', '', trim($badge_text))) == 'comminsoon'
            || strtolower(str_replace(' ', '', trim($badge_text))) == 'comingsoon';
    }
}