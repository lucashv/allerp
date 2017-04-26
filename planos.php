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
position: absolute; top: 200px;  width: 500px; height: 80px;	
margin-top: -130px; margin-left: -275px; display:block; z-index:3;}

.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="lerTABELAS();Avisa('');Muda_CSS();ColocaFocoCmpInicial();">

<script type="text/javascript" xml:space="preserve">
//<![CDATA[
/* prepara menu de contexto (botao direito do mouse) */
SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
SimpleContextMenu.attach('container', 'CM1');
//]]>
</script>
<ul id="CM1" class="SimpleContextMenu">
  <li><a href="javascript:incluirREG();">Novo registro</a></li>
  <li><a href="javascript:editarREG();"><b>Editar registro</b></a></li> 
  <li><a href="javascript:excluirREG(1);">Tornar ativo/inativo</a></li>  
</ul>

<form id="frmPLANOS" name="frmPLANOS" autocomplete="off" action="" >

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
  
    <table cellspacing="0" cellpadding="0" border="1" width="95%" bgcolor="white" style="text-align:left;">


      <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
        <td width="70%"><span class="lblTitJanela" id="lblTITULO">&nbsp;&nbsp;</span></td>
        
        <td title="Alternar entre ativos/inativos" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="alternar(2);" >
          <img src="images/trocar.png" />
        </td>
        
        <td title="Definir como tabela preços em uso" align="center" onmouseout="this.style.backgroundColor='#F6F7F7'" 
        onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="PADRAO();" >
          <img src="images/padrao.png" />
        </td>    
            
            
        <td style="cursor: pointer;text-align:right;"  
          onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
          class="lblTitJanela" >[ X ]</span>
        </td>      
      </tr></table></td></tr>
      
      <tr><td><table width="100%" height="100%" cellpadding="0" cellspacing="2" style="font-family:verdana;" ><tr>
        <td width="60px"><span class="lblTitJanela">&nbsp;Tabela:&nbsp;</span></td>

        <td  valign="top" id="divLISTBOX"></td>        
                
        <td width="50px"><span onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';" 
          onmouseout="this.style.backgroundColor='white';"
            title="Adicionar nova tabela" onclick="I_TABELA();" style="font-size:9px;color:black;" >
            &nbsp;&nbsp;&nbsp;NOVA&nbsp;&nbsp;&nbsp;</span>
        </td>
        <td width="50px"></td>
        <td><span onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';" 
          onmouseout="this.style.backgroundColor='white';"
            title="Editar nome da tabela" onclick="E_TABELA();" style="font-size:9px;color:black;" >
            &nbsp;&nbsp;&nbsp;EDITAR&nbsp;&nbsp;&nbsp;</span>
        </td>
        <td width="50px"></td>
        <td><span onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';" 
          onmouseout="this.style.backgroundColor='white';"
            title="Ativar/desativar tabela" onclick="excluirREG(2);" style="font-size:9px;color:black;" >
            &nbsp;&nbsp;&nbsp;DESATIVAR&nbsp;&nbsp;&nbsp;</span>
        </td>
                    
      </tr>
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

var colATUAL = -1;

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
function lerTABELAS() {

showAJAX(1);

ajax.criar('ajax/ajaxPLANOS.php?acao=lerTABELAS&ativos='+
  document.getElementById('somenteATIVOS').value, desenhaListBox);
}


/*******************************************************************************/
function desenhaListBox() {
if ( ajax.terminouLER() ) {

  var info = ajax.ler();
  showAJAX(0);
  
  if (info.indexOf('nada')!=-1)  {

    var titulo=document.getElementById('lblTITULO');
    titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Planos</font>&nbsp;&nbsp;&nbsp;&nbsp;';

    if (document.getElementById('somenteATIVOS').value=='S') 
      titulo.innerHTML += '(Ativos)';
    else if (document.getElementById('somenteATIVOS').value=='N') 
      titulo.innerHTML += '(Inativos)';
    else  
      titulo.innerHTML += '(Todos)';
  
    document.getElementById('divLISTBOX').innerHTML = '<font color=red>NENHUMA TABELA</font>';
    
      
    document.getElementById("divTABELA").scrollTop=0;
    document.getElementById("divTABELA").innerHTML = '';      
    
  }    
  else {  
    info=info.replace(String.fromCharCode(13),''); info = info.replace(String.fromCharCode(10),'');
    info=info.replace(String.fromCharCode(13),''); info = info.replace(String.fromCharCode(10),'');        
    info=info.replace(String.fromCharCode(13),''); info = info.replace(String.fromCharCode(10),'');  
    document.getElementById('divLISTBOX').innerHTML = info;
    
    centerDiv( 'divEDICAO' );
    VerificaAcaoInicial();
  
    lerPLANOS();
  }
}
}

/*******************************************************************************/
function PADRAO() {
if (document.getElementById('lstTABS')) {
  var lstbox = document.getElementById('lstTABS'); 
  var idTABELA = lstbox[lstbox.selectedIndex].value;

  showAJAX(1);
  ajax.criar('ajax/ajaxPLANOS.php?acao=padrao&vlr='+idTABELA, '' ,  0);

  showAJAX(0); 
  lerTABELAS();
}
else 
  alert('Nenhuma tabela na lista');  
}
/*******************************************************************************/
function lerPLANOS() {

showAJAX(1);

document.getElementById("divTABELA").innerHTML = '';
document.getElementById("SELECAO").value='';

var lstbox = document.getElementById('lstTABS');
var idTABELA = lstbox[lstbox.selectedIndex].value;

ajax.criar('ajax/ajaxPLANOS.php?acao=lerPLANOS&ativos='+
  document.getElementById('somenteATIVOS').value +'&idTABELA='+idTABELA, desenhaPlanos);
}

/*******************************************************************************/
function desenhaPlanos() {
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
  titulo.innerHTML = '&nbsp;&nbsp;<font size="+1">Planos</font>&nbsp;&nbsp;&nbsp;&nbsp;';
  
  if (document.getElementById('somenteATIVOS').value=='S') 
    titulo.innerHTML += '(Ativos)';
  else if (document.getElementById('somenteATIVOS').value=='N') 
    titulo.innerHTML += '(Inativos)';
  else  
    titulo.innerHTML += '(Todos)';
    

 }
}




/*******************************************************************************/
function incluirREG() {

var lstbox = document.getElementById('lstTABS');

resp = prompt('Novo plano', '');

if (resp==null) return;
if (resp.rtrim()=='') return;

ajax.criar('ajax/ajaxPLANOS.php?acao=novoPLANO&vlr=' + resp, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
lerPLANOS();



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
mudarVLR(colATUAL);
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
  	window.top.document.getElementById('infoTrab').value = 'frmPLANOS:GRAVOU=' + cID
  	lerTABELAS();
  }
  else
  	alert('Erro ao gravar: \n\n ' + resp);	
}
  
}


/*******************************************************************************/
function excluirREG( oQUE ) {

if (oQUE==1) {
  var idPLANO = colATUAL.substr(0, colATUAL.indexOf('_') );

  ajax.criar('ajax/ajaxPLANOS.php?acao=mudarSITUACAO_PLANO&vlr=' + idPLANO, '', 0);
  if (ajax.ler().indexOf('ok')==-1) 
    alert('Erro ao gravar!!! \n\n' + ajax.ler());
    
  lerTABELAS();
}

if (oQUE==2) {
  var lstbox = document.getElementById('lstTABS');
  var idTABELA = lstbox[lstbox.selectedIndex].value;
  
  ajax.criar('ajax/ajaxPLANOS.php?acao=mudarSITUACAO_TABELA&vlr=' + idTABELA, '', 0);
  if (ajax.ler().indexOf('ok')==-1) 
    alert('Erro ao gravar!!! \n\n' + ajax.ler());
    
  lerTABELAS();
}    
    
}



/************************************/
function alternar()  {
ativos = document.getElementById('somenteATIVOS').value;
 
if (ativos=='S') var agora='N';
else if (ativos=='N') var agora='';
else var agora='S';

document.getElementById('somenteATIVOS').value = agora;
setTimeout('void(0)', 200);

lerTABELAS();
}

/************************************/
function mudarVLR(idOBJ)  {


var idPLANO = idOBJ.substr(0, idOBJ.indexOf('_') )
var idCOLUNA = idOBJ.substr(idOBJ.indexOf('_')+1, idOBJ.indexOf('P')-idOBJ.indexOf('_')-1 );
var qualPRECO = idOBJ.substr(idOBJ.indexOf('P')+1);

var vlr=document.getElementById(idOBJ).innerHTML.rtrim().ltrim();

if (idCOLUNA==0) {
  vlr= vlr.substring(0, vlr.indexOf('&nbsp;&nbsp;') );
  if  (! confirm('Tem certeza que quer alterar o nome do plano '+vlr.toUpperCase())) return;  
}

resp = prompt('Novo valor', vlr);

if (resp==null) return;
if (resp.rtrim()=='') return;



if (idCOLUNA>0) {
  if (isNaN(resp.replace(',','.'))) return;
  if (resp.length>7) return;
}  
   
strPRECOS='';
/* se clicou sobre coluna de valores - concatena */
if (idOBJ.indexOf('P')>-1) {
 for (r=1; r<11; r++) {
   var col = idPLANO+'_'+r.toString()+'P'+qualPRECO;
   
   strPRECOS+= strPRECOS=='' ? '' : ';'; 
   strPRECOS+= document.getElementById(col).innerHTML.ltrim().rtrim().replace(',','.');
 }
}
var lstbox = document.getElementById('lstTABS');
var idTABELA = lstbox[lstbox.selectedIndex].value;

showAJAX(1);
ajax.criar('ajax/ajaxPLANOS.php?acao=mudarVALOR&vlr=' + resp +'&id='+idOBJ+'&precos='+strPRECOS +
  '&idTABELA='+idTABELA+'&preco='+qualPRECO, '', 0);
showAJAX(0);  
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
else {
  if (idCOLUNA==0) {
    document.getElementById(idOBJ).innerHTML = resp;
    lerPLANOS();
  }  
  else    
    document.getElementById(idOBJ).innerHTML = parseFloat(resp.replace(',','.'), 10).toFixed(2).toString().replace('.',',');
  
}  

}


/************************************/
function mudarDESCONTO(idOBJ)  {

var vlr=document.getElementById(idOBJ).innerHTML.rtrim().ltrim();
resp = prompt('Novo valor', vlr);

if (resp==null) return;
if (resp.rtrim()=='') return;

var idPLANO = idOBJ.substr(0, idOBJ.indexOf('_') )
var idCOLUNA = idOBJ.substr(idOBJ.indexOf('_')+1 );

if (isNaN(resp.replace(',','.'))) return;
if (resp.length>7) return;
   
strDESCONTOS='';
for (r=1; r<=7; r++) {
  var col = idPLANO+'_'+r.toString();

  strDESCONTOS+= strDESCONTOS=='' ? '' : ';'; 
  strDESCONTOS+= document.getElementById(col).innerHTML.ltrim().rtrim().replace(',','.');
}

var lstbox = document.getElementById('lstTABS');
var idTABELA = lstbox[lstbox.selectedIndex].value;

ajax.criar('ajax/ajaxPLANOS.php?acao=mudarDESCONTOS&vlr=' + resp +'&id='+idOBJ+'&descontos='+strDESCONTOS +
  '&idTABELA='+idTABELA, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
lerPLANOS();
}


/************************************/
function I_TABELA(idOBJ)  {

resp = prompt('Nova tabela', '');

if (resp==null) return;
if (resp.rtrim()=='') return;

ajax.criar('ajax/ajaxPLANOS.php?acao=novaTABELA&vlr=' + resp, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
lerTABELAS();
}

/************************************/
function E_TABELA(idOBJ)  {

var lstbox = document.getElementById('lstTABS');
var nomeTABELA = lstbox[lstbox.selectedIndex].innerHTML;
var idTABELA = lstbox[lstbox.selectedIndex].value;

resp = prompt('Editar nome da tabela', nomeTABELA);

if (resp==null) return;
if (resp.rtrim()=='') return;

ajax.criar('ajax/ajaxPLANOS.php?acao=editarTABELA&vlr=' + resp+'&idTABELA='+idTABELA, '', 0);
if (ajax.ler().indexOf('ok')==-1) 
  alert('Erro ao gravar!!! \n\n' + ajax.ler());
  
lerTABELAS();

}



//]]></script>
</body>
</html>
