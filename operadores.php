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
position: absolute; top: 200px;  width: 900px; height: 180px;	
margin-top: -260px; margin-left: -450px; display:block; z-index:3;}

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
  <li><a href="javascript:excluirREG();">Tornar ativo/inativo</a></li>  
  <li><a href="javascript:resetar();">Resetar senha</a></li>  
</ul>

<form id="frmOPERADORES" name="frmOPERADORES" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id="somenteATIVOS" type="hidden" value="S" />


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="55%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="80%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>
        
        <td title="Alternar entre ativo/inativo" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar();" >
          <img src="images/trocar.png" />
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
        <td>
          &nbsp;<span class="lblPADRAO">Pesquisa:</span>&nbsp;&nbsp;
          <input type="text" id="txtPR" style="width:200px;" onkeyup="PR();" maxlength="30" />
        </td>

        <td width="'30%" align="right">              
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
var nQtdeCamposTextForm = 1;

var largPR = 0;

var aCMPS=new Array(1);
aCMPS[0]='txtNOME;Digite o nome do operador';

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
	document.getElementById('txtNOME').focus();
}	

/*******************************************************************************/
function lerREGS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

ajax.criar('ajax/ajaxOPERADORES.php?acao=lerREGS&ativos='+
  document.getElementById('somenteATIVOS').value, desenhaTabela);
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
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Operadores do sistema</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativos)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativos)';
  else  
    titulo.innerHTML += '(Todos)';
    
  document.getElementById("totREGS").innerHTML = 'Filtrados: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  }
}


/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxOPERADORES.php?acao=incluirREG', desenhaJanelaREG);
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
	
showAJAX(1);
ajax.criar('ajax/ajaxOPERADORES.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

/* concatena */
cmps= document.getElementById('txtNOME').value+'|'+
      document.getElementById('numREG').value; 

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
			case 'txtNOME':
        if ( cVLR=='' ) erro=1;
        break;
        
 		}

	 if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  strPERMISSAO='';
  for(var i=0; i < document.forms[0].chkPERMISSAO.length; i++){
    if(document.forms[0].chkPERMISSAO[i].checked)    strPERMISSAO += document.forms[0].chkPERMISSAO[i].value;
  }
  strESCRITORIOS='';
  if (isNaN(document.forms[0].chkESCRIT.length))
    strESCRITORIOS='0';
  else {
    for(var i=0; i < document.forms[0].chkESCRIT.length; i++){
      if(document.forms[0].chkESCRIT[i].checked)    
        strESCRITORIOS += String.fromCharCode( parseInt(document.forms[0].chkESCRIT[i].value, 10)+64 );
    }
  }

	showAJAX(1);
	ajax.criar('ajax/ajaxOPERADORES.php?acao=gravar&vlr=' + cmps+'&vlr2='+strPERMISSAO+'&vlr3='+strESCRITORIOS, gravou);
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
  	if (cID.indexOf('INC_')>-1) alert("Registro de operador incluído com a senha '123'");
  	cID= cID.replace('INC_', '');
  	window.top.document.getElementById('infoTrab').value = 'frmOPERADORES:GRAVOU=' + cID
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

/*******************************************************************************/
function excluirREG() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

ajax.criar('ajax/ajaxOPERADORES.php?acao=mudarSITUACAO&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmOPERADORES:GRAVOU=' + id
lerREGS();  
}



/************************************/
function alternar()  {
ativos = document.getElementById('somenteATIVOS').value;
 
if (ativos=='S') var agora='N';
else if (ativos=='N') var agora='';
else var agora='S';

document.getElementById('somenteATIVOS').value = agora;
setTimeout('void(0)', 200);

lerREGS();
}

/*******************************************************************************/
function resetar() {
id = document.getElementById('SELECAO').value 
if (id=='') { alert('Selecione um registro');return;}

if (id==1) {
  alert('Administrador nao se reseta senha'); return;
}

ajax.criar('ajax/ajaxOPERADORES.php?acao=resetarSENHA&vlr=' + id, '', 0);

if (ajax.ler().indexOf('naopode')!=-1) { 
  alert('Você não tem acesso a resetar senhas');
  return; 
}  
  
if (ajax.ler().indexOf('ok')==-1) { 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  return;
}

alert('Senha deste usuário resetada para: 123'); 
}

/********************************************************************************/
function checar(item)  {

if (document.getElementById('lblADMIN')) {
  alert('Você não pode tirar permissões do usuário nº 1 (administrador)'); return;
}
rdBUTTON = document.forms['frmOPERADORES'].chkPERMISSAO[item];
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtNOME').focus();
}

/********************************************************************************/
function checarESCRIT(item)  {

if (document.getElementById('lblADMIN')) {
  alert('Você não pode tirar permissões do usuário nº 1 (administrador)'); return;
}
if (isNaN(document.forms['frmOPERADORES'].chkESCRIT.length)) {
  rdBUTTON = document.forms['frmOPERADORES'].chkESCRIT;
  rdBUTTON.checked = rdBUTTON.checked ? false : true;
}
else {
  rdBUTTON = document.forms['frmOPERADORES'].chkESCRIT[item];
  rdBUTTON.checked = rdBUTTON.checked ? false : true;
}

document.getElementById('txtNOME').focus();
}










//]]></script>
  </body>
</html>
