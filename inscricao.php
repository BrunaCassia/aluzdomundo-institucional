<?php
function sendTelegram($message)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://integram.org/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(["text" => $message]),
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);


}


$con = pg_connect();

$sql = "SELECT COUNT(*) as count FROM inscricoes";
$res = pg_query($con, $sql);
$res = pg_fetch_array($res);


if (count($_POST) > 0 && $_GET['ajax'] == 'signup') {


    header("Content-Type: application/json");

    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $estadoCivil = $_POST['estadoCivil'];
    $membro = $_POST['membro'];

    if (trim($estadoCivil) == "") {
        $error = "Informe o estado civil";
    }
    if (trim($telefone) == "") {
        $error = "Informe o telefone";
    }
    if (trim($nome) == "") {
        $error = "Informe o nome";
    }


    if ($error != "") {
        echo json_encode(["exception" => $error]);
        exit;
    }

    $data = new DateTime("now", new DateTimeZone("America/Sao_Paulo"));
    $data = $data->format("Y-m-d H:i:s");

    $sql = 'INSERT INTO inscricoes (nome, telefone, estado_civil, membro,confirmada, criada_em) values($1, $2, $3, $4, false, $5)';
    $res = pg_query_params($sql, [$nome, $telefone, $estadoCivil, $membro, $data]);
    if (!pg_last_error($con)) {


        $message = "Cadastro efetuado com sucesso";
        echo json_encode(["message" => $message]);

        @sendTelegram($nome . " - " . $telefone);
    } else {
        $error = "Ocorreu um erro ao efetuar seu cadastro, por favor tente novamente";
        echo json_encode(["error" => $error]);
    }


    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1">

    <title>Igreja A Luz do Mundo</title>
    <link rel="icon" href="img/MarcaRefatorada.png" type="image/png">
    <link rel="shortcut icon" href="img/MarcaRefatorada.png" type="img/x-icon">

    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,800italic,700italic,600italic,400italic,300italic,800,700,600' rel='stylesheet' type='text/css'>

    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/font-awesome.css" rel="stylesheet" type="text/css">
    <link href="css/responsive.css" rel="stylesheet" type="text/css">
    <link href="css/animate.css" rel="stylesheet" type="text/css">

    <!--[if IE]>
    <style type="text/css">.pie {
        behavior: url(PIE.htc);
    }</style><![endif]-->

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/maskinput/jquery.maskedinput.js"></script>


    <!-- =======================================================
        Theme Name: Knight
        Theme URL: https://bootstrapmade.com/knight-free-bootstrap-theme/
        Author: BootstrapMade
        Author URL: https://bootstrapmade.com
    ======================================================= -->

</head>
<body>


<nav class="main-nav-outer" id="test"><!--main-nav-start-->
    <div class="container">
        <ul class="main-nav">
            <li><a href="index.php"><i class="fa fa-arrow-circle-o-left"></i> Voltar para site</a></li>
        </ul>
        <a class="res-nav_click" href="#"><i class="fa-bars"></i></a>
    </div>
</nav><!--main-nav-end-->


<section class="business-talking"><!--business-talking-start-->
    <div class="container">
        <h2>Inscrição</h2>
    </div>
</section><!--business-talking-end-->
<div class="container">
    <section class="main-section contact" id="contact">
        <div class="row">
            <div class="col-lg-6 col-sm-7 wow fadeInLeft">
                <img src="img/seminario.png" class="img-responsive img-rounded" alt="">
            </div>
            <div class="col-lg-6 col-sm-5 wow fadeInUp delay-05s">
                <div class="form">
                    <h4>Informações do evento</h4>
                    <p><b>Data:</b> Sábado - 06/10/2018 ás 19h</p>
                    <p><b>Local:</b> Av. Benedito Alves Delfino, 308, Igreja A Luz do Mundo Zone Norte</p>
                    <p><b>Investimento:</b> R$ 10,00</p>
                    <p><b>Quantidade de vagas restantes:</b> <?= 100 - $res['count'] ?> vagas</p>
                    <br>
                    <p>
                        <b>Importante:</b> Para maior comodidade de todos, não será permitido a entrada de
                        crianças, somente pessoas com faixa etária maior que 18 anos poderão participar
                    </p>
                    <br>
                    <p>
                        <b>Pagamento da Inscrição:</b> O investimento de <b>R$ 10,00</b> deve ser acertado com a irmã <b>Bruna de Cássia</b>
                    </p>
                    <br>
                    <p><b>A inscrição é individual, e só será confirmada após o pagamento</b></p>
                    <hr>
                    <br>
                    <div id="sendmessage"></div>
                    <div id="errormessage"></div>
                    <?php
                    if ($res['count'] < 100) {
                        ?>
                        <form id="form-signup" action="" method="post" role="form" class="inscricao">
                            <div class="form-group">
                                <label class="form-check-label" for="membro">
                                    Nome Completo
                                </label>
                                <input type="text" name="nome" class="form-control input-text" id="nome" placeholder="Nome Completo"/>
                                <div class="validation"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label" for="telefone">
                                    Telefone de Contato
                                </label>
                                <input type="text" name="telefone" class="form-control input-text" id="telefone" placeholder="(00) 0000-0000 "/>
                                <div class="validation"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-check-label" for="membro">
                                    Estado Civil
                                </label>
                                <select type="text" name="estado_civil" class="form-control input-text" id="estado_civil" placeholder="Estado Civil">
                                    <option value=""></option>
                                    <option value="CASADO">Casado</option>
                                    <option value="SOLTEIRO">Solteiro</option>
                                    <option value="OUTROS">Outros</option>
                                </select>
                                <div class="validation"></div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" name="membro" id="membro">
                                <label class="form-check-label" for="membro">
                                    É membro da Igreja A Luz do Mundo?
                                </label>
                            </div>
                            <hr>
                            <div class="text-center">
                                <button id="btn-enviar" type="button" class="input-btn">Inscrever-se</button>
                            </div>
                        </form>
                        <?php
                    } else {
                        ?>
                        <div class="alert alert-success">As vagas para esse evento estão esgotadas</div>
                        <?php
                    }

                    ?>

                </div>
            </div>
        </div>
    </section>
</div>
<footer class="footer">
    <div class="container">
        <div class="footer-logo"><a href="#"><img src="img/logo.png" alt=""></a></div>
        <span class="copyright">&copy; Grão Black. All Rights Reserved</span>
        <div class="credits">
            <!-- 
                All the links in the footer should remain intact. 
                You can delete the links only if you purchased the pro version.
                Licensing information: https://bootstrapmade.com/license/
                Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/buy/?theme=Knight
            -->
            <a href="http://graoblack.com/">Grão Black</a>
        </div>
    </div>
</footer>


<script type="text/javascript">
    $(function () {
        $("#telefone").mask("(99) 99999-9999");
        $("#btn-enviar").click(function () {
            var nome = $("#nome").val();
            var telefone = $("#telefone").val();
            var estadoCivil = $("#estado_civil").val();
            var membro = $("#membro").prop("checked");


            $("#sendmessage").fadeOut();
            $("#errormessage").fadeOut();

            $("#btn-enviar").html("<i class='fa fa-spin fa-refresh'></i> Processando...")

            $.ajax("inscricao.php?ajax=signup", {
                method: "POST",
                data: {
                    nome: nome,
                    telefone: telefone,
                    estadoCivil: estadoCivil,
                    membro: membro
                }
            }).done(function (response) {
                $("#btn-enviar").html("Inscrever-se");
                if (response.exception == undefined) {
                    if (response.error == undefined) {
                        if (response.message != undefined) {
                            $("#form-signup").fadeOut(1000, function () {
                                $("#sendmessage").html(response.message);
                                $("#sendmessage").fadeIn(1000);
                            })
                        } else {
                            $("#errormessage").html("Ocorreu algum problema ao efetuar sua inscrição, por favor tente novamente");
                            $("#errormessage").fadeIn(1000);
                        }
                    } else {
                        $("#errormessage").html(response.error);
                        $("#errormessage").fadeIn(1000);
                    }
                } else {
                    $("#errormessage").html(response.exception);
                    $("#errormessage").fadeIn(1000);
                }
                console.log(response);
            });
        });
    })
</script>


</body>
</html>