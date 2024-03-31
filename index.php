<?php
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");
    session_start();

    // identificando dispositivo
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $symbian =  strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");

    $eMovel="N";

    if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian == true) {
        $eMovel="S";
    }


    // incluindo bibliotecas de apoio
    include_once "banco.php";
    include_once "util.php";

    $cdusua = '00000000191';
    $deusua = 'Desconhecido ERR';
    $defoto = 'img/semfoto.jpg';
    $cdtipo = 'Demonstração';
    $demail = 'd@d.com';

    if (isset($_COOKIE['cdusua'])) {
        $cdusua = $_COOKIE['cdusua'];
    } Else {
        header('Location: index.html');
    }

    if (isset($_COOKIE['deusua'])) {
        $deusua = $_COOKIE['deusua'];
    } Else {
        header('Location: index.html');
    }

    if (isset($_COOKIE['defoto'])) {
        $defoto = $_COOKIE['defoto'];
    }

    if (isset($_COOKIE['cdtipo'])) {
        $cdtipo = $_COOKIE['cdtipo'];
    }

    if (isset($_COOKIE['demail'])) {
        $demail = $_COOKIE['demail'];
    }

    $detipo=TrazTipo($cdtipo);
    $cdtipo=substr($detipo, 0, 1);

    $deusua1=$deusua;
    $deusua = substr($deusua, 0,25);

    $aPara = ConsultarDados('','','','select * from parametros');
    if (count($aPara) > 0 ){
        $cdclie =   $aPara[0]['cdclie'];
        $declie =   $aPara[0]['declie'];
    } Else {
        echo 'Dados do condomínio não foram cadastrados. Contacte o suporte técnico!';
        die();
    }

    $aMens = ConsultarDados('','','',"select * from mensagens where (cdusud = '{$cdusua}' or cdusud = '99999999999') and fllido = 'N'");
    $qtmens = count($aMens); 

    $aUsua = ConsultarDados('','','',"select * from usuarios where flativ = 'P'");
    $qtusup = count($aUsua);

    $aAvis = ConsultarDados('','','',"select * from avisos where flativ = 'S' order by dtcada DESC");
    if (count($aAvis) < 1){

        $aNomes=array();
        $aNomes[]= "deavis";
        $aNomes[]= "fllido";
        $aNomes[]= "flmaie";
        $aNomes[]= "cdusuo";
        $aNomes[]= "cdusud";
        $aNomes[]= "dtcada";
        $aNomes[]= "flativ";

        $aDados=array();
        $aDados[]= "Os moradores desejam boas vindas";
        $aDados[]= "N";
        $aDados[]= "N";
        $aDados[]= "00000000191";
        $aDados[]= "00000000191";
        $aDados[]= date("Y-m-d");
        $aDados[]= "S";

        IncluirDados("avisos", $aDados, $aNomes);

    }

    $sql = "select distinct a.cdusua from receber a, usuarios b where a.cdusua = b.cdusua and a.vlpago <= 0 and (a.dtrece > CURRENT_DATE) = 0 and LEFT(b.cdtipo,1)='M'";
    $aTrab = ConsultarDados('','','', $sql);
    $qtinad = count($aTrab);

    $sql = "select distinct a.cdusua from receber a, usuarios b where a.cdusua = b.cdusua and a.vlpago >= 0 and LEFT(b.cdtipo,1)='M' and a.cdusua NOT IN (select distinct a.cdusua from receber a, usuarios b where a.cdusua = b.cdusua and a.vlpago <= 0 and (a.dtrece > CURRENT_DATE) = 0 and LEFT(b.cdtipo,1)='M')";
    $aTrab = ConsultarDados('','','', $sql);
    $qtadim = count($aTrab);

    $sql = "select * from reservas where flativ = 'P'";
    $aTrab = ConsultarDados('','','', $sql);
    $qtresp = count($aTrab);

    $aAces = ConsultarDados('','','',"select * from acessos where cdusua = '{$cdusua}' and flativ = 'S' ");
    if (count($aAces) < 1){
        $i=1;
        while ( $i <= 18) {

            $aNomes=array();
            $aNomes[]= "cdusua";
            $aNomes[]= "cdmenu";
            $aNomes[]= "flaces";
            $aNomes[]= "dtcada";
            $aNomes[]= "flativ";

            $aDados=array();
            $aDados[]= $cdusua;
            $aDados[]= str_pad($i, 2, "0", STR_PAD_LEFT);;
            $aDados[]= 'S';
            $aDados[]= date("Y-m-d");
            $aDados[]= 'S';


            if ($cdtipo == 'A'){
                IncluirDados("acessos", $aDados, $aNomes); 
            } Else {
                if ( $i !== 1 and $i !== 16 and $i !== 17 and $i !== 18 ){
                    IncluirDados("acessos", $aDados, $aNomes); 
                }
            }
            
            $i++;
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gerenciador de Condomínio | Principal </title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

</head>

<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element"> <span>
                                <img alt="foto" width="80" height="80" class="img-circle" src="<?php echo $defoto; ?>" />
                                 </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $deusua; ?></strong>
                                 </span> <span class="text-muted text-xs block"><?php echo $detipo; ?><b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <li><a href="meusdados.php">Alterar Meus Dados</a></li>
                                <li><a href="minhasenha.php">Alterar Minha Senha</a></li>
                                <li class="divider"></li>
                                <li><a href="logout.php">Sair</a></li>
                            </ul>
                        </div>
                    </li>

                    <?php if (Acesso("01", $cdusua) == true) {?>
                        <li>
                            <a href="index.php"><i class="fa fa-edit"></i><span class="nav-label">Cadastros</span><span class="caret"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="usuarios.php">Usuários</a></li>
                                <li><a href="areas.php">Áreas Comuns</a></li>
                                <li><a href="termo.php">Termo para Reservas</a></li>
                                <li><a href="fornecedores.php">Fornecedores</a></li>
                                <li><a href="aprovaru.php">Aprovar Usuários</a></li>
                                <li><a href="aprovarr.php">Aprovar Reservas</a></li>
                            </ul>
                        </li>
                    <?php }?>

                    <?php if ( $cdtipo == 'M') {?>
                        <li>
                            <a href="receber.php"><i class="fa fa-money"></i><span class="nav-label">Meus Pagamentos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("02", $cdusua) == true) {?>
                        <li>
                            <a href="prestacao.php"><i class="fa fa-eye"></i><span class="nav-label">Prestação de Contas</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("03", $cdusua) == true) {?>
                        <li>
                            <a href="reservas.php"><i class="fa fa-calendar"></i><span class="nav-label">Reservas</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("04", $cdusua) == true) {?>
                        <li>
                            <a href="avisos.php"><i class="fa fa-twitch"></i><span class="nav-label">Avisos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("05", $cdusua) == true) {?>
                        <li>
                            <a href="mensagens.php"><i class="fa fa-envelope"></i><span class="nav-label">Mensagens</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("06", $cdusua) == true) {?>
                        <li>
                            <a href="administracao.php"><i class="fa fa-building"></i><span class="nav-label">Síndico e Equipe</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("07", $cdusua) == true) {?>
                        <li>
                            <a href="classificados.php"><i class="fa fa-newspaper-o"></i><span class="nav-label">Anúncios Classificados</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("08", $cdusua) == true) {?>
                        <li>
                            <a href="livros.php"><i class="fa fa-book"></i><span class="nav-label">Livro de Ocorrências</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("09", $cdusua) == true) {?>
                        <li>
                            <a href="normas.php"><i class="fa fa-file-word-o"></i><span class="nav-label">Normas e Regulamentos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("10", $cdusua) == true) {?>
                        <li>
                            <a href="atas.php"><i class="fa fa-pencil-square-o"></i><span class="nav-label">Atas das Reuniões</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("11", $cdusua) == true) {?>
                        <li>
                            <a href="quitacao.php"><i class="fa fa-check"></i><span class="nav-label">Termo Quitação Anual</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("12", $cdusua) == true) {?>
                        <li>
                            <a href="eventos.php"><i class="fa fa-smile-o"></i><span class="nav-label">Eventos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("13", $cdusua) == true) {?>
                        <li>
                            <a href="veiculos.php"><i class="fa fa-truck"></i><span class="nav-label">Veículos Autorizados</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("14", $cdusua) == true) {?>
                        <li>
                            <a href="boletos.php"><i class="fa fa-barcode"></i><span class="nav-label">Boletos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("15", $cdusua) == true) {?>
                        <li>
                            <a href="ramais.php"><i class="fa fa-phone"></i><span class="nav-label">Ramais dos Condôminos</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("16", $cdusua) == true) {?>
                        <li>
                           <a href="index.php"><i class="fa fa-money"></i> <span class="nav-label">Financeiro</span> <span class="caret"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li><a href="pagar.php">Contas a Pagar</a></li>
                                <li><a href="receber.php">Contas a Receber</a></li>
                                <li><a href="fluxo.php">Fluxo de Caixa</a></li>
                                <li><a href="adimplentesr.php">Adimplentes</a></li>
                                <li><a href="inadimplentesr.php">Inadimplentes</a></li>
                            </ul>
                        </li>
                     <?php }?>

                    <?php if (Acesso("17", $cdusua) == true) {?>
                        <li>
                            <a href="parametros.php"><i class="fa fa-gears"></i><span class="nav-label">Parâmetros</span></a>
                        </li>
                    <?php }?>

                    <?php if (Acesso("18", $cdusua) == true) {?>
                        <li>
                            <a href="historico.php"><i class="fa fa-folder-open-o"></i><span class="nav-label">Histórico de Ações</span></a>
                        </li>
                    <?php }?>

                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-left">
                        <br>
                        <li>
                            <span class="m-r-sm text-muted welcome-message fa fa-home"> <?php echo formatar($cdclie,"cnpj")." - ".$declie;?></span>
                        </li>
                    </ul>
                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Bem-vindos ao  <strong>Gerenciador de Condomínio</strong></span>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                                <i class="fa fa-envelope"></i>  <span class="label label-danger"><?php echo $qtmens ;?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-messages">
                                <?php for ($f =0; $f < (count($aMens)); $f++) { ?>
                                    <li>
                                        <div class="dropdown-messages-box">
                                            <div class="media-body">
                                                <small class="pull-left"><?php echo $aMens[$f]["cdmens"];?></small>
                                                <strong><?php echo " - ".$aMens[$f]["demens"];?>.</strong><br>
                                                <?php $demail = "Mensagem também foi enviada por e-mail";?>
                                                <?php if ($aMens[$f]["flmaie"] == "N") {?>
                                                    <?php $demail = "Mensagem não foi enviada por e-mail";?>
                                                <?php }?>
                                                <small class="text-muted"><strong><?php echo $demail;?></strong></small><br>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="divider"></li>
                                    <?php if ($f == 2){?>
                                          <?php $f = count($aMens); ?>
                                    <?php }?>
                                <?php }?>
                                <li class="divider"></li>
                                <li>
                                    <div class="text-center link-block">
                                        <a href="mensagens.php">
                                            <i class="fa fa-envelope"></i> <strong>Veja todas as mensagens enviadas para você</strong>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="logout.php">
                                <i class="fa fa-sign-out"></i> Sair
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <button type="button" class="btn btn-primary btn-lg btn-block"><i
                                                             class="fa fa-home"></i> Menu Principal 
                                </button>
                            </div>
                            <br>
                            <div class="ibox-content">
                                <center>
                                    <button type="button" class="btn btn-danger m-r-sm"><?php echo $aAvis[0]['deavis'];?></button>
                                    <?php if ($cdtipo == 'A') {?>
                                        <br>
                                        <br>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-primary m-r-sm"><?php echo number_format($qtusup,0,",",".");?></button>
                                                        Usuários novos para aprovação/liberação de acesso ao sistema
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-success m-r-sm"><?php echo number_format($qtresp,0,",",".");?></button>
                                                        Reservas novas para aprovação 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-warning m-r-sm"><?php echo number_format($qtinad,0,",",".");?></button>
                                                        Condôminos inadimplentes
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-info m-r-sm"><?php echo number_format($qtadim,0,",",".");?></button>
                                                        Condôminos adimplentes
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php }?>
                                </center>
                                <br>
                                <?php if ($cdtipo !== 'A') {?>
                                    <div class="m-b-sm">
                                        <center> 
                                            <img alt="image" class="img-square img-responsive" src="img/logo.jpg"
                                                                                 style="width: 282px">
                                        </center>
                                    </div>
                                <?php }?>
                                <strong>Suporte</strong><br>
                                <small><?php echo $aPara[0]['demail']; ?></small><br>
                                <small><?php echo $aPara[0]['nrtele']; ?></small><br>
                                <small><?php echo $aPara[0]['nrcelu']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script src="js/plugins/jeditable/jquery.jeditable.js"></script>
    <script src="js/plugins/dataTables/datatables.min.js"></script>

    <!-- Peity -->
    <script src="js/plugins/peity/jquery.peity.min.js"></script>
    <script src="js/demo/peity-demo.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Jvectormap -->
    <script src="js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- EayPIE -->
    <script src="js/plugins/easypiechart/jquery.easypiechart.js"></script>

    <!-- Sparkline -->
    <script src="js/plugins/sparkline/jquery.sparkline.min.js"></script>

    <!-- Sparkline demo data  -->
    <script src="js/demo/sparkline-demo.js"></script>

    <script>
            $("body").addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });

            if (localStorageSupport){
                localStorage.setItem("fixedsidebar",'on');
            }
    </script>

</body>
</html>
