<?php 
ob_start();
require("../doctype.php"); 
session_start();
?>

<head>
<link href="../<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="../js/edicaoDados.js" xml:space="preserve"></script>

<title>
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');lerUltimo();Muda_CSS();ColocaFocoCmpInicial();">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -90px; margin-left: -10px; display:block; 
z-index:20; 
}

</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" >
  <table height="50px" bgcolor="#a9b2ca" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="../images/database.png" alt="" /></td></tr>
  </table>
</div>

<form id="frmREL" name="frmREL" action="">

<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>
<input id="SELECAO_2" type="hidden" value="" >
<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:90%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td id=titPROTOCOLO style="width:50%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Protocolo de envio de proposta</td>
          <td style="width:20%;cursor: pointer;" id="btnGERAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7';" 
          onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="return VerCmp('todos');" >
            <span  class="lblTitJanela" >[ F2= GERAR ]</span>
          </td>
          <td style="width:20%;cursor: pointer;" id="btnLIMPAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7';" 
          onmouseover="this.style.backgroundColor='#A9B2CA'" onclick="limpar();" >
            <span  class="lblTitJanela" >[ F4= NOVO ENVIO ]</span>
          </td>
          
          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='../propostas.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>

      </td></tr>					
    
  		<tr><td><table width="100%">  					
        <tr>
          <td valign="top" height="320px" >
            <div>
              <table border="0" style="font-family: Verdana;font-size: 10px;color: black;"  cellspacing="0" cellpadding="3" width="97%" >
              <thead class="headerFIXO">	
              <tr>
              <td align="center" width="50 px">Operadora</td>
              <td align="center" width="50 px">Tipo de contrato</td>
              <td align="center" width="50 px">Proposta</td>
              <td align="center" width="150px">Contratante</td>
              </tr>
              </thead></table>
            </div>
            <div style="overflow:auto;min-height:95%;height:95%;overflow:-moz-scrollbars-vertical;color:blue;" id=divREGS>
              <table id="tabREGs" width="99%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;">
              </table>                  
            </div>
          </td>
        </tr>
  		</table></td></tr>
        
  		<tr><td><table>  					
  			<tr>
          <td>&nbsp;Sequência:</td>
          <td><input type="text" id="txtSEQUENCIA" tabindex="1"  value=""  maxlength="20" size="20" ></td>

          <td>&nbsp;Operadora:</td>
    			<td onmouseover="this.style.cursor='pointer';" title="Escolher operadora (ou pressione F8)" 
              onclick="AuxilioF7('txtOPERADORA')";><image src="../images/setaBAIXO.gif"></td>
          <td><input type="text" id="txtOPERADORA" tabindex="2"  value="" maxlength="4" size="4"
                onKeyPress="return sistema_formatar(event, this, '0000');"></td>
          <td style="padding-left:5px;WIDTH:120px" align="left"><span class="lblPADRAO_VLR_CMP" id="lblOPERADORA"></span></td>
  			
          <td><table>
            <tr>
        			<td><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
                onmouseout="this.style.backgroundColor='#F6F7F7';" id=tabCHECK ><tr> 
                <td><input type="checkbox" id="chkREENVIO" name="chkREENVIO" value="" tabindex="3"   
                onblur="document.getElementById('tabCHECK').style.backgroundColor='#F6F7F7';"
                onfocus="cfoco='';document.getElementById('tabCHECK').style.backgroundColor='#a9b2ca';"  /></td>
                <td>Reenvio</td>
              </tr>
              </table></td>
            </tr>
       	  </table></td>

          <td>&nbsp;Proposta:</td>
          <td><input type="text" id="txtNUMPROPOSTA" tabindex="4"  value=""  maxlength="20" size="20" ></td>
  			</tr>

  			<tr><td colspan=11><table onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='#F6F7F7';" onclick="checar();"><tr> 
          <td onclick="checar();"><input type="checkbox" id="marcarENVIADAS" name="marcarENVIADAS" value="" /></td>
          <td>Marcar propostas como enviadas</td></td>
          <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>         
        </tr>
          					
    </table></td></tr>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var largPR = 0;
var nQtdeCamposTextForm = 3;

var aCMPS=new Array();
aCMPS[0]='txtOPERADORA;Identifique a operadora';
aCMPS[1]='txtNUMPROPOSTA;Digite o nº da proposta';

var ajax = new execAjax();



/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if  (tecla==113) document.getElementById('btnGERAR').click();
if  (tecla==115) document.getElementById('btnLIMPAR').click();
if  (tecla==27) {
  if (lJanAuxilio)   	fecharAUXILIO();
  else {
    e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='../propostas.php';
  }
}
if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if  ( tecla==119 && ! lJanAuxilio )  AuxilioF7(cfoco);

if  ( (tecla==13 || tecla==40) && cfoco=='txtOPERADORA') 
  {e.stopPropagation();e.preventDefault();document.getElementById('chkREENVIO').focus(); return;}

if  ( (tecla==13 || tecla==40) && document.getElementById('tabCHECK').style.backgroundColor=='rgb(169, 178, 202)') 
  {document.getElementById('txtNUMPROPOSTA').focus(); return;}
if  (tecla==38 && document.getElementById('tabCHECK').style.backgroundColor=='rgb(169, 178, 202)') 
  {document.getElementById('txtOPERADORA').focus(); return;}

if  (tecla==38 && cfoco=='txtNUMPROPOSTA') 
  {document.getElementById('chkREENVIO').focus(); return;}


eval("rel_teclasNavegacao(e);");

}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);




/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="white";

/* concatena */

if (nomeCMP=='todos')         {
	showAJAX(1);	
	ajax.criar('ajaxRELS.php?acao=protocolo&marcarENVIADAS='+document.forms[0].marcarENVIADAS.checked, '', 0);
	showAJAX(0);

  if (ajax.ler().indexOf('nada')!=-1) 
    alert('Nenhum registro encontrado');
  else
    window.open('../pdf/rel_PAISAGEM.php', 'nome', 'width=10,height=10' );
  return;    	
}

else {
	switch (nomeCMP) {
		case 'txtOPERADORA':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
		  /* deixou vlr em branco */
      if (vlr.rtrim().ltrim()=='') {cmpLBL.innerHTML = '';return true;}
      
			cmpLBL.innerHTML = 'lendo...';
			showAJAX(1);
			ajax.criar('../ajax/ajaxAUXILIO.php?oQueAuxiliar=pesquisa_' + nomeCMP + '&vlr=' +vlr, '', 0);
			showAJAX(0);
			
      aRESP = ajax.ler().split(';');

      cIDCMP = aRESP[0]; 	cVLR = aRESP[1];
      cIDCMP=cIDCMP.rtrim().ltrim();

    	if (cVLR.indexOf('ERRO')!=-1) cmpLBL.style.color='red';
    	else cmpLBL.style.color='blue';
    	  
    	cmpLBL.innerHTML = cVLR;
    	
			break;
	}
	return;
}


}


/*******************************************************************************/
function showAJAX(acao) {
dv= document.getElementById('divAJAX');
if (acao==1) {
	dv.setAttribute("className", "cssDIV_AJAX");
	dv.setAttribute("class", "cssDIV_AJAX");	
	
}
else   {
	dv.setAttribute("className", "cssDIV_ESCONDE");
	dv.setAttribute("class", "cssDIV_ESCONDE");	
}  	
}

/********************************************************************************/
function rel_teclasNavegacao(e)  {
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

// nao permite ;(59),'(39),"(34)Œ(179)  */
if ( (tecla==59) || (tecla==39) || (tecla==34) || (tecla==179)   )         void(0);


/* qdo a var CMP != null, significa que ha uma DIV de auxilio (auto complete) para o campo em questao 

nas linhas abaixo, somente 'navega' entre os campos caso a eventual DIV de auxilio do campo atual
nao esteja visivel, se estiver, quem vai manipular teclado é quem controla a DIV */

CMP = document.getElementById( cfoco.replace('txt','div') );

// seta para baixo ou Enter foi press
if ( tecla==40   ||  tecla==13   )         {
  if ( (CMP && CMP.style.display=='none') ||    ( ! CMP) ) {
    if (cfoco=='txtNUMPROPOSTA') {
        var prop=document.getElementById('txtNUMPROPOSTA').value.trim();
        if (prop=='') return;
        
        var tab = document.getElementById('tabREGs');
         
        var maiorID = -1;
        for (var f=0; f<tab.rows.length; f++) {
          var propTAB = parseInt(tab.rows[f].cells[0].innerHTML, 10);

          var idLIN = tab.rows[f].id;
          if (parseInt(idLIN, 10) > maiorID) maiorID = parseInt(idLIN, 10);
        }

        idOP=document.getElementById('txtOPERADORA').value;

      	showAJAX(1);	
      	ajax.criar('ajaxRELS.php?acao=verPROP&vlr='+prop+'&op='+idOP, '', 0);
      	showAJAX(0);

      	if (ajax.ler().indexOf('erro;')>-1) {            
      	 alert( ajax.ler().replace('erro;','') ); return;
        }
      	if (ajax.ler().indexOf('pergunta;')>-1 && ! document.forms[0].chkREENVIO.checked) {            
      	 alert( 'Proposta já enviada para operadora\n\nMarque a opção REENVIO neste caso\n\n\n' );  return;
        }
        
        var sequencia = ajax.ler().replace('pergunta;','');
      	showAJAX(1);	
      	ajax.criar('ajaxRELS.php?acao=novoEnvioProtocolo&vlr='+sequencia+'&reenvio='+
                document.forms[0].chkREENVIO.checked, '', 0);
      	showAJAX(0);

        lerUltimo();

        return;
    }
    if (cfoco=='txtSEQUENCIA') {
        var sequencia=document.getElementById('txtSEQUENCIA').value.trim();
        if (sequencia=='') return;
        
      	showAJAX(1);	
      	ajax.criar('ajaxRELS.php?acao=verSEQUENCIA&vlr='+sequencia, '', 0);
      	showAJAX(0);

      	if (ajax.ler().indexOf('erro;')>-1) {            
      	 alert( ajax.ler().replace('erro;','') ); return;
        }
      	if (ajax.ler().indexOf('pergunta;')>-1) {            
      	  if (! confirm( 'Proposta já enviada para operadora\n\nEnviar novamente?\n\n\n' ) ) return;
        }

        var sequencia = ajax.ler().replace('pergunta;','');
      	showAJAX(1);	
      	ajax.criar('ajaxRELS.php?acao=novoEnvioProtocolo&vlr='+sequencia+'&reenvio='+
                document.forms[0].chkREENVIO.checked, '', 0);
      	showAJAX(0);

        lerUltimo();

        return; 
 
    }
    else {
      if (foco < nQtdeCamposTextForm)  {
        var cProx = document.getElementById( aOrdemCmps[foco+1] );
        cProx.focus();
      }
    }         
  }   
}        

// seta para cima foi press
if ( tecla==38  )           {

  if (  (CMP && ! Element.visible(CMP))  ||  ! CMP )   {
    if (foco != 1)  {
      var cProx = document.getElementById(aOrdemCmps[foco-1]);
      cProx.focus();
    }
  }     
}
}
/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();
else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else 
	document.getElementById('txtOPERADORA').focus();


}

/*******************************************************************************/
function removePROP(SEQ)    {

SEQ=SEQ.replace('r_', '');

showAJAX(1);
ajax.criar('ajaxRELS.php?acao=removeENVIO&vlr='+SEQ, '', 0);
showAJAX(0);

lerUltimo();
document.getElementById('txtNUMPROPOSTA').focus();
}

/********************************************************************************/
function checar()  {

rdBUTTON = document.forms['frmREL'].marcarENVIADAS;
rdBUTTON.checked = rdBUTTON.checked ? false : true;

document.getElementById('txtNUMPROPOSTA').focus();
}
/********************************************************************************/
function lerUltimo()  {

document.getElementById('divREGS').innerHTML=
    '<table id="tabREGs" width="99%" cellpadding="3"  cellspacing="0" style="font-family:verdana;font-size:10px;color:black;"></table>';

showAJAX(1);	
ajax.criar('ajaxRELS.php?acao=lerUltimoProtocoloEnvio', '', 0);
showAJAX(0);
if (ajax.ler().indexOf('nada')==-1) {
  prop=ajax.ler().split('|');
  
  var tab = document.getElementById('tabREGs');
	for (i=0;i<prop.length;i++)   {
		info = prop[i].split(';');
		
    var lin = tab.insertRow(-1);
    lin.id = info[4];

    var tt='r_'+info[4];


    var col = lin.insertCell(-1); col.innerHTML = info[0];  col.width = '20%'; col.align='left';
    var col = lin.insertCell(-1); col.innerHTML = info[1];  col.width = '20%'; col.align='left';
    var col = lin.insertCell(-1); col.innerHTML = info[2];  col.width = '20%'; col.align='left';
    col = lin.insertCell(-1); col.innerHTML = info[3] ;  col.width = '40%'; col.align='left';
           
    col = lin.insertCell(-1); col.innerHTML = '<font color="red" style="font-size:14px;font-weight:bold;">X</font>'; col.align='center';
    col.width = '10px'; col.id=tt; col.onclick = function() { removePROP(this.id);}

    var corFORM = parent.window.document.getElementById('corFormJanela').value;
    lin.onmouseout = function() {this.style.backgroundColor = corFORM;}
    lin.onmouseover = function() {
      this.style.backgroundColor='#A9B2CA'; this.style.cursor='pointer';}
    lin.width='100%';
  }
  document.getElementById('divREGS').scrollTop = 1000;
  document.getElementById('txtNUMPROPOSTA').value='';
  document.getElementById('txtSEQUENCIA').value='';
  document.getElementById('txtNUMPROPOSTA').focus();
/*  document.forms[0].chkREENVIO.checked=false;*/ 
}
}

/********************************************************************************/
function limpar()  {

if (!confirm('Limpar protocolo atual?')) return;
var tab = document.getElementById('tabREGs');
for (var x=0; x<tab.rows.length; x++) {
 	showAJAX(1);	
 	ajax.criar('ajaxRELS.php?acao=novoProtocoloEnvio', '', 0);
 	showAJAX(0);
}

lerUltimo();
document.getElementById('txtOPERADORA').value=''; document.getElementById('lblOPERADORA').innerHTML='';
document.getElementById('txtOPERADORA').focus();
}


//]]>
</script>
</body>
</html>
