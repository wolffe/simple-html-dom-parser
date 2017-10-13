<?php
/**
 * Extract specific HTML tags and their attributes from a string.
 *
 * You can either specify one tag, an array of tag names, or a regular expression that matches the tag name(s).
 * If multiple tags are specified you must also set the $selfclosing parameter and it must be the same for
 * all specified tags (so you can't extract both normal and self-closing tags in one go).
 *
 * The function returns a numerically indexed array of extracted tags. Each entry is an associative array
 * with these keys:
 *  tag_name    - the name of the extracted tag, e.g. "a" or "img".
 *  offset      - the numberic offset of the first character of the tag within the HTML source.
 *  contents    - the inner HTML of the tag. This is always empty for self-closing tags.
 *  attributes  - a name -> value array of the tag's attributes, or an empty array if the tag has none.
 *  full_tag    - the entire matched tag, e.g. '<a href="http://example.com">example.com</a>'. This key
 *                will only be present if you set $return_the_entire_tag to true.
 *
 * @param string       $html The HTML code to search for tags.
 * @param string|array $tag The tag(s) to extract.
 * @param bool         $selfclosing Whether the tag is self-closing. Setting it to null will force the script to autodetect.
 * @param bool         $return_the_entire_tag Return the entire matched tag in 'full_tag' key of the results array.
 * @param string       $charset The character set of the HTML code. Defaults to UTF-8.
 *
 * @return array An array of extracted tags, or an empty array if no matching tags were found.
 */
function extract_tags($html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'utf-8') {
    if (is_array($tag)) {
        $tag = implode('|', $tag);
    }

    // If the user did not specify the self-closing tag, autodetect it
    $selfclosing_tags = array('area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param');
    if (is_null($selfclosing)) {
        $selfclosing = in_array($tag, $selfclosing_tags);
    }

    if ($selfclosing) {
        $tag_pattern = 
            '@<(?P<tag>'.$tag.')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*/?>                         # /> or just >, being lenient here 
            @xsi';
    } else {
        $tag_pattern = 
            '@<(?P<tag>'.$tag.')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*>                           # >
            (?P<contents>.*?)              # tag contents
            </(?P=tag)>                    # the closing </tag>
            @xsi';
    }

    $attribute_pattern = 
        '@
        (?P<name>\w+)                         # attribute name
        \s*=\s*
        (
            (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)    # a quoted value
            |                           # or
            (?P<value_unquoted>[^\s"\']+?)(?:\s+|$)           # an unquoted value (terminated by whitespace or EOF) 
        )
        @xsi';

    // Find all tags
    if (!preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
        // Return an empty array if nothing found
        return array();
    }

    $tags = array();
    foreach ($matches as $match) {
        // Parse tag attributes, if any
        $attributes = array();
        if (!empty($match['attributes'][0])) {
            if (preg_match_all($attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER)) {
                // Turn the attribute data into a name->value array
                foreach ($attribute_data as $attr) {
                    if (!empty($attr['value_quoted'])) {
                        $value = $attr['value_quoted'];
                    } else if (!empty($attr['value_unquoted'])) {
                        $value = $attr['value_unquoted'];
                    } else {
                        $value = '';
                    }

                    // Passing the value through html_entity_decode is handy when you want
                    // to extract link URLs or something like that. You might want to remove
                    // or modify this call if it doesn't fit your situation.
                    $value = html_entity_decode($value, ENT_QUOTES, $charset);

                    $attributes[$attr['name']] = $value;
                }
            }
        }

        $tag = array(
            'tag_name' => $match['tag'][0],
            'offset' => $match[0][1],
            'contents' => !empty($match['contents']) ? $match['contents'][0] : '', //empty for self-closing tags
            'attributes' => $attributes,
        );

        if ($return_the_entire_tag) {
            $tag['full_tag'] = $match[0][0];
        }

        $tags[] = $tag;
    }

    return $tags;
}
