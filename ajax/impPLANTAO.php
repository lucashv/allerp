<?
header("Content-Type: text/html; charset=iso-8859-1");


die("CONEXÃO NEGADA A BASE DE SÃO PAULO");
  $servidor = 'mysql.labspaulo.com.br';
  $loginMYSQL = 'labspaulo02';
  $baseMYSQL = 'labspaulo02';
  $senha = "sucesso";

$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());



$arq = fopen('indicacoes.csv', 'r');
  
mysql_query("DROP TABLE IF EXISTS importa_indicacoes;") or  die (mysql_error());
$sql="CREATE TABLE  importa_indicacoes ( ".
  "numreg int(10) unsigned NOT NULL AUTO_INCREMENT, ".
  "data datetime NOT NULL, ".
  "corretorEXCEL varchar(60) DEFAULT NULL, ".
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "indicacaoEXCEL varchar(60) DEFAULT NULL, ".
  "idINDICACAO int(10) unsigned DEFAULT NULL, ".
  "idRESULTADO int(10) unsigned DEFAULT NULL, ".
  "idOPERADOR int(10) unsigned DEFAULT NULL, ".
  "nome varchar(60) DEFAULT NULL, ".
  "email varchar(100) DEFAULT NULL, ".
  "obs text, ".
  "resultado varchar(100) DEFAULT NULL, ".
  "retorno varchar(40) DEFAULT NULL, ".
  "telefones varchar(60) DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`) ".
") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

mysql_query("DROP TABLE IF EXISTS resultados_indicacoes;") or  die (mysql_error());
$sql="CREATE TABLE  resultados_indicacoes ( ".
  "`numreg` int(10) unsigned NOT NULL AUTO_INCREMENT,".
  "`descricao` varchar(60) DEFAULT NULL,".
  "PRIMARY KEY (`numreg`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());

/*
mysql_query("DROP TABLE IF EXISTS corretorTEMP;") or  die (mysql_error());
$sql="CREATE TABLE  corretorTEMP ( ".
  "`numreg` int(10) unsigned NOT NULL AUTO_INCREMENT,".
  "`nome` varchar(60) DEFAULT NULL,".
  "idCORRETOR int(10) unsigned DEFAULT NULL, ".
  "PRIMARY KEY (`numreg`)".
  ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"; 

mysql_query($sql) or  die (mysql_error());
*/
$cont=1;
$sql='insert into importa_indicacoes(idoperador,nome,data,corretorEXCEL,indicacaoEXCEL,telefones,email,obs,retorno,resultado) values ';

while(true)       {
	$lin = fgets($arq);

	if ($lin == null)  break;
	if (trim($lin) == '')  continue;
	if ($cont<1)  {$cont++;continue;}

  $cont++;

  if (strlen($sql)>20000) {
    echo($sql); 
    mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

    $sql='insert into importa_indicacoes(idoperador,nome,data,corretorEXCEL,indicacaoEXCEL,telefones,email,obs,retorno,resultado) values ';
  }
  $info = explode(';', $lin);
  $data=$info[0];
  $hora=$info[1];
  if (strlen($data)!=8) continue;

  if (! is_numeric(substr($data, 3, 2)) || ! is_numeric(substr($data, 0, 2)) || !is_numeric('20'.substr($data, 6, 2)) ) continue;  

  if (checkdate( substr($data, 3, 2), substr($data, 0, 2), '20'.substr($data, 6, 2) )    )  {  
    $data='20'.substr($data, 6, 2).'-'.substr($data, 3, 2).'-'.substr($data, 0, 2);

    $hora=str_replace('h', '', $hora);    
    $hora=str_replace('H', '', $hora);
    if ($hora!='') $data .= ' '.$hora;

    $corretor=''; $nome=''; $fones=''; $indicacao=''; $email=''; $resultado='';
 
    $corretor=str_replace("'", '', $info[2]);  $corretor=str_replace(chr(92), '', $corretor); $corretor=substr($corretor,0,59);
    $indicacao=str_replace("'", '', $info[3]); $indicacao=str_replace(chr(92), '', $indicacao); $indicacao=substr($indicacao,0,59);
    $nome=str_replace("'", '', $info[4]); $nome=str_replace(chr(92), '', $nome); $nome=substr($nome,0,59);
    $fones=str_replace("'", '', $info[5]); $fones=str_replace(chr(92), '', $fones); $fones=substr($fones,0,59);
    $resultado=str_replace("'", '', $info[7]); $resultado=str_replace(chr(92), '', $resultado); $resultado=substr($resultado,0,59);
    $email=str_replace("'", '', $info[9]); $email=str_replace(chr(92), '', $email); $email=substr($email,0,59);
    $retorno=str_replace("'", '', $info[8]); $retorno=str_replace(chr(92), '', $retorno); $retorno=substr($retorno,0,59);
    $obs=str_replace("'", '', $info[6]); $obs=str_replace(chr(92), '', $obs); $obs=substr($obs,0,300);
    $fones=str_replace('-', '', $fones);

    $nome = strtoupper($nome);

/*
    if ( isset($info[5]) ) {    
      $indicacao=str_replace("'", '', $info[5]);

      $indicacao = trim( preg_replace( '/\s+/', ' ', $indicacao ) );
//      $indicacao = str_replace("\n","&nbsp;",$indicacao);  
//      $indicacao = str_replace("\r\n","&nbsp;",$indicacao);

      $indicacao=str_replace(chr(92), '', $indicacao); $indicacao=substr($indicacao,0,59);
    }
*/
    $corretor = str_replace('Jenniffer','Jennifer',$corretor);
    $corretor = str_replace('Ademir/Aguia','Ademir',$corretor);
    $corretor = str_replace('Ademir/Águia','Ademir',$corretor);
    $corretor = str_replace('Anderson/Paranaguá','Anderson',$corretor);
    $corretor = str_replace('Andrea B.','Andrea B',$corretor);
    $corretor = str_replace('Andressa','Andressa B',$corretor);
    $corretor = str_replace('Ane','Lidiane Dalpra',$corretor);
    $corretor = str_replace('Angela','Angela Gencisski',$corretor);
    $corretor = str_replace('Ariane','Ariane Pinheiro',$corretor);
    $corretor = str_replace('Bruna','Bruna (comercial)',$corretor);
    $corretor = str_replace('E. Borges','Eduardo Borges',$corretor);
    $corretor = str_replace('Edylaine','Edilaine',$corretor);
    $corretor = str_replace('Fábio','Fabio Lima',$corretor);
    $corretor = str_replace('Francieli','Ademir',$corretor);
    $corretor = str_replace('G. Brugnari','Giovanni Brugnari',$corretor);
    $corretor = str_replace('Giovani','Giovani Bartolo',$corretor);
    $corretor = str_replace('Idiane/Paranaguá','Idiane',$corretor);
    $corretor = str_replace('Inês','Sandra Ines',$corretor);
    $corretor = str_replace('Ivone/SJP','Ivone/SJP',$corretor);
    $corretor = str_replace('Jéssica','Jessica Sforza Carvalho ',$corretor);
    $corretor = str_replace('Joelma','Joelma Campos',$corretor);
    $corretor = str_replace('Jolidia','Jo Lidia',$corretor);
    $corretor = str_replace('Juliane','Juliane Alexandre',$corretor);
    $corretor = str_replace('Kelly','Kelly (comercial)',$corretor);
    $corretor = str_replace('Leandro','Leandro Borges',$corretor);
    $corretor = str_replace('Luciano','Luciano G',$corretor);
    $corretor = str_replace('LuizCarlos','Carlos Lopes',$corretor);
    $corretor = str_replace('Marcos/Paranaguá','Marcos Souza',$corretor);
    $corretor = str_replace('Mari','Mariana',$corretor);
    $corretor = str_replace('Marilene','Marilene K',$corretor);
    $corretor = str_replace('Marianaana','Mariana',$corretor);
    $corretor = str_replace('Marlene/Paranaguá','Marlene',$corretor);
    $corretor = str_replace('Ivone/SJP','Ivone',$corretor);
    $corretor = str_replace('Marianalene','Marilene K',$corretor);
    $corretor = str_replace('Mary','Mary Costa',$corretor);
    $corretor = str_replace('Mirella/Paranaguá','Mirella (paranaguá)',$corretor);
    $corretor = str_replace('Paranagua','',$corretor);
    $corretor = str_replace('PrimeBroker','Prime Broker',$corretor);
    $corretor = str_replace('Raul/SJP','Raul',$corretor);
    $corretor = str_replace('Regina','Regina (comercial)',$corretor);
    if ($corretor=='Sandro') $corretor='Sandro Martins';
    $corretor = str_replace('Sônia','Sonia Moraes',$corretor);
    $corretor = str_replace('Sueli','Sueli Borges',$corretor);
    $corretor = str_replace('Suelli','Sueli F',$corretor);

    $sql .= $sql=='insert into importa_indicacoes(idoperador,nome,data,corretorEXCEL,indicacaoEXCEL,telefones,email,obs,retorno,resultado) values ' ? 
            '' : ',';
    $sql .= "(5,upper('$nome'), '$data', '$corretor', '$indicacao', '$fones', '$email', '$obs', '$retorno', '$resultado')";
  }
}
if ($sql!='insert into importa_indicacoes(idoperador,nome,data,corretorEXCEL,indicacaoEXCEL,telefones,email,obs,retorno,resultado) values ') 
  mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql="insert into resultados_indicacoes(descricao) select distinct resultado from importa_indicacoes;"; 
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='update importa_indicacoes, resultados_indicacoes set idresultado=resultados_indicacoes.numreg where resultados_indicacoes.descricao=resultado;';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql="insert into origens_atendimento(nome) select distinct indicacaoEXCEL from importa_indicacoes;"; 
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='update importa_indicacoes, origens_atendimento set idindicacao=origens_atendimento.numero where '.
      ' importa_indicacoes.indicacaoexcel=origens_atendimento.nome;';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql="update origens_atendimento set ativo='S'";
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql="update origens_atendimento set nome=replace(nome,'www.','');"; 
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

//$sql="insert into corretorTEMP(nome) select distinct corretorEXCEL from importa_indicacoes;"; 
//mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='update importa_indicacoes, representantes set idcorretor=representantes.numero where importa_indicacoes.corretorexcel=representantes.nome;';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

$sql='insert into indicacoes_atendimento(data,idcorretor,idindicacao,idoperador,nome,obs,telefones, email,infoveioexcel) '.
      'select data,idcorretor,idindicacao,idoperador,nome,obs,telefones, email, 1 from importa_indicacoes; ';
mysql_query($sql) or  die ($sql.'<br><br>'.mysql_error());

//mysql_query("DROP TABLE IF EXISTS importa_indicacoes;") or  die (mysql_error());


fclose($arq);
    
    
  
?>