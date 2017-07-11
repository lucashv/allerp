<?php 
ob_start();
require("doctype.php"); 
session_start();

?>

<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="js/funcoes.js" xml:space="preserve"></script>
<script type="text/javascript" src="js/edicaoDados.js" xml:space="preserve"></script>
</head>

<body style="HEIGHT: 100%; width:100%;" onload="Avisa('');Muda_CSS();lerINFO();ColocaFocoCmpInicial();">

<style>
.cssDIV_AJAX {
position: absolute; top: 200px; top:50%; left: 50%; 
width: 50px; height: 50px;	margin-top: -40px; margin-left: -10px; display:block; 
z-index:20; 
}
</style>

<div id="divAJAX" class="cssDIV_ESCONDE"  style="text-align:center;" bgcolor=red>
  <table height="50px" bgcolor="#7CA7FF" rules="rows"  bgcolor="white" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="middle"><td><img src="images/database.png" alt="" /></td></tr>
  </table>
</div>


<form id="frmSENHA" name="frmSENHA" action="">

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1"  bgcolor="white"   class="frmJANELA"    
    style="text-align:left;height:20%;width:60%"    >

      <tr height="5%"><td >

        <table width="100%"><tr >      
          <td><span class="lblTitJanela">&nbsp;Comissão (da empresa) sobre mensalidades</span></td>
          <td align="right" id="btnGRAVAR" align="center" onmouseout="this.style.backgroundColor='#fffff0'" 
          onmouseover="this.style.backgroundColor='#efefef'" onclick="return VerCmp('todos');" >
            <span style="cursor: pointer;" class="lblTitJanela" >[ F2= gravar ]</span>            
          </td>      
         <td align="right" style="cursor: pointer;text-align:right;"  
           onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
           class="lblTitJanela" >[ X ]</span></td>
        </tr></table>

      </td></tr>					
    
      <tr valign="top"  ><td style="height:95%;width:100%" >

        <table><tr >
          <td>&nbsp;</td>
    		</tr></table>

        <table>
          <tr>
            <td width="30px">&nbsp;1ª: </td>
            <td><input style="text-align:right" type="text" id="txt1A" tabindex="1" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;2ª: </td>
            <td><input style="text-align:right" type="text" id="txt2A" tabindex="2" value="" maxlength="3"  size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;3ª: </td>
            <td><input style="text-align:right" type="text" id="txt3A" tabindex="3" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;4ª: </td>
            <td><input style="text-align:right" type="text" id="txt4A" tabindex="4" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;5ª: </td>
            <td><input style="text-align:right" type="text" id="txt5A" tabindex="5" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;6ª: </td>
            <td><input style="text-align:right" type="text" id="txt6A" tabindex="6" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;7ª: </td>
            <td><input style="text-align:right" type="text" id="txt7A" tabindex="7" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;8ª: </td>
            <td><input style="text-align:right" type="text" id="txt8A" tabindex="8" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
          <tr>  
            <td>&nbsp;9ª: </td>
            <td><input style="text-align:right" type="text" id="txt9A" tabindex="9" value="" maxlength="3" size="5" onKeyPress="return sistema_formatar(event, this, '000');">&nbsp;&nbsp;%</td>
          </tr>
            
            
            
    		</tr></table>
    		
    		
    		
    		
    		<table><tr >
          <td>&nbsp;</td>
    		</tr></table>

    		
          
      </td></tr>
    
    </table>   
    
    
</td></tr></table>




<script language="javascript" type="text/javascript" xml:space="preserve">
//<![CDATA[
/* qtde de campos text na tela de edição, necessario informar  */
var nQtdeCamposTextForm = 9;

var ajax = new execAjax();

/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;

var lF2=tecla==113;  

if  (lF2) document.getElementById('btnGRAVAR').click();
if (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}
  
eval("teclasNavegacao(e);");  
}

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);



/*******************************************************************************/
function ColocaFocoCmpInicial(cmp)   {
document.getElementById('txt1A').focus();
}	


/*******************************************************************************/
function VerCmp(nomeCMP)      {

if (nomeCMP!='todos')    document.getElementById(nomeCMP).style.backgroundColor="white";


/* concatena */
//cmps= document.getElementById('txtNOME').value+'|'+
//document.getElementById('SELECAO').value;

if (nomeCMP=='todos')         {
	
  /* tudo ok, aciona gravacao do registro, concatena campos em uma variavel */
	showAJAX(1);
	
  strGRAVAR='';
  for (var rr=1; rr<=9; rr++)  {
    strGRAVAR += (rr==1 ? '' : ';');
    strGRAVAR += document.getElementById( ('txt'+rr+'A') ).value;  
  }

	ajax.criar('ajax/ajax.php?acao=gravarComissaoEmpresa&vlr=' + strGRAVAR, gravou);
}

}

/*******************************************************************************/
function gravou() {

showAJAX(0);

if ( ajax.terminouLER() ) {
  resp = ajax.ler();
  
  if (resp.indexOf('OK')!=-1) {   
    alert('Comissão gravada.');
    window.top.frames['framePRINCIPAL'].location.href='inicial.php';
  }      
 	else alert('Erro ao gravar: \n\n ' + resp);  	
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

/*******************************************************************************/
function lerINFO()  {

ajax.criar('ajax/ajax.php?acao=lerComissaoEmpresa', '' , 0);

for (var rr=1; rr<=9; rr++)  {
  document.getElementById( ('txt'+rr+'A') ).value = ajax.ler().split(';')[rr-1];  
}


}


//]]>
</script>
</body>
</html>
