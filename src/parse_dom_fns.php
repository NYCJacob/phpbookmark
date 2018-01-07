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

function bm_importer($file)
{
    $bmArray = [];

    $uploadedHtml = file_get_contents($file);
    $doc = new DOMDocument;
    $doc->loadHTML($uploadedHtml);

    // from php manual
// example 1:
    $elements = $doc->getElementsByTagName('*');
// example 2:
//    $elements2 = $doc->getElementsByTagName('html');
// example 3:
//$elements = $doc->getElementsByTagName('body');
// example 4:
//$elements4 = $doc->getElementsByTagName('table');
// example 5:
//$elements5 = $doc->getElementsByTagName('div');

    // category pulled from H3 text
    $bmCategory = '';
    // bm lists are with two unclosed <p> tags
    // a second p indicated folder list ended.
    // this was needed to caption Other Bookmarks
    //TODO: there must be a better way :(
    $pCount = 0;

    if (!is_null($elements)) {
        foreach ($elements as $element) {
            $bmLink = '';

            $elName = $element->nodeName;
            $nodeNames[] = $elName;
            if ($elName === 'h3'){
                    $bmCategory = $element->nodeValue;
                }
            if ($elName === 'a') {
                    $bmLink = $element->nodeValue;
                    $bmArray[] = array( 'category' => $bmCategory, 'link' => $bmLink );
                }
            if ( $elName === 'p'){
                $pCount += 1;
            }
            if ($pCount === 2){
                $bmCategory = '';
                $pCount = 0;
            }

//            $nodes = $element->childNodes;
//            foreach ($nodes as $node) {
//                // if <dt><h3> this is a folder designation
//                if ($node->tagName === 'h3'){
//                    $bmCategory = $node->nodeValue;
//                }
//                if ($node->tagName === 'a') {
//                    $bmLink = $node->nodeValue;
//                    $bmArray[] = array( 'category' => $bmCategory, 'link' => $bmLink );
//                }
//
////                ${ $element->nodeName . 'children' }[] = $node->nodeValue;
//
//            }
        }
    }

    return $bmArray;
}