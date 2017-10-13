# html-dom-parse
HTML DOM Parse

This is a PHP function that can extract any HTML tag and its attributes from a given string.

Note that this is the initial release and the code needs optimization.

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
