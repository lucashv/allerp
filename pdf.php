<?php

  $sourceFile='66.pdf';
  
    $textArray = array ();
    $objStart = 0;
   
    $fp = fopen ($sourceFile, 'rb');
    $content = fread ($fp, filesize ($sourceFile));
    fclose ($fp);
   
    $searchTagStart = chr(13).chr(10).'stream';
    $searchTagStartLenght = strlen ($searchTagStart);
   
    while ((($objStart = strpos ($content, $searchTagStart, $objStart)) && ($objEnd = strpos ($content, 'endstream', $objStart+1))))
    {
      $data = substr ($content, $objStart + $searchTagStartLenght + 2, $objEnd - ($objStart + $searchTagStartLenght) - 2);
      $data = @gzuncompress ($data);
     
      if ($data !== FALSE && strpos ($data, 'BT') !== FALSE && strpos ($data, 'ET') !== FALSE)
      {
        $textArray [] = ExtractText ($data);
        echo ExtractText ($data) .'<br>';
      }
     
      $objStart = $objStart < $objEnd ? $objEnd : $objStart + 1;
    }
   
    
  
 
  function ExtractText ($postScriptData)
  {
    while ((($textStart = strpos ($postScriptData, '(', $textStart)) && ($textEnd = strpos ($postScriptData, ')', $textStart + 1)) && substr ($postScriptData, $textEnd - 1) != '\\'))
    {
      $plainText .= substr ($postScriptData, $textStart + 1, $textEnd - $textStart - 1);
      if (substr ($postScriptData, $textEnd + 1, 1) == ']') //this adds quite some additional spaces between the words
      {
        $plainText .= ' ';
      }
     
      $textStart = $textStart < $textEnd ? $textEnd : $textStart + 1;
    }
   
    return stripslashes ($plainText);
  }


?>