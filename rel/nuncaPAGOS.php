<?php 
ob_start();
require("../doctype.php"); 
session_start();
?>

<head>
<link href="../<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="../js/edicaoDados.js" xml:space="preserve"></script>

<!-- Folha de estilos do calend�rio -->
<link rel="stylesheet" type="text/css" media="all" href="../js/jscalendar-1.0/calendar-win2k-1.css" title="win2k-cold-1" />

<!-- biblioteca principal do calendario -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar.js"></script>

<!-- biblioteca para carregar a linguagem desejada -->
<script type="text/javascript" src="../js/jscalendar-1.0/lang/calendar-en.js"></script>

<!-- biblioteca que declara a fun��o Calendar.setup, que ajuda a gerar um calend�rio em poucas linhas de c�digo -->
<script type="text/javascript" src="../js/jscalendar-1.0/calendar-setup.js"></script> 


<title>
ALLCROSS
</title>
</head>

<body style="HEIGHT: 100%; width:100%;" 
  onload="Avisa('');lerDATAS();Muda_CSS();lerEXCECOES();ColocaFocoCmpInicial();">

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


<div id="divAUXILIO" class="cssDIV_ESCONDE">&nbsp;</div>
<form id="frmREL" name="frmREL" action="">
<input id="SELECAO_2" type="hidden" value="" >

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:70%"    >

      <tr height="5%"><td>

        <table WIDTH="100%"><tr  >      
          <td style="width:60%" style="cursor: move;"><span class="lblTitJanela">&nbsp;Contratos sem nenhuma confirma��o banc�ria</td>
          <td style="width:30%" id="btnGERAR" align="center" onmouseout="this.style.backgroundColor='#F6F7F7;'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gerar ]</span>
          </td>
          <td style="cursor: pointer;text-align:right;"  
            onclick="window.top.frames['framePRINCIPAL'].location.href='../relatorios.php';" 
            class="lblTitJanela" >[ X ]</span></td>      
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" ><table width="100%">

    		<tr><td><table>
  				<td width="170px" align="right">Corretor:</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher representante (ou pressione F8)"  
          onclick="AuxilioF7('txtREL_REPRESENTANTE');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtREL_REPRESENTANTE" tabindex="1"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td><span class="lblPADRAO_VLR_CMP" id="lblREL_REPRESENTANTE"></td>
    		</table></td></tr>

    		<tr><td><table>
  				<td width="170px" align="right">(9999= todos)</td>					
    		</table></td></tr>

    		<tr><td><table>
  				<td width="170px" align="right">Operadora:</td>					
			    <td onmouseover="this.style.cursor='pointer';" title="Escolher operadora (ou pressione F8)"  
          onclick="AuxilioF7('txtOPERADORA');"><image src="../images/setaBAIXO.gif"></td>
  				<td><input type="text" id="txtOPERADORA" tabindex="2"   
            maxlength="4" size="6" onKeyPress="return sistema_formatar(event, this, '0000');"></td>
			    <td><span class="lblPADRAO_VLR_CMP" id="lblOPERADORA"></td>
    		</table></td></tr>

    		<tr><td><table>  					
    			<tr>
    				<td width="170px" align="right">Propostas cadastradas entre:</td>					
    				<td><input type=text id="txtDATAINI" tabindex="3"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAINI" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>
    		
    		<tr><td><table>  					
    			<tr>
    				<td width="170px" >&nbsp;</td>					
    				<td><input type=text id="txtDATAFIN" tabindex="4"  maxlength="8" size="10"
              onKeyPress="return sistema_formatar(event, this, '00/00/00');"  ></td>
          	<td><input type="button" id="btnDATAFIN" title="Escolher data" class="btnSUBMIT" value="..."></td>              
    		</tr>
    		</table></td></tr>


        <tr><td>&nbsp;</td></tr>
 
        <tr><td><table width="100%" bgcolor='#FFCCCC'>
      		<tr><td><table>
  			    <td style="color:red;font-size:16px">ATEN��O</td>
      		</table></td></tr>

      		<tr><td><table>
    				<td>A(s) operadora(s):</td>					
  			    <td id=excecoes style="color:red;">&nbsp;</td>
            <td>&nbsp;&nbsp;possui(em) a seguinte regra:</td>
  			    <td id=excecoesCODIGOS style="display:none"></td>
      		</table></td></tr>
  
  
      		<tr><td><table style="font-weight:bold;">
            <tr>
              <td>&nbsp;&nbsp;&nbsp;A data da 1� mensalidade ser� igual a data da vig�ncia do contrato.</td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;<b><font color=blue>N�o haver�</font></b> 
                repasse para corretor sobre confirma��o da 1� mensalidade, exceto se quantidade vidas </td>
            </tr>
            <tr>
              <td>&nbsp;&nbsp;&nbsp;maior ou igual a 100</td>
            </tr>
       
      		</table></td></tr>


      		<tr><td><table>
    				<td>e por isso contratos desta(s) operadora(s) cuja 1a mensalidade foi a �nica paga, equivaler� a nada ter sido pago</td>
      		</table></td></tr>

  
          <tr><td>&nbsp;</td></tr>
        </table></td></tr>


      </td></tr>
    
    </table>   
    
    
</td></tr></table>


<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edi��o, necessario informar  */

var nQtdeCamposTextForm = 4;
var largPR = 0;

var aCMPS=new Array();
aCMPS[0]='txtREL_REPRESENTANTE;Identifique um representante v�lido ou 9999= todos';
aCMPS[1]='txtOPERADORA;Identifique uma operadora v�lida ou deixe em branco para TODAS';
aCMPS[2]='txtDATAINI;Preencha a data inicial';
aCMPS[3]='txtDATAFIN;Preencha a data final';


Calendar.setup({
inputField:    "txtDATAINI",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAINI"    
});

Calendar.setup({
inputField:    "txtDATAFIN",     
ifFormat  :     "%d/%m/%y",     
button    :    "btnDATAFIN"    
});


var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

var lJanAuxilio= document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';
if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

if  (tecla==113) document.getElementById('btnGERAR').click();
if  (tecla==27 && lJanAuxilio)   	{fecharAUXILIO();return;}

if  (tecla==13 && lJanAuxilio)   	usouAUXILIO();
if  ( tecla==119 )  AuxilioF7(cfoco);  

if  (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='../relatorios.php';}

eval("rel_teclasNavegacao(e);");

}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);



/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {
var lJanAuxilio = document.getElementById('divAUXILIO').getAttribute(propCLASSE)!='cssDIV_ESCONDE';

if (cmp!=null) 
	document.getElementById(cmp).focus();

else if (lJanAuxilio) 
  document.getElementById('txtPR2').focus();	
else 
  document.getElementById('txtREL_REPRESENTANTE').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    {
  document.getElementById(nomeCMP).style.backgroundColor="white";
  
	switch (nomeCMP) {
		case 'txtREL_REPRESENTANTE':
		case 'txtOPERADORA':
		  vlr = document.getElementById(nomeCMP).value;
		  cmpLBL = document.getElementById( nomeCMP.replace('txt', 'lbl') );
		  
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

if (nomeCMP=='todos')         {
	
  for (h=0;h<aCMPS.length;h++)   {
  	cmp = aCMPS[h].split(';');
		cCMP = cmp[0]; 
		cMSG = cmp[1];
		
		cVLR = document.getElementById(cCMP).value.rtrim().ltrim();
    var label = cCMP.replace('txt', 'lbl');				
		erro=0;
		switch (cCMP)   {
  		case 'txtREL_REPRESENTANTE':      
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ||
            document.getElementById(label).innerHTML=='') erro=1;
        break;

  		case 'txtOPERADORA':      
        if ( document.getElementById(label).innerHTML.indexOf('ERRO')!=-1 ) erro=1;
        break;

			case 'txtDATAINI':
			case 'txtDATAFIN':			
        if ( cVLR=='' || ! verifica_data(cCMP) )   erro=1;
        break;

	  }
	  if (erro==1) {alert(cMSG);	document.getElementById(cCMP).focus(); return false;}	
	}	

  var DATAINIMOSTRAR = document.getElementById('txtDATAINI').value.trim();
  var DATAINI = document.getElementById('txtDATAINI').value.trim();
  DATAINI = '20'+DATAINI.substring(6, 10)+DATAINI.substring(3, 5)+DATAINI.substring(0, 2);

  var DATAFINMOSTRAR = document.getElementById('txtDATAFIN').value.trim();
  var DATAFIN = document.getElementById('txtDATAFIN').value.trim();
  DATAFIN = '20'+DATAFIN.substring(6, 10)+DATAFIN.substring(3, 5)+DATAFIN.substring(0, 2);


	showAJAX(1);	
	ajax.criar('ajaxRELS2.php?acao=nuncaPAGOS&repre='+document.getElementById('txtREL_REPRESENTANTE').value+
        '&ope='+document.getElementById('txtOPERADORA').value+
        '&nomeope='+document.getElementById('lblOPERADORA').innerHTML+
        '&excecoes='+document.getElementById('excecoesCODIGOS').innerHTML+
        '&DATAINI=' + DATAINI+'&DATAFIN='+DATAFIN+
        '&dataIniMostrar='+DATAINIMOSTRAR+'&dataFinMostrar='+DATAFINMOSTRAR, '', 0);
	showAJAX(0);

/*
alert(ajax.ler());return;
*/
  if (ajax.ler().indexOf('nada')!=-1) 
    alert('Nenhum registro encontrado');
  else
    window.open('../pdf/rel.php', 'nome', 'width=10,height=10' );	
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

// nao permite ;(59),'(39),"(34)�(179)  */
if ( (tecla==59) || (tecla==39) || (tecla==34) || (tecla==179)   )         void(0);


/* qdo a var CMP != null, significa que ha uma DIV de auxilio (auto complete) para o campo em questao 

nas linhas abaixo, somente 'navega' entre os campos caso a eventual DIV de auxilio do campo atual
nao esteja visivel, se estiver, quem vai manipular teclado � quem controla a DIV */

CMP = document.getElementById( cfoco.replace('txt','div') );


// seta para baixo ou Enter foi press
if ( tecla==40   ||  tecla==13   )         {

//alert(CMP);
//alert(CMP.style.display);
  if ( (CMP && CMP.style.display=='none') ||    ( ! CMP) ) {
    if (foco < nQtdeCamposTextForm)  {                 
      var cProx = document.getElementById( aOrdemCmps[foco+1] );
      cProx.focus();
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
function lerEXCECOES() {

showAJAX(1);
ajax.criar('../ajax/ajaxOPERADORAS.php?acao=excecoesParaMensNuncaPagas', '', 0);
showAJAX(0);

document.getElementById('excecoes').innerHTML = ajax.ler().split(';')[0];
document.getElementById('excecoesCODIGOS').innerHTML = ajax.ler().split(';')[1];
}

/********************************************************************************/
function lerDATAS()     {
ajax.criar('ajaxRELS.php?acao=lerDATAS', '', 0);  

var resp = ajax.ler().replace(/-/g, '/');
document.getElementById('txtDATAINI').value = resp.split(';')[2];
document.getElementById('txtDATAFIN').value = resp.split(';')[3];
}




//]]>
</script>
</body>
</html>
