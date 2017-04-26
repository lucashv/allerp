<?php

echo( is_numeric('1,2') );

die();
/**
 *    Exemplo de utiliza��o de utiliza��o de WebService Kinghost
 *    www.kinghost.com.br
 */

$webservice_url     = 'http://webservice.kinghost.net/web_cep.php';
$webservice_query    = array(
    'auth'    => '6453785c592a3b8230e2710deb9af4fc', //Chave de autentica��o do WebService - Consultar seu painel de controle
    'formato' => 'query_string', //Valores poss�veis: xml, query_string ou javascript
    'cep'     => '80230-090' //CEP que ser� pesquisado
);

//Forma URL
$webservice_url .= '?';
foreach($webservice_query as $get_key => $get_value){
    $webservice_url .= $get_key.'='.urlencode($get_value).'&';
}

parse_str(file_get_contents($webservice_url), $resultado);

switch($resultado['resultado']){  
    case '2':  
        $texto = " 
    Cidade com logradouro �nico 
    <b>Cidade: </b> ".$resultado['cidade']." 
    <b>UF: </b> ".$resultado['uf']." 
        ";    
    break;  
      
    case '1':  
        $texto = " 
    Cidade com logradouro completo 
    <b>Tipo de Logradouro: </b> ".$resultado['tipo_logradouro']." 
    <b>Logradouro: </b> ".$resultado['logradouro']." 
    <b>Bairro: </b> ".$resultado['bairro']." 
    <b>Cidade: </b> ".$resultado['cidade']." 
    <b>UF: </b> ".$resultado['uf']." 
        ";  
    break;  
      
    default:  
        $texto = "Fala ao buscar cep: ".$resultado['resultado'];  
    break;  
}

echo $texto;

?> 