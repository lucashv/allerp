<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/encoder.js" xml:space="preserve"></script>
<script language=javascript>
Date.prototype.addDays = function(days) {
this.setDate(this.getDate()+days);
} 
</script>


<!-- Folha de estilos do calendário -->
<link rel="stylesheet" type="text/css" media="all" href="js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a função Calendar.setup, que ajuda a gerar um calendário em poucas linhas de código -->
<script type="text/javascript" src="js/jscalendar-1.0/calendar-setup.js"></script> 

<style type="text/css" xml:space="preserve">
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 750px; height: 80px;	
margin-top: -180px; margin-left: -380px; display:block; z-index:3;}

.cssDIV_PLANTAO {
position: absolute; top: 200px;  width: 750px; height: 80px;	
margin-top: -200px; margin-left: -380px; display:block; z-index:3;}


.cssDIV_EDICAO_INDICACAO {
position: absolute; top: 200px;  width: 960px; height: 80px;	
margin-top: -270px; margin-left: -480px; display:block; z-index:3;}

.cssDIV_OCORRENCIA {
position: absolute; top: 200px;  width: 750px; height: 80px;	
margin-top: -180px; margin-left: -380px; display:block; z-index:6;}


.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerHOJE();qualUltimoPlantao();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();">Editar registro</a></li>
  <li><a href="javascript:excluirREG();">Excluir registro</a></li>
</ul>

<form id="frmLIGACOES" name="frmLIGACOES" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divOCORRENCIA" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="somentePROPRIAS" type="hidden" value="" />
<input id="proximoREPRE" type="hidden" value="" />

<input id="SELECAO" type="hidden" value="" />
<input id="SELECAO_2" type="hidden" value="" >

<input id="dataTRAB" type="hidden" value="" >
<input id="lendoATUAL" type="hidden" value="Medicina" >

<input id="recarregarINC" type="hidden" value="" />

<input id="hidNOME" type="hidden" value="" />
<input id="hidFONES" type="hidden" value="" />
<input id="hidATENDIMENTO_PRODUTO" type="hidden" value="" />
<input id="hidINDICACAO" type="hidden" value="" />
<input id="hidOBS" type="hidden" value="" />


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%"  bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>

        <td width="60%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span><br>
        <table cellpadding=0 cellspacing=0><tr>
          <td style="padding-left:9px" height="25px"><span class="lblTitJanela" id=tdPLANTAO style="color:blue;font-size:14px;"></span></td>
        </tr></table>
        </td>
        
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
    
        if (strpos($permissoes, 'S')!==false || $idUSUARIO==1) {
      ?>

          	<td title="Novo atendimento" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" 
            onclick="incluirREG();" >
          	  <img src="images/ligacao.png" />
              <input type="text" id="txtDATATRAB" value="" 
                  style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
                  onchange="lerHOJE(1);lerREGS();";/>        
    
          	</td>
    
          	<td title="Relatório de atendimentos" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" 
            onclick="window.top.frames['framePRINCIPAL'].location.href='rel/ligacoes.php';" >
          	  <img src="images/protocolo.png" />
          	</td>
    
    
          	<td title="Define plantão" align="center" onmouseout="this.style.backgroundColor='white';" 
          	onmouseover="this.style.backgroundColor='#A9B2CA';"  onclick="plantao();" >
          	  <img src="images/plantao.png" />
          	</td>
    
          	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
          	  <img src="images/buscadata.png" />
          	</td>
            
            <td id="btnPESQUISAR" title="Pesquisa nome"  align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="buscar();" >
              <img src="images/pesquisa.png" />
            </td>
    
            <td  id="btnRETORNAR" title="Retornar 1 dias (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >
              <img src="images/setaESQUERDA.png" />
            </td>
            
            <td id="btnAVANCAR" title="Avançar 1 dia (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >
              <img src="images/setaDIREITA.png" />
            </td>
        <?
        }
        else {
        ?>
          	<td title="Novo atendimento" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" 
            onclick="incluirREG();" >
          	  <img src="images/ligacao.png" />
              <input type="text" id="txtDATATRAB" value="" 
                  style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
                  onchange="lerHOJE(1);lerREGS();";/>        
    
          	</td>
    
          	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
          	  <img src="images/buscadata.png" />
          	</td>
            
            <td id="btnPESQUISAR" title="Pesquisa nome"  align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="buscar();" >
              <img src="images/pesquisa.png" />
            </td>
    
            <td  id="btnRETORNAR" title="Retornar 1 dias (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >
              <img src="images/setaESQUERDA.png" />
            </td>
            
            <td id="btnAVANCAR" title="Avançar 1 dia (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='white'" 
            onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >
              <img src="images/setaDIREITA.png" />
            </td>

        <?
        }
        ?>      
      
            
        <td style="cursor: pointer;text-align:right;"  
          onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
          class="lblTitJanela" >[ X ]</span>
        </td>      
      </tr></table></td></tr>

      <tr>
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .55); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>

      <tr><td align=left><table width="100%"  ><tr>
        <td width="50%"><table><tr>
          <td>&nbsp;<span class="lblUSUARIO" id="totREGS" /></td>
        </tr></table></td>

        <?
        if (strpos($permissoes, 'S')!==false || $idUSUARIO==1) {
        ?>
          	<td style='cursor:pointer;' width="10%"  align=center title="Atendimento presencial/demais ligações" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='Atendimento Presencial/demais ligações';lerREGS();" >
          	  <img src="images/presencial.png" />
          	</td>
    
          	<td style='cursor:pointer;' width="10%"  align=center title="Indicações" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='Indicações';lerREGS();" >
          	  <img src="images/indicacoes.png" />
          	</td>
    
          	<td style='cursor:pointer;' width="10%"  align=center title="Plantão odontologia" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='Odontologia';lerREGS();" >
          	  <img src="images/odonto.png" />
          	</td>
    
          	<td style='cursor:pointer;' width="10%"  align=center title="Plantão medicina" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='Medicina';lerREGS();" >
          	  <img src="images/medicina.png" />
          	</td>
            
          	<td style='cursor:pointer;' width="10%"  align=center title="Plantão Clinipam" align="center" onmouseout="this.style.backgroundColor='white'" 
          	onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="document.getElementById('lendoATUAL').value='Clinipam';lerREGS();" >
          	  <img src="images/clinipam.png" />
          	</td>
            
        <?
        }
        ?>
      </tr></table></td></tr>

  
    <tr><td><table width="100%"><tr>
        <td align="left" >
          &nbsp;<span class="lblPADRAO">Pesquisa telefone ou e-mail:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" maxlength="100" size="100" />
        </td>
        <td id=tdATUALIZA style="display:none"><font face=tahoma color=red><b>Atualizando...</b></font></td>
        
        <td> 
        <input id="txtFOCADO" type="text" value="" 
          style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />


        </td>
      </tr></table></td></tr>


    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 6;
var timer;

var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtATENDIMENTO_PRODUTO;Identifique um produto válido ou deixe em branco';
aCMPS[1]='txtDATA;Digite uma data da ligação válida';
aCMPS[2]='txtINDICACAO;Identifique uma origem válida ou deixe em branco';
aCMPS[3]='txtNOME;Preencha o nome';
aCMPS[4]='txtFONES;Preencha um telefone';
aCMPS[5]='txtSITUACAO;Identifique a situação ou deixe em branco';
aCMPS[6]='txtREPRESENTANTE_OCORRENCIA;Identifique um corretor válido ou deixe em branco';
aCMPS[7]='txtREPRESENTANTE;Identifique um corretor válido ou deixe em branco';

Calendar.setup({
inputField:    "txtDATATRAB",     
ifFormat  :     "%d/%m/%Y",     
button    :    "btnDATATRAB"    
});



var ajax = new execAjax();

/*******************************************************************************/
function lerHOJE( buscarDataEscolhida )         {

if (typeof buscarDataEscolhida=='undefined')     {
  showAJAX(1);
  ajax.criar('ajax/ajaxCAIXA.php?acao=lerDataHoje', '', 0);  
  var hoje=ajax.ler();
  
  hoje = hoje.replace(/<br>/g, String.fromCharCode(13));
} 
else
  hoje = document.getElementById('txtDATATRAB').value;   

/*var pridiaMES = '01'+ hoje.substring(2);*/
var pridiaMES = hoje;
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');
pridiaMES = pridiaMES.replace('/', '');


document.getElementById('dataTRAB').value = pridiaMES;

showAJAX(0);
}

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

lCTRL = e.ctrlKey;

var lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanOCORRENCIA= document.getElementById('divOCORRENCIA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanPLANTAO= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_PLANTAO';

if  (tecla==45 && ! lJanRegistro)   	incluirREG();

if  (tecla==27) {        
  if (lJanAuxilio)   	fecharAUXILIO();
  else if (lJanPLANTAO)   	escondePLANTAO();
  else if (lJanOCORRENCIA)   	fecharOCORRENCIA();
  else if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
if  ( (tecla==13|| tecla==40) && lJanOCORRENCIA && cfoco=='txtDATA_PROXIMO' )  {
  e.stopPropagation();e.preventDefault();
  document.getElementById('txtOCORRENCIA').focus();
  cfoco='txtOCORRENCIA';
  return;
}

if  (tecla==13 && ! lJanAuxilio && ! lJanRegistro && ! lJanOCORRENCIA && ! lJanPLANTAO) {
 if (document.getElementById('txtPR').value.length<4) {
    alert('Preencha pelo menos 4 letras ou números para pesquisar');
    return;
 }
 showAJAX(1);
 ajax.criar('ajax/ajaxLIGACOES.php?acao=lerREGS&vlr='+
   document.getElementById('dataTRAB').value+'&vlr2='+document.getElementById('txtPR').value.rtrim().ltrim()+
    '&lendo='+document.getElementById('lendoATUAL').value+
    '&proprias='+document.getElementById('somentePROPRIAS').value, desenhaTabela);
 return; 
}  
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();  

if ( (lJanRegistro || lJanOCORRENCIA) && cfoco!='txtOCORRENCIA') eval("teclasNavegacao(e);");


var soLEITURA='nao';
if (document.getElementById('hidSO_LEITURA')) {
  soLEITURA = document.getElementById('hidSO_LEITURA').value;  
}
if  ( tecla==113 && lJanRegistro && ! lJanOCORRENCIA ) document.getElementById('btnGRAVAR').click();
if  ( tecla==113 && lJanOCORRENCIA ) document.getElementById('btnGRAVAR_OCORRENCIA').click();
if  ( tecla==115 && lJanRegistro && document.getElementById('lendoATUAL').value!='Indicações' 
    && document.getElementById('lendoATUAL').value!='Atendimento Presencial/demais ligações') perdeu();
if  ( tecla==119 && lJanRegistro && soLEITURA=='nao')  AuxilioF7(cfoco); 
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


/*******************************************************************************/
function fecharEDICAO()     {

showAJAX(1);
if (document.getElementById('hidREPRESENTANTE'))
  ajax.criar('ajax/ajaxLIGACOES.php?acao=desbloquearCORRETOR&vlr='+document.getElementById('hidREPRESENTANTE').value+
          '&lendo='+document.getElementById('lendoATUAL').value, '', 0);
 
showAJAX(0);

document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");


ColocaFocoCmpInicial();
}

/*******************************************************************************/
function fecharOCORRENCIA()     {

document.getElementById("divOCORRENCIA").innerHTML='';
document.getElementById("divOCORRENCIA").setAttribute(propCLASSE, "cssDIV_ESCONDE");

ColocaFocoCmpInicial();
}



/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro) 
  lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO_INDICACAO';
var lJanOCORRENCIA = document.getElementById('divOCORRENCIA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else if (lJanOCORRENCIA ) {
	document.getElementById('txtDATA_OCORRENCIA').focus();
  nQtdeCamposTextForm = 3;
}
else if (lJanRegistro )
	document.getElementById('txtNOME').focus();
else 
  document.getElementById('txtFOCADO').focus();  	
}	

/*******************************************************************************/
function lerREGS( avancarDATA ) {

if ( typeof(avancarDATA)=='undefined' )  avancarDATA=0;
showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

data = document.getElementById('dataTRAB').value;
var data2 = new Date(parseInt(data.substring(4, 10),10), parseInt(data.substring(2, 4),10)-1, parseInt(data.substring(0, 2),10));

data2.addDays(avancarDATA);
var dia=data2.getDate(); if (dia.toString().length<2) dia = '0'+dia;
var mes=data2.getMonth()+1; if (mes.toString().length<2) mes = '0'+mes;

document.getElementById('dataTRAB').value = dia+''+mes+''+data2.getFullYear();
dataLER = data2.getFullYear()+''+mes+''+dia; 



ajax.criar('ajax/ajaxLIGACOES.php?acao=lerREGS&vlr='+dataLER+
      '&lendo='+document.getElementById('lendoATUAL').value+
        '&proprias='+document.getElementById('somentePROPRIAS').value, desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
if ( ajax.terminouLER() ) {
  document.getElementById("SELECAO").value='';

  aRESP = ajax.ler().split('|');
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = ajax.ler().split('^')[1];
  var filtro = ajax.ler().split('^')[2];
  var qtdeVALIDAS = ajax.ler().split('^')[3];
  var diaSEMANA = ajax.ler().split('^')[4];
/*  var qtdePRESENCIAL = ajax.ler().split('^')[5]; */
  var qtdeDEMAISTIPOS = ajax.ler().split('^')[6];

  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  centerDiv( 'divOCORRENCIA' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Plantão</font>&nbsp;&nbsp;&nbsp;&nbsp;';

  data=document.getElementById("dataTRAB").value;
  dataLER = data.substring(0, 2)+'/'+data.substring(2, 4)+'/'+data.substring(6, 10);

  if (filtro=='') 
    titulo.innerHTML += '&nbsp;&nbsp;&nbsp;Registrados em: ' + dataLER + ', '+diaSEMANA; 
  else
    titulo.innerHTML += '&nbsp;&nbsp;&nbsp;&nbsp;'+filtro;


    var legenda='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color:blue">&nbsp;&nbsp;&nbsp;</span>'+
                '<span  width="100px">&nbsp;= Ligações&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>'+
                '<span style="background-color:black">&nbsp;&nbsp;&nbsp;</span>'+
                '<span >&nbsp;= presenças</span>';

    var legendaINDICACOES=
                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="background-color:blue">&nbsp;&nbsp;&nbsp;</span>'+
                '<span  width="100px">&nbsp;= Agendamento&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';


  if (document.getElementById("lendoATUAL").value=='Indicações' || document.getElementById("lendoATUAL").value=='Atendimento Presencial/demais ligações') { 
    document.getElementById("totREGS").innerHTML = 'Registros: &nbsp;&nbsp;'+qtdeDEMAISTIPOS +
       (document.getElementById("lendoATUAL").value=='Atendimento Presencial/demais ligações' ? legenda : legendaINDICACOES);
  }
  else
    document.getElementById("totREGS").innerHTML = 'Ligações: &nbsp;&nbsp;'+qtdeVALIDAS+'&nbsp;&nbsp;&nbsp;&nbsp;'+
         (document.getElementById("proximoREPRE").value!='' ? 
              '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Último corretor atendeu: '+document.getElementById("proximoREPRE").value : '');  
/*
    document.getElementById("totREGS").innerHTML = 'Ligações: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;'+
              '('+qtdeVALIDAS + ' não perdidas)'; */

  /* aqui é o seguinte,   nao sei onde, nao sei qual arquivo esta sujando a variavel lendoATUAL
   colocando caracteres do nada exemplo: Indica^&@*@$@&*(           
   como nao descobri onde esta o erro, eu conserto aqui */
  if (document.getElementById("lendoATUAL").value.indexOf('Indica')!=-1)
    document.getElementById("lendoATUAL").value='Indicações';
  document.getElementById("tdPLANTAO").innerHTML = document.getElementById("lendoATUAL").value;


  document.getElementById('tdATUALIZA').style.display='none';

  if (document.getElementById('recarregarINC').value==1) {
    incluirREG();
  } 
  if (document.getElementById("lendoATUAL").value=='Plantão medicina') {
    clearTimeout(timer);
    timer=setTimeout('atualiza()', 180000);
  }
}
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=incluirREG'+
  '&lendo='+document.getElementById('lendoATUAL').value, desenhaJanelaREG);
}

/*******************************************************************************/
function novaOCORRENCIA() {
if (document.getElementById('hidSO_LEITURA')) 
  soLEITURA = document.getElementById('hidSO_LEITURA').value;

if (document.getElementById('numREG').value=='') {
  alert('Primeiro grave a indicação para poder adicionar ocorrências');return;
}
if (soLEITURA!='nao') {
  alert('Somente leitura');return;
}



showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=incluirOCORRENCIA', desenhaJanelaOCORRENCIA);
}

/*******************************************************************************/
function editarOCORRENCIA(vlr) {
if (document.getElementById('hidSO_LEITURA')) 
  soLEITURA = document.getElementById('hidSO_LEITURA').value;

if (soLEITURA!='nao') {
  alert('Somente leitura');return;
}

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=editarOCORRENCIA&vlr='+vlr, desenhaJanelaOCORRENCIA);
}

/*******************************************************************************/
function removerOCORRENCIA(vlr) {
alert('Nao e possivel excluir ocorrencia');
}



/*******************************************************************************/
function desenhaJanelaOCORRENCIA()     {
if ( ajax.terminouLER() ) {

  showAJAX(0);

  var divOCORRENCIA = document.getElementById('divOCORRENCIA');
  divOCORRENCIA.setAttribute(propCLASSE, 'cssDIV_OCORRENCIA');    
  divOCORRENCIA.innerHTML = ajax.ler();

  Muda_CSS();

  ColocaFocoCmpInicial();
}

}  




/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {

  showAJAX(0);

  if (ajax.ler()=='erroCONEXAO') {
    document.getElementById('recarregarINC').value=0;
    alert('Erro de conexão - reabra tela de ligações'); return;
  }
  var divEDICAO = document.getElementById('divEDICAO');

  
  if (ajax.ler()=='nada') {
    document.getElementById('recarregarINC').value=0;
    alert('Plantão não foi configurado ou corretores do plantão estão bloqueados'); 
    return;
  }
  
  if (ajax.ler().indexOf('SÓ LEITURA')!=-1) {
    var tipo=ajax.ler().substr( ajax.ler().indexOf('TABELA:')+8 );
    tipo = tipo.substring(0, tipo.indexOf(' ')).toUpperCase() ; 
    alert('Atenção:\n\n'+
          'Você está trabalhando com registros do tipo '+document.getElementById('lendoATUAL').value.toUpperCase()+'\n\n'+
          'O registro que você clicou é do tipo '+tipo+'\n\n'+
          'Por uma questão técnica, você não poderá alterar e gravar o registro a seguir, somente visualizá-lo\n\n'+
          'Se quiser alterar este registro, clique no ícone '+tipo+' e pesquise o registro novamente');
  }

  if (document.getElementById('lendoATUAL').value=='Indicações' || ajax.ler().indexOf('Iniciado')!=-1)
    divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO_INDICACAO');    
  else
    divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    
  divEDICAO.innerHTML = ajax.ler();

  Muda_CSS(); 

  if ( document.getElementById('recarregarINC').value==1 ) {
    document.getElementById('txtNOME').value = document.getElementById('hidNOME').value; 
    document.getElementById('txtINDICACAO').value = document.getElementById('hidINDICACAO').value;
    document.getElementById('txtFONES').value = document.getElementById('hidFONES').value;
    document.getElementById('txtATENDIMENTO_PRODUTO').value = document.getElementById('hidATENDIMENTO_PRODUTO').value;
    document.getElementById('txtOBS').value = document.getElementById('hidOBS').value;

    document.getElementById('txtATENDIMENTO_PRODUTO').focus();   document.getElementById('txtINDICACAO').focus();
  }
  document.getElementById('recarregarINC').value=0;

  if (document.getElementById('lendoATUAL').value=='Indicações') nQtdeCamposTextForm = 9;
  else if (document.getElementById('lendoATUAL').value=='Atendimento Presencial/demais ligações') nQtdeCamposTextForm = 8;
  else nQtdeCamposTextForm = 6;

  if (document.getElementById('tdPERDEU')) {
    if (document.getElementById('hidSO_LEITURA').value=='sim')  
      document.getElementById('tdPERDEU').style.display='none';
  }
  
  ColocaFocoCmpInicial();
  
  if (document.getElementById('numREG').value=='' && document.getElementById('txtFONES').value=='')
      document.getElementById('txtFONES').value=document.getElementById('txtPR').value; 
}
}  
/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

var tab = document.getElementById('tabREGs');
for (var f=0; f<tab.rows.length; f++) {

  idLIN = tab.rows[f].id;

  if (idLIN==document.getElementById('SELECAO').value) {
      if (tab.rows[f].cells[6].innerHTML=='nao') {
        alert('Esta indicação não pertence a um corretor de sua responsabilidade\n\nNão é possível editá-la'); return;
      } 
    tabela=tab.rows[f].cells[5].innerHTML; break;     
  }
}

showAJAX(1);

ajax.criar('ajax/ajaxLIGACOES.php?acao=editarREG&vlr=' + id+
    '&lendo='+tabela, desenhaJanelaREG);

}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro = document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (!lJanRegistro)
  lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO_INDICACAO';
lJanOCORRENCIA= document.getElementById('divOCORRENCIA').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (! lJanRegistro && ! lJanOCORRENCIA)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

if (document.getElementById('hidSO_LEITURA')) {
  if (document.getElementById('hidSO_LEITURA').value=='sim') return;
}

if (nomeCMP!='todos')         {

  vlr = document.getElementById(nomeCMP).value;
	switch (nomeCMP) {
		case 'txtREPRESENTANTE':
		case 'txtINDICACAO':
		case 'txtATENDIMENTO_PRODUTO':
		case 'txtSITUACAO':
		case 'txtREPRESENTANTE_OCORRENCIA':
	  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
			
			cmpLBL.innerHTML = 'lendo...';
			ajax.criar('ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			
      aRESP = ajax.ler().split(';');  
    	
      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();
    	
    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';
        
    	cmpLBL.innerHTML = cVLR;
			break;

	  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );

	}
	return;
}

else  {
	for (i=0;i<aCMPS.length;i++)   {
		cmp = aCMPS[i].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];

    if ( ! document.getElementById(cCMP)) {continue;}
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl'); 		
				
		erro=0;
		switch (cCMP)   {
			case 'txtNOME':
        break;
        
			case 'txtDATA':
        if ( cVLR=='' || ! verifica_data('txtDATA') )   erro=1;
        break;
        
			case 'txtREPRESENTANTE':        			 
  		case 'txtREPRESENTANTE_OCORRENCIA':
			case 'txtATENDIMENTO_PRODUTO':
			case 'txtINDICACAO':
			case 'txtSITUACAO':
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1) erro=1;
        break;

		case 'txtFONES':
      if ( document.getElementById('numREG').value=='' && cVLR!='' ) {
        if (cVLR.length<8) {
          alert('Preencha um telefone com pelo menos 8 digitos'); return;
        }
        showAJAX(1);
  			ajax.criar('ajax/ajaxLIGACOES.php?acao=verTELEFONE&vlr=' +cVLR, '', 0);
        showAJAX(0);

        if (ajax.ler()=='existe') {
              if (! confirm('Telefone já registrado no sistema, registrar de novo?')) return;
        }
      }  
      break;
 		}
    if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	
  var data = document.getElementById('txtDATA').value;
  var dataGRAVAR='null';
  if (data.rtrim().ltrim()!='') 
    dataGRAVAR = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);
  
  var nomeREPRE=document.getElementById('lblREPRESENTANTE').innerHTML.replace(/&nbsp;/g, '');
  nomeREPRE=nomeREPRE.substring(0, nomeREPRE.indexOf('<b>'));
  var nomeOPERADORA=document.getElementById('lblATENDIMENTO_PRODUTO').innerHTML.replace(/&nbsp;/g, '');
  nomeOPERADORA=nomeOPERADORA.substring(0, nomeOPERADORA.indexOf('<b>'));

  var nomeINDICACAO=document.getElementById('lblINDICACAO').innerHTML.replace(/&nbsp;/g, '');
  nomeINDICACAO=nomeINDICACAO.substring(0, nomeINDICACAO.indexOf('<b>'));

  var radTIPO=0;
  if (document.forms['frmLIGACOES'].elements['radTIPO']) {
    rdBUTTON = document.forms['frmLIGACOES'].elements['radTIPO'];
    var radTIPO='';
    for( i = 0; i < rdBUTTON.length; i++ ) {
      if( rdBUTTON[i].checked == true )     radTIPO = rdBUTTON[i].value;
    }
  }

  if ( document.getElementById('lendoATUAL').value=='Atendimento Presencial/demais ligações') {
    cmps= dataGRAVAR+'|'+
          document.getElementById('txtNOME').value+'|'+document.getElementById('txtFONES').value+ '|' +
          document.getElementById('txtATENDIMENTO_PRODUTO').value+'|'+document.getElementById('txtREPRESENTANTE').value+'|'+
          document.getElementById('numREG').value+'|'+
          document.getElementById('txtINDICACAO').value+'|'+
          document.getElementById('txtOBS').value+'|'+
          document.getElementById('txtEMAIL').value;
  }
  else if ( document.getElementById('lendoATUAL').value=='Indicações' ) {
    cmps= dataGRAVAR+'|'+
          document.getElementById('txtNOME').value+'|'+document.getElementById('txtFONES').value+ '|' +
          document.getElementById('txtATENDIMENTO_PRODUTO').value+'|'+document.getElementById('txtREPRESENTANTE').value+'|'+
          document.getElementById('numREG').value+'|'+
          document.getElementById('txtINDICACAO').value+'|'+
          document.getElementById('txtOBS').value+'|'+
          document.getElementById('txtEMAIL').value+'|'+
          document.getElementById('txtSITUACAO').value;
  }

  else {
  
    cmps= dataGRAVAR+'|'+
          document.getElementById('txtNOME').value+'|'+document.getElementById('txtFONES').value+ '|' +
          document.getElementById('txtATENDIMENTO_PRODUTO').value+'|'+document.getElementById('hidREPRESENTANTE').value+'|'+
          document.getElementById('numREG').value+'|'+
          nomeREPRE+'|'+nomeOPERADORA+'|'+
          nomeINDICACAO+'|'+document.getElementById('txtINDICACAO').value+'|'+
          document.getElementById('txtOBS').value;
  }

  showAJAX(1);
	ajax.criar('ajax/ajaxLIGACOES.php?acao=gravar&vlr=' + cmps+
          '&lendo='+document.getElementById('lendoATUAL').value+
          '&tipo='+radTIPO, '', 0);
  showAJAX(0);

  resp = ajax.ler();

  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1 )   {
    document.getElementById('SELECAO').value="";
  
    var info = resp.split(';');
  	cID = info[1];
  	proximoREPRE = info[2];

    if (proximoREPRE!='') 
      document.getElementById("proximoREPRE").value = proximoREPRE;

  	window.top.document.getElementById('infoTrab').value = 'frmLIGACOES:GRAVOU=' + cID
  	lerREGS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
}

/*******************************************************************************/
function excluirREG() {

if (document.getElementById('lendoATUAL').value=='Indicações') {
  alert('Para excluir uma indicação, você precisa da senha de um usuário que tenha acesso à todas as indicações ou do administrador');

  var senha=prompt('Senha:','')

  if (senha==null) return;
  if (senha.rtrim()=='') return;

  showAJAX(1);
  ajax.criar('ajax/ajaxLIGACOES.php?acao=senhaEXCLUIR&vlr='+senha, '', 0);
  showAJAX(0);

  if (ajax.ler()=='nao') {
    alert('Nenhum operador do sistema com esta senha possui acesso à todas as indicações');
    return;
  }
}
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

if (! confirm('Excluir este registro?')) return;

ajax.criar('ajax/ajaxLIGACOES.php?acao=excluir&vlr=' + id+
    '&lendo='+document.getElementById('lendoATUAL').value, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao excluir!!! \n\n' + ajax.ler());
  
lerREGS();  
}

/*******************************************************************************/
function buscar() {

var palavra=prompt('Digite uma nome de cliente para procurar:','');

if (palavra==null) return;
if (palavra.rtrim()=='') return;
if (palavra.rtrim().length<4) {
  alert('Mínimo 4 letras');
  return;
}

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=lerREGS&vlr='+
   document.getElementById('dataTRAB').value+'&vlr3='+palavra+
      '&lendo='+document.getElementById('lendoATUAL').value+
    '&proprias='+document.getElementById('somentePROPRIAS').value, desenhaTabela);
}

/********************************************************************************/
function proximoCORRETOR()  {

if (document.getElementById('hidSO_LEITURA').value=='sim') return;

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=proximoCORRETOR&vlr='+document.getElementById('hidREPRESENTANTE').value+
        '&lendo='+document.getElementById('lendoATUAL').value, '', 0);
showAJAX(0);

if (ajax.ler()=='erroCONEXAO') {
  document.getElementById('recarregarINC').value=0;
  alert('Erro de conexão - reabra tela de ligações'); return;
}

if (ajax.ler()=='nada') {
  alert('Nenhum corretor disponível');
  return;
}

document.getElementById('lblREPRESENTANTE').innerHTML = ajax.ler().split('|')[0];  
document.getElementById('hidREPRESENTANTE').value = ajax.ler().split('|')[1];

document.getElementById('txtNOME').focus(); 
}

/*******************************************************************************/
function qualUltimoPlantao()         {

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=qualUltimoPlantao', '', 0);  
showAJAX(0);

document.getElementById('somentePROPRIAS').value='nao';
if (ajax.ler()=='SOMENTE AS PROPRIAS INDICACOES!!!') {
  document.getElementById('lendoATUAL').value='Indicações';
  document.getElementById('somentePROPRIAS').value='sim';
} 
else 
  document.getElementById('lendoATUAL').value=ajax.ler();


}

/*******************************************************************************/
function perdeu()         {
document.getElementById('hidNOME').value= document.getElementById('txtNOME').value;
document.getElementById('hidINDICACAO').value= document.getElementById('txtINDICACAO').value;
document.getElementById('hidATENDIMENTO_PRODUTO').value= document.getElementById('txtATENDIMENTO_PRODUTO').value;
document.getElementById('hidFONES').value= document.getElementById('txtFONES').value;
document.getElementById('hidOBS').value= document.getElementById('txtOBS').value;


document.getElementById('txtNOME').value='* PERDEU LIGAÇÃO *';
document.getElementById('txtFONES').value='';
document.getElementById('txtATENDIMENTO_PRODUTO').value='';
document.getElementById('txtOBS').value='';
document.getElementById('txtINDICACAO').value='';

document.getElementById('recarregarINC').value = 1;
document.getElementById('btnGRAVAR').click();
}

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmLIGACOES'].elements['radTIPO'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtDATA').focus();
}

/********************************************************************************/
function plantao()  {
if (document.getElementById('lendoATUAL').value=='Indicações' || document.getElementById('lendoATUAL').value=='Atendimento Presencial/demais ligações') {
  alert('Indicações e atendimento presencial não funcionam com plantão, \n\nvocê seleciona qualquer corretor nestes casos'); 
  return;
}
window.top.frames['framePRINCIPAL'].location.href='plantao.php';
}
/********************************************************************************/
function atualiza()  {
clearTimeout(timer);
if ( document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE' && 
    document.getElementById("lendoATUAL").value=='Plantão medicina') {
  timer=setTimeout('atualiza()', 180000);
  return;
}
document.getElementById('tdATUALIZA').style.display='inline';
lerREGS();
}

/*******************************************************************************/
function gravarOCORRENCIA()      {

var data = document.getElementById('txtDATA_OCORRENCIA').value;
var dataGRAVAR='';
if (data.rtrim().ltrim()!='') 
  dataGRAVAR = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

if (dataGRAVAR=='') {
  alert('Preencha uma data válida'); return;
}

if (document.getElementById('lblREPRESENTANTE_OCORRENCIA').innerHTML.indexOf('ERRO')!=-1 ||
    document.getElementById('lblREPRESENTANTE_OCORRENCIA').innerHTML=='') {
  alert('Identifique o corretor da ocorrência'); return;
}


var data = document.getElementById('txtDATA_PROXIMO').value;
var dataGRAVAR2='';
if (data.rtrim().ltrim()!='') 
  dataGRAVAR2 = data.substring(6, 10)+'-'+data.substring(3, 5)+'-'+data.substring(0, 2);

cmps= document.getElementById('numREG').value+'|'+
      document.getElementById('numREG_OCORRENCIA').value+'|'+
      dataGRAVAR+'|'+dataGRAVAR2+'|'+
      document.getElementById('txtREPRESENTANTE_OCORRENCIA').value+'|'+
      escape( Encoder.htmlEncode(document.getElementById('txtOCORRENCIA').value) );

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=gravarOCORRENCIA&vlr=' + cmps, '', 0);
showAJAX(0);

resp = ajax.ler();

if (resp.indexOf('ok')==-1) alert(resp);

fecharOCORRENCIA();
editarREG();
}

/********************************************************************************/
function mostraPLANTAO()   {
var lJanPLANTAO= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_PLANTAO';

if (lJanPLANTAO) return;

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=mostrarPLANTAO', '', 0);
showAJAX(0);  

var divEDICAO = document.getElementById('divEDICAO');
 
divEDICAO.setAttribute(propCLASSE, 'cssDIV_PLANTAO');    
divEDICAO.innerHTML =  ajax.ler();
}

/********************************************************************************/
function escondePLANTAO()   {
var divEDICAO = document.getElementById('divEDICAO');
 
divEDICAO.innerHTML =  '';
divEDICAO.setAttribute(propCLASSE, 'cssDIV_ESCONDE');  
}

/********************************************************************************/
function mudarRESPONSAVEL()   {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=verOPERADOR_ALTERA_RESP', '', 0);
showAJAX(0);

if (ajax.ler().indexOf('nao')!=-1) {
  alert('Para alterar um responsável, você precisa da senha de um usuário que tenha acesso à todas as indicações ou do administrador');
  
  var senha=prompt('Senha:','')
  
  if (senha==null) return;
  if (senha.rtrim()=='') return;
  
  showAJAX(1);
  ajax.criar('ajax/ajaxLIGACOES.php?acao=senhaEXCLUIR&vlr='+senha, '', 0);
  showAJAX(0);
  
  if (ajax.ler()=='nao') {
    alert('Nenhum operador do sistema com esta senha possui acesso à todas as indicações');
    return;
  }
}

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=operadoresINDICACOES', '', 0);
showAJAX(0);
  
var novo=prompt('Digite o número do novo operador de sistema responsável por esta indicação:\n\nOperadores possíveis:\n\n'+ajax.ler()+'\n','')

if (novo==null) return;
if (novo.rtrim()=='') return;
if (isNaN(novo)) return;

showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=verOPERADOR&vlr='+novo, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('nao')!=-1) {
  alert('Operador não existe');
  return;
}


if (! confirm('Confirma fazer a seguinte troca de operador responsável:\n\n' + 
    document.getElementById('lblRESPONSAVEL').innerHTML+'\n\npor\n\n'+ajax.ler()+'\n\n') ) return;


showAJAX(1);
ajax.criar('ajax/ajaxLIGACOES.php?acao=mudarRESPONSAVEL&vlr='+id+'&novo='+novo, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('ok')!=-1) {
  alert('Operador responsável alterado com sucesso');
  editarREG();
}

else alert('Erro ao gravar \n\n'+ajax.ler()); 


}

//]]></script>
  </body>
</html>
