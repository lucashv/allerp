<?
header("Content-Type: text/html; charset=iso-8859-1");

require_once( '../includes/definicoes.php'  );
require_once( '../includes/funcoes.php'  );


$conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());

$arq = fopen('contas.csv', 'r');
  
while(true)       {
	$lin = fgets($arq);
	if ($lin == null)  break;

  $cmps = explode(',', $lin);

  $tipo='S'; $tipoCAIXA='I';
  if ($cmps[0]<200) $tipo='E';
  if ($cmps[2]=='2') $tipoCAIXA='E';

  $nome=str_replace('"','', $cmps[1]);
  $sql="insert into contas(nome,ativo,entOUsai,tipoCAIXA) select '$nome', 'S', '$tipo', '$tipoCAIXA'; ";

  mysql_query($sql, $conexao) or die (mysql_error());
}
  
mysql_close($conexao);
die('ok');


?>



