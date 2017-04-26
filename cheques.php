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
position: absolute; top: 200px;  width: 500px; height: 80px;	
margin-top: -130px; margin-left: -275px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerHOJE();lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu_MAIOR">
  <li><a href="javascript:buscar();">Pesquisar cheque (Ctrl+B)</a></li>
</ul>

<form id="frmCHEQUES" name="frmCHEQUES" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />

<input id="dataTRAB" type="hidden" value="" >


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="60%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span>
          <input type="text" id="txtDATATRAB" value="" 
              style="color: white;background-color: white; border: 0px solid white;font-size:0px;height:0px" 
              onchange="lerHOJE(1);lerREGS();";/>        
        </td>
        
      	<td id="btnDATATRAB" title="Escolher data" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
      	onmouseover="this.style.backgroundColor='#A9B2CA'"  >
      	  <img src="images/buscadata.png" />
      	</td>
        
        <td id="btnPESQUISAR" title="Pesquisa cheque (Ctrl+B)"  align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="buscar();" >
          <img src="images/pesquisa.png" />
        </td>

        <td  id="btnRETORNAR" title="Retornar 1 mês  (seta p/ esquerda)" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(-1);" >
          <img src="images/setaESQUERDA.png" />
        </td>
        
        <td id="btnAVANCAR" title="Avançar 1 mês (seta p/ direita)" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="lerREGS(1);" >
          <img src="images/setaDIREITA.png" />
        </td>    
            
        <td style="cursor: pointer;text-align:right;"  
          onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
          class="lblTitJanela" >[ X ]</span>
        </td>      
      </tr></table></td></tr>

      <tr>
        <td valign="top" height="<?php echo ($_SESSION['altIFRAME'] * .75); ?> px" >
          <div id="titTABELA">&nbsp;</div>
          <div id="divTABELA" style="overflow:auto;min-height:95%;height:95%" class="container"></div>
        </td>
      </tr>

      <tr><td><table width="100%"><tr>

        <td align="right">

<input id="txtFOCADO" type="text" value="" 
  style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />
                      
        <span class="lblUSUARIO" id="totREGS">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;<span>
        </td>
        
      </tr></table></td></tr>
    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 5;

var largPR = 0;

var aCMPS=new Array(1);
aCMPS[0]='txtTIPO;Digite o tipo';

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
  ajax.criar('ajax/ajaxRECEBIMENTOS.php?acao=lerDataHoje', '', 0);  
  var hoje=ajax.ler();
  
  hoje = hoje.replace(/<br>/g, String.fromCharCode(13));
} 
else
  hoje = document.getElementById('txtDATATRAB').value;   

var pridiaMES = '01'+ hoje.substring(2);
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

if (tecla==39 && !lJanRegistro) document.getElementById('btnAVANCAR').click();
if (tecla==37 &&  !lJanRegistro) document.getElementById('btnRETORNAR').click();
if (tecla==66 && lCTRL &&  ! lJanRegistro) document.getElementById('btnPESQUISAR').click();

if  (tecla==27) {        
  if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  

if (lJanRegistro)  eval("teclasNavegacao(e);");

}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);


/*******************************************************************************/
function fecharEDICAO()     {
document.getElementById("divEDICAO").innerHTML='';
document.getElementById("divEDICAO").setAttribute(propCLASSE, "cssDIV_ESCONDE");
ColocaFocoCmpInicial();
}


/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

var lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else 
  document.getElementById('txtFOCADO').focus();  	
}	

/*******************************************************************************/
function lerREGS( avancarDATA ) {

if ( typeof(avancarDATA)=='undefined' )  avancarDATA=0;
showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxCHEQUES.php?acao=lerREGS&vlr='+
  document.getElementById('dataTRAB').value+'&vlr2='+avancarDATA, desenhaTabela);
}


/*******************************************************************************/
function  desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 
  var anoMES = aRESP[1].split('^')[2];
  
  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Cheques</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  titulo.innerHTML += anoMES;
  
  document.getElementById('dataTRAB').value = aRESP[1].split('^')[3];    
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  }
}


/*******************************************************************************/
function buscar() {

var palavra=prompt('Digite o nº cheque para procurar:','');

if (palavra==null) return;
if (palavra.rtrim()=='') return;

showAJAX(1);
ajax.criar('ajax/ajaxCHEQUES.php?acao=lerREGS&vlr=cheque'+palavra, desenhaTabela);

}


//]]></script>
  </body>
</html>
