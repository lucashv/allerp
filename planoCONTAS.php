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
.cssDIV_EDICAO {
position: absolute; top: 200px;  width: 400px; height: 80px;	
margin-top: -130px; margin-left: -200px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerREGS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

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
</ul>

<form id="frmPLANOCONTAS" name="frmPLANOCONTAS" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="55%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="80%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>
        
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
        <td>
          &nbsp;<span class="lblPADRAO">Pesquisa:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:200px;" onkeyup="PR();" maxlength="30" />
        </td>
      </tr></table></td></tr>
    </table>

  </td>

</tr>
</table>

</form>

<script language="javascript" type="text/javascript" xml:space="preserve">//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 3;

var largPR = 0;

var aCMPS=new Array(3);
aCMPS[0]='txtCODIGO;Digite o código da conta';
aCMPS[1]='txtDESCRICAO;Digite a descricao da conta';
aCMPS[2]='txtNIVEL;Identifique o nível da conta';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==27) {        
  if (lJanRegistro)   	fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
  
if  (tecla==45 && ! lJanRegistro)   	incluirREG();
if (lJanRegistro)  eval("teclasNavegacao(e);");

var lF2=tecla==113;  

if  ( lF2 && lJanRegistro ) 
  document.getElementById('btnGRAVAR').click();
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

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanRegistro )
	document.getElementById('txtCODIGO').focus();
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxPLANOCONTAS.php?acao=lerREGS', desenhaTabela);
}


/*******************************************************************************/
function  desenhaTabela() {
if ( ajax.terminouLER() ) {
  aRESP = ajax.ler().split('|');
  
  document.getElementById("titTABELA").innerHTML = aRESP[0];
  document.getElementById("divTABELA").scrollTop=0;
  document.getElementById("divTABELA").innerHTML = aRESP[1].split('^')[0];
  
  var qtdeREGS = aRESP[1].split('^')[1]; 

  showAJAX(0);
  
  centerDiv( 'divEDICAO' );
  VerificaAcaoInicial();
  
  var titulo=document.getElementById('lblTITULO');
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Plano de contas</font>&nbsp;&nbsp;&nbsp;&nbsp;';
}
}

/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxPLANOCONTAS.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');
  
  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();
  
  /* muda estilo de campos text, span para o padrão do site */
  Muda_CSS(); 
  
  showAJAX(0);
  ColocaFocoCmpInicial();
}
}  

/*******************************************************************************/
function editarREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

var tab=document.getElementById('tabREGs');
for (var t=0; t<tab.rows.length; t++)   {
  
  if (tab.rows[t].id == document.getElementById('SELECAO').value) {
    if (tab.rows[t].cells[0].innerHTML.trim()=='&nbsp;&nbsp;1' ||
        tab.rows[t].cells[0].innerHTML.trim()=='&nbsp;&nbsp;2') {
      alert('Conta não editável');   return;
    }
    break;
  }              

}
	
showAJAX(1);
ajax.criar('ajax/ajaxPLANOCONTAS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";


if (nomeCMP!='todos')         {

}

else  {
	for (i=0;i<aCMPS.length;i++)   {
		cmp = aCMPS[i].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl'); 		
				
		erro=0;
		switch (cCMP)   {
			case 'txtDESCRICAO':
        if ( cVLR=='' ) erro=1;
        break;

			case 'txtNIVEL':
        if ( cVLR=='' ) erro=1;
        var nivel = parseInt(cVLR, 10);
        if (nivel<1 || nivel>3) erro=1;
        break;
        
      case 'txtCODIGO':
        if ( cVLR=='' ) erro=1;
        else {
          if (document.getElementById('tituloEDICAO').innerHTML.toLowerCase().indexOf('incluir')!=-1)
            var op='incluir';
          else  
            var op='edicao';
            
        	showAJAX(1);
        	ajax.criar('ajax/ajaxPLANOCONTAS.php?acao=verDUPLICIDADE&vlr=' + 
            document.getElementById('txtCODIGO').value+'&numreg='+
            document.getElementById('numREG').value+'&op='+op, '', 0);
          showAJAX(0);
          
          if (ajax.ler().indexOf('jaCAD')!=-1) {
            alert('Já há uma conta registrada com este código.');
            return;
          }
        } 
        break;
        
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}
  
  cmps= document.getElementById('txtCODIGO').value+'|'+
        document.getElementById('txtDESCRICAO').value+'|'+
        document.getElementById('txtNIVEL').value+'|'+
        document.getElementById('numREG').value;
        

  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	ajax.criar('ajax/ajaxPLANOCONTAS.php?acao=gravar&vlr=' + cmps, gravou);
}

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
  	window.top.document.getElementById('infoTrab').value = 'frmPLANOCONTAS:GRAVOU=' + cID
  	lerREGS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}  
}


/************************************/
function PR()  {
var tabela = document.getElementById("tabREGs");
var PR = document.getElementById('txtPR').value.toUpperCase();    larg = PR.length;

if (larg < largPR) {largPR = larg; return; }

largPR = larg;

if (PR.rtrim().ltrim() != '')   {
	for(var lin=0;lin < tabela.rows.length; lin++) {
		if ( tabela.rows[lin].cells[1].innerHTML.substring(0, larg).toUpperCase() == PR ) {Selecionar(tabela.rows[lin].id, 1);break;}
	}
}
}


//]]></script>
  </body>
</html>
