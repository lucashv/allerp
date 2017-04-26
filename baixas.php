<?php 
ob_start();
require("doctype.php"); 
session_start();
?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script><script
type="text/javascript" src="js/menuContexto.js" xml:space="preserve"></script><script
type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
<style type="text/css" xml:space="preserve">
.cssUPLOAD  {
position: absolute; top: 200px;  width: 570px; height: 230px;	
margin-top: -120px; margin-left: -280px; display:block; z-index:3; 
background:#E6E8EE;border:1px solid grey;  
}


.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}


</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerREGS();Avisa('');Muda_CSS();centerDiv('divUPLOAD');ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:verMENSALIDADES();">Ver mensalidades</a></li>
  <li><a href="javascript:verERROS();">Ver erros</a></li>
  <li><a href="javascript:verESTORNOS();">Ver estornos</a></li>
  <li><a href="javascript:excluirBAIXA();">Excluir/reverter baixa</a></li>
</ul>

<form id="frmBAIXAS" name="frmBAIXAS" autocomplete="off" action="" >

<div id="divUPLOAD" class="cssDIV_ESCONDE">
  <iframe src="" style="height:100%;width:100%;" scrolling="no" id="fraUPLOAD" frameborder="0" ></iframe>
</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
    <tr valign="middle"><td><font face="verdana" color="white" size="1"><span id="lblAJAX">
        Gravando.....AGUARDE</span></font> </td></tr>    
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="85%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;Baixas de mensalidades&nbsp;&nbsp;(Últimas 800)</span></td>
            
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

    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;


var lJanRegistro=0; 

lJanUPLOAD= document.getElementById('divUPLOAD').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==27) {        
  if (lJanUPLOAD)   	fechaBAIXA();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);




/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {

if (cmp!=null) 
	document.getElementById(cmp).focus();
}	

/*******************************************************************************/
function lerREGS() {
document.getElementById('lblAJAX').innerHTML = 'Lendo...';
showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxBAIXAS.php?acao=lerREGS', desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
if ( ajax.terminouLER() ) {
  showAJAX(0);
  
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 
  
  centerDiv( 'divUPLOAD' );
  VerificaAcaoInicial();
  }
}


/*******************************************************************************/
function novaBAIXA()     {
document.getElementById('divUPLOAD').setAttribute('class', "cssUPLOAD");
document.getElementById('fraUPLOAD').src = "baixa.php";
}  

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";
}

/*******************************************************************************/
function gravou() {

if ( ajax.terminouLER() ) {
  showAJAX(0);
  
  resp = ajax.ler();
  
  fecharEDICAO();
  
  if (resp.indexOf('OK')!=-1)   {
    document.getElementById('SELECAO').value="";
  
  	cID = resp.substring(resp.indexOf(';')+1);
  	window.top.document.getElementById('infoTrab').value = 'frmBAIXAS:GRAVOU=' + cID
  	lerREGS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
  
}

/*******************************************************************************/
function excluirBAIXA() {
alert('em desenvolvimento');return;
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

if (! confirm('Tem certeza?')) return;

showAJAX(1);
ajax.criar('ajax/ajaxBAIXAS.php?acao=cancelarBAIXA&vlr=' + id, '', 0);
showAJAX(0);

if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
lerREGS();  
}

/*******************************************************************************/
function baixa(nomeARQ) {

document.getElementById('lblAJAX').innerHTML = 'Gravando...';

showAJAX(1);
ajax.criar('ajax/ajaxBAIXAS.php?acao=baixar&arq='+nomeARQ, '', 0);
showAJAX(0);

fechaBAIXA();
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao baixar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmBAIXAS:GRAVOU=' + ajax.ler().replace('ok;', '');
lerREGS();  
}

/*******************************************************************************/
function fechaBAIXA()   {
document.getElementById('fraUPLOAD').src="";
document.getElementById('divUPLOAD').setAttribute('class', "cssDIV_ESCONDE");
}

/*******************************************************************************/
function verMENSALIDADES() {

document.getElementById('lblAJAX').innerHTML = 'Lendo...';
showAJAX(1);
ajax.criar('ajax/ajaxBAIXAS.php?acao=verMENSALIDADES&vlr='+
  document.getElementById('SELECAO').value, '', 0);
showAJAX(0);

window.open( ajax.ler() );  
}

/*******************************************************************************/
function verESTORNOS() {

document.getElementById('lblAJAX').innerHTML = 'Lendo...';
showAJAX(1);
ajax.criar('ajax/ajaxBAIXAS.php?acao=verESTORNOS&vlr='+
  document.getElementById('SELECAO').value, '', 0);
showAJAX(0);

window.open( ajax.ler() );  
}


/*******************************************************************************/
function verERROS() {

document.getElementById('lblAJAX').innerHTML = 'Lendo...';
showAJAX(1);
ajax.criar('ajax/ajaxBAIXAS.php?acao=verERROS&vlr='+
  document.getElementById('SELECAO').value, '', 0);
showAJAX(0);

window.open( ajax.ler() );  
}





//]]></script>
  </body>
</html>
