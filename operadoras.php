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
position: absolute; top: 200px;  width: 900px; height: 80px;  
margin-top: -300px; margin-left: -405px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;  margin-top: -40px; margin-left: -10px; display:block; 
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
</ul>

<form id="frmOPERADORAS" name="frmOPERADORAS" autocomplete="off" action="" >

<div id="divEDICAO" class="cssDIV_ESCONDE"></div>
<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>


<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>

<input id="SELECAO" type="hidden" value="" />
<input id=SELECAO_2 type=hidden value="" >
<input id="somenteATIVOS" type="hidden" value="S" />


<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">
  
    <table cellspacing="0" cellpadding="0" border="1" width="75%" bgcolor="white" style="text-align:left;">

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
/* qtde de campos text na tela de edi��o, necessario informar  */
var nQtdeCamposTextForm = 3;

var largPR = 0;

var aCMPS=new Array(1);
aCMPS[0]='txtNOME;Digite o nome da operadora';

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;


var lJanRegistro=0; 

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if  (tecla==45 && ! lJanRegistro)     incluirREG();
if  (tecla==27) {        
  if (lJanRegistro)     fecharEDICAO();
  else {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
}  
  
if (lJanRegistro)  eval("teclasNavegacao(e);");

var lF2=tecla==113;  

if  ( lF2 && lJanRegistro )   document.getElementById('btnGRAVAR').click();
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
    
ajax.criar('ajax/ajaxOPERADORAS.php?acao=lerREGS&ativos='+
  document.getElementById('somenteATIVOS').value, desenhaTabela);
}


/*******************************************************************************/
function desenhaTabela() {
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
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Operadoras</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativas)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativas)';
  else  
    titulo.innerHTML += '(Todas)';
    
  document.getElementById("totREGS").innerHTML = 'Filtradas: &nbsp;&nbsp;'+qtdeREGS + '&nbsp;&nbsp;';
  //if (this.resposta.indexOf("ondblclick")!=-1) f_sort( document.all.tabREGs, 1, true, 3, 'h2' );  
  }
}

/*******************************************************************************/
function incluirREG() {
showAJAX(1);
ajax.criar('ajax/ajaxOPERADORAS.php?acao=incluirREG', desenhaJanelaREG);
}

/*******************************************************************************/
function desenhaJanelaREG()     {
if ( ajax.terminouLER() ) {
  var divEDICAO = document.getElementById('divEDICAO');

  divEDICAO.setAttribute(propCLASSE, 'cssDIV_EDICAO');    divEDICAO.innerHTML = ajax.ler();
  
  /* muda estilo de campos text, span para o padr�o do site */
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
ajax.criar('ajax/ajaxOPERADORAS.php?acao=editarREG&vlr=' + id, desenhaJanelaREG);
}

/*
function VerCmp(nomeCMP)      {
  console.log(document.);
}*/





/*******************************************************************************/
function VerCmp(nomeCMP)      {

lJanRegistro= document.getElementById('divEDICAO').getAttribute(propCLASSE)=='cssDIV_EDICAO';
if (! lJanRegistro)  return;

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="#F6F7F7";

rdBUTTON = document.forms['frmOPERADORAS'].elements['tipoDATA'];
//rdBUTTON = document.frmOPERADORA.elements['tipoDATA'];

var tipoDATA='';
for( i = 0; i < rdBUTTON.length; i++ ) {
  if( rdBUTTON[i].checked == true )     tipoDATA = rdBUTTON[i].value;
}

rdBUTTON = document.forms['frmOPERADORAS'].elements['qtdeMENS'];
//rdBUTTON = document.frmOPERADORA.elements['qtdeMENS'];
var qtdeMENS='';
for( i = 0; i < rdBUTTON.length; i++ ) {
  if( rdBUTTON[i].checked == true )     qtdeMENS = rdBUTTON[i].value;
}


cmps= document.getElementById('txtNOME').value+'|'+
      document.getElementById('numREG').value+'|'+
      document.getElementById('txtSEGUNDO_NOME').value+'|'+
      tipoDATA+'|'+qtdeMENS;
      
if (nomeCMP!='todos')         {
  switch (nomeCMP) {
  }
  return;
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
   if (erro==1) {alert(cMSG); document.getElementById(cCMP).focus(); return false;} 
  } 

  
  showAJAX(1);
  ajax.criar('ajax/ajaxOPERADORAS.php?acao=gravar&vlr=' + cmps, gravou);
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
    window.top.document.getElementById('infoTrab').value = 'frmOPERADORAS:GRAVOU=' + cID
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

ajax.criar('ajax/ajaxOPERADORAS.php?acao=mudarSITUACAO&vlr=' + id, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
window.top.document.getElementById('infoTrab').value = 'frmOPERADORAS:GRAVOU=' + id
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

/********************************************************************************/
function seleciona2(opcao)  {

rdBUTTON = document.forms['frmOPERADORA'].elements['tipoDATA'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtNOME').focus();
}

/********************************************************************************/
function seleciona3(opcao)  {

rdBUTTON = document.forms['frmOPERADORA'].elements['qtdeMENS'];
rdBUTTON[opcao-1].checked = true;

document.getElementById('txtNOME').focus();
}





//]]></script>
  </body>
</html>
