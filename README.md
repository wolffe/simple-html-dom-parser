# Simple HTML DOM Parser

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/87df9463010447ff94ab238885fcf8df)](https://www.codacy.com/app/wolffe/simple-html-dom-parser?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=wolffe/simple-html-dom-parser&amp;utm_campaign=Badge_Grade)

This is a PHP function that can extract any HTML tag and its attributes from a given string.

Note that this is the initial release and the code needs optimization.

@TODO: Test cases
@TODO: Regex improvements
@TODO: Documentation

## Usage

```php
require_once 'simpleHtmlDomParser.php';

function getElementContent($tag, $type, $identifier, $content) {
    /*
    $nodes = extract_tags($handle, 'code');
    foreach ($nodes as $link) {
        if ($link['attributes']['id'] == 'pattern') {
            echo strip_tags($link['contents']) . '<br>';
        } else if ($link['attributes']['id'] == 'css') {
            echo nl2br(strip_tags($link['contents'])) . '<br>';
        }
    }
    /**/

    $nodes = extract_tags($content, $tag);

    foreach ($nodes as $link) {
        if ($link['attributes'][$type] === $identifier) {
            return strip_tags($link['contents']);
        }
    }
}

$handle = file_get_contents($file); // or use an HTML string
$extractedContent = getElementContent('div', 'id', 'my-id-here', $handle);
```

### More examples

Extract all links and output their URLs:

```php
$html = file_get_contents('example.html');
$nodes = extract_tags($html, 'a');
foreach ($nodes as $link) {
    echo $link['attributes']['href'] . '<br>';
}
```

Extract all heading tags and output their text:

```php
$nodes = extract_tags($html, 'h\d+', false);
foreach ($nodes as $node) {
    echo strip_tags($link['contents']) . '<br>';
}
```

Extract meta tags:

```php
$nodes = extract_tags($html, 'meta');
```

Extract bold and italicized text fragments:

```php
$nodes = extract_tags($html, array('b', 'strong', 'em', 'i'));
foreach ($nodes as $node) {
    echo strip_tags($node['contents']) . '<br>';
}
```

## Notes

Comments are not ignored and their contents may mess up the output. ALso, nested tags may return unexpected output.

## Regex

```
@<(?P<tag>'.$tag.')           # <tag
(?P<attributes>\s[^>]+)?      # attributes, if any
\s*/?>                        # /> or just >, being lenient here 
@xsi

@<(?P<tag>'.$tag.')           # <tag
(?P<attributes>\s[^>]+)?      # attributes, if any
\s*>                          # >
(?P<contents>.*?)             # tag contents
</(?P=tag)>                   # the closing </tag>
@xsi

@
(?P<name>\w+)                                         # attribute name
\s*=\s*
(
    (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)  # a quoted value
    |                                                 # or
    (?P<value_unquoted>[^\s"\']+?)(?:\s+|$)           # an unquoted value (terminated by whitespace or EOF) 
)
@xsi
