<?php 
ob_start();
require("doctype.php"); 
session_start();
?>


<head>
<link href="<?php echo $_SESSION['arqCSS']; ?>" type="text/css" rel="stylesheet" />
<script language="JavaScript" src="js/funcoes.js" type="text/javascript" xml:space="preserve"></script>
  
</head>

<body LEFTMARGIN="0" TOPMARGIN="0" style="HEIGHT: 100%; width:100%;" >

<script language="javascript" type="text/javascript" xml:space="preserve">
</script>  

<body style="HEIGHT: 100%; width:100%;" onload="document.getElementById('txtFOCADO').focus();">

<form id="frmRELS" name="frmCOMISSOES" autocomplete="off" action="" >

<script language="JavaScript">
/*******************************************************************************/
function teclado(e)         {

if (window.event) tecla=window.event.keyCode;
else tecla=e.which;


if (tecla==27) {e.stopPropagation();e.preventDefault();window.top.frames['framePRINCIPAL'].location.href='inicial.php';}

        	
}  

if (window.document.addEventListener) 
 window.document.addEventListener("keydown", teclado, false);
else 
 window.document.attachEvent("onkeydown", teclado);
 
</script>

<table>
<tr align="center" valign="middle">
  <td width="<?php echo $_SESSION['largIFRAME']; ?>" height="<?php echo $_SESSION['altIFRAME']; ?>">

    <table cellspacing="0" cellpadding="0" border="1" width="55%" bgcolor="white" style="text-align:left;">


     <tr><td><table width="100%" cellpadding="0" cellspacing="2"><tr>
       <td width="90%"><span class="lblTitJanela" id="lblTITULO"><font size="+1">&nbsp;Relatórios</font></span></td>
           
       <td style="cursor: pointer;text-align:right;"  
         onclick="window.top.frames['framePRINCIPAL'].location.href='inicial.php';" 
         class="lblTitJanela" >[ X ]</span>
       </td>      
     </tr></table></td></tr>

     <tr><td colspan="2"><table width="100%">


       <tr><td id="btn1" width="100%"  onclick="window.top.Ir(12);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Vendas</span>

<input id="txtFOCADO" type="text" value="" 
  style="color: white;background-color: white; border: 0px solid white;font-size:0px;" />

       </td></tr>
       

       <tr><td id="btn1" width="100%"  onclick="window.top.Ir(13);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Contratos sem operadora/tipo de contrato definido</span>
       </td></tr>
       
       
       <tr><td id="btn1" width="100%"  onclick="window.top.Ir(15);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Confirmações</span>
       </td></tr>
       
       
       
       
       <tr><td width="100%" onclick="window.top.Ir(18);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;PJ</span>
       </td></tr>
       

       <tr><td width="100%" onclick="window.top.Ir(34);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Contratos sem nenhuma confirmação bancária</span>
       </td></tr>
       

      <tr><td width="100%" id="btn5"   onclick="window.top.Ir(35);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Clientes cadastrados (pós venda)</span>
       </td></tr>
       

       <tr><td width="100%" id="btn6"   onclick="window.top.Ir(36);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Clientes com mensalidade vencendo</span>
       </td></tr>
       
       <tr><td width="100%" id="btn7"   onclick="window.top.Ir(49);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Inadimplência</span>
       </td></tr>
       
<!--       

       
       <tr><td width="100%"    onclick="window.top.Ir(28);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Situação dos contratos (Blitz)</span>
       </td></tr>
       

       <tr><td width="100%" id="btn8"   onclick="window.top.Ir(17);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Usuários por faixa etária/plano</span>
       </td></tr>


       <tr><td width="100%" id="btn10"   onclick="rel(10);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;L= Relatório gerencial</span>
       </td></tr>
       
       <tr><td width="100%" onclick="window.top.Ir(19);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Caixa</span>
       
       <tr><td width="100%" id="btn11"   onclick="rel(10);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;N= Mensalidades pagas à AMEG</span>
       </td></tr>
       

       <tr><td width="100%" id="btn16"   onclick="window.top.Ir(21);"  
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Propostas canceladas na entrega</span>



       
       <tr><td width="100%" id="btn15"   onclick="window.top.Ir(34);"   
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Aniversariantes</span>
       </td></tr>

       <tr><td width="100%"  onclick="window.top.Ir(35);"   
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Clientes por bairro</span>
       </td></tr>
       
       <tr><td width="100%"  onclick="window.top.Ir(38);"   
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Vendedores/produtos</span>
       </td></tr>
       
       <tr><td width="100%"  onclick="window.top.Ir(41);"   
          onmouseover="this.style.backgroundColor='#a9b2ca';this.style.cursor='pointer';"  
          onmouseout="this.style.backgroundColor='white';">
          <span class="lblTitJanela">&nbsp;&nbsp;Operadoras anteriores</span>
       </td></tr>
       
!-->       

       

       
    </table></td></tr>

  </td>

</tr>
</table>



</body>

</html>
