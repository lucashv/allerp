<?php
ob_start(); 
require("doctype.php"); 

session_start();
?>

<head>
  <title>
      <?php echo $_SESSION['empresa']; ?>
  </title>
  <link rel="stylesheet" href="css/theme.css" type="text/css" />
  <script language="JavaScript" src="js/JSCookMenu.js" type="text/javascript" xml:space="preserve"></script>
  <script language="JavaScript" src="js/funcoes.js" type="text/javascript" xml:space="preserve"></script>
  <link href="css/padroes.css" type="text/css" rel="stylesheet" />
</head>
<body style="HEIGHT: 100%; width:100%;" onload="prepararTELA();
posicionarIframe();">

<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[

var myMenu;                                                           
var buscaAjax = new execAjax();
    	
var aDestinos = new Array();
aDestinos[0] = 'representantes.php';
aDestinos[2] = 'operadores.php';
aDestinos[3] = 'senha.php';
aDestinos[4] = 'creditos.php';
aDestinos[5] = 'planos.php';
aDestinos[6] = 'caixa.php';
aDestinos[7] = 'cheques.php';
aDestinos[8] = 'bancos.php';
aDestinos[9] = 'motivos.php';
aDestinos[10] = 'midias.php';
aDestinos[11] = 'propostas.php';
aDestinos[12] = 'rel/vendas.php';
aDestinos[13] = 'rel/contratos_erros.php';
aDestinos[14] = 'Sair();';
aDestinos[15] = 'rel/confirmacoes.php';
aDestinos[16] = 'rel/protocolo.php';
aDestinos[17] = 'rel/creditos.php';
aDestinos[18] = 'rel/pj.php';
aDestinos[19] = 'agrupadores.php';
aDestinos[20] = 'escritorios.php';
aDestinos[21] = 'comissaoADESAO.php';
aDestinos[22] = 'contas.php';
aDestinos[23] = 'caixa.php';
aDestinos[24] = 'comissaoEMPRESA.php';
aDestinos[25] = 'infoEMPRESA.php';
aDestinos[26] = 'relatorios.php';
aDestinos[27] = 'baixas.php';
aDestinos[28] = 'ligacoes.php';
aDestinos[29] = 'indicacoes.php';
aDestinos[30] = 'produtos_atendimento.php';
aDestinos[31] = 'rel/bordero2.php';
aDestinos[32] = 'resultados.php';
aDestinos[33] = 'download3.php';
aDestinos[34] = 'rel/nuncaPAGOS.php';
aDestinos[35] = 'rel/posvenda.php';
aDestinos[36] = 'rel/clientes_mensalidades.php';
aDestinos[37] = 'download.php';
aDestinos[38] = 'seguros.php';
aDestinos[39] = 'transferencias.php';
aDestinos[40] = 'tipos_seguro.php';
aDestinos[41] = 'seguradoras.php';
aDestinos[42] = 'operadoras.php';
aDestinos[43] = 'tipos_contrato.php';
aDestinos[44] = 'grupos_venda.php';
aDestinos[45] = 'corretores_seguro.php';
aDestinos[46] = 'tipos_comissao_representante.php';
aDestinos[47] = 'tipos_comissao_prestadora.php';
aDestinos[48] = 'download2.php';
aDestinos[49] = 'rel/inadimplencia.php';

/*************************************************/
function teclado(e)         {
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);

/*************************************************/
function Ir(Onde)  {
if (aDestinos[Onde].indexOf('http://')!=-1)
  window.open(aDestinos[Onde]);
else  
  window.frames['framePRINCIPAL'].location.href=aDestinos[Onde];
}

/*************************************************/
function posicionarIframe() {
var altura = document.documentElement.clientHeight;
var largura = document.getElementById('framePRINCIPAL').scrollWidth-20;

altura -= document.getElementById('framePRINCIPAL').offsetTop;
/* considerar como margem inferior, onde ficará usuário 
altura -= 125;     
*/

/* o site foi todo feito levando em conta 2 resolucoes, 1024x768 e 800x600 */ 
if (screen.width>1000)
  altura -= 125;
else  
  altura -= 95;

document.getElementById('framePRINCIPAL').style.height = altura +"px";
document.getElementById('framePRINCIPAL').src='inicial.php';

buscaAjax.criar('ajax/ajax.php?acao=salvarDimensoesIFRAME&altura=' + altura + '&largura=' + largura, tudoOK);
}

/*************************************************/
function tudoOK() {

/* le esquema d cores escolhido pelo usuario */
if (buscaAjax.terminouLER())    {
  info= buscaAjax.ler();
  
  info=info.replace(String.fromCharCode(13),''); info= info.replace(String.fromCharCode(10),'');
  info=info.replace(String.fromCharCode(13),''); info= info.replace(String.fromCharCode(10),'');        
  info=info.replace(String.fromCharCode(13),''); info= info.replace(String.fromCharCode(10),'');
  
  cores = info.split(',');
          
  
  document.getElementById('corFormJanela').value=cores[0];
  document.getElementById('corMouseOver').value=cores[1];
  document.getElementById('corMouseDown').value=cores[2];
  document.getElementById('corFormAuxilio').value=cores[3];
  document.getElementById('corMouseOverAuxilio').value=cores[4];
  document.getElementById('corMouseDownAuxilio').value=cores[5];  
  document.getElementById('corTextBox').value=cores[6];        
  
}
}


/*************************************************/
function prepararTELA() {
buscaAjax.criar('ajax/ajax.php?acao=montarMENU', '', 0);

/*alert( buscaAjax.ler() );*/
eval("var myMenu = 	[ " + buscaAjax.ler() + "]; ");

cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');

}


/*************************************************/
function Sair() {
if (confirm('Sair do sistema?'))    sairPROGRAMA();   //sai = new execAjax('ajax/ajax.php?acao=sairPROGRAMA', "sairPROGRAMA");
}

/*************************************************/
function sairPROGRAMA()  {
window.top.close();
}

//]]>
</script>
<!--<form id="frmPRINCIPAL" action=""> !-->

<table style="width:98%;height:100%;" border="0" cellpadding="0" cellspacing="0">

<tr width="100%"><td width="100%">
  <div id="myMenuID" width="100%"></div>
</td></tr>

<tr><td>
  <table cellpadding="2" border="0" align="left" width="100%" >
    <tr>

  <?
    require_once( 'includes/definicoes.php'  );

    $conexao = mysql_connect($servidor, $loginMYSQL, $senha) or die(mysql_error());
    mysql_select_db($baseMYSQL, $conexao) or die(mysql_error());


    $infoUSUARIO  = explode(';', $_SESSION['idUSUARIO_LOGADO']);
    $idUSUARIO = $infoUSUARIO[1]; 

    $sql  = "select permissoes from operadores where numero = $idUSUARIO ";
    $resultado = mysql_query($sql, $conexao) or die (mysql_error());

    $row = mysql_fetcH_object($resultado);
    $permissoes=$row->permissoes;

    if (strpos($permissoes, 'D')!==false || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Corretores" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(0);">
      <img src="images/BR_representantes.png" alt="" />
    </td>
  <?
  }
  if (strpos($permissoes, 'B')!==false  || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Produtos" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" 
    onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(43);"> 
      <img src="images/BR_planos.png" alt="" />
    </td>
  <?
  }
  if (strpos($permissoes, 'G')!==false || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Créditos/Débitos" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" 
    onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(4);">
      <img src="images/BR_creditos2.png" alt="" />
    </td>
  <?
  }
  if ( (strpos($permissoes, 'J')!==false || strpos($permissoes, 'U')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Propostas" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(11);">
      <img src="images/BR_propostas.png" alt="" />
    </td>
  <?
  }
  if (strpos($permissoes, 'R')!==false || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Confirmações" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(27);">
      <img src="images/BR_baixar.png" alt="" />
    </td>
  <?
  }
  if ((strpos($permissoes, 'H')!==false || strpos($permissoes, 'I')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Caixa" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(23);">
      <img src="images/BR_caixa.png" alt="" />
    </td>
  <?
  }
  if ((strpos($permissoes, 'K')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Operadores do sistema" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(2);">
      <img src="images/BR_operadores.png" alt="" />
    </td>
  <?
  }
  ?>


  <?
  if ((strpos($permissoes, 'S')!==false || strpos($permissoes, 'T')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Plantão" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(28);">
      <img src="images/BR_ligacoes.png" alt="" />
    </td>
  <?
  }
  ?>

  <?
  if ((strpos($permissoes, 'X')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Seguros" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" 
      onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(38);">
      <img src="images/BR_seguros.png" alt="" />
    </td>
  <?
  }
  ?>


  <?
  if ((strpos($permissoes, 'Q')!==false) || $idUSUARIO==1) {
  ?>
    <td width="5%" title="Relatórios" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Ir(26);">
      <img src="images/BR_relatorios.png" alt="" />
    </td>
  <?
  }
  ?>



    
    <td width="5%" title="Sair" align="center" onmouseover="this.className='btnBarraRapidaSelecionado'" onmouseout="this.className='btnBarraRapidaNaoSelecionado'" onclick="Sair();">
      <img src="images/BR_sair.png" alt="" />
    </td>
    

   
<?php
if ($_SESSION['usarTipoIMAGEM'] == '_HD') { ?>
    <td width="60%" align="right" ><table width="100%" style="text-align:right">
      <tr>
        <td><span class="lblTitUSUARIO">&nbsp;&nbsp;&nbsp;Usuário:&nbsp;&nbsp;</span></td>
        <td width="250px" style="text-align:left"><span class="lblUSUARIO" id="lblUSUARIO">
          <?php  
          $usuario = $_SESSION['idUSUARIO_LOGADO']; 
          echo substr($usuario, 0, strpos($usuario, ';')) .' ('. substr($usuario, strpos($usuario, ';')+1).')' ; ?>
        </span></td>
      </tr>
    </table>
    <td><span class="lblACAO" id="lblACAO" style="display:none">&nbsp;</span></td>
<?php
}
else { ?>
    <td width="60%" align="right" ><table width="100%" style="text-align:right">
      <tr><td><table>
        <tr>
          <td ><span class="lblTitUSUARIO">&nbsp;&nbsp;&nbsp;Usuário:&nbsp;&nbsp;</span></td>
        <td style="text-align:left"><span class="lblUSUARIO" id="lblUSUARIO">
          <?php  
          $usuario = $_SESSION['idUSUARIO_LOGADO']; 
          echo substr($usuario, 0, strpos($usuario, ';'))  ; ?>
        </span></td>
        </tr>
      </table></td></tr>
    </table>
    <td><span class="lblACAO" id="lblACAO" style="display:none">&nbsp;</span></td>
<?php
}
?>
              
    </tr>
  </table>
</td></tr>

<!--  cmp usado constantemente para enviar info entre arquivos PHP -->
<input type="hidden" id="infoTrab" value="" />

<input type="hidden" id="corFormJanela" value="" />  
<input type="hidden" id="corMouseOver" value="" />
<input type="hidden" id="corMouseDown" value="" />

<input type="hidden" id="corFormAuxilio" value="" />  
<input type="hidden" id="corMouseOverAuxilio" value="" />
<input type="hidden" id="corMouseDownAuxilio" value="" />  

<input type="hidden" id="corTextBox" value="" />


<tr><td>  
  <iframe  id="framePRINCIPAL" name="framePRINCIPAL" 
  style="width:100%;height:100%;" width="100%" scrolling="no"></iframe>  
</td></tr>   

</table>  
</form>
</body>
</html>