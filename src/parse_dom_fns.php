<?php
/**
 * Created by PhpStorm.
 * User: devJacob
 * Date: 12/29/17
 * Time: 5:33 PM
 */


function parseDom($fileName){
    $urlHtml = file_get_contents( USERDIR . $fileName);
    $dom = new DOMDocument;
    @$dom->loadHTML($urlHtml);
    $urlTitle = $dom->getElementsByTagName('title');
    // there is only one title tag nodelist length = 1
    $urlTitleText = $urlTitle->item(0)->nodeValue;

    $urlMetaTags = $dom->getElementsByTagName('meta');
    foreach ($urlMetaTags as $metaTag){
        if( $metaTag->getAttribute('name') == 'description'){
//            see http://de2.php.net/manual/en/domnode.c14n.php
//            $html = $Node->ownerDocument->saveHTML( $Node );
            $urlDescription = $metaTag->ownerDocument->saveHTML( $metaTag );
            break;
        }
    }
    // strip out content attribute text
    preg_match( '/content="(.*?)"/', $urlDescription, $matches);
    $urlMetaArray = array('title' => $urlTitleText, 'description' => $matches[1]);
    return $urlMetaArray;
}

function bm_importer($file){
    $uploadedHtml = file_get_contents( $file );
    $uploadeDom = new DOMDocument;
    @$uploadeDom->loadHTML($uploadedHtml);

    //  see https://stackoverflow.com/questions/18182857/using-php-dom-document-to-select-html-element-by-its-class-and-get-its-text
    $xpath     = new DOMXPath($uploadeDom);
// need to get DL>P>DT  tags
//  then H3 is a folder name
// another nested DL>p>dt are the bookmarks
    $elements = $xpath->query("//dl");
//    need to parse through xpath

// this is from php manual example  http://php.net/manual/en/class.domxpath.php
    if (!is_null($elements)) {
        $bmExtractedArray = array();

        foreach ($elements as $element) {
            echo "<br/>[". $element->nodeName. "]";
            $nodes = $element->childNodes;
            foreach ($nodes as $node) {
                echo $node->nodeValue. "\n";
                $bmExtractedArray[] = extractBookmark($node);
            }
        }
    }
    echo $bmExtractedArray;
}

function extractBookmark(DOMElement $nodeElement){
    $bmCategory= null;
    $bmName= null;
    $bmLink= null;

    // array to store bm info
    $bmExtracted = array();

    // only process dt tags
    if ($nodeElement->tagName !== 'dt' || !$nodeElement->hasChildNodes() ){
        return 0;
    }
    // check dt first child
    $firstchildTag = $nodeElement->firstChild->tagName;

    // h3 indicates bookmark folder/category
    if ($firstchildTag === 'h3'|| 'H3'){
        $bmCategory = $nodeElement->firstChild->nodeValue;
    }
    if ($firstchildTag === 'a'||'A'){
        $bmName = $nodeElement->firstChild->nodeValue;
        $bmLink = $nodeElement->firstChild->attributes->item(0)->nodeValue;
    }
    $bmExtracted= [$bmCategory, $bmName, $bmLink];
    return $bmExtracted;
}


