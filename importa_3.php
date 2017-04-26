<?
header("Content-Type: text/html; charset=iso-8859-1");

//  $servidor = 'localhost';
//  $loginMYSQL = 'root';
//  $baseMYSQL = 'rae';


  $servidor = 'mysql.netnigro.com.br';
  $loginMYSQL = 'netnigbr27';
  $baseMYSQL = 'netnigbr27';
  $senha = "03netn13";




$conexao = mysql_connect($servidor, $loginMYSQL, 'sucesso') or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$resultado = mysql_query('select *, ifnull(valor,0) as valor2 from propostas_incluir', $conexao) or die (mysql_error());
while ($row = mysql_fetcH_object($resultado)) {

  $data='null';
  

  if ( is_numeric(substr($row->DATA_VDA, 6)) && is_numeric(substr($row->DATA_VDA, 3, 2)) && is_numeric(substr($row->DATA_VDA, 0, 2)) ) {
    if ( checkdate(substr($row->DATA_VDA, 3, 2), substr($row->DATA_VDA, 0, 2), ('20'.substr($row->DATA_VDA, 6)))) {
      if ( strlen(trim($row->DATA_VDA))==8 ) 
        $data=chr(39).'20'.substr($row->DATA_VDA, 6) . substr($row->DATA_VDA, 3, 2) . substr($row->DATA_VDA, 0, 2).chr(39);
    }    
  }        

  $fone=$row->telefone;
  $contrato=$row->N_CONTRATO;
  $contratante= substr($row->contratante, 0, 45);
  $vidas=$row->N_VIDAS; $vidas=trim($vidas)=='' ? '0' : $vidas;
  $plano=$row->PLANO;
  $idREPRE=$row->idREPRE;
  $obs=$row->OBS_PPTA;
  $deb= (strpos(strtolower($row->OBS_PPTA), 'deb ')!==false) ? 1 : 0;
  $vlr= str_replace(',', '.', $row->valor2);
  if (trim($vlr=='')) $vlr='0';
      

  $idREPRE=$row->idREPRE=='' ? -1 : $row->idREPRE;
  $sql="insert into propostas(foneRes, numREPRESENTANTE, observacoes, debitoAUTOMATICO, vlrADESAO) ".
       " select '$fone', $idREPRE, '$obs',  $deb, $vlr ";
  
  mysql_query($sql) or  die (mysql_error().'<br><br>'.$sql);
  
  $sequencia = mysql_insert_id();
  $sql="insert into listadepropostas(sequencia,numcontrato,contratante,datacadastro,numrepresentante,tipocontrato,dataassinatura,qtdeusuarios) ".
       " select $sequencia,  $contrato, '$contratante', $data, $idREPRE, 0, $data, $vidas; ";

  mysql_query($sql) or  die (mysql_error());       
}


    
  
?>