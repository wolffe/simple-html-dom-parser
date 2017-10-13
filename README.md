# html-dom-parse
HTML DOM Parse

# Usage

```php
<?php
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
