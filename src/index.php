<?php

DEFINE('API_VERSION','1.53.3');


define('ABSPATH',__DIR__);
define('PRIV_KEY' , "AFyQYuDzh7yAvte6yKVBNwjKZkPyX8z9p8sJz3UNsjJFNNEm2q86KhXKPWs7kunYUxGqXfbjCq8vcvFr9QNS5PhtVfHQYfx8tPbXXNszZnGe6mTUuHzZFqBJ9VR9kmN9");
header('Access-Control-Allow-Origin: * ');
header('Access-Control-Allow-Headers: * ');

ob_start('ob_gzhandler');

$debug = isset($_GET['__debug__']) ? $_GET['__debug__'] : false;

if(!$debug){
    error_reporting(E_ERROR);
    header("Content-type: application/json; charset=utf-8");
}else{
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

require_once("phpmailer/PHPMailerAutoload.php");

// #### Zenvia ####
//Carregando Api Zenvia
require_once("zenvia/zenvia_autoload.php");

//Configurações do Zenvia
define("LOGIN_CONTA_ZENVIA",'login');
define("SENHA_CONTA_ZENVIA",'password');
// ################


// #### FPDF ######
include_once('fpdf/fpdf.php');
// #### FPDF ######

ini_set('smtp_port', '587');
ini_set('default_charset','UTF-8');
require_once('includes/uuid.class.php');
require_once('includes/db.php');
require_once('includes/utils/Date.php');
require_once('includes/utils/ImagesUtils.php');


if(@$_REQUEST['checkServer']=='checkServer'){

    require_once('includes/globals.php');
    $db = new db(DBPATH,DBNAME,DBUSER,DBPASS);
    if($db->conecta()) {
        $retorno['status'] = 'OK';
        $retorno['mensagem'] = 'Servidor acessível';
        $retorno['android'] = $db->getVersaoAndroid();
        $retorno['ios'] = $db->getVersaoIOS();
        $retorno['apiVersion'] = API_VERSION;
        $db->desconecta();
    }else{
        $retorno['status'] = 'NOK';
        $retorno['mensagem'] = 'Banco de dados inacessível';
    }

    echo json_encode(arrayToUTF8($retorno));
    die();
}

//*** validação de API autenticada */
require_once('includes/jwtauth.class.php');

$auth = null;
$userJwt = null;
try {

    if($_SERVER['HTTP_AUTHORIZATION'] != null){

        $auth = explode(" ",$_SERVER['HTTP_AUTHORIZATION']);

        if($auth[0] == 'Bearer'){
            $auth_info = Jwtauth::validadeWebToken($auth[1]);
            $auth = $auth_info['payload'];
            $retorno['jwt_auth'] = true;
        }else{
            throw new Exception("Acesso negado!", 1);
        }
    }else{
        // verifica requisição de OPTIONS
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("HTTP/1.1 200 OK");
            die();
        }
        $retorno['jwt_auth'] = false;
        $retorno['jwt_auth_error'] = "HTTP_AUTHORIZATION";
    }

}catch(Exception $err){

    $retorno['jwt_auth'] = false;
    $retorno['jwt_auth_error'] = $err->getMessage();
    echo json_encode($retorno);
    die();

}


function safe_json_encode($value, $options = 0, $depth = 512) {
    $encoded = json_encode($value, $options, $depth);
    if ($encoded === false && $value && json_last_error() == JSON_ERROR_UTF8) {
        $encoded = json_encode(utf8ize($value), $options, $depth);
    }
    return $encoded;
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}

function arrayToUTF8($data){
    array_walk(
        $data,
        function (&$entry) {
            if(is_array($entry)){
                $entry = arrayToUTF8($entry);
            }else{

                if(mb_detect_encoding($entry) == "UTF-8"){
                    if( preg_match('/([^ A-Za-z0-9.#$-:aàáâãäåcçćĉċčeèéêëiìíîïnñoòóôõöuùúûüyýÿ])/i', $entry ) ){
                        //echo $entry . " == " .utf8_decode($entry)." == ".utf8_encode($entry)."\n";
                        $entry = utf8_decode($entry);
                    }
                }
                else{
                    $entry = utf8_encode($entry);
                }
            }
        }
    );
    return $data;
}

function validaemail($email){
	//$email = test_input($email);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	  return false;
	}
	return true;
}

	global $_POST;
//	requires

    date_default_timezone_set('America/Sao_Paulo');

    require_once('includes/cryptojs-aes.php');

	require_once('includes/globals.php');
    require_once('includes/actions.php');
	require_once('includes/db.php');
	require_once('includes/basics.php');
	require_once('includes/url.php');
	require_once('includes/session.php');
	require_once('includes/cookie.php');
	require_once('includes/views.php');
	require_once('includes/user.php');
    require_once('includes/local.php');
	require_once('includes/eventos.php');
	require_once('includes/SimpleImage.php');

    require_once('includes/geo.php');
    require_once('includes/geoMaps.php');

	require_once('includes/rota.php');
    require_once('includes/formularios.php');
    require_once('includes/MatrizDados.php');
    require_once('includes/capability.php');
    require_once('includes/empresas.php');
    require_once('includes/idioma.php');

    require_once('includes/tarefas.php');
    require_once('includes/modeloTarefa.class.php');
    require_once('includes/tarefaAutomatica.class.php');
    require_once('includes/tarefaGrupo.class.php');
    require_once('includes/imagemTarefa.class.php');
    require_once('includes/imagemFormulario.class.php');
    require_once('includes/registroFolhaPonto.class.php');

    require_once('includes/notificacao.php');

    require_once('includes/grupo.class.php');
    require_once('includes/relatorio.class.php');
    require_once('includes/email.class.php');
    require_once('includes/tarefasLog.class.php');
    require_once('includes/qrCode.class.php');
    require_once('includes/kpi.class.php');
    require_once('includes/form_resposta.class.php');

    require_once('includes/regimeHorario.class.php');


    require_once('includes/geometria.php');
    require_once('includes/AwsController.php');

    /**
     * Task-520 - Separando Funções do SLA em Outro Objeto
     */
    require_once('includes/tarefas-indicadores.php');

    /**
     * Task/533 - Criando Kpi-Medias
     */
    require_once('includes/kpi-medias.class.php');


    // Clientes
    require_once('includes/clientes/jamef/jamef.class.php');
    require_once('includes/clientes/icomon/icomon.class.php');
    require_once('includes/clientes/resolv/resolv.class.php');
    require_once('includes/clientes/whirlpool/whirlpool.class.php');
    require_once('includes/clientes/getnet/Getnet.class.php');
    require_once('includes/clientes/cielo/cielo.class.php');
    require_once('includes/clientes/tegma/tegma.class.php');
    require_once('includes/clientes/omnilink/Omnilink.class.php');
    require_once('includes/clientes/wbs/Wbs.class.php');
    require_once("zenvia/zenvia_autoload.php");
    require_once("config.php");
      /**
     * PR-/ask-658 - Validacao de condiçoes
     */
    require_once('includes/verificacao.util.php');

//	require_once('/includes/geo.php');
    require_once('includes/clientes/cpfl/cpfl.class.php');
    require_once('includes/clientes/cpfl/cpflPDF.class.php');
    require_once('includes/clientes/cpfl/html2pdf.php');
    require_once('includes/clientes/cpfl/cpflPDFManutencao.class.php');
    require_once('includes/clientes/cpfl/cpflPDFManutencaoCAG.class.php');
    require_once('includes/clientes/cpfl/cpflPDFInspecao.class.php');
    require_once('includes/clientes/cpfl/cpflPDFManutencaoFotovoltaicos.class.php');
    require_once('includes/clientes/cpfl/cpflPDFManutencaoMotores.class.php');
    require_once('includes/clientes/cpfl/cpflPDFAnaliseRisco.class.php');

	$db = new db();
    $db->conecta();

    $userJwt = new user();
    $userJwt = mysqli_fetch_object($userJwt->getOne('*',$auth->userId,'users'));

    $session = new session();


	if($_REQUEST['pullTracker']=='urlTest'){
		$act = $_REQUEST['act'];
	}else{
		$act = $_REQUEST['pullTracker']['act'];
	}

	switch($act){
		default:
				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Dados insuficientes.';
				$retorno['post'] = $_REQUEST;
				echo json_encode(arrayToUTF8($retorno));
            break;

        case 'processarFormulario':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Formulário não encontrado';
                //$retorno['post'] = $_POST;
                if($formulario = $form->salvarFormularioDesktop($_POST,0)){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Formulário salvo com sucesso';

                    if($_POST['idRespostaPai'] != null){
                        if($_POST['idRespostaPai'] != 0){
                            $retorno['idRespostaPai'] = $_POST['idRespostaPai'];
                            /**
                             * bugfix/TR-651
                             * Caso ja estiver idResposta pai, ele apenas associa a resposta pai com o filho
                             */
                            $form->associaRespostaPaiAndFilho($_POST['idRespostaPai'], $formulario);
                        }else{
                            $formID = $_POST['idFormPai'];
                            $tarefa = ($_POST['tarefa'] ? $_POST['tarefa'] : 1) ;
                            $retorno['idRespostaPai'] = $form->createRespostaPaiAndAssociaFilho(time()+$formID, $tarefa, $formID, date('Y-m-d H:i:s'), $formulario);
                        }
                    }

                    /**
                     * bugfix/TR-651 Adicionando a resposta no pai
                     *
                     */
                    $idPergunta = $_POST['idPergunta'];
                    $form->marcaRespostaFilhoNoPai($retorno['idRespostaPai'], $idPergunta, $formulario);

                    $retorno['formulario'] = $formulario;
                }

                echo safe_json_encode($retorno);
            break;
        case 'logout':
                $user = new user();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';

                if($u = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token'])){
                    $user->updateUser('','notid',$u['id']);
                    $user->updateUser('','token',$u['id']);
                    $user->update_user_meta($u['id'],'__userStatus','deslogado');
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Usuário saiu com sucesso';
                }

                echo json_encode($retorno);

            break;
        case 'initRastreamento':
                    $user = new user();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Dados insuficientes.';

                    if($u = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token'])){
                        $user->updateUser($_REQUEST['extra']['notId'],'notid',$u['id']);
                        $user->update_user_meta($u['id'],'__userStatus','initRastreamento');
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Usuário iniciou rastreamento';
                    }

                    echo json_encode($retorno);

                break;
            case 'replaceUserIdByNamesOnSpans':
                    $user = new user();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhum nome encontrado.';
                    $ids = '';
                    $first = true;
                    foreach($_REQUEST['ids'] as $i){
                        if($first){
                            $first = false;
                        }else{
                            $ids .=',';
                        }
                        $ids.=$i;
                    }
                    $retorno['nomes'] = [] ;
                    $sql = "SELECT id, nome FROM `users` WHERE id IN (".$ids.")";
                    if($resposta = $user->execute($sql)){
                        $retorno['status'] = 'true';
                        $retorno['mensagem'] = 'Dados encontrados.';
                        while($temp = mysqli_fetch_array($resposta)){
                            $retorno['nomes'][$temp['id']] = $temp['nome'] ;
                        }
                    }

                    echo json_encode($retorno);
                break;
            case 'pausaDeAlmoco':
                        $user = new user();
                        $geo = new geo();
                        $retorno['status'] = 'false';
                        $retorno['mensagem'] = 'Dados insuficientes.';
                        if($u = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token'])){
                            $user->updateUser('','notid',$u['id']);
                            $user->update_user_meta($u['id'],'__userStatus','pausaDeAlmoco');
                           if($geo->updateLocationType($_REQUEST['user'],date("Y-m-d"),'stopRastreamento')){
                            $retorno['status'] = 'ok';
                            $retorno['mensagem'] = 'Usuário em horario de almoco';
                           }
                        }

                        echo json_encode($retorno);

                    break;
                case 'encerrarRastreamento':
                            $user = new user();

                            $retorno['status'] = 'false';
                            $retorno['mensagem'] = 'Dados insuficientes.';

                            if($u = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token'])){
                                $user->updateUser('','notid',$u['id']);
                                $user->update_user_meta($u['id'],'__userStatus','encerrarRastreamento');
                                $retorno['status'] = 'ok';
                                $retorno['mensagem'] = 'Usuário encerrou rastreamento';
                            }

                            echo json_encode($retorno);

                        break;

        case 'getKpiHoje':
                $resolv = new resolv();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';
                if($dados = $resolv->getKpiHoje()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'KPI OK';
                    $retorno['dados'] = $dados;
                }
                echo json_encode($retorno);
            break;
        case 'getKpiSemana':
            $resolv = new resolv();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Dados insuficientes.';
            if($dados = $resolv->getKpiSemana()){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'KPI OK';
                $retorno['dados'] = $dados;
            }
            echo json_encode($retorno);
            break;
        case 'getKpiMes':
                $resolv = new resolv();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';
                if($dados = $resolv->getKpiMes()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'KPI OK';
                    $retorno['dados'] = $dados;
                }
                echo json_encode($retorno);
            break;
        case 'getKpiFormByUser':
                $resolv = new resolv();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';
                if($dados = $resolv->getKpiFormByUser()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'KPI OK';
                    $retorno['dados'] = $dados;
                }
                echo json_encode($retorno);
            break;
        case 'getKpiFormByUserSemana':
                $resolv = new resolv();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';
                if($dados = $resolv->getKpiFormByUserSemana()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'KPI OK';
                    $retorno['dados'] = $dados;
                }
                echo json_encode($retorno);
            break;
        case 'getKpiFormByUserMes':
                $resolv = new resolv();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados insuficientes.';
                if($dados = $resolv->getKpiFormByUserMes()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'KPI OK';
                    $retorno['dados'] = $dados;
                }
                echo json_encode($retorno);
            break;
        case 'kpi':
            $kpi = new Kpi();
            $user = new user();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma Resposta Encontrada Para a KPI';
            $retorno['kpi'] = '';

            if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                if($r = $kpi->montaKpi($_REQUEST['pullTracker']['idEmpresa'],$_REQUEST['pullTracker']['mensal'],$_REQUEST['grupoAtivo'])){
                    $retorno['kpi'] = $r['tabela'];
                    $retorno['formularios'] = $r['forms'];
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Kpi Montada';
                }
            }else{
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Usuario Não autorizado';
            }

            echo json_encode($retorno);
        break;
        case 'getKpiMelhoramentos':
                $kpi = new Kpi();
                $user = new user();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma Resposta Encontrada Para a KPI';
                $retorno['kpi'] = '';

                // if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                    if($retorno['kpi'] = $kpi->montaKpiMelhoramentos($userJwt->empresa)){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Kpi Montada';
                    }
                // }else{
                //     $retorno['status'] = 'false';
                //     $retorno['mensagem'] = 'Usuario Não autorizado';
                // }

                echo json_encode($retorno);
            break;

        case 'kpi-visitas-dias':
            $kpi = new Kpi();
            $user = new user();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum dado encontrado no espaço de tempo informado.';

            // if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                if($r = $kpi->montaKpiPorAncora($_REQUEST['pullTracker']['idEmpresa'],
                                                        '__kpi_visitas_por_dia',
                                                        $_REQUEST['pullTracker']['dataInicio'],
                                                        $_REQUEST['pullTracker']['dataFim'],
                                                        'dia',
                                                        $_REQUEST['filtroAgente'],
                                                        $_REQUEST['grupoAtivo'])){

                    if(count($r['linhas']) > 0){
                        $retorno['linhas'] = $r['linhas'];
                        $retorno['colunas'] = $r['colunas'];
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Dados das visitas encontrados!';
                    }
                }
            // }else{
            //     $retorno['status'] = 'false';
            //     $retorno['mensagem'] = 'Usuario Não autorizado';
            // }

            echo json_encode($retorno);
        break;

        case 'kpi-ocorrencias-mes':
            $kpi = new Kpi();
            $user = new user();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum dado encontrado no espaço de tempo informado.';

            // if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                if($r = $kpi->montaKpiPorAncora($_REQUEST['pullTracker']['idEmpresa'],
                                                        '__kpi_ocorrencias_por_mes',
                                                        $_REQUEST['pullTracker']['dataInicio'],
                                                        $_REQUEST['pullTracker']['dataFim'],
                                                        'mes',
                                                        $_REQUEST['filtroAgente'],
                                                        $_REQUEST['grupoAtivo'])){
                    if(count($r['linhas']) > 0){
                        $retorno['linhas'] = $r['linhas'];
                        $retorno['colunas'] = $r['colunas'];
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Dados das ocorrências encontrados!';
                    }
                }
            // }else{
            //     $retorno['status'] = 'false';
            //     $retorno['mensagem'] = 'Usuario Não autorizado';
            // }

            echo json_encode($retorno);
        break;

        case 'relatorioExtratoVisitasCielo':


            if(!preg_match('/([0-9]{4}[-][0-9]{2}[-][0-9]{2})/', $_REQUEST['dataInicio'], $matches, PREG_OFFSET_CAPTURE) ||
               !preg_match('/([0-9]{4}[-][0-9]{2}[-][0-9]{2})/', $_REQUEST['dataFim'], $matches, PREG_OFFSET_CAPTURE)){
                echo json_encode([
                    "retorno" => false,
                    "mensagem" => "Data de Inicio e Data Fim obrigatórios"
                ]);
                die();
            }

            echo json_encode([
                "retorno" => true,
                "dados" => $cielo->relatorioExtratoVisitas()
            ]);

            break;
            case 'sincronizarTodasAsImagens':
                $respostas          = $_REQUEST['respostas'] ? json_decode($_REQUEST['respostas']) : null;
                $imagensFormulario  = $_REQUEST['imagensFormulario'] ? json_decode($_REQUEST['imagensFormulario']) : null;
                $retorno['imagensFormularioSincronizadas'] = array();
                if($_REQUEST['imagensFormulario']){

                    $imagemFormulario = new ImagemFormulario();
                    if($respostas && count($respostas) > 0 && count($imagensFormulario) > 0){
                        foreach($respostas as $j){



                            // Verifica se a imagem ja esta no banco de dados caso positivo pula para próxima foto
                            if($imagensFormulario->idServer){
                                $idServer = mysqli_fetch_assoc($imagemFormulario->getOne("*",$imagensFormulario->idServer,"imagens_formulario"));
                                if($idServer){

                                    $retorno['imagensFormularioSincronizadas'][] = array(
                                        'id'=> $idServer["id"],
                                        'url'=> $idServer["url"],
                                        'idApp'=>$imagem->id
                                    );
                                    continue;
                                }
                            }

                            //verifica se a imagem é da resposta em questão
                            if($imagensFormulario->idResposta == $j->id){
                                // troca o idapp para id do server
                                $imagensFormulario->idResposta = $j->idResposta;
                                $imagensFormulario->idTarefa = $j->idTarefa;

                                // salva a imagem na pasta do servidor e insere url no banco de dados
                                $idServer = $imagemFormulario->saveBase64Imagem($imagensFormulario);

                                // Adiciona no retorno imagensSincronizadas o id da imagem e o idApp
                                $retorno['imagensFormularioSincronizadas'][] = array(
                                    'id'=> $idServer["id"],
                                    'url'=> $idServer["url"],
                                    'idApp'=>$imagensFormulario->id
                                );

                                // Verifica se a tarefa gerou tarefa automática
                                $tarefas = new tarefas();
                                $tarefaAutomatica = new TarefaAutomatica();
                                $metasRespostas = $tarefas->buscarMetasResposta($imagensFormulario->idResposta);
                                foreach($metasRespostas as $metaResp){
                                    switch ($metaResp['tipo']) {
                                        case 'tarefaAutomatica':
                                            if($idTarefa = $tarefaAutomatica->verificaSeRespostaJaCriouTarefa($imagensFormulario->idResposta, $metaResp['id'])){
                                                $tarefas->montarDetalhesOs($idTarefa,$j->idResposta);
                                            }
                                            break;
                                    }
                                }

                            }

                        }
                    }
                }
                echo safe_json_encode($retorno);

            break;
        case 'sincronizarTodasAsTarefas':
            $rota = new rota();
            $user = new user();
            $formulario = new formulario();
            $modelosTarefasJaEnviados = [];

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma tarefa encontrada';
            $tarefas = json_decode($_REQUEST['tarefas']);

            $respostas          = $_REQUEST['respostas'] ? json_decode($_REQUEST['respostas']) : null;
            $metarespostas      = $_REQUEST['metaRespostas'] ? json_decode($_REQUEST['metaRespostas']) : null;
            $logs               = $_REQUEST['tarefa_log'] ? json_decode($_REQUEST['tarefa_log']) : null;
            $imagens            = $_REQUEST['imagens'] ? json_decode($_REQUEST['imagens']) : null;
            $imagensFormulario  = $_REQUEST['imagensFormulario'] ? json_decode($_REQUEST['imagensFormulario']) : null;


            $status = $_REQUEST['pullTracker']['status'];

            $retorno['tarefasSincronizadas']  = array();

            foreach($tarefas as $tarefa){

                $idUsuario = $status = $_REQUEST['pullTracker']['id'];

                $data = array(
                            'id'			=>	$tarefa->id,
                            'idServer'		=>	$tarefa->idServer,
                            'lastChange'	=>	$tarefa->lastChange,
                            'coment'		=>	$tarefa->coment,
                            'taskStart'		=>	$tarefa->taskStart,
                            'taskEnd'		=>	$tarefa->taskEnd,
                            'travelStart'	=>	$tarefa->travelStart,
                            'travelEnd'		=>	$tarefa->travelEnd,
                            'travelLat'		=>	$tarefa->travelLat,
                            'travelLng'		=>	$tarefa->travelLng,
                            'status'		=>	$tarefa->status,
                            'getSing'       =>	$tarefa->getSign,
                            'getPic'        =>	$tarefa->getPic,
                            'user'		    =>	$tarefa->user,
                            'rota'	        =>	$tarefa->rota,
                            'livre'		    =>	$tarefa->livre,
                            'sign'		    =>	$tarefa->sign,
                            'sinc'          =>  1,
                            'pic'		    =>	$tarefa->pic,
                            'lat'	        =>	$tarefa->lat,
                            'lng'		    =>	$tarefa->lng,
                            'planedStart'	=>	$tarefa->planedStart,
                            'planedEnd'		=>	$tarefa->planedEnd,
                            'nome'		    =>	$tarefa->nome,
                            'endereco'		=>	$tarefa->endereco,
                            'descricao'     =>	$tarefa->descricao,
                            'comentTime'    =>	$tarefa->comentTime,
                            'json'		    =>	$tarefa->json,
                            'log'           =>	$tarefa->log,
                            'forms'         =>	$tarefa->forms,
                            'dataTarefa'    =>	$tarefa->data,
                            'urgente'       =>  $tarefa->sign,
                            'uuid'          =>  $tarefa->uuid

                );



                /* Update no estado do usuario e Criação das notificações para a tarefa */
                //Variável que irá conter os dados inserido em uma Notificação
                $texto = '';




                // call ponto de entrada pre_save_tarefa_[action]
                do_action("pre_save_tarefa_" . $tarefa->status ,array($data['idServer'],$idUsuario));

                /* Update no Status da Tarefa */
                if($tempRt = $rota->updateTarefa($data)){

                    // verifica se o idServer foi menor que zero para casos de tarefas offline
                    if($data['idServer'] < 0){
                        // caso for atualiza os outros array com o novo idServer criado da tarefa
                        foreach($respostas as $resposta){
                            if(abs($resposta->idTarefa) == abs($data['idServer']) ){
                                $resposta->idTarefa = $tempRt['id'];
                            }
                        }

                        foreach($logs as $log){
                            if(abs($log->idTarefa) == abs($data['idServer']) ){
                                $log->idTarefa = $tempRt['id'];
                            }
                        }

                        foreach($imagens as $imagen){
                            if(abs($imagen->idTarefa) == abs($data['idServer']) ){
                                $imagen->idTarefa = $tempRt['id'];
                            }
                        }

                    }

                    $retorno['tarefasSincronizadas'][] = $tempRt;
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefa atualizada com sucesso!';

                    $data['idServer'] = $tempRt['id'];
                    $tarefa->id = $tempRt['id'];

                    // call ponto de entrada save_tarefa_[action]
                    do_action("save_tarefa_" . $tarefa->status ,array($data['idServer'],$idUsuario));
                }

                //echo $tarefa->status;
                switch($tarefa->status){
                    case 'travelStarted':
                        /* Caso a tarefa em questão for uma tarefa de grupo, ações adicionais serão tomadas */
                        $tarefaGrupo = new TarefaGrupo();
                        $tarefaGrupo->verificaTarefaGrupo($data);

                        $user->updateUser($tarefa->status,'extrainfo',$idUsuario);
                        $user->updateUser($tarefa->id,'foco',$idUsuario);
                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $texto = ' Endereço '.$tarefa->endereco;

                            $notificacao = new notificacao();
                            /**
                            * Adicionando "deletada = 0" no Where
                            */
                            $sql = "SELECT T.status FROM `tarefas` T WHERE deletada = 0 AND id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'pendente'){
                                $notificacao->criarNotificacao(3,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        break;
                    case 'travelDone':
                        $user->updateUser($tarefa->status,'extrainfo',$idUsuario);
                        $user->updateUser($tarefa->id,'foco',$idUsuario);
                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        // $tarefaGrupo = new TarefaGrupo();
                        // $tarefaGrupo->verificaTarefaGrupo($data);

                        if($texto == ''){
                            $notificacao = new notificacao();
                            $texto = $notificacao->montaTextoNotificacao(4,$tarefa,$_REQUEST['language']);
                            /**
                            * Adicionando "deletada = 0" no Where
                            */
                            $sql = "SELECT T.status FROM `tarefas` T WHERE deletada = 0 AND id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'travelStarted'){
                                $notificacao->criarNotificacao(4,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        break;
                    case 'iniciada':
                        $user->updateUser($tarefa->status,'extrainfo',$idUsuario);
                        $user->updateUser($tarefa->id,'foco',$idUsuario);

                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $notificacao = new notificacao();
                            $texto = $notificacao->montaTextoNotificacao(5,$tarefa,$_REQUEST['language']);
                            /**
                            * Adicionando "deletada = 0" no Where
                            */
                            $sql = "SELECT T.status FROM `tarefas` T WHERE deletada = 0 AND id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'travelDone'){
                                $notificacao->criarNotificacao(5,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        break;
                    case 'concluida':
                        $user->updateUser('','extrainfo',$idUsuario);
                        $user->updateUser(0,'foco',$idUsuario);

                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $notificacao = new notificacao();
                            $texto = $notificacao->montaTextoNotificacao(6,$tarefa,$_REQUEST['language']);
                            /**
                                * Adicionando "deletada = 0" no Where
                            */
                            $sql = "SELECT T.status FROM `tarefas` T WHERE deletada = 0 AND id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'iniciada'){
                                $notificacao->criarNotificacao(6,$texto,$idUsuario,$data['idServer']);
                            }
                        }

                        break;

                    case 'malsucedida':
                        if($texto == ''){
                            $notificacao = new notificacao();
                            $texto = $notificacao->montaTextoNotificacao(7,$tarefa,$_REQUEST['language']);
                            /**
                                * Adicionando "deletada = 0" no Where
                            */
                            $sql = "SELECT T.status FROM `tarefas` T WHERE deletada = 0 AND id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'iniciada' || $status == 'pendente' || $status == 'travelDone' || $status == 'travelStarted'){
                                $notificacao->criarNotificacao(7,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        break;

                    case 'pendente':

                        break;


                }
            }

            if($_REQUEST['tarefa_log']){
                $retorno['tarefa_log_sincronizada'] = array();

                if(count($logs) > 0){
                    $sqlp1 = "INSERT INTO trackerup.tarefas_log(".
                    "idTarefa".
                    ",tipo".
                    ",valor".
                    ",time".
                    ",hash".
                    ",sinc".
                    ")VALUES";
                    $first = true;
                    $sqlp2 = "";

                    foreach($logs as $log){
                        if(!$rota->existe('hash','trackerup.tarefas_log',$log->id,' and idTarefa = '.$log->idTarefa)){
                            if($first){
                                $first = false;
                            }else{
                                $sqlp2 .= ",";
                            }
                            $sqlp2 .= "(".
                                $log->idTarefa.
                                ",'". $log->tipo."'".
                                ",'". $log->valor."'".
                                ",'". $log->time."'".
                                ",'". $log->id."'".
                                ",'1'".
                            ")";
                            $retorno['tarefa_log_sincronizada'][] = $log->id;
                        }
                    }
                    $rota->execute($sqlp1.$sqlp2);

              }
            }
            // verifica o array de imagens em busca de imagens a ser relacionadas a esta tarefa

            if($_REQUEST['imagens']){
                $retorno['imagensSincronizadas'] = array();
                $imagemTarefa = new ImagemTarefa();

                if(count($imagens) > 0){
                    foreach($imagens as $imagem){

                        // Verifica se a imagem ja esta no banco de dados caso positivo pula para próxima foto
                        if($imagem->idServer){
                            if($imagemTarefa->existe("id","imagens",$imagem->idServer)){
                                continue;
                            }
                        }

                        // Verifica se a imagem esta atrelado à tarefa salva no servidor
                        if($imagemTarefa->existe("id","tarefas",$imagem->idTarefa)){

                            // salva a imagem na pasta do servidor e insere url no banco de dados
                            $idServer = $imagemTarefa->saveBase64Imagem($imagem);

                            // Adiciona no retorno imagensSincronizadas o id da imagem e o idApp
                            $retorno['imagensSincronizadas'][] = array(
                                'id'=> $idServer["id"],
                                'url'=> $idServer["url"],
                                'idApp'=>$imagem->id
                            );

                        }

                    }
                }
            }
            // die();
            $respostasAjustadas = array();
            if($respostas && count($respostas) > 0){
                foreach($respostas as $resposta){
                    $tempMeta = array();
                    $tempMetaById = array();
                    if($metarespostas && count($metarespostas) > 0){
                        foreach($metarespostas as $metaresposta){
                            if($metaresposta->idResposta==$resposta->id){
                                $tempMeta[] = $metaresposta;

                                $tempMetaById['p_'.$metaresposta->idPergunta] = $metaresposta;
                            }
                        }
                    }
                    $resposta->metaRespostas = $tempMeta;

                    $resposta->metaRespostasById = $tempMetaById;
                    $respostasAjustadas[] = $resposta;
                }
            }
            $retorno['respostasSalvas'] = array();
            $retorno['respostasSalvasIDs'] = array();

            $retorno['imagensFormularioSincronizadas'] = array();
            $temAutomatica = false;
            if($respostas && count($respostas) > 0){
                foreach($respostas as $j){

                $resp = null;
                if($j->data){

                    $dataReg = date('Y-m-d H:i:s', ($j->data/1000));
                    $resp = $formulario->createResposta($j->id,$j->idTarefa,$j->idForm,$dataReg,$j->parent);
                    $j->idResposta = $resp;
                    $j->idUsuarioOrigem = $idUsuario;

                    $retorno['respostasSalvas'][]=$j->id;
                    $data['idResposta'] = $resp;
                    $data['idForm'] = $j->idForm;
                    $data['idUsuarioOrigem'] = $idUsuario;
                    /*
                        Teste Se o Formulario conte uma pergunta do tipo Tarefa Automatica, caso ele possua uma tarefa automatica deverá ser gerada para essa resposta.

                    */
                    $respostadados = array();
                    $respostadados['id'] = $j->id;
                    $respostadados['idResposta'] = $resp;
                    $respostadados['idTarefa'] = $j->idTarefa;

                    $retorno['respostasSalvasIDs'][] = $respostadados;

                    $SaberSeFormularioTemTarefaAutomatica = "select * from form_questions f where  f.tipo = 'tarefaAutomatica' and f.formulario=".$data['idForm']." LIMIT 1";

                    if($automaticas = $formulario->execute($SaberSeFormularioTemTarefaAutomatica)){
                        $tarefaAutomaticaClass = new TarefaAutomatica();
                        $temAutomatica = true;

                        $sql = 'SELECT * FROM form_questions P WHERE formulario = '.$data['idForm'];
                        $perguntas = array();
                        $perguntasById = array();
                        if($result = $db->execute($sql)){
                            while($auto = mysqli_fetch_array($result)){
                                $perguntas[] = $auto;
                                $perguntasById[$auto['id']] = $auto;
                            }
                        }
                    }

                    if(count($j->metaRespostas)>0){

                        foreach($j->metaRespostas as $r){
                            if($r->tipo == 'submiter'){
                                continue;
                            }
                            $r->idResposta = $resp;
                            $r->id = $formulario->createMetaResposta($r->idResposta, $r->idPergunta, $r->valor, $r->tipo, $r->slug);

                       }
                    }


                    if($_REQUEST['imagensFormulario']){

                        $imagemFormulario = new ImagemFormulario();

                        if(count($imagensFormulario) > 0){
                            foreach($imagensFormulario as $imagem){

                                // Verifica se a imagem ja esta no banco de dados caso positivo pula para próxima foto
                                if($imagem->idServer){
                                    $idServer = mysqli_fetch_assoc($imagemFormulario->getOne("*",$imagem->idServer,"imagens_formulario"));
                                    if($idServer){

                                        $retorno['imagensFormularioSincronizadas'][] = array(
                                            'id'=> $idServer["id"],
                                            'url'=> $idServer["url"],
                                            'idApp'=>$imagem->id
                                        );
                                        continue;
                                    }
                                }

                                //verifica se a imagem é da resposta em questão
                                if($imagem->idResposta == $j->id){
                                    // troca o idapp para id do server
                                    $imagem->idResposta = $resp;
                                    $imagem->idTarefa = $j->idTarefa;

                                    // salva a imagem na pasta do servidor e insere url no banco de dados
                                    $idServer = $imagemFormulario->saveBase64Imagem($imagem);

                                    // Adiciona no retorno imagensSincronizadas o id da imagem e o idApp
                                    $retorno['imagensFormularioSincronizadas'][] = array(
                                        'id'=> $idServer["id"],
                                        'url'=> $idServer["url"],
                                        'idApp'=>$imagem->id
                                    );
                                }

                            }
                        }
                    }

                    $j->perguntas = $perguntas;

                    do_action("after_save_resposta_tarefa",array($j->idTarefa,$resp));
                }
            }
          }

            if($temAutomatica){
                $tarefaAutomatica = new tarefas();
                if($respostas && count($respostas) > 0){
                    foreach($respostas as $j){
                        // apos salvar todas respostas de formulário verifica tarefa automática
                        $data = [];
                        $data['idResposta'] = $j->idResposta;
                        $data['idForm'] = $j->idForm;
                        $data['idUsuarioOrigem'] = $j->idUsuarioOrigem;
                        $data['tarefa'] = $tarefa->id;

                        foreach($j->perguntas as $pergunta){

                            if($pergunta['tipo'] == 'tarefaAutomatica'){
                                $verificacao = new verificacao();
                                $p = $pergunta;
                                $preencheRequisito = true;
                                if(isset($p['condicional'])&&$p['condicional']!='null'&&$p['condicional']!='false'&&$p['condicional']!='"false"'){

                                    $numeroCamposCondicao = $verificacao->verificaCondicionalResposta($pergunta,$j,true);

                                    $preencheRequisito = $numeroCamposCondicao>0;
                                }

                                $data['idMetaResposta'] = $j->metaRespostasById['p_'.$pergunta['id']]->id;

                                $flagVerificaGeracaoTarefa = true;
                                $sql = 'SELECT * FROM modelo_tarefa WHERE id = '.$pergunta['lista'];
                                $data['modelo'] = mysqli_fetch_array($db->execute($sql));

                                // busca o valor da resposta
                                $valor = false;
                                foreach($j->metaRespostas as $r){
                                    if($r->idPergunta == $pergunta['id']){
                                        $valor = $r->valor;
                                    }
                                }
                                // setar usuario que recebera a tarefa
                                if($valor){
                                    $data['modelo']['usuario'] = $valor;
                                }
                                if($data['modelo']['tipo'] == 'agente'){
                                    $data['modelo']['usuario'] = $idUsuario;
                                }
                                $data['nome'] = $pergunta['texto'];
                                $data['idUsuarioQueGerouTarefa'] = $idUsuario;
                                $resposta = $j;

                                $idTarefa = $resposta->idTarefa;
                                $idForm = $pergunta['formulario'];
                                $idModelo = $pergunta['lista'];
                                $idResposta = $resposta->id;
                                $idAgente = $data['modelo']['usuario'];

                                if($preencheRequisito){
                                    $jaEnviouModelo = false;
                                    for($m=0;$m<count($modelosTarefasJaEnviados);$m++){

                                        if($modelosTarefasJaEnviados[$m]['idModelo']==$idModelo&&
                                        $modelosTarefasJaEnviados[$m]['idTarefa']==$idTarefa&&
                                        $modelosTarefasJaEnviados[$m]['formulario']==$idForm&&
                                        $modelosTarefasJaEnviados[$m]['resposta']==$idResposta&&
                                        $modelosTarefasJaEnviados[$m]['agente'] == $idAgente
                                        ){
                                            $jaEnviouModelo = true;
                                        }
                                    }


                                    if(!$jaEnviouModelo){

                                        $modelosTarefasJaEnviados[] = ['idTarefa' => $idTarefa,'idModelo'=>$idModelo,'formulario'=>$idForm,'resposta'=>$idResposta,'agente'=>$idAgente];
                                        $retorno['tarefasAutomaticasGeradas'][] = $tarefaAutomatica->gerarTarefaAutomatica($data);
                                    }

                                }

                            }
                        }
                    }
                }
            }


            foreach($tarefas as $tarefa){
                $log = 'post_save_tarefa_'.$tarefa->status . '-> ' . json_encode(array($tarefa->id,$idUsuario));
                file_put_contents('/var/log/post_save_tarefa_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
                // call ponto de entrada save_tarefa_[action]
                do_action("post_save_tarefa_" . $tarefa->status ,array($tarefa->id,$idUsuario));
            }


            if($_REQUEST['pullTracker']['data']!=''){
                if($r = $rota->loadRotaForUser($_REQUEST['pullTracker']['id'],$_REQUEST['pullTracker']['data'])){
                    $retorno['rota'] = $r;
                    $retorno['tarefasAgendadas'] = $r['tarefasAgendadas'];
                    $retorno['tarefasDeletadas'] = $r['tarefasDeletadas'];
                }
            }else{
                if($r = $rota->loadRotaForUser($_REQUEST['pullTracker']['id'])){
                    $retorno['rota'] = $r;
                    $retorno['tarefasAgendadas'] = $r['tarefasAgendadas'];
                    $retorno['tarefasDeletadas'] = $r['tarefasDeletadas'];
                }
            }

            echo safe_json_encode($retorno);

            break;

        case 'desvincularConta':
            $user = new user();
            $user->update_user_meta($_REQUEST['user'],'status','');
            $retorno['mensagem'] = 'Conta desvinculada com sucesso';

            echo json_encode($retorno);

        break;


        case 'loginCompleto':
				$user = new user();
                $form = new formulario();
                $empresas = new empresas();
                $capability = new capability();
                $registro = new RegistroFolhaPonto();
                $geoMaps = new geoMaps();
                $local = new local();

                $userId = $_REQUEST['user'] ? $_REQUEST['user'] : $userJwt->id;
                $token = $_REQUEST['token'] ? $_REQUEST['token'] : $userJwt->token;
                try{
                if($u = $user->getUserByToken($userId,$token)){

                    $retorno['status'] = 'ok';
                    $retorno['log'] = [];

                    $lastsinc = isset($_REQUEST['lastsinc']) && strtotime($_REQUEST['lastsinc']) ? $_REQUEST['lastsinc'] : '2000-00-00 00:00:00';
                    //sempre full
                    //$lastsinc = '2000-00-00 00:00:00';
                    $retorno['log'][] = ["passo" => "inicio",'time' => date('Y-m-d H:i:s')];

                    // atualizando dados do aparelho
                    if(isset($_REQUEST['OS'])){
                        $user->updateUser($_REQUEST['OS'],'os',$u['id']);
                    }
                    if(isset($_REQUEST['osVersion'])){
                        $user->updateUser($_REQUEST['osVersion'],'osVersion',$u['id']);
                    }
                    if(isset($_REQUEST['screenSize'])){
                        $user->updateUser($_REQUEST['screenSize'],'screenSize',$u['id']);
                    }
                    if(isset($_REQUEST['model'])){
                        $user->updateUser($_REQUEST['model'],'model',$u['id']);
                    }
                    if(isset($_REQUEST['notid'])){
                        $user->updateUser($_REQUEST['notid'],'notid',$u['id']); // id para notificação push
                    }
                    if(isset($_REQUEST['bateria'])){
                        $user->updateUser($_REQUEST['bateria'],'Bateria',$u['id']);
                    }
                    $u['token'] = $token;

                    // puxando dados atualizados
                    $retorno['user'] = $user->getUserByToken($userId,$token);


                    // pegando formularios
                    //TR-488: formulario

                    $retorno['log'][] = ["passo" => "before getFormsSinc",'time' => date('Y-m-d H:i:s')];
                    if($forms = $form->getFormsSinc($retorno['user']['empresa'],$lastsinc)){
                        $retorno['formularios'] = $forms;
                    }else{
                        $retorno['formularios'] = [];
                    }

                    if(strlen($retorno['user']['groupString'])>0){
                        $gp = $retorno['user']['groupString'];
                    }
                    if($hasLocais = $empresas->get_empresa_meta($retorno['user']['empresa'],'appPlaces')){
                        $retorno['appPlaces'] = $hasLocais;
                        if($hasLocais=='all'){
                            $gp = false;
                        }
                        if($locais = $local->loadLocais($retorno['user']['empresa'],$gp,array(), false, false, $lastsinc)){
                            $retorno['locais'] = $locais;
                        }else{
                            $retorno['locais'] = [];
                        }
                    }else{
                        $retorno['locais'] = [];
                    }


                    /**
                     * Task/413 - Categorias de Formulario
                     *      Pegando Todas AS Categorias e os form_Meta_Categoria
                     */
                    //TR-488: categoria
                    if($categorias = $form->getFormCategoriasAndMetasSinc($retorno['user']['empresa'])){
                        $retorno['categorias'] = $categorias['categorias'];
                        $retorno['formMetaCategoria'] = $categorias['formMetaCategoria'];
                    }else{
                        $retorno['categorias'] = [];
                        $retorno['formMetaCategoria'] = [];
                    }

                    $usr = $retorno['user']['id'];
                    $retorno['meta'] = array();
                    if($meta = $user->getMetaDadosAgente($userId)){
                        $retorno['meta'] = $meta;
                    }

                  //  $retorno['meta']['meta_empresa']['mostrarBotoesExtras'] = $empresas->get_empresa_meta($retorno['user']['empresa'],'mostrarBotoesExtras');
                    $retorno['tema'] = $empresas->get_empresa_meta($retorno['user']['empresa'],'tema');
                    if(!$retorno['tema']){
                        $retorno['tema'] = '';
                    }

                    /**
                     * Task-402 - Bloquear a criação de tarefas no APP
                     *    Após Pegar o tema da empresa Para o Aplicativo,
                     * ela buscará por uma informação de bloqueio de Criação de Tarefas no APP.
                     *    Caso Encontre, adicionará ela ao final do
                     */
                    $bloqueioCriacaoTarefaAppUser = $user->get_user_meta($userId,'bloqueioCriacaoTarefaApp');
                    $bloqueioCriacaoTarefaAppEmpresa = $empresas->get_empresa_meta($retorno['user']['empresa'],'bloqueioCriacaoTarefaApp');
                    if($bloqueioCriacaoTarefaAppEmpresa == 'on'){

                        /**
                         * $bloqueioCriacaoTarefaAppUser == 'off'
                         *     Significa que o usuario está cadastrado com as configurações de bloqueio como 'off', ou seja,
                         *  permitindo que elas sejam criadas no app.
                         *
                         *     Caso a Configuração esteja o oposto disso, ou seja !='off', o usuario está com o bloqueio ligado, ou seja == 'on'
                         *  ou não possui valor algum para esta chave de metadado, que deixa a configuração setada na empresa como sendo a que
                         *  ele utilizará.
                         */
                        if($bloqueioCriacaoTarefaAppUser != 'on' || $bloqueioCriacaoTarefaAppUser != '' || $bloqueioCriacaoTarefaAppUser != null){
                            $retorno['meta']['bloqueioCriacaoTarefaApp'] = 'on';
                        }
                    }else{
                        if($bloqueioCriacaoTarefaAppUser == 'on'){
                            $retorno['meta']['bloqueioCriacaoTarefaApp'] = 'on';
                        }
                    }

                    /**
                     *     Enviando MetaDados Relevantes da Empresa Para o Aplicativo ao Fazer o Login
                     */

                    $mostrarBotoesExtras = $empresas->get_empresa_meta($retorno['user']['empresa'],'mostrarBotoesExtras');
                    if($mostrarBotoesExtras && $mostrarBotoesExtras != null){
                        $retorno['meta']['mostrarBotoesExtras'] = $mostrarBotoesExtras;
                    }

                    /**
                     * Task-450 - URL dinâmica
                     *
                     *      Poder escolher dinâmicamente a apontação de api no aplicativo
                     */
                    if(!isset($retorno['meta']['__apiUrl'])){
                        $trocaDeApiEmpresa = $empresas->get_empresa_meta($retorno['user']['empresa'],'__apiUrl');
                        $trocaDeApiUser = $user->get_user_meta($retorno['user']['id'],'__apiUrl');

                        $trocaDeApi = array('valor'=>$trocaDeApiUser ? $trocaDeApiUser : $trocaDeApiEmpresa,'idRegistro'=>'empresa');


                        // variavel no globals para não trocar API url
                        if( !defined('BYPASS_API_URL') || !BYPASS_API_URL ){
                            $retorno['meta']['__apiUrl'] = $trocaDeApi;
                        }
                    }

                    /**
                     * Divisao TarefasPor Periodo
                     *
                     * Poder escolher dinâmicamente a apontação de api no aplicativo
                     */
                    $divisaoPeriodoDiaEmpresa = $empresas->get_empresa_meta($retorno['user']['empresa'],'divisaoPeriodoDia');
                    if($divisaoPeriodoDiaEmpresa == 'on'){
                        $divisaoPeriodoDiaEmpresa = array('valor'=>'on','idRegistro'=>'empresa');
                        $retorno['meta']['divisaoPeriodoDia'] = $divisaoPeriodoDiaEmpresa;
                    }

                     /**
                     * task/451 - Criador de Tarefas Via QRCode
                     */
                    $qrcode = $empresas->get_empresa_meta($retorno['user']['empresa'],'qrcode_tarefa');
                    if($qrcode == 'on'){
                        $retorno['meta']['qrcode_tarefa'] = 'on';
                    }

                    /**
                     * task/462 - Função de marcação de Ocorrência
                     */
                    $modoOcorrencia = $empresas->get_empresa_meta($retorno['user']['empresa'],'marcacaoOcorrencia');
                    if($modoOcorrencia == 'on'){
                        $retorno['meta']['marcacaoOcorrencia'] = 'on';
                    }

                    /**
                     * GEOMAP
                     */
                    $modoOcorrencia = $empresas->get_empresa_meta($retorno['user']['empresa'],'geoMap');
                    if($modoOcorrencia == 'on'){
                        $retorno['meta']['geoMap'] = 'on';
                    }

                    /**
                     * task/PR-645 - Formulários auto sincronizados pós salvamento
                     */
                    $salvarRespFinalizar = $empresas->get_empresa_meta($retorno['user']['empresa'],'__salvar_resp_finalizar');
                    if($salvarRespFinalizar == 'on'){
                        $retorno['meta']['__salvar_resp_finalizar'] = 'on';
                    }

                    /**
                     * task/473 - Exibição de OS na tela de Tarefa
                     */
                    $exibicaoOSTarefa = $empresas->get_empresa_meta($retorno['user']['empresa'],'exibicaoOSTarefa');
                    if($exibicaoOSTarefa == 'on'){
                        $retorno['meta']['exibicaoOSTarefa'] = 'on';
                    }

                    /**
                     * TR-268 - Exibição botão concluir tarefa form
                     */
                    $concluir_tarefa_form = $empresas->get_empresa_meta($retorno['user']['empresa'],'concluir_tarefa_form');
                    if($concluir_tarefa_form == 'on'){
                        $retorno['meta']['concluir_tarefa_form'] = 'on';
                    }

                    /**
                     * task/473 - Exibição dos Nome Corretos no Lugar da meta_key
                     */
                    $exibicaoNomeOSTarefa = $empresas->get_empresa_meta($retorno['user']['empresa'],'nomenclatura_metaDadosTarefa');
                    if($exibicaoNomeOSTarefa){
                        $retorno['meta']['nomenclatura_metaDadosTarefa'] = $exibicaoNomeOSTarefa;
                    }

                    //Buscando a Linguagem do Painel Definida pela Empresa.
                    $retorno['language'] = $user->get_user_meta($userId,'language');
                    if(!$retorno['language']){
                        $retorno['language'] = $empresas->get_empresa_meta($retorno['user']['empresa'],'language');
                        if(!$retorno['language']){
                            $retorno['language'] = '';
                        }
                    }

                    $retorno['imagemTema'] = $empresas->get_empresa_meta($retorno['user']['empresa'],'imagemTema');
                    if(!$retorno['imagemTema']){
                        $retorno['imagemTema'] = '';
                    }

                    $retorno['actions_menu'] = $empresas->get_empresa_meta($retorno['user']['empresa'],'actions_menu');
                    if(!$retorno['actions_menu']){
                        $retorno['actions_menu'] = array(
                            'tarefas','tarefasConcluidas','mensagens','intervalo','encerrar'
                        );
                    }else{
                        $retorno['actions_menu'] = json_decode($retorno['actions_menu']);
                    }

                    // pegando listas

                    //TR-488: listas
                    if($listas = $form->loadListasWithItensSinc($retorno['user']['empresa'],false,false,$lastsinc)){
                        $retorno['listas'] = $listas ;
                    }else{
                        $retorno['listas'] = [];
                    }

                    // verifica o status de folhaPonto
                    $registroFolhaPonto = new RegistroFolhaPonto();
                    $retorno['folhaPonto'] = $registroFolhaPonto->getStatusUsuario($u['id']);


                    // obtem informações de perfis
                    $retorno['perfis'] = $capability->loadUserPerfis($retorno['user']['id']);


                    // buscar mapas
                    $retorno['mapas'] = $geoMaps->loadImportedMaps($retorno['user']['empresa'],false,true);

                    // retorna o lastsinc sendo o atual
                    // verifica se a versão é 1.15.0
                    // nesta versão temos um problema que as fotos de metados
                    // de formulário não são baixadas, devido a isso não podemos
                    // atualizar o lastsic
                    $versao = '1.15.0';
                    if(isset($retorno['meta']) &&
                        isset($retorno['meta']['meta_user']) &&
                        isset($retorno['meta']['meta_user']['appVersion']) &&
                        isset($retorno['meta']['meta_user']['appVersion']['valor']) ){

                        $versao = $retorno['meta']['meta_user']['appVersion']['valor'];
                    }
                    // caso positivo
                    if($versao == '1.15.0'){
                        // a data é de 2019-01-01
                        $retorno['lastsinc'] = date('2019-01-01 00:00:00');
                    }else{
                        // a data é atual
                        $retorno['lastsinc'] = date('Y-m-d H:i:s');
                    }
                    // FIM da tratativa da versão 1.15.0
                }else{
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Dados incorretos.';
                }
                }catch(Exception $e){
                    echo $e;
                }
				echo json_encode(($retorno));
			break;
        case 'apagaForm':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível excluir o formulário';

                $editarId = false;
                if(is_numeric($_REQUEST['editarId'])){
                    $editarId = $_REQUEST['editarId'];
                    $form->update(0,'empresa','formulario',$editarId);
                    $form->update($userJwt->empresa,'originalId','formulario',$editarId);
                    $form->removeCategoriaForm($editarId);
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Formulário excluído com sucesso!';
                }

                echo json_encode($retorno);

            break;
        case 'doForm':
                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Não foi possível criar formulários';

                $retorno['post'] = json_decode($_REQUEST['json']);
                $retorno['meta'] = $_REQUEST['meta'];

                echo json_encode($form->salvarFormulario($retorno));

                die();

            break;

        case 'enviarLink':

            $retorno['status'] = 'false';
            $retorno['mensagem']  = '';
            $email = $_REQUEST['email'];

			$verificar = $db->execute("SELECT `email` FROM `users` WHERE email = '$email'");

            if($verificar){

				$codigo = base64_encode($email);
				$data_expirar = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                $codigoData = base64_encode($data_expirar);
                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Debugoutput = 'html';
                $mail->Host = "sub4.mail.dreamhost.com";
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->Username = "nao-responda@trackerup.com.br";
                $mail->Password = "macaco12";
                $mail->setFrom('nao-responda@trackerup.com.br', 'TrackerUp');
                $mail->addAddress($email);
                $mail->Subject = utf8_decode('Recuperação de Senha - TrackerUp');
                $mail->msgHTML(utf8_decode('<html>'));

                $link = 'https://painel.trackerup.com.br/recuperar_senha.php?9c04160_'.$codigo.'&'.$codigoData;

                $tempBody = file_get_contents('phpmailer/recuperarSenha.php');
                $tempBody = str_replace('$$$link$$$', $link, $tempBody);

                $mail->Body = $tempBody;


                $inserir = $db->execute("UPDATE `users`SET `chave` = '$codigo', `dataExpirar` = '$data_expirar' WHERE `email` = '$email'");
                if($inserir){
                    $retorno['status'] = 'ok';
                    if (!$mail->send()) {
                      $retorno['mensagem']  = 'Não foi possível enviar o e-mail';

                    } else {

                      $retorno['mensagem']  = 'Enviamos um e-mail com um link para recuperação de senha, para o endereço de e-mail informado!';
                    }
                }
			}


            echo json_encode($retorno);
            break;


        case 'enviarRelatorioEmail':

            $notificacao = new notificacao();
            $retorno['status'] = 'false';
            //$retorno['mensagem']  = '';
            $arq = urldecode($_REQUEST['arquivo']);



            $output_file = tempnam ("includes/uploads/", "relatorio");

            // open the output file for writing
            $ifp = fopen( $output_file, 'wb' );


            // we could add validation here with ensuring count( $data ) > 1
            fwrite( $ifp,   $arq );

            // clean up the file resource
            fclose( $ifp );


            if($notificacao -> alertasEmail($_REQUEST['email'],utf8_decode('<html>'),$_REQUEST['assunto'],$_REQUEST['mensagem'],'phpmailer/layout.php',$output_file)){
                $retorno['status'] = 'ok';
                $retorno['mensagem']  = 'Email enviado com sucesso !';
                unlink($output_file);
            }


            echo json_encode(($retorno));
            break;

        case 'enviarLinhaRelatorioEmail':

            $notificacao = new notificacao();
            $retorno['status'] = 'false';
            if($notificacao -> alertasEmail($_REQUEST['email'],'<p>'.$_REQUEST['mensagem'].'</p><div>'.$_REQUEST['tabela'].'</div>',$_REQUEST['assunto'])){

                $retorno['status'] = 'ok';
                $retorno['mensagem']  = 'Email enviado com sucesso !';

            }

            echo json_encode(($retorno));
            break;

        case 'enviarRelatorioApp':

                $notificacao = new notificacao();
                $retorno['status'] = 'false';
                //print_r ($_REQUEST['tabela']);
                $html = '<p>'.$_REQUEST['mensagem'].'</p><div>';

                foreach ($_REQUEST['tabela'] as $tabela){

                    $verificar = str_replace('<h5','<h5 style="font-weight:bold;font-size: 1.2em; border-bottom: 1px #666 solid;"',$tabela);
                    $html.= $verificar;

                }

                $html .= '</div>';

                if($notificacao -> alertasEmail($_REQUEST['email'],$html,$_REQUEST['assunto'])){

                    $retorno['status'] = 'ok';
                    $retorno['mensagem']  = 'Email enviado com sucesso !';

                }

                echo json_encode(($retorno));
                break;


        case 'recuperarSenha':

            $retorno['status'] = 'false';
            $retorno['mensagem']  = '';
            $data_atual = date('Y-m-d H:i:s');
            $codigo = $_REQUEST['codigo'];

                    $verificar = $db->execute("SELECT * FROM `users` WHERE chave = '$codigo' and dataExpirar > '$data_atual' ");
                    if(mysqli_num_rows($verificar) >= 1){

                            $nova_senha = md5($_REQUEST['novasenha']);
                            $atualizar = $db->execute("UPDATE `users` SET `senha` = '$nova_senha' WHERE `chave` = '$codigo'");
                            if($atualizar){
	                            $retorno['status'] = 'ok';
				                $retorno['mensagem']  = 'A senha foi modificada com sucesso!';
			                 }

                    } else{
                         $retorno['status'] = 'ok';
                         $retorno['mensagem']  = 'Desculpe mas este link já expirou!';
            }

            echo json_encode($retorno);
            break;


        case 'loadForm':
                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Formulário não encontrado';
				$retorno['post'] = $_REQUEST;

                if($formulario = $form->loadForm($_REQUEST['formId'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Formulário  encontrado';
                    $retorno['form'] = $formulario;

                    $retorno['meta'] = $form->get_formulario_meta($_REQUEST['formId']);
                }

                echo safe_json_encode($retorno);

            break;

        case 'loadFormRelAutomaticos':
            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Formulário não encontrado';
            $retorno['post'] = $_REQUEST;

            if($formulario = $form->loadFormRelAutomaticos($_REQUEST['formId'])){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulário  encontrado';
                $retorno['form'] = $formulario;

                $retorno['meta'] = $form->get_formulario_meta($_REQUEST['formId']);
            }

            echo safe_json_encode($retorno);

            break;

        case 'loadFormCompleto':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Formulário não encontrado';

                if($formulario = $form->loadFormCompleto($_POST['formId'],true)){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Formulário  encontrado';
                    $retorno['form'] = $formulario;
                    $retorno['meta'] = $form->get_formulario_meta($_POST['formId']);
                    if($_POST['carregarRespostas']!=''){
                        $retorno['respostas'] = $form->getMetaRespostas($_POST['carregarRespostas']);
                    }
                }
             //   echo '<pre>'.print_r($retorno,true).'</pre>';
                echo safe_json_encode($retorno);

            break;

        case 'montaFormDeFormfilhoApi':
            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Formulário não encontrado';


            $dadosResposta = $form->getRespostabyIdOnAppAndIdTarefa($_POST['pullTracker']['idFilho'], $_POST['pullTracker']['idTarefa']);
            $dadosTarefas = mysqli_fetch_array($form->getOne('rota, travelStart, travelEnd, inicio, termino, dataTarefa',$_POST['pullTracker']['idTarefa'],'tarefas'));
            $dadosResposta['rota'] = $dadosTarefas['rota'];
            $dadosResposta['inicioDeslocamentoTarefa'] = $dadosTarefas['travelStart'];
            $dadosResposta['fimDeslocamentoTarefa'] = $dadosTarefas['travelEnd'];
            $dadosResposta['inicioTarefa'] = $dadosTarefas['inicio'];
            $dadosResposta['terminoTarefa'] = $dadosTarefas['termino'];
            $dadosResposta['data'] = $dadosTarefas['dataTarefa'];

            $dadosResposta['user'] = mysqli_fetch_array($form->getOne('user',$dadosResposta['rota'],'rotas'))[0];
            $dadosResposta['meta'] = $form->getMetaRespostas($dadosResposta['id']);

            if($formulario = $form->makeTreeForms($dadosResposta)){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulário encontrado';
                $retorno['resposta'] = $formulario;
            }
            echo safe_json_encode($retorno);

            break;

        case 'loadRespostas':
            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma resposta encontrada';
            $retorno['post'] = $_REQUEST;

            $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
            $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';

            //echo ini_get('memory_limit');

            if($respostas = $form->loadRespostas($_REQUEST['formulario'], $inicio, $fim, $_REQUEST['pullTracker']['grupoAtivo'], $userJwt->empresa)){
                        $retorno['status'] = 'true';
                        $retorno['mensagem'] = count($respostas) . ' respostas encontrada(s)';
                        $retorno['respostas'] = $respostas;
                        $retorno['colunas'] = $_REQUEST['colunas'];
            }



            if($json = json_encode(($retorno))){
                echo $json;
                die();
            }else{
                $retorno = array();
                $retorno['status'] = 'false';
                $retorno['post'] = $_REQUEST;


                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                            $retorno['mensagem'] =  ' - No errors';
                    break;
                    case JSON_ERROR_DEPTH:
                            $retorno['mensagem'] =  ' - Maximum stack depth exceeded';
                    break;
                    case JSON_ERROR_STATE_MISMATCH:
                            $retorno['mensagem'] =  ' - Underflow or the modes mismatch';
                    break;
                    case JSON_ERROR_CTRL_CHAR:
                            $retorno['mensagem'] =  ' - Unexpected control character found';
                    break;
                    case JSON_ERROR_SYNTAX:
                            $retorno['mensagem'] =  ' - Syntax error, malformed JSON';
                    break;
                    case JSON_ERROR_UTF8:
                            $retorno['mensagem'] =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                    default:
                            $retorno['mensagem'] =  ' - Unknown error';
                    break;
                }
                echo json_encode(($retorno));

            }
        break;

        case 'loadRespostasSemFormFilho':
            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma resposta encontrada';
            $retorno['post'] = $_REQUEST;

            $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
            $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';

            if($respostas = $form->loadRespostas($_REQUEST['formulario'], $inicio, $fim, $_REQUEST['pullTracker']['grupoAtivo'], $userJwt->empresa, true )){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = count($respostas) . ' respostas encontrada(s)';
                $retorno['respostas'] = $respostas;
                $retorno['colunas'] = $_REQUEST['colunas'];
            }

            if($json = json_encode(($retorno))){
                echo $json;
                die();
            }else{
                $retorno = array();
                $retorno['status'] = 'false';
                $retorno['post'] = $_REQUEST;


                switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                            $retorno['mensagem'] =  ' - No errors';
                    break;
                    case JSON_ERROR_DEPTH:
                            $retorno['mensagem'] =  ' - Maximum stack depth exceeded';
                    break;
                    case JSON_ERROR_STATE_MISMATCH:
                            $retorno['mensagem'] =  ' - Underflow or the modes mismatch';
                    break;
                    case JSON_ERROR_CTRL_CHAR:
                            $retorno['mensagem'] =  ' - Unexpected control character found';
                    break;
                    case JSON_ERROR_SYNTAX:
                            $retorno['mensagem'] =  ' - Syntax error, malformed JSON';
                    break;
                    case JSON_ERROR_UTF8:
                            $retorno['mensagem'] =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                    default:
                            $retorno['mensagem'] =  ' - Unknown error';
                    break;
                }
                echo json_encode(($retorno));

            }
        break;

        case 'loadRespostasCSV':
            $form = new formulario();

            $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
            $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';

            $form->loadRespostasCSV($_REQUEST['formulario'], $inicio, $fim, $_REQUEST['pullTracker']['grupoAtivo'], $userJwt->empresa );


            die();
            break;
        case 'loadRespostasSimplificado':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma resposta encontrada';
                $retorno['post'] = $_REQUEST;

                $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
                $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';

                //echo ini_get('memory_limit');

                if($respostas = $form->loadRespostasSimplificado($_REQUEST['formulario'], $inicio, $fim)){
                            $retorno['status'] = 'true';
                            $retorno['mensagem'] = 'Respostas  encontrado';
                            $retorno['respostas'] = $respostas;
                            $retorno['colunas'] = $_REQUEST['colunas'];
                }



                if($json = json_encode(($retorno))){
                    echo $json;
                    die();
                }else{
                    $retorno = array();
                    $retorno['status'] = 'false';
                    $retorno['post'] = $_REQUEST;


                    switch (json_last_error()) {
                        case JSON_ERROR_NONE:
                            $retorno['mensagem'] =  ' - No errors';
                        break;
                        case JSON_ERROR_DEPTH:
                            $retorno['mensagem'] =  ' - Maximum stack depth exceeded';
                        break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $retorno['mensagem'] =  ' - Underflow or the modes mismatch';
                        break;
                        case JSON_ERROR_CTRL_CHAR:
                            $retorno['mensagem'] =  ' - Unexpected control character found';
                        break;
                        case JSON_ERROR_SYNTAX:
                            $retorno['mensagem'] =  ' - Syntax error, malformed JSON';
                        break;
                        case JSON_ERROR_UTF8:
                            $retorno['mensagem'] =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                        break;
                        default:
                            $retorno['mensagem'] =  ' - Unknown error';
                        break;
                    }
                    echo json_encode(($retorno));

                }
            break;
        case 'loadRespostasRelExterno':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma resposta encontrada';
                $retorno['post'] = $_REQUEST;

                $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
                $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';

                //echo ini_get('memory_limit');

                if($respostas = $form->loadRespostasRelExterno($_REQUEST['formulario'], $inicio, $fim)){
                            $retorno['status'] = 'true';
                            $retorno['mensagem'] = 'Respostas  encontrado';
                            $retorno['respostas'] = $respostas;
                            $retorno['colunas'] = $_REQUEST['colunas'];
                }



                if($json = json_encode(($retorno))){
                    echo $json;
                    die();
                }else{
                    $retorno = array();
                    $retorno['status'] = 'false';
                    $retorno['post'] = $_REQUEST;


                    switch (json_last_error()) {
                        case JSON_ERROR_NONE:
                            $retorno['mensagem'] =  ' - No errors';
                        break;
                        case JSON_ERROR_DEPTH:
                            $retorno['mensagem'] =  ' - Maximum stack depth exceeded';
                        break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $retorno['mensagem'] =  ' - Underflow or the modes mismatch';
                        break;
                        case JSON_ERROR_CTRL_CHAR:
                            $retorno['mensagem'] =  ' - Unexpected control character found';
                        break;
                        case JSON_ERROR_SYNTAX:
                            $retorno['mensagem'] =  ' - Syntax error, malformed JSON';
                        break;
                        case JSON_ERROR_UTF8:
                            $retorno['mensagem'] =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                        break;
                        default:
                            $retorno['mensagem'] =  ' - Unknown error';
                        break;
                    }
                    echo json_encode(($retorno));

                }
            break;
        case 'loadFormsFull':

                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhum formulário encontrado.';
				$retorno['post'] = $_REQUEST;


                if($forms = $form->getFormsFull($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'])){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Formulários encontrados!';
                    $retorno['post'] = $forms;

                    //Retornando Categorias
                    if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                        $retorno['categorias'] = $categorias;
                    }else{
                        $retorno['categorias'] = [];
                    }
                }else{
                    $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                    $retorno['post'] = array();
                }

				echo json_encode($retorno);
            break;
        case 'loadCompartiBikeData':
                $formulario = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Dados insuficientes.';
				$retorno['post'] = $_REQUEST;

                $inicio = $_REQUEST['pullTracker']['inicio'].' 00:00:00';
                $fim = $_REQUEST['pullTracker']['fim'].' 23:59:59';
                if($retorno['dados'] = $formulario->getFormRespostasNoPeriodo($inicio,$fim,$userJwt->empresa)){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Dados encontrados!';
                }

				echo json_encode(arrayToUTF8($retorno));
            break;
        case 'getPecasName':
                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma peça encontrada.';
				$retorno['dados'] = $_REQUEST;
                if($forms = $form->getPecasName($_REQUEST['pullTracker']['pecas'])){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Peças encontradas!';
				    $retorno['dados'] = $forms;
                }

				echo json_encode(($retorno));
            break;
        case 'doLista':
                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Não foi possível criar uma nova lista';
				$retorno['post'] = $_REQUEST;
                $retorno['str']=array();

                $itens=json_decode($_REQUEST['itens']);

                // if(is_array($_REQUEST['itens'])){
                //     $itens = $_REQUEST['itens'];
                // }else{
                //     $itens = array();
                // }
                $editarId = $_REQUEST['editarId'];
                $groupRestriction = 0;
                if(is_numeric($_REQUEST['groupRestriction'])){
                    $groupRestriction = $_REQUEST['groupRestriction'];
                }

                if($editarId == null){
                    $lista = $form->criarLista($_REQUEST['titulo'],$userJwt->empresa,false,$groupRestriction);
                    //funcao para tirar itens duplicados do array com base no seu codigo
                    $itens = $form->multi_unique($itens);
                    $idItem = $form->insertVarios($itens,'form_listas_itens',$lista);
                    foreach($itens as $i){
                       $gruposDaLista = explode(',', $i->grupos);
                       $form->insertVarios($gruposDaLista,'form_listas_itens_groups',$idItem);
                       $idItem=$idItem+1;
                       $retorno['str'][] = 'criado '.$idItem;
                    }
                }
                else if($editarId > 0){
                    $mensagem = $form->editarLista($editarId,$itens,$_REQUEST['titulo'],$groupRestriction);
                    $lista=$editarId;
                }
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Lista criada com sucesso!';
                    $retorno['mensagem'] = $mensagem;
                    $retorno['post'] = $forms;
                    $retorno['lista'] = $idItem;

            echo json_encode(($retorno));
        break;
        case 'loadListas':
                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma lista encontrada.';
				$retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if(is_numeric($_REQUEST['grupo'])){
                    $gp = $_REQUEST['grupo'];
                }
                if(is_numeric($_REQUEST['user'])){
                    $usr = $_REQUEST['user'];
                }
                if($forms = $form->loadListas($userJwt->empresa,$gp)){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Listas encontradas!';
                    $r2 = array();
                    foreach($forms as $r){
                        $r['itens'] = $form->getListItens($r['id'],$r['groupRestriction'],$usr);
                        $r2[]=$r;
                    }
				    $retorno['post'] = $r2;
                }
				echo json_encode(($retorno));
            break;
        case 'loadListasPanel':
                $form = new formulario();
                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma lista encontrada.';
				$retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $form->loadListasPanel($userJwt->empresa)){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Listas encontradas!';
				    $retorno['post'] = $listas;
                }
				echo json_encode(($retorno));
            break;

        case 'getListItensForPanel':
                $form = new formulario();
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Nenhum item de lista encontrado.';
                $retorno['post'] = $_REQUEST;

                if($itens = $form->getListItensForPanel($_REQUEST['lista'])){
                    $retorno['mensagem'] = 'itens encontrados.';
                    $retorno['itens'] = $itens;
                }

                echo json_encode(($retorno));

            break;

        case 'DeleteListaForPanel':
            $form = new formulario();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma lista encontrada.';
            $retorno['post'] = $_REQUEST;
           if($form->deleteListForPanel($_REQUEST['lista'])){
            $retorno['status'] = 'true';
            $retorno['mensagem'] = 'Lista deletada com sucesso.';
           }


           echo json_encode(($retorno));
        break;
        case 'atualizaGruposListItem':
            $form = new formulario();
            $form->normalizeItensGrupo($_REQUEST);
            $retorno['status'] = 'true';
            $retorno['mensagem'] = 'Grupos modificados com sucesso.';

            echo json_encode(($retorno));
        break;
        case 'loadForms':

                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhum formulário encontrado.';
				$retorno['post'] = $_REQUEST;


                if($forms = $form->getForms($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'])){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Formulários encontrados!';
                    $retorno['post'] = $forms;

                    //Retornando Categorias
                    if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                        $retorno['categorias'] = $categorias;
                    }else{
                        $retorno['categorias'] = [];
                    }
                }else{
                    $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                    $retorno['post'] = array();
                }

				echo json_encode($retorno);
            break;

            case 'loadFormsPorData':

                $form = new formulario();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhum formulário encontrado.';
				$retorno['post'] = $_REQUEST;


                if($forms = $form->getFormsCategorizadosPorData($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['inicio'],$_REQUEST['termino'])){
                    $retorno['status'] = 'true';
				    $retorno['mensagem'] = 'Formulários encontrados!';
                    $retorno['forms'] = $forms;

                    //Retornando Categorias
                    if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                        $retorno['categorias'] = $categorias;
                    }else{
                        $retorno['categorias'] = [];
                    }
                }else{
                    $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                    $retorno['post'] = array();
                }

				echo json_encode($retorno);
            break;

            case 'loadFormsExternos':

            $form = new formulario();
            $qrcode = new qrcode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum formulário encontrado.';
            $retorno['post'] = $_REQUEST;


            if($forms = $form->getForms($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'])){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulários encontrados!';
                $retorno['post'] = $forms;

                /*
                if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                    $retorno['categorias'] = $categorias;
                }else{
                    $retorno['categorias'] = [];
                }*/
                //Retornando Categorias
                if($qrcodemeta = $qrcode->qrcodeMeta($userJwt->empresa)){
                    $retorno['categorias'] = $qrcodemeta;
                }else{
                    $retorno['categorias'] = [];
                }
            }else{
                $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                $retorno['post'] = array();
            }

            echo json_encode($retorno);
        break;

        case 'loadFormsParaEditarAgentes':

            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum formulário encontrado.';
            $retorno['post'] = $_REQUEST;


            if($forms = $form->getFormsSimple($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'])){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulários encontrados!';
                $retorno['post'] = $forms;

                //Retornando Categorias
                if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                    $retorno['categorias'] = $categorias;
                }else{
                    $retorno['categorias'] = [];
                }
            }else{
                $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                $retorno['post'] = array();
            }

            echo json_encode($retorno);
        break;

        case 'loadFormsExternosPorData':

            $form = new formulario();
            $qrcode = new qrcode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum formulário encontrado.';
            $retorno['post'] = $_REQUEST;


            //if($forms = $form->getForms($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'])){
            if($forms = $form->getFormsPorData($userJwt->empresa,true,$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['inicio'],$_REQUEST['termino'])){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulários encontrados!';
                $retorno['post'] = $forms;

                /*
                if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                    $retorno['categorias'] = $categorias;
                }else{
                    $retorno['categorias'] = [];
                }*/
                //Retornando Categorias
                if($qrcodemeta = $qrcode->qrcodeMeta($userJwt->empresa)){
                    $retorno['categorias'] = $qrcodemeta;
                }else{
                    $retorno['categorias'] = [];
                }
            }else{
                $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                $retorno['post'] = array();
            }

            echo json_encode($retorno);
        break;

        case 'getRelatoriosExternosPorData':

            $form = new formulario();
            $qrcode = new qrcode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum formulário encontrado.';
            $retorno['post'] = $_REQUEST;

            if($forms = $form->getRelatoriosExternosPorData($userJwt->empresa,false,$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['inicio'],$_REQUEST['termino'])){
                $retorno['status'] = 'true';
                $retorno['mensagem'] = 'Formulários encontrados!';
                $retorno['post'] = $forms;

            }else{
                $retorno['mensagem'] = 'Nenhum formulário encontrado!';
                $retorno['post'] = array();
            }

            echo json_encode($retorno);
        break;

        case 'loadImportedMaps':

                $gm = new geoMaps();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum mapa encontrado.';

                if($mapas = $gm->loadImportedMaps($userJwt->empresa,$_REQUEST['grupoAtivo'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Mapas encontrados!';
                    $retorno['post'] = $mapas;
                }

                echo json_encode($retorno);
            break;
        case 'loadMapasCompletos':

                $gm = new geoMaps();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum mapa encontrado.';

                if($mapas = $gm->loadImportedMaps($userJwt->empresa,false,true)){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Mapas encontrados!';
                    $retorno['post'] = $mapas;
                }

                echo json_encode($retorno);
            break;
        case 'salvarMapa':
                    $gm = new geoMaps();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhum mapa salvo.';
                    //$retorno['r'] = $_REQUEST;


                    $target_path  = dirname(__FILE__)."/uploads/";
                    $caminho = '/uploads/';
					$string = 'file_';

                    $cod_cliente = $_POST['empresa'];
                    $date = new DateTime();
                    $tempo = $date->getTimestamp();

                    $retorno['tempo'] = $tempo;


					if(!is_dir($target_path)){
						mkdir($target_path);
					}

					$target_path .= $cod_cliente."/";
                    $caminho .= $cod_cliente."/";

                    if(!is_dir($target_path)){
                            mkdir($target_path);
                    }
                    $target_path .= $tempo."/";
                    $caminho .= $tempo."/";

                    if(!is_dir($target_path)){
                            mkdir($target_path);
                    }

                    $nomeDoArquivo = basename($_FILES['arquivo']['name']);

                    $target_path = $target_path . $nomeDoArquivo;
                    $caminho = $caminho . $nomeDoArquivo;

                    //$retorno['target_path'] = $target_path;
                    $retorno['caminho'] = $caminho;

			        if($retorno['subiu'] = move_uploaded_file($_FILES["arquivo"]["tmp_name"],$target_path)){

                        if($mapa = $gm->saveImportedMaps($userJwt->empresa,$caminho,$_REQUEST['gruposPorArea'],$_REQUEST['selectFields'],$_REQUEST['especie'],$_REQUEST['uso'],1)){
                            $retorno['status'] = 'true';
                            $retorno['mensagem'] = 'Mapa  salvo! ';
                            $retorno['mapa'] = $mapa;
                        }

                        echo json_encode($retorno);

                    } else {
                        print_r($_FILES);
                        print_r(error_get_last());
                        die();
                    }
                break;
        case 'mudarStatus':
                    $gm = new geoMaps();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhum mapa encontrado.';

                    if($mapas = $gm->mudarStatus($_REQUEST['mapa'],$_REQUEST['status'])){
                        $retorno['status'] = 'true';
                        $retorno['mensagem'] = 'Mapa modificado com sucesso!';
                    }

                    echo json_encode($retorno);
                break;
        /*
        case 'categoriasFormularios-b2':

            $form = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma categoria encontrada.';

            if($categorias = $form->categoriasFormularios($userJwt->empresa)){

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Categorias encontradas com sucesso!.';
                $retorno['categorias'] = $categorias;
            }

            echo json_encode($retorno);

        break;

        case 'loadFormulariosCategorias-b2':

            $forms = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum formulario encontrado.';

            if($formsCat = $forms->loadFormulariosCategorias($userJwt->empresa)){

                $retorno['status'] = 'ok';
                $retorno['forms'] = $formsCat;
            }

            echo json_encode($retorno);

        break;

        case 'criarCategoriaFormulario-b2':

            $forms = new formulario();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Não foi possível criar a categoria!';

            if($id = $forms->categoriaExiste($_REQUEST['nome'],$userJwt->empresa) &&  $_REQUEST['editarId']==''){
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Esta categoria já existe';
            }else{
                if($_REQUEST['editarId']!=''){
                    $id = $_REQUEST['editarId'];
                    $forms->update($_REQUEST['nome'],'nome','form_categorias',$id);
                    $retorno['mensagem'] = 'Categoria alterada com sucesso!';
                }else{
                    $id = $forms->criarCategoria($_REQUEST['nome'],$userJwt->empresa);
                    $retorno['mensagem'] = 'Categoria criada com sucesso!';
                }

                $retorno['status'] = 'ok';

            }

            if(is_array($_REQUEST['exitForms'])){
                foreach($_REQUEST['exitForms'] as $u){
                   $forms->exitCatForm($u,$id);
                }
            }

            if(is_array($_REQUEST['forms'])){
                foreach($_REQUEST['forms'] as $u){
                   $forms->enterCategorias($u,$id);
                }
            }

            echo json_encode($retorno);

        break;

        case 'montarCategoriaFormulario-2':

            $forms = new formulario();

            $retorno['status'] = 'false';
            if($catForm = $forms->montarCategoriaFormulario($userJwt->empresa)){
            $retorno['mensagem'] = 'Não foi possível encontrar formulários!';

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Formulários encontrados com sucesso!';
                $retorno['forms'] = $catForm;
            }
            echo json_encode($retorno);

        break;*/

        case 'criarGrupo':
                $retorno['post'] = $_REQUEST;

                 $optionArray = $_REQUEST['checkbox'];
                for ($i=0; $i<count($optionArray); $i++) {
                   $retorno['checkbox'][]=  $optionArray[$i];
                }


                $user = new user();

                if($id = $user->grupoExiste($_REQUEST['name'],$userJwt->empresa) &&  $_REQUEST['editarId']==''){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Este grupo já existe';
                    $retorno['idGrupo'] = $id;
                }else{
                    if($_REQUEST['editarId']!=''){
                        $id = $_REQUEST['editarId'];
                        $user->update($_REQUEST['name'],'nome','groups',$id);
                        $user->update($_REQUEST['cor'],'cor','groups',$id);
                        $user->update($_REQUEST['idPai'],'parent','groups',$id);
                        $retorno['mensagem'] = 'Grupo alterado com sucesso!';
                    }else{
                        $id = $user->criarGrupo($_REQUEST['name'],$userJwt->empresa,$_REQUEST['cor'],$_REQUEST['idPai']);
                        $retorno['mensagem'] = 'Grupo criado com sucesso!';
                    }

                    $retorno['status'] = 'ok';

                    $retorno['idGrupo'] = $id;
                }

                if(is_array($_REQUEST['exitUsers'])){
                    foreach($_REQUEST['exitUsers'] as $u){
                       $user->exitGroup($u,$id);
                    }
                }

                if(is_array($_REQUEST['users'])){
                    foreach($_REQUEST['users'] as $u){
                       $user->enterGroup($u,$id);
                    }
                }

                echo json_encode(arrayToUTF8($retorno));
            break;
         case 'enterGroup':
                $user = new user();
                $user->enterGroup($_REQUEST['user'],$_REQUEST['grupo']);
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Usuário: '.$_REQUEST['user'].' incluido no grupo:'.$_REQUEST['grupo'];
                echo json_encode(arrayToUTF8($retorno));
            break;
        case 'criarLocal':
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Ocorreu um erro! por favor, confirme os dados informados e tente novamente.';
                $retorno['post'] = $_REQUEST;

                $user = new user();

                $local = new local();

                if($_REQUEST['name']!='' && $_REQUEST['endereco']!=''  && $_REQUEST['lat']!=''  && $_REQUEST['lng']!='' ){

                    if($_REQUEST['editarId']!='' && $local->existe('id','locais',$_REQUEST['editarId'])){
                        $idLocal = $_REQUEST['editarId'];
                        $local->editarLocal($idLocal,'nome',$_REQUEST['name']);
                        $local->editarLocal($idLocal,'lat',$_REQUEST['lat']);
                        $local->editarLocal($idLocal,'lng',$_REQUEST['lng']);
                        $local->editarLocal($idLocal,'endereco',$_REQUEST['endereco']);
                        $local->editarLocal($idLocal,'empresa',$userJwt->empresa);
                        $local->editarLocal($idLocal,'status','1');

                    }else{
                        $idLocal = $local->criarLocal($_REQUEST);
                    }



                    $local->clearGroupLocal($idLocal);

                    if(is_array($_REQUEST['grupos'])){
                        foreach($_REQUEST['grupos'] as $g){
                           $local->enterLocalGroup($idLocal,$g);
                        }
                    }

                    $retorno['local'] = $idLocal;
                    $retorno['status'] = 'Ok';
                    $retorno['mensagem'] = 'Local criado com sucesso!';



                }

                echo json_encode($retorno);
            break;
        case 'apagarLocal':
                $retorno['post'] = $_REQUEST;

                $user = new user();


                echo json_encode(arrayToUTF8($retorno));
            break;
        case 'loadLocais':
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum local encontrado!';
                $retorno['post'] = $_REQUEST;

                $local = new local();
                $gp = false;
                if($_REQUEST['grupo']!='false'){
                    $gp = $_REQUEST['grupo'];
                }
                $metaLocais = $local->getMetaLocais($userJwt->empresa);
                if($dados=$local->loadLocais($userJwt->empresa,$gp,$metaLocais)){
                    $retorno['dados'] = $dados;
                    $retorno['status'] = 'Ok';
                    $retorno['mensagem'] = 'Locais encontrados!';
                }

                echo safe_json_encode($retorno);


            break;
        case 'importarLocais':
            $empresa = $_POST['empresa'];
            $dados = $_POST['dados'];

            $local = new local();
            $retorno = $local->importarLocais($empresa,$dados);

            echo safe_json_encode($retorno);
            break;

        case 'apagarTarefaRecorrente':
                $retorno['status'] = 'Ok';
                $retorno['mensagem'] = 'Tarefa recorrente excluída com sucesso!';
                $retorno['post'] = $_REQUEST;
                $rota = new rota();
                $rota->apagarTarefaRecorrente($_REQUEST['tarefaRecorrente']);
                echo json_encode($retorno);
            break;
        case 'loadRecorrentes':
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma tarefa encontrada!';
                $retorno['post'] = $_REQUEST;

                $rota = new rota();
                $gp = false;
                if($_REQUEST['grupo']!='false'){
                    $gp = $_REQUEST['grupo'];
                }
                if($dados=$rota->loadRecorrentes($userJwt->empresa,$gp)){
                    $retorno['dados'] = $dados;
                    $retorno['status'] = 'Ok';
                    $retorno['mensagem'] = 'Tarefas encontradas!';
                }

                echo json_encode(($retorno));


            break;
        case 'getGrupos':

                $user = new user();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum grupo cadastrado';

                if($grupos = $user->getGrupos($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Grupos encontrados';
                    $retorno['grupos'] = $grupos;
                }
                echo safe_json_encode($retorno);
            break;

        case 'listGroupsByUser':

            $grupo = new Grupo();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum grupo cadastrado';

            if($grupos = $grupo->listGroupsByUser($_REQUEST['userId'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Grupos encontrados';
                $retorno['grupos'] = $grupos;
            }
            echo safe_json_encode(arrayToUTF8($retorno));
            break;
        case 'listGroupsByCompany':
            $grupo = new Grupo();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum grupo cadastrado';

            if($grupos = $grupo->listGroupsByCompany($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Grupos encontrados';
                $retorno['grupos'] = $grupos;
            }
            echo safe_json_encode(arrayToUTF8($retorno));
            break;

        case 'listGroupsByUserAndCompany':
            $grupo = new Grupo();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum grupo cadastrado';

            if($grupos = $grupo->listGroupsByUser($_REQUEST['userId'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Grupos encontrados';
                $retorno['gruposUser'] = $grupos;
            }

            if($grupos = $grupo->listGroupsByCompany($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Grupos encontrados';
                $retorno['gruposCompany'] = $grupos;
            }
            echo safe_json_encode(arrayToUTF8($retorno));

            break;

        case 'relatorios':


            $rota = new rota();
            $geo = new geo();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma rota encontrada';

            $in = "";

            // elimina grupos vazios
            $grupos = array();

            foreach($_REQUEST['pullTracker']['grupos'] as $grupo){
                if($grupo && $grupo != ''){
                    $grupos[] = $grupo;
                }
            }

            if(count($grupos) > 0){
                /**
                 *  Task-577 - Remoção de Grupos
                 *  Precisa verificar se o grupo está deletado ou não.
                 *
                 *  Adicionando código para verificar isso.
                 *
                    INNER JOIN groups GR ON GR.id = G.grupo AND GR.deletado = 0
                */
                $in = " INNER JOIN
                        groupMembers G ON U.id = G.user AND G.grupo in (" . implode(',', $grupos) .')

                        INNER JOIN groups GR ON GR.id = G.grupo AND GR.deletado = 0
                    ';
            }

            if($_REQUEST['pullTracker']['agenteFilterID']!=''){
                if($_REQUEST['pullTracker']['desativado']=='true'){
                    $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota FROM users U INNER JOIN rotas R ON U.id = R.user WHERE U.id='.$_REQUEST['pullTracker']['agenteFilterID'].' AND U.empresa='.$userJwt->empresa.'  AND U.ativo=1 AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                }else{
                    $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota FROM users U INNER JOIN rotas R ON U.id = R.user WHERE U.id='.$_REQUEST['pullTracker']['agenteFilterID'].' AND U.empresa='.$userJwt->empresa.' AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                }
            }else{
                if($_REQUEST['pullTracker']['grupoAtivo']!='false' && $_REQUEST['pullTracker']['grupoAtivo']!=false){
                    /**
                     *  Task-577 - Remoção de Grupos
                     *  Precisa verificar se o grupo está deletado ou não.
                     *
                     *  Adicionando código para verificar isso.
                     *
                        INNER JOIN groups GR ON GR.id = G.grupo AND GR.deletado = 0
                    */
                    if($_REQUEST['pullTracker']['desativado']=='true'){
                        $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota
                                FROM users U
                                INNER JOIN rotas R ON U.id = R.user
                                INNER JOIN groupMembers G ON U.id = G.user

                                INNER JOIN groups GR ON GR.id = G.grupo AND GR.deletado = 0

                                WHERE U.empresa='.$userJwt->empresa.' AND G.grupo='.$_REQUEST['pullTracker']['grupoAtivo'].' AND U.ativo=1 AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                    }else{
                        $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota
                                FROM users U
                                INNER JOIN rotas R ON U.id = R.user
                                INNER JOIN groupMembers G ON U.id = G.user

                                INNER JOIN groups GR ON GR.id = G.grupo AND GR.deletado = 0

                                WHERE U.empresa='.$userJwt->empresa.' AND G.grupo='.$_REQUEST['pullTracker']['grupoAtivo'].' AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                    }
                }else{
                    if($_REQUEST['pullTracker']['desativado']=='true'){
                        $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota FROM users U INNER JOIN rotas R ON U.id = R.user '.$in.' WHERE U.empresa='.$userJwt->empresa.' AND U.ativo=1 AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                    }else{
                        $sql = 'SELECT U.id as id, U.nome as nome, R.id as rota FROM users U INNER JOIN rotas R ON U.id = R.user '.$in.' WHERE U.empresa='.$userJwt->empresa.' AND (U.isEmpresa IS NULL OR U.isEmpresa !=1)';
                    }
                }
            }
            $agentes = '';
            $rotas = '';

			 if($dados = $rota->execute($sql)){
                 $first = true;
                 while($temp = mysqli_fetch_array($dados)){
                     if($first){
                         $first=false;
                     }else{
                        $agentes .= ', ';
                        $rotas .= ', ';
                     }
                    $retorno['users'][] = $temp;
                    $agentes .= $temp['id'];
                    $rotas .= $temp['rota'];
                }
            }


            $sql = '
                SELECT max(tracker.id),
                        tracker.user,
                        tracker.lastaTrack,
                        tracker.data,
                        tracker.criado,
                        tracker.`timestamp`,
                        tracker.latitude,
                        tracker.longitude,
                        tracker.started,
                        tracker.changed
                FROM tracker
                WHERE     user IN ('.$agentes.')
                        AND criado BETWEEN \''.$_REQUEST['pullTracker']['inicio'].' 00:00:00\' AND \''.$_REQUEST['pullTracker']['fim'].' 23:59:59\'
                        and timestamp is not null
                GROUP BY tracker.user,
                        tracker.lastaTrack,
                        tracker.data,
                        tracker.criado,
                        tracker.`timestamp`,
                        tracker.latitude,
                        tracker.longitude,
                        tracker.started,
                        tracker.changed';

            //echo $sql."\n";
            $retorno['tracker'] = array();
            if($dados3 = $rota->execute($sql)){

                while($temp = mysqli_fetch_array($dados3)){
                    //echo $geo->pegarPontosDoDia($temp['user'],date("Y-m-d", strtotime($temp['criado'])));;

                   // $trakerUser = $geo->pegarPontosDoDia($temp['user'],date("Y-m-d", strtotime($temp['criado'])));

                //   $add = array();
                //   $add['registers'] = $trakerUser;
                 //  $temp["data"] = json_encode(arrayToUTF8($add));

                   $retorno['tracker'][] = $temp;
                }
            }


            /**
             * Adicionando "deletada = 0" no Where
             */

            $inicio = $_REQUEST['pullTracker']['inicio'] ? $_REQUEST['pullTracker']['inicio'] : date('Y-m-d');
            $fim = $_REQUEST['pullTracker']['fim'] ? $_REQUEST['pullTracker']['fim'] : date('Y-m-d');

            $sql = 'SELECT * FROM tarefas WHERE deletada = 0 AND rota IN ('.$rotas.') AND dataTarefa BETWEEN \''.$inicio.'\' AND \''.$fim.'\'';
            $imagemTarefa = new ImagemTarefa();
            if($dados = $rota->execute($sql)){
                 while($temp = mysqli_fetch_array($dados)){
                   $temp['getPic'] = $imagemTarefa->loadByTarefa($temp);
                   $retorno['tarefas'][] = $temp;
                }
            }

    		echo json_encode(($retorno));


            break;


        case 'detalhesTarefa':
            $formulario = new formulario();

            $idTarefa = $_REQUEST['idtarefa'];

            $formularios = ($formulario->loadRespostasTarefa($idTarefa));
            $retorno = array();

            foreach($formularios as $resposta){

                if(! is_array($retorno[$resposta['idForma']])  ){

                    $retorno[$resposta['idForma']] = array(
                            "id"=>$resposta['idForma'],
                            "titulo" => $resposta['titulo'],
                            "data" => array()
                        );
                }
                $retorno[$resposta['idForma']]["data"][] = $resposta;

            }

            echo json_encode($retorno);

            break;

		case 'ativarAgente':
                $user = new user();

				if($u = $user->getUserByCode($_REQUEST['codeCode'],$_REQUEST['passCode'])){
					$retorno['status'] = 'ok';

                    //$number = rand();
					$token = md5($u['id'].date('YMD'));

                    if($user->ativarAgente($token,$_REQUEST['OS'],$_REQUEST['osVersion'],$_REQUEST['screenSize'],$_REQUEST['notid'],$_REQUEST['bateria'],$u['id'])) {
                        if(@$_REQUEST['model']){
                            $user->updateUser($_REQUEST['model'],'model',$u['id']);
                        }
                        $user->update_user_meta($u['id'],'appVersion',$_REQUEST['appVersion']);
                    } else {
                        $retorno['status'] = 'error';
                    }

					$retorno['user'] = $user->getUserByCode($_REQUEST['codeCode'],$_REQUEST['passCode']);


				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
				}

				echo json_encode($retorno);
			break;
        case 'bateriaTeste':


				 mail('glaysonramos@gmail.com','bateria',json_encode($_POST));
            echo json_encode($_POST);
            $db->desconecta();
            die();

			break;
		case 'setAdminAgente':
				$user = new user();

				if($user->editarUser($_REQUEST['id'],'isAdmin',$_REQUEST['isAdmin'])){

                    $retorno['status'] = 'ok';

					$retorno['status'] = 'true';
					$retorno['mensagem'] = 'Alterado com sucesso!';

				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
				}

				echo json_encode($retorno);
            break;

        case 'getSuperAdmin':
            $user = new user();
            $retorno['status'] = 'false';
            if($superAdmin = $user->getSuperAdmin($userJwt->empresa)){

                $retorno['status'] = 'ok';
                $retorno['superadmins'] = $superAdmin;
            }
            echo json_encode($retorno);
        break;

        case 'criarSuperAdm':

            $user = new user();

            if($_REQUEST['email']!=''){
                if($d = $user->existe('email','users',$_REQUEST['email'])){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'][] = 'Este e-mail já está cadastrado em nosso sistema.';
                }
            }

            if($_REQUEST['editarId']!=''|| !$d){

                $retorno['status'] = 'ok';

                if($_REQUEST['name']==''){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'][] = 'Por favor, informe um nome.';
                }

                if($_REQUEST['senhaToPut']=='' && $_REQUEST['editarId']==''){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'][] = 'Por favor, informe uma senha';
                }
                if($_REQUEST['email']==''){

                    $retorno['status'] = 'false';
                    $retorno['mensagem'][] = 'Usuários super administradores precisam de um e-mail para realizar o login.';

                }

                if($retorno['status'] == 'ok'){

                    if($d || $_REQUEST['editarId']){

                        $id = $_REQUEST['editarId'];
                        $retorno['mensagem'] = 'Usuário atualizado com sucesso!';
                        $user->updateUser($_REQUEST['name'],'nome',$id);

                    }else{
                        if($id = $user->criarUser($_REQUEST['name'])){
                            $retorno['mensagem'] = 'Usuário super administrador cadastrado com sucesso!';
                        }
                    }


                    if($id){

                        if($_REQUEST['senhaToPut']!=''){
                            $user->updateUser(md5($_REQUEST['senhaToPut']),'senha',$id);
                        }

                        if($_REQUEST['email']!=''){
                            $user->updateUser($_REQUEST['email'],'email',$id);
                        }

                        $token = md5($id.date('YMD'));

                        $user->updateUser($token,'tokenPainel',$id);

                        $user->updateUser($userJwt->empresa,'empresa',$id);

                        $user->updateUser('1','isSuperAdmin',$id);
                    }

                    $retorno['token'] = $token;
					$retorno['user'] = $id;

                }

            }
            echo json_encode($retorno);
        break;

		case 'massRegistro':

				$geo = new geo();
				$user = new user();

				$json = json_decode($_REQUEST['json']);

				$retorno = array();

				$retorno = $geo->registerSync($_POST['pullTracker']['id'],$_REQUEST['json']);

				echo safe_json_encode(arrayToUTF8($retorno));
                $db->desconecta();
				die();

			break;
		case 'sincronizarUmaTarefa':

				$rota = new rota();
			     $user = new user();


				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';

				$tarefa = $_REQUEST['idServer'];
				$status = $_REQUEST['pullTracker']['status'];

                $idUsuario = $status = $_REQUEST['pullTracker']['id'];

				$data = array(
							'id'			=>	$_REQUEST['id'],
							'idServer'		=>	$_REQUEST['idServer'],
							'lastChange'	=>	$_REQUEST['lastChange'],
							'coment'		=>	$_REQUEST['coment'],
							'taskStart'		=>	$_REQUEST['taskStart'],
							'taskEnd'		=>	$_REQUEST['taskEnd'],
							'travelStart'	=>	$_REQUEST['travelStart'],
							'travelEnd'		=>	$_REQUEST['travelEnd'],
							'travelLat'		=>	$_REQUEST['travelLat'],
							'travelLng'		=>	$_REQUEST['travelLng'],
							'status'		=>	$_REQUEST['status'],
                            'getSing'       =>	$_REQUEST['getSign'],
                            'getPic'       =>	$_REQUEST['getPic'],
							'user'		=>	$_REQUEST['user'],
							'rota'	=>	$_REQUEST['rota'],
							'livre'		=>	$_REQUEST['livre'],
							'sign'		=>	$_REQUEST['sign'],
							'pic'		=>	$_REQUEST['pic'],
							'lat'	=>	$_REQUEST['lat'],
							'lng'		=>	$_REQUEST['lng'],
							'planedStart'		=>	$_REQUEST['planedStart'],
							'planedEnd'		=>	$_REQUEST['planedEnd'],
							'endereco'		=>	$_REQUEST['endereco'],
                            'descricao'       =>	$_REQUEST['descricao'],
                            'comentTime'       =>	$_REQUEST['comentTime'],
							'json'		=>	$_REQUEST['json'],
                            'log'       =>	$_REQUEST['log'],
                            'forms'       =>	$_REQUEST['forms'],
                            'dataTarefa'       =>	$_REQUEST['data']

				);

                //Variável que irá conter os dados inserido em uma Notificação
                $texto = '';
				switch($_REQUEST['status']){
                    case 'travelStarted':
                        /* Caso a tarefa em questão for uma tarefa de grupo, ações adicionais serão tomadas */
                        $tarefaGrupo = new TarefaGrupo();
                        $tarefaGrupo->verificaTarefaGrupo($data);

                        $user->updateUser($_REQUEST['status'],'extrainfo',$idUsuario);
                        $user->updateUser($_REQUEST['id'],'foco',$idUsuario);
                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $texto = ' Endereço :'.$_REQUEST['endereco'];

                            $notificacao = new notificacao();
                            $notificacao->criarNotificacao(3,$texto,$idUsuario,$data['idServer']);
                        }
                        $user->updateUser($_REQUEST['status'],'extrainfo',$idUsuario);
                        $user->updateUser($_REQUEST['id'],'foco',$idUsuario);

                        do_action("save_tarefa_travel_started",array($data['idServer'],$idUsuario));

                        break;
                    case 'travelDone':
                        $user->updateUser($_REQUEST['status'],'extrainfo',$idUsuario);
                        $user->updateUser($_REQUEST['id'],'foco',$idUsuario);
                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $texto = ' Concluiu o Deslocamento até a Tarefa : '.$_REQUEST['descricao'].' em '.$_REQUEST['endereco'];

                            $notificacao = new notificacao();
                            $notificacao->criarNotificacao(4,$texto,$idUsuario,$data['idServer']);
                        }


                        $user->updateUser($_REQUEST['status'],'extrainfo',$idUsuario);
                        $user->updateUser($_REQUEST['id'],'foco',$idUsuario);
                        do_action("save_tarefa_travel_done",array($data['idServer'],$idUsuario));

                        break;
                    case 'iniciada':

                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $texto = ' Iniciou a Tarefa : '.$_REQUEST['descricao'].' em '.$_REQUEST['endereco'];

                            $notificacao = new notificacao();
                            $notificacao->criarNotificacao(5,$texto,$idUsuario,$data['idServer']);
                        }
                        $user->updateUser($_REQUEST['status'],'extrainfo',$idUsuario);
                        $user->updateUser($_REQUEST['id'],'foco',$idUsuario);
                        do_action("save_tarefa_iniciada",array($data['idServer'],$idUsuario));
                        break;
                    case 'concluida':
                        /* Montando as Informações que irão compor a Notificação gerada pela Mudança de Status desta Tarefa */
                        if($texto == ''){
                            $texto = ' Concluiu uma Tarefa '.$_REQUEST['descricao'].' em '.$_REQUEST['endereco'];
                            $notificacao = new notificacao();

                            $sql = "SELECT T.status FROM `tarefas` T WHERE id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'iniciada'){
                                $notificacao->criarNotificacao(6,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        $user->updateUser('','extrainfo',$idUsuario);
                        $user->updateUser(0,'foco',$idUsuario);
                        do_action("save_tarefa_concluida",array($data['idServer'],$idUsuario));
                        break;

                    case 'malsucedida':

                         if($texto == ''){
                            $texto = ' Informou a Tarefa '.$_REQUEST['descricao'].' como Mal Sucedida';
                            $notificacao = new notificacao();

                            $sql = "SELECT T.status FROM `tarefas` T WHERE id = ".$data['idServer']."";
                            $status = mysqli_fetch_array($notificacao->execute($sql))['status'];

                            if($status == 'iniciada' || $status == 'pendente' || $status == 'travelDone' || $status == 'travelStarted'){
                                $notificacao->criarNotificacao(7,$texto,$idUsuario,$data['idServer']);
                            }
                        }
                        do_action("save_tarefa_malsucedida",array($data['idServer'],$idUsuario));
                        break;
                }

//                if($data["status"] == "malsucedida"){
//                    print_r($data);
//                    die();
//                }
				if($retorno['tarefa'] = $rota->updateTarefa($data)){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Tarefa atualizada com sucesso!';
				}


				echo json_encode($retorno);
			break;

		case 'salvarUmaTarefa':

                $rota = new rota();
                $tarefas = new tarefas();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';

                $data = json_decode($_REQUEST['pullTracker']['tarefa']);
                if(isset($_REQUEST['pullTracker']['usoPeloPainel'])){
                    $data->usoPeloPainel = true;
                }else{
                    $data->usoPeloPainel = false;
                }
                if(@count($data->tarefasLote)>0){
                    for($i=0;$i<count($data->tarefasLote);$i++){
                        $retorno['tarefa'][] = $rota->salvarUmaTarefa($data,$data->tarefasLote[$i]);
                        do_action("save_tarefa_".$data->status,array($retorno['tarefa']['id'],$data->autor));

                    }
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefas em lote criadas com sucesso!';
                }else{

                    /**
                     * HOTFIX/QRCODEAPP -
                     *
                     * As vezes a variável dataTarefa não está vindo do app, mas vem com nome trocado.
                     *  Caso ela não estiver carregada joga o valor que estiver nessa outra variavel;
                     */
                    if($data->dataTarefa == false || $data->dataTarefa == null){
                        $data->dataTarefa = $data->data;
                    }

                    if($retorno['tarefa'] = $rota->salvarUmaTarefa($data)){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Tarefa atualizada com sucesso!';

                        do_action("save_tarefa_".$data->status,array($retorno['tarefa']['id'],$data->autor));


                        do_action("post_save_tarefa_".$data->status,array($retorno['tarefa']['id'],$data->autor));

                        $retorno['tarefa'] = mysqli_fetch_assoc($rota->getOne("*",$retorno['tarefa']['id'],"tarefas"));
                        $retorno['tarefa']['meta'] = $tarefas->get_tarefa_meta($retorno['tarefa']['id']);

                        $notificacao = new notificacao();
                        $language = '';
                        $texto = $notificacao->montaTextoNotificacao(2,$data,$_REQUEST['language']);

                        $notificacao->criarNotificacao(2,$texto,$data->autor,$retorno['tarefa']['id'],$data->usoPeloPainel);

                        /**
                         * Tarefas Criadas Pelo App
                         */
                        if(!$data->usoPeloPainel){
                            do_action("post_create_task_by_app",array($retorno['tarefa']));
                        }
                    }

                }


				echo json_encode($retorno);
            break;
        case 'importarTarefas':

            $rota = new rota();
            $tarefas = new tarefas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma tarefa encontrada';

            $tarefas = json_decode($_REQUEST['pullTracker']['tarefas']);


            if(@count($tarefas)>0){
                for($i=0;$i<count($tarefas);$i++){

                    $data = $tarefas[$i];
                    $data->usoPeloPainel = true;

                    if($data->dataTarefa == false || $data->dataTarefa == null){
                        $data->dataTarefa = $data->data;
                    }

                    if($retorno['tarefa'][$i] = $rota->salvarUmaTarefa($data)){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Tarefa importadas com sucesso!';

                        do_action(".".$data->status,array($retorno['tarefa'][$i]['id'],$data->autor));


                        do_action("post_save_tarefa_".$data->status,array($retorno['tarefa'][$i]['id'],$data->autor));
                        do_action("post_save_tarefa_pre_resp",array($userJwt->empresa, $retorno['tarefa'][$i]['id'], $tarefas[$i]->forms));

                        $retorno['tarefa'][$i] = mysqli_fetch_assoc($rota->getOne("*",$retorno['tarefa'][$i]['id'],"tarefas"));
                        //$retorno['tarefa'][$i]['meta'] = $tarefas->get_tarefa_meta($retorno['tarefa'][$i]['id']);

                        $notificacao = new notificacao();
                        $language = '';
                        $texto = $notificacao->montaTextoNotificacao(2,$data,$_REQUEST['language']);

                        $notificacao->criarNotificacao(2,$texto,$data->autor,$retorno['tarefa'][$i]['id'],$data->usoPeloPainel);

                        /**
                         * Tarefas Criadas Pelo App
                         */
                        if(!$data->usoPeloPainel){
                            do_action("post_create_task_by_app",array($retorno['tarefa'][$i]));
                        }
                    }


                }
            }








            echo json_encode($retorno);
            break;
        case 'quickUpdateTask':

                $tarefas = new tarefas();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma tarefa encontrada';

                $data = $_REQUEST['tarefa'];

                if($result = $tarefas->quickUpdateTask($_REQUEST['tarefa']['id'],$_REQUEST['tarefa'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Tarefa atualizada';
                    $retorno['result'] = $result;
                }

                echo json_encode($retorno);
            break;
		case 'eraseUmaTarefa':

                $rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';

                $tarefaGrupo = new TarefaGrupo();
                if($t['tarefasGrupo']['tarefas'] = $tarefaGrupo->apagarTarefaGrupoByID($_REQUEST['pullTracker']['tarefa'])){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Todas as Tarefa de Grupo excluídas com sucesso!';
                    $retorno['a'] = $a;
                }else{
                    if($a = $rota->eraseUmaTarefa($_REQUEST['pullTracker']['tarefa'])){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Tarefa excluída com sucesso!';
                        $retorno['a'] = $a;
                    }
                }
                echo json_encode($retorno);
            break;
		case 'forcarConcluir':

                $rota = new rota();
                $user = new user();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';


				if($a = $rota->forcarConcluir($_REQUEST['pullTracker']['tarefa'],$_REQUEST['pullTracker']['modificado'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Tarefa concluída com sucesso!';
                    $retorno['a'] = $a;

                    do_action("post_save_tarefa_iniciada" ,array($a['id']));
                    do_action("post_save_tarefa_concluida" ,array($a['id'],$user->getUserByRota($a['rota'])->id));
				}
                echo json_encode($retorno);
            break;

        case 'carregarEmpresaMetas':

            $empresas = new empresas();
            $retorno['status'] = 'false';

            if($metas = $empresas->carregarEmpresaMetas($_REQUEST['empresa'])){
                 $retorno['status'] = 'ok';
                 $retorno['metaEmpresa'] = $metas;
            }

            echo json_encode($retorno);
            break;

        case 'cadastrarMetaEmpresa':

            $empresas = new empresas();
            $retorno['status'] = 'false';

            if($empresas->update_empresa_meta((in_array($userJwt->empresa,['7']) ? $_REQUEST['empresa'] : $userJwt->empresa),$_REQUEST['metakey'],$_REQUEST['metavalue'])){

                if($_REQUEST['metakey'] == 'bloqueioCriacaoTarefaApp'){
                    $retorno['ids_usuarios_updatiados'] = $empresas->bloqueiaCriacaoTarefaTodosUsuarios((in_array($userJwt->empresa,['7']) ? $_REQUEST['empresa'] : $userJwt->empresa), $_REQUEST['metavalue']);
                }

                 $retorno['status'] = 'ok';
            }

            echo json_encode($retorno);
            break;


        case 'apagarMetaEmpresa':

            $retorno['status'] = 'Ok';
            $retorno['mensagem'] = 'Meta excluído com sucesso!';
            $empresas = new empresas();
            $empresas->remover_empresa_meta($_REQUEST['idMeta']);

            echo json_encode($retorno);

        break;


		case 'autenticarAgente':
				$user = new user();

				if($u = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token'])){
					$retorno['status'] = 'ok';

					$token = $_REQUEST['token'];

				//$user->updateUser($token,'token',$u['id']);

					$user->updateUser($_REQUEST['OS'],'os',$u['id']);
					$user->updateUser($_REQUEST['osVersion'],'osVersion',$u['id']);
					$user->updateUser($_REQUEST['screenSize'],'screenSize',$u['id']);
					$user->updateUser($_REQUEST['model'],'model',$u['id']);
                    $user->updateUser($_REQUEST['notid'],'notid',$u['id']);

                    $user->updateUser($_REQUEST['bateria'],'bateria',$u['id']);

					//$user->updateUser(date('Y-m-d H:i:s'),'lastseen',$u['id']);


					$u['token'] = $token;

					$retorno['user'] = $user->getUserByToken($_REQUEST['user'],$_REQUEST['token']);


				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
				}

				echo json_encode(arrayToUTF8($retorno));
			break;
		case 'autenticarPanel':
				$user = new user();

				if($u = $user->getUserByTokenPainel($_REQUEST['pullTracker']['id'],$_REQUEST['pullTracker']['token'])){
                    $retorno['status'] = 'ok';

                    //$number = rand();
					$token = md5($u['id'].date('YMD'));

					$user->updateUser($token,'tokenPainel',$u['id']);

					$u['tokenPainel'] = $token;

					$retorno['status'] = 'ok';


                    //$number = rand();
					$token = md5($u['id'].date('YMD'));


                    /**
                     * hotfix/TR-264 - Adicionando as roles do usuario no momento da autenticação;
                     */
                    $capability = new capability();
                    $u['roles'] = array();

                    if($usergroup = $capability->loadUserRoles($u['id'])){
                        $u['roles'] = $usergroup;
                    }

                    /**
                     * hotfix/PR-417 - Adicionando superAdmin nas roles do usuario para tratar igualmente no painel
                     */
                    if ($u['isSuperAdmin'] != 1) {
                        $u['roles'][] = 'AcessarPainelSuperAdmin';
                    }

                    $empresas = new empresas();
                    $u['menus'] = array();
                    if($menusDaEmpresa = $empresas->loadUserRoles($u['empresa'])){
                        $u['menus'] = $menusDaEmpresa;
                    }

					$u['tokenPainel'] = $token;
					$r = array(
								'nome'		=>	$u['nome'],
								'id'		=>	$u['id'],
                                'isAdmin'	=>	$u['isAdmin'],
                                'isSuperAdmin'	=>	$u['isSuperAdmin'],
                                'empresa'	=>	$u['empresa'],
                                'rotaEmpresa'   => $user->rotaDaEmpresa($u['empresa']),
								'token' =>	$u['tokenPainel'],
								'rota'		=>	$u['rota'],
                                'maxUsers'		=>	$u['maxUsers'],
                                'roles'		=> $u['roles'],
                                'menus' => $u['menus']
								);

					// buscando agentes



					$notificacoes = array();

					$tarefas = array();
					$retorno['user'] = $r;
                    $retorno['mensagem'] = 'Autenticação realizada com sucesso.';

				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
				}

				echo json_encode($retorno);

			break;
        case 'registerPosition':

                $geo = new geo();
                $user = new user();

                /*

                is_moving:false
                uuid:f699a49d-1667-4100-8d92-01a79945087e
                timestamp:1505342057363
                odometer:0
                sample:true
                coords[latitude]:-20.4617301
                coords[longitude]:-45.4286034
                coords[accuracy]:92.9
                coords[speed]:-1
                coords[heading]:-1
                coords[altitude]:-1
                activity[type]:still
                activity[confidence]:100
                battery[is_charging]:true
                battery[level]:0.42
                location_type:background
                bateria:42
                pullTracker[id]:161
                pullTracker[act]:registerPosition
                pullTracker[status]:ok
                idOnApp:23
                user:161

                */

                if(!$_POST['longitude']||!$_POST['latitude']){
                    $_POST['longitude'] = $_POST['coords']['longitude'];
                    $_POST['latitude'] = $_POST['coords']['latitude'];
                    $_POST['accuracy'] =    $_POST['coords']['accuracy'];
                    $_POST['speed'] =   $_POST['coords']['speed'];
                    $_POST['heading'] =     $_POST['coords']['heading'];
                    $_POST['altitude'] =    $_POST['coords']['altitude'];
                }

                if($_POST['location_type']=='realtime' || $_POST['location_type']=='background' ){
                    $user->updateUser($_POST['longitude'],'longitude',$_POST['pullTracker']['id']);
                    $user->updateUser($_POST['latitude'],'latitude',$_POST['pullTracker']['id']);
                    $user->updateUser(date('Y-m-d H:i:s'),'lastseen',$_POST['pullTracker']['id']);
                    $user->updateUser($_POST['bateria'],'bateria',$_POST['pullTracker']['id']);
                }

                $retorno = $geo->registerPosition($_POST['pullTracker']['id'],$_POST['latitude'],$_POST['longitude'],$_POST['timestamp'],json_encode($_POST));

                //$retorno["status"] = "ok";

                if($_POST['location_type'] == "initRastreamento"){

                    $texto = 'Iniciado as '.date('H:i:s j-m-Y', ($_POST['timestamp']/1000)).'';
                    $notificacao = new notificacao();
                    $notificacao->criarNotificacao(8,$texto,$_POST['pullTracker']['id'],1);
                    $registroNotificacao['notificacao'] = "Notificação Gerada com Sucesso!";
                }
                //echo $registroNotificacao;

                $db->desconecta();

                echo json_encode($retorno);


            break;

        case 'deletePosition':

                $geo = new geo();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Dados incorretos.';

                if($_POST['pullTracker']['id']){

                    if($geo->deletePosition($_POST['pullTracker']['id'])){
                        $retorno['status'] = 'true';
                        $retorno['mensagem'] = 'Ponto Deletado.';
                    }
                }

                echo json_encode(arrayToUTF8($retorno));
                $db->desconecta();
                die();


            break;

		case 'registerPositionB':

				$geo = new geo();
				$user = new user();

				$retorno['_GET'] = $_GET;

				$retorno['dia'] = date('Y-m-j', ($_GET['timestamp']/1000));

				$retorno['track']	=	$geo->getTodayUserTrack($_REQUEST['user'],$retorno['dia'] );

				echo '<pre>'.print_r($retorno,true).'</pre>';
                $db->desconecta();

				die();

			break;
		case 'registrarnNotID':
				$user = new user();
				$user->updateUser($_REQUEST['pullTracker']['notID'],'notid',$_REQUEST['pullTracker']['user']);
				//$user->updateUser($_REQUEST['pullTracker']['OS'],'os',$_REQUEST['pullTracker']['user']);
				//mail('glaysonramos@gmail.com','meep',$_REQUEST['pullTracker']['notID']);
			break;
		case 'novaRota':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
					$rota = new rota();
					if($id = $rota->criarRota($_POST['pullTracker']['responsavel'])){
						$retorno['status'] = 'ok';
						$retorno['id'] = $id;
						echo json_encode(arrayToUTF8($retorno));
                        $db->desconecta();
						die();
					}
				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Não foi possível criar uma nova rota';
				echo json_encode(arrayToUTF8($retorno));
            $db->desconecta();
				die();
			break;
		case 'reportarErro':
				if(mail('glaysonramos@gmail.com','[Tracker] Erro Reportado',json_encode($_POST))){
					$retorno['status'] = 'Ok';
					$retorno['mensagem'] = 'Obrigado! Mensagem enviada com sucesso!';

				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Não foi possível enviar sua mensagem. Por favor, tente novamente mais tarde';
				}
				echo json_encode(arrayToUTF8($retorno));
            $db->desconecta();
				die();
			break;
		case 'novaTarefa':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
					$rota = new rota();

					if($_POST['pullTracker']['idDaTarefa']!=''){
						$id = $_POST['pullTracker']['idDaTarefa'];
						$ms = 'Uma de suas tarefas foi modificada!';
					}else{
						if($id = $rota->criarTarefa($_POST['pullTracker']['rota'])){
							$ms = 'Você tem uma nova tarefa!';
						}else{
							$id = false;
						}
					}

					if($id){

						$rota->editarTarefa($id,'endereco',$_POST['endereco']);
						$rota->editarTarefa($id,'comentario',$_POST['comentario']);

						if($_POST['agendada']!=''){
							$rota->editarTarefa($id,'livre','1');
							$rota->editarTarefa($id,'agendada',$_POST['dataDaTarefa'].' '.$_POST['horaDaTarefa']);
						}
						if($_POST['dataDoEncerramento']!=''){
							$rota->editarTarefa($id,'estimado',$_POST['dataDoEncerramento'].' '.$_POST['horaDoEncerramento']);
						}

						$rota->editarTarefa($id,'latitude',$_POST['latitude']);
						$rota->editarTarefa($id,'longitude',$_POST['longitude']);
						//$rota->editarTarefa($id,'estimado',$_POST['tempoFindPoint']);

						$retorno['status'] = 'ok';
						$retorno['id'] = $id;

						$retorno['ntttt'] = $rota->notificar($_POST['pullTracker']['user'],'Alerta de tarefas',$ms,'tarefa');

						echo json_encode(arrayToUTF8($retorno));
                        $db->desconecta();
						die();
					}
				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Não foi possível criar uma nova rota';
				echo json_encode(arrayToUTF8($retorno));
            $db->desconecta();
				die();
			break;



		case 'tellServer':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
            if(strcmp($_POST['pullTracker']['status'],"Intervalo de almoço/café") == 0){
                    $texto = 'Iniciado as '.date('H:i:s j-m-Y').'';
                    $notificacao = new notificacao();
                    $notificacao->criarNotificacao(9,$texto,$_POST['pullTracker']['user'],1);
            }else

            if(strcmp($_POST['pullTracker']['status'],"Fim da jornada de trabalho") == 0){

                $texto = 'Encerrou as '.date('H:i:s j-m-Y').'';
                $notificacao = new notificacao();
                $notificacao->criarNotificacao(10,$texto,$_POST['pullTracker']['user'],1);
            }
            $geo = new geo();
            echo $geo->tellServer($_POST['pullTracker']['user'],$_POST['pullTracker']['status']);

            $db->desconecta();
				die();
			break;
		case 'responderChat':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
                    $geo = new geo();
					$mensagem = $geo->responderChat($_POST['chatUser'],$_POST['chatAdmin'],$_POST['mensagem']);

            //Adicionando uma notificação para a Mensagem Enviada
                if($mensagem){
                    echo json_encode($mensagem);
                    $notificacao = new notificacao();
                    //Verifica se a mensagem está vindo de um administrador ou não
                    if(($_POST['chatAdmin'] == $_POST['chatUser'])||($_REQUEST['pullTracker']['usoPeloPainel'] == 'false')){
                        //Caso a mensagem tenha sido inserida no banco da dados corretamente, ela insere uma notificação referente a mensagem criada
                        //$texto = ' disse : '.$_POST['mensagem'];
                        $notificacao->criarNotificacao(1,$_POST['mensagem'],$_POST['chatUser'],$mensagem['msgId'],$_REQUEST['pullTracker']['usoPeloPainel']);
                    }
                }else{
                    echo json_encode(array('mensagem'=>'Ocorreu um erro ao enviar a mensagem','status'=>'notok'));
                }
            $db->desconecta();
				die();
			break;
		case 'mensagemEntregue':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
					$geo = new geo();
					echo json_encode($geo->entregarChat($_POST['id'],$_POST['entregue']));
            $db->desconecta();
				die();
			break;
		case 'mensagemLida':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
					$geo = new geo();
					echo json_encode($geo->lerChat($_POST['id'],$_POST['lida']));
            $db->desconecta();
				die();
			break;
		case 'bosslida':
			//	mail('glaysonramos@gmail.com','teste',print_r($_POST,true));
					$geo = new geo();
					echo json_encode($geo->bosslida($_POST['id'],$_POST['lida']));
                    $db->desconecta();
				die();
            break;
        case 'marcarlidas':
					$geo = new geo();
					echo json_encode($geo->marcarlidas($_POST['user'],$_POST['lida']));
			break;

		/*case 'getPositionForUser':
					$geo = new geo();
					echo json_encode($geo->getPositionForUser($_REQUEST['pullTracker']['user']));
                    $db->desconecta();
				die();
			break;*/
		case 'trackUsers':
            echo "trackUsers";
					$geo = new geo();
					echo json_encode($geo->trackUsers($userJwt->empresa));
                    $db->desconecta();
				die();
			break;
		case 'getPositionHistoryForUser':
					$geo = new geo();

					$retorno = array();
					$retorno = $geo->getPositionHistoryForUser($_REQUEST['pullTracker']['user']);
					echo json_encode(arrayToUTF8($retorno));
                    $db->desconecta();
				die();
			break;
		case 'getHistoryForUserOnDate':
					$geo = new geo();


					$retorno = array();
					$retorno = $geo->getHistoryForUserOnDate($_REQUEST['pullTracker']['user'],$_REQUEST['pullTracker']['dia']);
					echo json_encode(arrayToUTF8($retorno));
                    $db->desconecta();
				die();
			break;
		case 'loadchat':
					$geo = new geo();
					$retorno = array();
					$retorno = $geo->loadchat($_REQUEST['pullTracker']['user'],$_REQUEST['pullTracker']['last']);
					echo json_encode($retorno);
                    $db->desconecta();
				die();
			break;
		case 'entregarChat':
					$geo = new geo();
					$retorno = array();
					$retorno = $geo->entregarChat($_REQUEST['pullTracker']['id'],$_REQUEST['pullTracker']['time']);
					echo json_encode(arrayToUTF8($retorno));
                    $db->desconecta();
				die();
			break;
		case 'lerChat':
					$geo = new geo();
					$retorno = array();
					$retorno = $geo->lerChat($_REQUEST['pullTracker']['id'],$_REQUEST['pullTracker']['time']);
					echo json_encode(arrayToUTF8($retorno));
                    $db->desconecta();
				die();
			break;



		case 'login':

				$user = new user();

				if($u = $user->getUserBySenha($_REQUEST['id'],$_REQUEST['loginEmail'],$_REQUEST['loginSenha'])){
					$retorno['status'] = 'ok';


					$token = md5($u['id'].date('YMD'));

					$user->updateUser($token,'token',$u['id']);
					$u['tokenPainel'] = $token;
					$r = array(
								'nome'		=>	$u['nome'],
								'id'		=>	$u['id'],
								'isAdmin'	=>	$u['isAdmin'],
                                'empresa'	=>	$u['empresa'],
                                'rotaEmpresa'   => $user->rotaDaEmpresa($u['empresa']),
								'token'		=>	$u['tokenPainel']
								);

					// buscando agentes



					$notificacoes = array();

					$tarefas = array();
					$retorno['user'] = $r;




				}else{




					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
				}

				echo json_encode(arrayToUTF8($retorno));

			break;

        case 'hyperlogin':

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Dados incorretos.';

				$user = new user();

                $tipo = $user->userAdminData($_REQUEST['loginEmail']);

                if(!$tipo['valido']){
                    $retorno['status'] = 'false';


                    if($tipo['tipo'] == 'agente'){
                        $retorno['mensagem'] = 'Este agente não tem autorização para usar o painel';
                    }
                    if($tipo['tipo'] == 'inexistente'){
                        $retorno['mensagem'] = 'E-mail desconhecido';

                    }

                    if($tipo['tipo'] == 'desativado'){

                        $retorno['mensagem'] = 'Administrador Desativado';

                    }

                    if($tipo['tipo'] == 'superAdminDesativado'){

                        $retorno['mensagem'] = 'Super Administrador Desativado';

                    }

                    echo json_encode(arrayToUTF8($retorno));
                    $db->desconecta();
                    die();
                }

				if($u = $user->getUserBySenha($tipo['id'],$_REQUEST['loginEmail'],$_REQUEST['loginSenha'])){






					$retorno['status'] = 'ok';


					$token = md5($u['id'].date('YMD'));

					$user->updateUser($token,'tokenPainel',$u['id']);
					$u['token'] = $token;
					$r = array(
								'nome'		=>	$u['nome'],
								'id'		=>	$u['id'],
                                'isAdmin'	=>	$u['isAdmin'],
                                'isSuperAdmin'	=>	$u['isSuperAdmin'],
								'empresa'	=>	$u['empresa'],
								'token'		=>	$u['token']
								);

					// buscando agentes

                    //Buscando a Linguagem do Painel Definida pela Empresa.
                    $empresa = new Empresas();
                    $r['language'] = $user->get_user_meta($u['id'],'language');
                    if(!$r['language']){
                        $r['language'] = $empresa->get_empresa_meta($u['empresa'],'language');
                        if(!$r['language']){
                            $r['language'] = '';
                        }
                    }

                    /**
                     * Task-402 - Bloquear a criação de tarefas no APP
                     *      Enviando os metados da empresa ao Realizar o Login do Usuario no Painel
                     */
                    $r['metaDadosEmpresa'] = $empresa->getMetaDadosEmpresa($u['empresa']);


					$notificacoes = array();

					$tarefas = array();
					$retorno['user'] = $r;

					   echo json_encode($retorno);
                    $db->desconecta();
					die();

				}else{
					$retorno['status'] = 'false';
					$retorno['mensagem'] = 'Dados incorretos.';
                }

				echo json_encode(arrayToUTF8($retorno));

			break;

			// mobile relogin
		case 'reLogin':
				$senha	=	$_REQUEST['pwd'];
				$nome		=	$_REQUEST['name'];
				$plataforma = $_REQUEST['plataforma'];
				$notificationID = $_REQUEST['notID'];

				$xml = new SimpleXMLElement('<retorno></retorno>');

				$user = new user();
				$eventos = new eventos();

				if($dono = $user->getUserByPair($nome,$senha)){

					$ses = $session->mobileSession($dono['id']);

					$user->editarUser($dono['id'],'session',$ses);
					$user->editarUser($dono['id'],'toReset','');

				//	$user->setNotId($dono['id'],$dono['email'],$notificationID,$plataforma);

					$xml->addChild('status','true');

					$xml->addChild('email',$dono['email']);
					$xml->addChild('token',$ses);

					// pegar o nome

					$xml->addChild('nome',$dono['nome']);

					// pegar foto

					if($dono['foto']!=''){

						$xml->addChild('foto',$dono['foto']);

					}else{

						$xml->addChild('foto',"false");

					}
					$xml->addChild('nascimento',$dono['nascimento']);
					$xml->addChild('mensagem','Logado com sucesso.');

					if($us = $db->existe('email','users',$nome," ",true)){

						$session->setUser($us);
					}

				}else{
					$xml->addChild('status','false');
					$xml->addChild('idUser','');
					$xml->addChild('tempo','2000');
					$xml->addChild('mensagem','Login Incorreto');

				}
				echo $xml->asXML();
			break;

					// notReg

			// chegar e-mail
		case 'mobileCheckMail':
				//$senha	=	'>'.md5($_REQUEST['pwd']).'<';
				$nome		=	$_REQUEST['email'];
				$xml = new SimpleXMLElement('<retorno></retorno>');

				$user = new user();


				if($us = $db->existe('email','users',$nome,"",true)){


					//$dados = mysqli_fetch_array($us);

				//	$ses = $session->mobileSession($dados['id']);

				//	$user->editarUser($dados['id'],'session',$ses);
				//	$user->editarUser($dados['id'],'toReset','');
					//print_r(mysqli_fetch_array($us));
					$xml->addChild('status','true');
				//	$xml->addChild('email',$dados['email']);
					$xml->addChild('tempo','2000');

				//	$xml->addChild('token',$ses);

					$xml->addChild('mensagem','existe.');

				//	$session->setUser($us);

				}else{
					$xml->addChild('status','false');
					$xml->addChild('idUser','');
					$xml->addChild('tempo','2000');
					$xml->addChild('mensagem','novo');

					//$session->setUser($us);


				}
				echo $xml->asXML();
			break;
			// mobile resenha
		case 'mobileReSenha':
				$nome		=	$_REQUEST['name'];
				$xml = new SimpleXMLElement('<retorno></retorno>');

				$user = new user();

				if($us = $db->existe('email','users',$nome,'',true)){

					$string = '';

					for($i=0;$i<8; $i++){
						$string .= rand(0,9);
					}

					$msg = '<html>
							  <head>
								 <title>Recadastramento de senha</title>
							  </head>
							  <body>
								<h2>Re-cadastramento de senha</h2>
								<p>Caro usuário,<br />
									Foi solicitada uma nova senha para sua conta do <projeto>. Se você realmente realizou essa solicitação, uma tela de recadastramento de senha está sendo exibida e solicita que você digite o código a seguir:
								</p>
								<h3>'.$string.'</h3>
								<p>Caso você não tenha solicitado a recuperação de senha, basta ignorar essa mensagem e, no seu próximo login, o código em questão será inutilizado.</p>
								</body>
								</html>';

					// multiple recipients (note the commas)
					$to = $nome;

// subject
					$subject = "Código para recadastramento de senha do <Projeto>";



					// To send HTML mail, the Content-type header must be set
					$headers = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

// send email
					mail($to, $subject, $msg, $headers);

				$dados = mysqli_fetch_array($us);

				$user->editarUser($dados['id'],'toReset',$string);

					//print_r(mysqli_fetch_array($us));
					$xml->addChild('status','true');
					$xml->addChild('toReset',$string);
					$xml->addChild('tempo','2000');
					$xml->addChild('mensagem','user existe');


					//$session->setUser($us);

				}else{
					$xml->addChild('status','false');
					$xml->addChild('idUser','');
					$xml->addChild('tempo','2000');
					$xml->addChild('mensagem','Este usuário não existe!');

					//$session->setUser($us);


				}
				echo $xml->asXML();
			break;


		case 'mudarsenha':
				$user = new user();
				$json = json_decode($_POST['json']);

				$email = $json->email;
				$codigo = $json->codigo;
				$senha = $json->senha;

				$xml2 = new SimpleXMLElement('<retorno></retorno>');

				if($us = $db->existe('email','users',$email," AND toReset='".$codigo."' ",true)){
					$dados = mysqli_fetch_array($us);

					$user->editarUser($dados['id'],'senha',$senha);
					$user->editarUser($dados['id'],'toReset','');

					$xml2->addChild('email',$email);
					$xml2->addChild('status','true');
					$xml2->addChild('tempo','2000');
					$xml2->addChild('mensagem','Senha alterada com sucesso!');
				}else{
					$xml2->addChild('status','false');
					$xml2->addChild('tempo','2000');
					$xml2->addChild('mensagem','Dados incorretos');
				}

				echo $xml2->asXML();
			break;
			// edit user


        case 'salvarMetaDadosUsuario':

                $user = new user();

                $user->update_user_meta($_REQUEST['id'],'PrecisaoMetros',$_REQUEST['PrecisaoMetros']);
                $user->update_user_meta($_REQUEST['id'],'PrecisaoSegundos',$_REQUEST['PrecisaoSegundos']);
                $user->update_user_meta($_REQUEST['id'],'Bateria',$_REQUEST['Bateria']);
                $user->update_user_meta($_REQUEST['id'],'Rastreamento',$_REQUEST['Rastreamento']);


        break;


		case 'criarUser':
				$user = new user();
				$rota = new rota();
                $capability = new capability();
                $grupo = new Grupo();
                $email = new email();

                $retorno = array();
                $emailBV = false;
                $repetido = false;
				if($_REQUEST['email']!=''){
					if($user->EmailRepetido($_REQUEST['email'],$_REQUEST['editarId'])){
						$retorno['status'] = 'false';
						$retorno['mensagem'][] = 'Este e-mail já está cadastrado em nosso sistema.';
                        $repetido = true;
                    }
				}
                // if($_REQUEST['editarId']!='' || !$d)
                if(!$repetido){

					$retorno['status'] = 'ok';

						if($_REQUEST['editarId']){

							$id = $_REQUEST['editarId'];
							$retorno['mensagem'] = 'Agente atualizado com sucesso!';

						}else{

                            if(!$user->podeCriarUsuario($userJwt->empresa)){

                                $retorno['status'] = 'false';
                                $retorno['mensagem'][] = 'Número máximo de agentes atingido.';


                            }else{
                                if($id = $user->criarUser($_REQUEST['name'])){
                                    $retorno['mensagem'] = 'Agente cadastrado com sucesso!';
                                    $emailBV = true;
                                }
                            }
						}

						if($id){

                            if($_REQUEST['isAdmin'] == 'on'){
								$isAdmin = 1;
							}else{
								$isAdmin = 0;
                            }

                            if($_REQUEST['senhaToPut']!=''){
								$senha = md5($_REQUEST['senhaToPut']);
							} else {
                                $senha = '';
                            }

                            $token = md5($id.date('YMD'));

                            $user->updateUserData($_REQUEST['name'],$isAdmin,$user->gerarUserCode($id),$_REQUEST['cellPhone'],$senha,$_REQUEST['veiculo'],$token,$id);

                            /**
                             * hotfix/TR-258 - Permitindo deixar o campo Email vazio numa edição;
                             */
							if(($_REQUEST['email']!='') || ($_REQUEST['email'] == '' && $_REQUEST['editarId'])){
								$user->updateUser($_REQUEST['email'],'email',$id);
							}

                            $user->update_user_meta($id,'cor',$_REQUEST['cor']);
							/*
                            if($_REQUEST['maxUsers']!=''){
								$user->updateUser($_REQUEST['maxUsers'],'maxUsers',$id);
							}
                            */


							if($_REQUEST['iscliente']=='true'){
								$user->updateUser('1','isEmpresa',$id);
							}else{
								$user->updateUser($userJwt->empresa,'empresa',$id);
                            }

                            $capability->updateUserPerfil($id, $_REQUEST['perfisUser']);
                            $grupo->updateUserGrupo($id, $_REQUEST['admGrps'], $_REQUEST['admGrpsUncheck']);

                            if($_REQUEST['idFormulario']){
                                $user->update_user_meta($id, "__formularios", ($_REQUEST['idFormulario']) ? implode(",",$_REQUEST['idFormulario']) : "" );
                            }else{
                                $user->delete_user_meta($id, "__formularios" );
                            }

                            $metasDados = array(
                                'matricula',
                                'IniciarTarefa',
                                'DistanciaChegada',
                                'PrecisaoMetros',
                                'PrecisaoSegundos',
                                'Bateria',
                                'editarTarefa',
                                'Rastreamento',
                                'bloqueioCriacaoTarefaApp',
                                'pis',
                                'numeroInspecoes'
                            );

                            foreach($metasDados as $meta){
                                if($_REQUEST[$meta]){
                                    $user->update_user_meta($id, $meta, $_REQUEST[$meta] );
                                }else{
                                    $user->delete_user_meta($id, $meta );
                                }
                            }

                            // todos campos que começarem com #__meta serão considerados metas dinamicos
                            $idx = '#__meta';
                            foreach($_REQUEST as $req => $valor){
                                if(substr($req,0,strlen($idx))==$idx){
                                    $key = substr($req,strlen($idx));
                                    $user->update_user_meta($id, $key, $valor );
                                }
                            }
                        }

                        if(($_REQUEST['emailBV'] == 'on') || ($_REQUEST['emailBV']!== null)) {
                            $nomeEmpresa = mysqli_fetch_object($email->getOne('nome',$userJwt->empresa,'empresas'));
                            $code = mysqli_fetch_object($email->getOne('code',$id,'users'));
                            $listaDeParametros = array(
                              'nome_usuario' => $_REQUEST['name'],
                               'nome_empresa' => $nomeEmpresa->nome,
                               'code' => $code->code,
                               'mail' => $_REQUEST['email'],
                                'password'=> $_REQUEST['senhaToPut'],
                            );
                            if($emailBV){
                                $urlModeloEmail = './phpmailer/boasVindas.php';
                                $layout = file_get_contents($urlModeloEmail);
                                $listaDeMarcacoes = $email->acertaParamentros($listaDeParametros);
                                $layout = $email->substituiMarcacoesTexto($layout,$listaDeMarcacoes);
                                $email->configEmailTrackerup->Username = 'atendimento@trackerup.com.br';
                                $copia = array(
                                    'joice@trackerup.com.br'=> 'Joice Laureano'
                                );
                                $email->sendEmail($email->configEmailTrackerup,$_REQUEST['email'],'Bem Vindo ao Tracker Up!',$layout,'',$copia);
                           }
                        }

                        $retorno['rota'] = $rota->criarRota($id);
						$retorno['token'] = $token;
						$retorno['user'] = $id;
						$retorno['code'] = $user->gerarUserCode($id);

				}

				echo json_encode($retorno);

            break;

        case 'notificaAgenteDesativado':
                $empresas = new empresas();
                $email = new email();
                $notificacao = new notificacao();
                $user =	$_REQUEST['user'];
                $agente = $_REQUEST['agente'];
                $mail = $_REQUEST['email'];
                $data =	$_REQUEST['data'];
                $idioma = $_REQUEST['idioma'];
                $empresa = $userJwt->empresa;

                if($idioma=="es-ES"){
                    $mensagem = "Estimado se&ntilde;or ".$agente." le informamos que su perfil de agente TrackerUp ha sido desactivado por ".$user." en la fecha: ".$data;
                    $assunto = "Su perfil de agente TrackerUp se ha deshabilitado";
                }
                elseif($idioma=="en-US"){
                    $mensagem = "Dear Mr. ".$agente." we inform that your TrackerUp agent profile has been disabled by ".$user." on date: " .$data;
                    $assunto = "Your TrackerUp agent profile has been disabled";
                }
                else{
                    $mensagem = "Prezado(a) Sr(a). ".$agente." informamos que seu perfil de agente TrackerUp foi desativado por ".$user." na data: ". $data;
                    $assunto = "Seu perfil de agente TrackerUp foi desativado";
                }

                $listaDeParametros =  ['texto' => "$mensagem" ];
                $urlModeloEmail = 'phpmailer/layout.php';
                if($tema = $empresas->get_empresa_meta($empresa,'_email_theme')){
                    $urlModeloEmail = $tema;
                }
                $layout = file_get_contents($urlModeloEmail);
                $listaDeMarcacoes = $email->acertaParamentros($listaDeParametros, '$');
                $layout = $email->substituiMarcacoesTexto($layout,$listaDeMarcacoes);
                $email->configEmailTrackerup->Username = $user;
                // $email->sendEmail($email->configEmailTrackerup,$mail,$assunto,$layout);
                $notificacao->alertasEmail($mail,utf8_decode('<html>'),$assunto,$layout);
                if($idioma=="es-ES"){
                    $retorno['mensagem'] = "¡Email enviado con éxito!";
                }
                elseif($idioma=="en-US"){
                    $retorno['mensagem'] = "Email successfully sent!";
                }
                else{
                    $retorno['mensagem'] = "Email enviado com sucesso!";
                }
                echo json_encode($retorno);
            break;

        case 'loadSuperAdmin':

            $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum superAdmin encontrado !';

            if($superAdmin = $empresas->listarSuperAdmin($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'SuperAdmin encontrado!';
                $retorno['superAdmin'] = $superAdmin;
            }
            echo json_encode(($retorno));
        break;

		case 'apagarUser':


				$user = new user();
				$retorno = array();

				if($user->apagarUser($_REQUEST['pullTracker']['id'])){
					$retorno['status'] = 'ok';
					$retorno['id'] = $_REQUEST['pullTracker']['id'];
					$retorno['mensagem'] = 'Usuário apagado com sucesso!';
				}else{
					$retorno['status'] = 'false';
					$retorno['id'] = 0;
					$retorno['mensagem'] = 'Não foi possível completar sua solicitação.';
				}

				echo json_encode(arrayToUTF8($retorno));
			break;

        case 'update_user_meta' :
                $user = new user();
                $retorno = [];
                if($retorno['updated_meta'] = $user->update_user_meta($_REQUEST['pullTracker']['id'],$_REQUEST['pullTracker']['meta_key'],$_REQUEST['pullTracker']  ['meta_value'])){
                    $retorno['mensagem'] = 'Meta Dado atualizado com sucesso!';
                }

                echo json_encode($retorno);

            break;

        case 'delete_user_meta' :
                $user = new user();

                echo json_encode($user->delete_user_meta($_REQUEST['pullTracker']['id'],
                                                           $_REQUEST['pullTracker']['meta_key']));

            break;
        case 'get_user_meta' :
                $user = new user();
                return json_encode($user->get_user_meta($_REQUEST['pullTracker']['id'],
                                                        $_REQUEST['pullTracker']['meta_key']));
            break;

        case 'getMetaDadosAgente':
                $user = new user();
                $empresas = new empresas();
                $empresas->update_empresa_meta(7,'__default_user_meta_fields',json_encode(['tegma_login'=>'Login na Tegma']));
				$retorno = array();

				if($metaDadosAgentes = $user->getMetaDadosAgente($_REQUEST['id'])){
					$retorno['status'] = 'ok';
					$retorno['id'] = $_REQUEST['id'];
                    $retorno['metaDadosAgentes'] = $metaDadosAgentes['meta_user'];
                    $retorno['metaDadosEmpresa'] = $metaDadosAgentes['meta_empresa'];
                    $retorno['mensagem'] = 'Dados do Agente Coletados com sucesso!';

				}else{
					$retorno['status'] = 'false';
					$retorno['id'] = 0;
					$retorno['mensagem'] = 'Não foi possível coletar Dados Para esse Agente.';
                }

				echo json_encode($retorno);
			break;

		case 'apagarCliente':

				$user = new user();
				$retorno = array();

				if($user->apagarCliente($_REQUEST['pullTracker']['id'])){
					$retorno['status'] = 'ok';
					$retorno['id'] = $_REQUEST['pullTracker']['id'];
					$retorno['mensagem'] = 'Cliente e usuários apagados com sucesso!';
				}else{
					$retorno['status'] = 'false';
					$retorno['id'] = 0;
					$retorno['mensagem'] = 'Não foi possível completar sua solicitação.';
				}

				echo json_encode(($retorno));
			break;


		case 'loadUsers':

				$user = new user();

				$retorno['status'] = 'false';
				$retorno['mensagem'] .= '';
				if($us = $user->loadUsers($_REQUEST['pullTracker']['page'],$_REQUEST['pullTracker']['busca'],'ASC','nome',$userJwt->empresa)){
					$retorno['status'] = 'ok';
					$retorno['users'] = $us;
				}

				echo json_encode(($retorno));
			break;


        case 'desativarAgente':

            $user = new user();

            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'ERRO - Não Foi Possivel Desativar Agente';
            if($u = $user->getUserByTokenPainel($_REQUEST['admin'],$_REQUEST['token'])){
                $user->update('0','ativo','users',$_REQUEST['userParaDesativar']);
                $user->update($_REQUEST['data'],'dataDeDesativacao','users',$_REQUEST['userParaDesativar']);
                $retorno['mensagem'] = 'Agente desativado com sucesso !';
                $retorno['status'] = 'ok';
            }



            echo json_encode(($retorno));
            break;

        case 'resincRespostasAgente':

            $user = new user();

            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'ERRO - Não Foi Possivel Desativar Agente';
            if($userJwt->tokenPainel == $_REQUEST['token']){
                $user->notificarAgenteParaSincronizacaoRespostas($_REQUEST['userParaSincronizar']);
                $retorno['mensagem'] = 'A sincronização ocorrerá na proxima vez que o agente reiniciar o aplicativo!';
                $retorno['status'] = 'ok';
            }

            echo json_encode($retorno);
            break;

         case 'statusSuperAdmin':

            $users = new user();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma resposta encontrada !';

            if($status = $users->statusSuperAdmin($_REQUEST['pullTracker']['id'])){
                $retorno['status'] = 'ok';
                $retorno['statusSuperAdmin'] = $status;
            }

            echo json_encode(($retorno));

        break;


        case 'desativarSuperAdmin':

            $retorno['status'] = 'ok';
            $users = new user();
            $users->updateUser('0','ativo',$_REQUEST['id']);
            $retorno['mensagem'] .= 'Super administrador desativado com sucesso !';

            echo json_encode(($retorno));

         break;

         case 'ativarSuperAdmin':

            $retorno['status'] = 'ok';
            $users = new user();
            $users->updateUser('1','ativo',$_REQUEST['id']);
            $retorno['mensagem'] .= 'Super administrador ativado com sucesso !';

            echo json_encode(($retorno));

        break;

        case 'statusLocal':

            $local = new local();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma resposta encontrada !';

            if($status = $local->statusLocal($_REQUEST['pullTracker']['id'])){
                $retorno['status'] = 'ok';
                $retorno['statusLocal'] = $status;
            }

            echo json_encode(($retorno));

        break;


       case 'desativarLocal':
            $retorno['status'] = 'ok';
            $local = new local();
            $local->update('0','status','locais',$_REQUEST['id']);
            $retorno['mensagem'] .= 'Local desativado com sucesso !';

            echo json_encode(($retorno));

       break;

       case 'ativarLocal':
            $retorno['status'] = 'ok';
            $local = new local();
            $local->update('1','status','locais',$_REQUEST['id']);
            $retorno['mensagem'] .= 'Local ativado com sucesso !';

            echo json_encode(($retorno));

       break;

    case 'ativarAgenteDesativado':

            $user = new user();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'ERRO - Não foi Possível Ativar Este Agente';

            if($user->EmailRepetido($_REQUEST['email'],$_REQUEST['userParaAtivar'])){
                $retorno['status'] = 'Email Repetido';
                $retorno['mensagem'] = 'Este e-mail já está cadastrado em nosso sistema.';
            }
            else{
                if($u = $user->getUserByTokenPainel($_REQUEST['admin'],$_REQUEST['token'])){
                    $user->update('1','ativo','users',$_REQUEST['userParaAtivar']);
                    $retorno['mensagem'] = 'Agente ativado com sucesso !';
                    $retorno['status'] = 'ok';
                }
            }

            echo json_encode(($retorno));
            break;





		case 'loadAgentes':

				$user = new user();
                $retorno = [];
				$retorno['status'] = 'false';
				$retorno['mensagem'] = '';
				if($us = $user->loadAgentes($userJwt->empresa,$_REQUEST['pullTracker']['dataAtiva'], $_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['pullTracker']['user'])){
					$retorno['status'] = 'ok';
					$retorno['users'] = $us;
				}
                 $retorno['grupos'] = false;
                $retorno['grupoAtivo'] = $_REQUEST['pullTracker']['grupoAtivo'];

                /*
                if($grupos = $user->getGrupos($userJwt->empresa,$_REQUEST['pullTracker']['user'],@$_REQUEST['superAdmin'])){
                    $retorno['grupos'] = $grupos;
                }
                */

				echo json_encode(($retorno));
            break;

        case 'loadAgenteUnico':

            $user = new user();
            $retorno = [];
            $retorno['status'] = 'false';
            $retorno['mensagem'] = '';
            if($us = $user->loadAgenteUnico($userJwt->empresa,$_REQUEST['pullTracker']['user'])){
                $retorno['status'] = 'ok';
                $retorno['users'] = $us;
            }
             $retorno['grupos'] = false;
            $retorno['grupoAtivo'] = $_REQUEST['pullTracker']['grupoAtivo'];

            if($grupos = $user->getGrupos($userJwt->empresa,$_REQUEST['pullTracker']['user'],@$_REQUEST['superAdmin'])){
                $retorno['grupos'] = $grupos;
            }

            echo json_encode($retorno);
        break;

        case 'loadAgentesDeOutraEmpresa':

                $user = new user();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = '';
                if(mysqli_fetch_array($user->getOne('empresa',$_REQUEST['pullTracker']['user'],'users'))[0] == 7){
                    if($us = $user->loadAgentesDeOutraEmpresa($_REQUEST['pullTracker']['empresa'])){
                        $retorno['status'] = 'ok';
                        $retorno['users'] = $us;
                        $retorno['mensagem'] = 'Usuarios Encontrados!';
                    }
                }else{
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Usuario Não Autenticado!';
                }


                echo json_encode(($retorno));
            break;

			case 'loadClientes':

				$user = new user();

				$retorno['status'] = 'false';
				$retorno['mensagem'] .= '';
				if($us = $user->loadClientes($_REQUEST['pullTracker']['page'],$_REQUEST['pullTracker']['busca'],$_REQUEST['pullTracker']['busca'],'nome','ASC',$userJwt->empresa)){
					$retorno['status'] = 'ok';
					$retorno['users'] = $us;
				}

				echo json_encode(arrayToUTF8($retorno));
			break;


		case 'loadRotas':

				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';

				if($retorno['rotas'] = $rota->loadRotas($_REQUEST['pullTracker']['page'],$_REQUEST['pullTracker']['busca'],'ASC','U.nome',$userJwt->empresa)){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}


				echo json_encode(arrayToUTF8($retorno));
            break;

        case 'todosMenus':
                $empresas = new empresas();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum menu encontrado';

                if($menus = $empresas->todosMenus()){
                    $retorno['status'] = 'ok';
                    $retorno['menus'] = $menus;
                    $retorno['mensagem'] = 'Menus encontrado';

                }

                if($metas = $empresas->tipoMetaEmpresa()){
                    $retorno['tipoMetas'] = $metas;
               }

                echo json_encode($retorno);

            break;

		case 'loadTarefas':
				//print_r($_REQUEST);
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';



				if($retorno['tarefas'] = $rota->loadTarefas($_REQUEST['pullTracker']['rota'],$_REQUEST['pullTracker']['data'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}


				echo json_encode(arrayToUTF8($retorno));
            break;
        case 'buscarTarefasDoDiaNaTimeline':
                    $tarefas = new tarefas();
                    $rota = new rota();

                    $rota->checkRecorrentesPorData($_REQUEST['data'],$userJwt->empresa);

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhuma tarefa encontrada';

                   // print_r($_REQUEST);

                    if($tarefasF = $tarefas->buscarTarefasDoDiaNaTimeline($_REQUEST['data'],$userJwt->empresa,$_REQUEST['grupos'])){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = '';
                        //print_r($tarefasF);
                        $retorno['tarefas'] = ($tarefasF);
                    }


                    echo json_encode(($retorno));
                break;
        case 'buscarTarefasDoDiaModoLista':
                    $tarefas = new tarefas();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhuma tarefa encontrada';

                   // print_r($_REQUEST);

                    if($tarefasF = $tarefas->buscarTarefasDoDiaModoLista($_REQUEST['data'],$_REQUEST['dataFim'],$userJwt->empresa,$_REQUEST['grupos'],$_REQUEST['agente'],$_REQUEST['statusEspecifico'])){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = '';
                        //print_r($tarefasF);
                        $retorno['tarefas'] = ($tarefasF);
                    }


                    echo json_encode(($retorno));
                break;

        case 'buscarMetasdaTarefa':
                    $tarefas = new tarefas();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhum Meta encontrado';

                    if($metasF = $tarefas->buscarMetasTarefa($_REQUEST['tarefaid'])){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = '';
                        $retorno['metas'] = ($metasF);
                    }


                    echo json_encode(($retorno));
                break;

        /**
         * TASK-300 : Vizualizar e not. Tar Encadeadas
         */
        case 'loadTarefasDelegadas':
				//print_r($_REQUEST);
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';



				if($retorno['tarefas'] = $rota->loadTarefasDelegadas($_REQUEST['pullTracker']['rota'],$_REQUEST['pullTracker']['data'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Tarefas Delegads Encontradas';
				}


				echo json_encode($retorno);
            break;

        /**
         * TASK-300 : Vizualizar e not. Tar Encadeadas
         */
        case 'loadHistoricoTarefasDelegadas':
				//print_r($_REQUEST);
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';

				if($retorno['tarefas'] = $rota->loadHistoricoTarefasDelegadas($_REQUEST['pullTracker']['rota'],$_REQUEST['pullTracker']['data'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Tarefas Delegadas Encontradas';
				}


				echo json_encode($retorno);
            break;

        /**
         * TASK-300 : Vizualizar e not. Tar Encadeadas
         */
        case 'checkTarefasDelegadas':
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';



				if($retorno['delegadas'] = $rota->checkTarefasDelegadas($_REQUEST['pullTracker']['rota'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Este Usuario Possui Tarefas Delegadas';
				}
				echo json_encode($retorno);
            break;

		case 'loadUserTasks':
                //print_r($_REQUEST);

				$rota = new rota();
				$geo = new geo();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma tarefa encontrada';



				if($retorno = $rota->loadUserTasks($_REQUEST['pullTracker']['rota'],$_REQUEST['pullTracker']['data'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';

				}


				$dia = $_REQUEST['pullTracker']['data'];
				$dia = $dia[6].$dia[7].$dia[8].$dia[9].'-'.$dia[3].$dia[4].'-'.$dia[0].$dia[1];

                if($_REQUEST['pullTracker']['resetGoogleFill']=='resetGoogleFill'){
                    $geo->resetGoogleFill($_REQUEST['pullTracker']['user'],$dia);
                }

				if($track = $geo->getTodayUserTrack($_REQUEST['pullTracker']['user'],$dia)){
					$retorno['track'] = $track;
				}


                if($json = json_encode($retorno, JSON_PARTIAL_OUTPUT_ON_ERROR)){

                    echo $json;
                }else{
                    //print_r($retorno);

                    $retorno = array();
                    $retorno['status'] = 'false';

                    switch (json_last_error()) {

                        case JSON_ERROR_NONE : $retorno['mensagem'] = 'No error has occurred	 ';break;
                        case JSON_ERROR_DEPTH : $retorno['mensagem'] = 'The maximum stack depth has been exceeded	 ';break;
                        case JSON_ERROR_STATE_MISMATCH : $retorno['mensagem'] = 'Invalid or malformed JSON	 ';break;
                        case JSON_ERROR_CTRL_CHAR : $retorno['mensagem'] = 'Control character error, possibly incorrectly encoded	 ';break;
                        case JSON_ERROR_SYNTAX : $retorno['mensagem'] = 'Syntax error	 ';break;
                        case JSON_ERROR_UTF8 : $retorno['mensagem'] = 'Malformed UTF-8 characters, possibly incorrectly encoded	PHP 5.3.3';break;
                        case JSON_ERROR_RECURSION : $retorno['mensagem'] = 'One or more recursive references in the value to be encoded	PHP 5.5.0';break;
                        case JSON_ERROR_INF_OR_NAN : $retorno['mensagem'] = 'One or more NAN or INF values in the value to be encoded	PHP 5.5.0';break;
                        case JSON_ERROR_UNSUPPORTED_TYPE : $retorno['mensagem'] = 'A value of a type that cannot be encoded was given	PHP 5.5.0';break;
                        case JSON_ERROR_INVALID_PROPERTY_NAME : $retorno['mensagem'] = 'A property name that cannot be encoded was given	PHP 7.0.0';break;
                        case JSON_ERROR_UTF16 : $retorno['mensagem'] = 'Malformed UTF-16 characters, possibly incorrectly encoded	PHP 7.0.0';break;

                    }

                    echo json_encode($retorno);
                }
                die();


            break;
        case 'loadUserTasksPeriodo':

            $rota = new rota();
            $tarefas = new tarefas();

            /**
             * HOTFIX/408 - Gerando as Tarefas Recorrentes para todo o mes ao entrar em minhas tarefas
             *
             */
            $diaInicial = new DateTime($_REQUEST['dataInicio']);
            $diaFinal = new DateTime($_REQUEST['dataFim']);
            $diaAux = $diaInicial;

            $intervaloDias = $diaFinal->diff($diaInicial);
            $numeroDeDias = $intervaloDias->days;

            for($d = 0 ; $d <= $numeroDeDias; $d++){
                if($d != 0){
                    $diaAux->add(new DateInterval('P1D'));
                }
                $diasAnalizado = $diaAux->format('Y-m-d');

                $rota->checkRecorrentes($_REQUEST['rota'], $diasAnalizado);
            }



                $userTasks = $tarefas->pegaMinhasTarefasMensal($_REQUEST['rota'],$_REQUEST['dataInicio'],$_REQUEST['dataFim']);
                $userScheduledTasks = $tarefas->pegaMinhasTarefasMensal($_REQUEST['rota'],$_REQUEST['dataInicio'],$_REQUEST['dataFim'],true);
                $retorno = [];

                foreach($userTasks as $tarefa){
                    $tarefa['planedStart'] = json_decode($tarefa['planedStart']);
                    $tarefa['planedStartDate'] = $tarefa["dataTarefa"]." ". str_pad($tarefa['planedStart']->hora, 2, "0", STR_PAD_LEFT).":".str_pad($tarefa['planedStart']->minuto, 2, "0", STR_PAD_LEFT).":00";
                    $tarefa['planedEnd'] = json_decode($tarefa['planedEnd']);
                    $tarefa['planedEndDate'] = $tarefa["dataTarefa"]." ".   str_pad($tarefa['planedEnd']->hora, 2, "0", STR_PAD_LEFT).":".str_pad($tarefa['planedEnd']->minuto, 2, "0", STR_PAD_LEFT).":00";
                    $tarefa['log'] = json_decode($tarefa['log']);
                    $tarefa['forms'] = json_decode($tarefa['forms']);

                    // $tarefa['formularios'] = [];
                    // foreach($tarefa['forms'] as $form){
                    //     $tarefa['formularios'][] =  mysqli_fetch_assoc($tarefas->getOne('*',$form,'formulario'));
                    // }

                    $retorno['tarefas'][] = $tarefa;
                }

                foreach($userScheduledTasks as $tarefa){
                    $tarefa['planedStart'] = json_decode($tarefa['planedStart']);
                    $tarefa['planedStartDate'] = $tarefa["dataTarefa"]." ". str_pad($tarefa['planedStart']->hora, 2, "0", STR_PAD_LEFT).":".str_pad($tarefa['planedStart']->minuto, 2, "0", STR_PAD_LEFT).":00";
                    $tarefa['planedEnd'] = json_decode($tarefa['planedEnd']);
                    $tarefa['planedEndDate'] = $tarefa["dataTarefa"]." ".   str_pad($tarefa['planedEnd']->hora, 2, "0", STR_PAD_LEFT).":".str_pad($tarefa['planedEnd']->minuto, 2, "0", STR_PAD_LEFT).":00";
                    $tarefa['log'] = json_decode($tarefa['log']);
                    $tarefa['forms'] = json_decode($tarefa['forms']);

                    // $tarefa['formularios'] = [];
                    // foreach($tarefa['forms'] as $form){
                    //     $tarefa['formularios'][] =  mysqli_fetch_assoc($tarefas->getOne('*',$form,'formulario'));
                    // }

                    $retorno['tarefasAgendadas'][] = $tarefa;
                }

                if($json = json_encode( $retorno)){

                    echo $json;
                }else{


                    $retorno['tarefas'] = array();
                    $retorno['tarefasAgendadas'] = array();

                    $retorno['status'] = 'false';

                    switch (json_last_error()) {

                        case JSON_ERROR_NONE : $retorno['mensagem'] = 'No error has occurred	 ';break;
                        case JSON_ERROR_DEPTH : $retorno['mensagem'] = 'The maximum stack depth has been exceeded	 ';break;
                        case JSON_ERROR_STATE_MISMATCH : $retorno['mensagem'] = 'Invalid or malformed JSON	 ';break;
                        case JSON_ERROR_CTRL_CHAR : $retorno['mensagem'] = 'Control character error, possibly incorrectly encoded	 ';break;
                        case JSON_ERROR_SYNTAX : $retorno['mensagem'] = 'Syntax error	 ';break;
                        case JSON_ERROR_UTF8 : $retorno['mensagem'] = 'Malformed UTF-8 characters, possibly incorrectly encoded	PHP 5.3.3';break;
                        case JSON_ERROR_RECURSION : $retorno['mensagem'] = 'One or more recursive references in the value to be encoded	PHP 5.5.0';break;
                        case JSON_ERROR_INF_OR_NAN : $retorno['mensagem'] = 'One or more NAN or INF values in the value to be encoded	PHP 5.5.0';break;
                        case JSON_ERROR_UNSUPPORTED_TYPE : $retorno['mensagem'] = 'A value of a type that cannot be encoded was given	PHP 5.5.0';break;
                        case JSON_ERROR_INVALID_PROPERTY_NAME : $retorno['mensagem'] = 'A property name that cannot be encoded was given	PHP 7.0.0';break;
                        case JSON_ERROR_UTF16 : $retorno['mensagem'] = 'Malformed UTF-16 characters, possibly incorrectly encoded	PHP 7.0.0';break;

                    }

                    echo json_encode($retorno);
                }
            die();
            break;
        case 'loadDadosDeUmaTarefa':

            $tarefas = new tarefas();
            $tarefasautomaticas = new TarefaAutomatica();
            $tarefa = $tarefas->pegaRespostasAndMetas($_REQUEST['idTarefa']);
            $tarefa['historicoPais'] = $tarefasautomaticas->retornTarefasPais($_REQUEST['idTarefa']);
            echo json_encode($tarefa);

            break;

        case 'loadTracks':
				//print_r($_REQUEST);
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';

				if($retorno['tracks'] = $rota->loadTracks($_REQUEST['pullTracker']['user'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}

				echo json_encode(arrayToUTF8($retorno));
			break;

		case 'loadRotaForUser':
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';
				if($_REQUEST['pullTracker']['data']!=''){
                    if($r = $rota->loadRotaForUser($_REQUEST['pullTracker']['user'],$_REQUEST['pullTracker']['data'])){
                        $retorno['rota'] = $r;
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Rota encontrada';
                    }
                }else{
                    if($r = $rota->loadRotaForUser($_REQUEST['pullTracker']['user'])){
                        $retorno['rota'] = $r;
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Rota encontrada';
                    }
                }


                /**
                 * Adição Task-176
                 *  Verificando Se a Data Resuitada é anterior ao dia de Hoje
                 *
                 *  Caso seja ele irá buscar as respostas dos Formularios para aquele dia
                 */
                $dia = $_REQUEST['pullTracker']['data'];
                $dia = $dia[6].$dia[7].$dia[8].$dia[9].'-'.$dia[3].$dia[4].'-'.$dia[0].$dia[1];

                $hoje = date('Y-m-d',strtotime("now"));
                if(strtotime($hoje) > strtotime($dia)){
                    $inicio = $dia.' 00:00:00';
                    $fim = $dia.' 23:59:59';
                    $listaForms = array();


                    $formulario = new formulario();
                    $retorno['respostas'] = array();

                    foreach($retorno['rota']['tarefas'] as $tarefa){
                        if($respostas = $formulario->loadRespostasTarefaAndMakeTreeForm($tarefa['id'])){
                            $retorno['respostas'][] = $respostas;
                        }
                    }

                }

				echo safe_json_encode($retorno);

			break;
		case 'routeDetails':
				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';


				if($r = $rota->routeDetails($_REQUEST['pullTracker']['rota'])){
					$retorno['rota'] = $r;
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = 'Rota encontrada';
				}

				echo json_encode(arrayToUTF8($retorno));

			break;



		case 'apagaRota':

				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';

				if($rota->apagarRota($_REQUEST['pullTracker']['id'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}


				echo json_encode(arrayToUTF8($retorno));
			break;
		case 'apagaTarefa':

				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';

				if($rota->apagaTarefa($_REQUEST['pullTracker']['id'])){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}


				echo json_encode(arrayToUTF8($retorno));
			break;
		case 'updateTarefaStatus':

				$rota = new rota();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';

				$tarefa = $_REQUEST['pullTracker']['tarefa'];
				$status = $_REQUEST['pullTracker']['status'];

				if($retorno['tarefa'] = $rota->updateTarefaStatus($tarefa,$status)){
					$retorno['status'] = 'ok';
					$retorno['mensagem'] = '';
				}

				echo json_encode(arrayToUTF8($retorno));
			break;


			case 'uploadImage':
					$target_path  = dirname(__FILE__)."/uploads/";
                    $caminho = '/uploads/';
					$string = 'file_';

					$cod_cliente = $_POST['user'];
					$cod_operador = $_POST['tarefa'];

					if(!is_dir($target_path)){
						mkdir($target_path);
					}

					$target_path .= $cod_cliente."/";
                    $caminho .= $cod_cliente."/";

                    if(!is_dir($target_path)){
                            mkdir($target_path);
                    }
                    $target_path .= $cod_operador."/";
                    $caminho .= $cod_operador."/";

                    if(!is_dir($target_path)){
                            mkdir($target_path);
                    }

                    $nomeDoArquivo = basename( $string.'_'.$_FILES['file']['name']);

                    $target_path = $target_path . $nomeDoArquivo;
                    $caminho = $caminho . $nomeDoArquivo;

			        move_uploaded_file($_FILES["file"]["tmp_name"],$target_path);

				    //	$target_path = $target_path . $nomeDoArquivo;


					// echo json_encode($_FILES);
					//
                    echo json_encode(array(
                                        'status'        => 'ok',
                                        'path'          => $caminho,
                                        'nomeDoArquivo' => $nomeDoArquivo,
                                        'task'          => $_POST['tarefa']
                                        ));




				break;

        case 'getTreeCapability':

				$capability = new capability();
                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma capability encontrada';


            if($capabilities = $capability->getTreeCapability(false,$_REQUEST['pullTracker']["perfilID"])){

                    $retorno['status'] = 'Ok';
                    $retorno['mensagem'] = 'Lista de capabilities encontradas';
                    $retorno['capabilities'] = $capabilities;

                    }


                echo json_encode($retorno);

			break;


        case 'criarPerfil':


            $capability = new capability();

            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Perfil não criado';

            if($_REQUEST['pullTracker']['perfilID']!=''){

                $idPerfil = $_REQUEST['pullTracker']['perfilID'];
                if($capability->atualizarPerfil($_REQUEST['pullTracker']['nome'],$_REQUEST['pullTracker']['perfilID'])){


                    $capability->editarPerfilCapability($_REQUEST['pullTracker']['perfilID']);
                    $idPerfis = array();

                    foreach($_REQUEST['caps'] as $cap){

                        $idPerfis[] = $capability->addCapabilityPerfil($_REQUEST['pullTracker']['perfilID'], $cap);

                    }


                    $retorno['status'] = 'ok';
                    $retorno['perfis'] = $idPerfis;
                    $retorno['mensagem'] = 'Perfil atualizado com sucesso!';


                }

            }else{

                if($idPerfil = $capability->criarPerfil($_REQUEST['pullTracker']['nome'],$userJwt->empresa)){


                    foreach($_REQUEST['caps'] as $c){

                        $idPerfis[] = $capability->addCapabilityPerfil($idPerfil, $c);

                    }
                    $retorno['status'] = 'ok';
                    $retorno['perfis'] = $idPerfis;
                    $retorno['mensagem'] = 'Perfil criado com sucesso!';

                }

            }

            $retorno['idPerfil'] = $idPerfil;

            // seta os formulários de perfil
            if($idPerfil){
                $capability->update_perfil_meta($idPerfil,"__formularios", is_array($_REQUEST['forms']) ? implode(',',$_REQUEST['forms']) : $_REQUEST['forms'] );
                $retorno['metadados'] = $capability->get_perfil_meta($idPerfil);
            }

            echo json_encode(($retorno));


        break;

        case 'carregarPerfis':

				$capability = new capability();
                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhum perfil encontrado';

                if($perfis = $capability->carregarPerfis($userJwt->empresa)){
                    $retorno['status'] = 'Ok';
                    $retorno['mensagem'] = 'Lista de Perfis encontradas';
                    $retorno['perfis'] = $perfis;

                    }

                echo json_encode(($retorno));

			break;


        case 'apagarPerfil':

                $retorno['status'] = 'Ok';
                $retorno['mensagem'] = 'Perfil excluído com sucesso!';
                $capability = new capability();
                $capability->apagarPerfil($_REQUEST['perfil']);

                echo json_encode($retorno);

            break;



        case 'carregarPerfisPorID':

               $capability = new capability();

                $retorno['status'] = 'false';
				$retorno['mensagem'] = 'Perfil não encontrado';

                if($perfil = $capability->carregarPerfisPorID($_REQUEST['pullTracker']['perfilID'])){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Perfil encontrado';
                    $retorno['capabilities'] = $perfil;
                }

                echo json_encode(($retorno));

            break;

        case 'editarPerfil':

            $capability = new capability();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Perfil não atualizado !';

            if($capability->atualizarPerfil($_REQUEST['pullTracker']['nome'],$_REQUEST['pullTracker']['perfilID'])){


                $capability->editarPerfilCapability($_REQUEST['pullTracker']['perfilID']);

                print_r($_REQUEST['caps']);
                die();

                foreach($_REQUEST['caps'] as $c){

                    $capability->addCapabilityPerfil($_REQUEST['pullTracker']['perfilID'], $c['name']);

                }

                $retorno['status'] = 'ok';
                $retorno['perfis'] = $idPerfis;
				$retorno['mensagem'] = 'Perfil atualizado com sucesso!';


            }

            echo json_encode(($retorno));

            break;

        case 'getNotificacoes':

            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Notificacao Encontrada Para Esse Usuario!';

            if($idNotificacoes = $notificacao->getNotificacoes($_REQUEST['pullTracker']['idDestino'],$_REQUEST['pullTracker']['tipoNotificacao'])){

                $retorno['status'] = 'ok';
                $retorno['notificacoes'] = $idNotificacoes;
                $retorno['mensagem'] = 'Notificacao encontradas com sucesso!';
            }
            echo json_encode(($retorno));

            break;

        case 'getAllNotificacoes':

            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Notificacao Encontrada Para Esse Usuario!';

            if($idNotificacoes = $notificacao->getAllNotificacoes($_REQUEST['pullTracker']['idDestino'],$_REQUEST['pullTracker']['dataInicio'],$_REQUEST['pullTracker']['dataTermino'],$_REQUEST['pullTracker']['agenteFilterID'],$_REQUEST['pullTracker']['tipoFilter'])){


                $retorno['status'] = 'ok';
                $retorno['notificacoes'] = $idNotificacoes;
                $retorno['mensagem'] = 'Notificacao encontradas com sucesso!';
            }
            echo safe_json_encode(($retorno));

            break;

        case 'criarNotificacao':

            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Notificacao Foi Criada!';

            if($notificacao->criarNotificacao($_REQUEST['pullTracker']['tipo'],$_REQUEST['pullTracker']['mensagem'],$_REQUEST['pullTracker']['idDestino'])){

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Notificacao encontradas com sucesso!';
            }
            echo json_encode(($retorno));

            break;

         case 'entregaNotificacoes':
            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Notificacao Foi Alterada!';

            if($notificacao->setNotificacaoVisualizada($_REQUEST['pullTracker']['idDestino'],$_REQUEST['pullTracker']['tipoNotificacao'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Notificacao entregues com sucesso!';
            }
            echo json_encode(($retorno));

            break;

        case 'habilitarNotificacoes':
            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Notificação Foi Atualizada!';


            for($i=0;$i<count($_REQUEST['pullTracker']['tipoNotificacao']); $i++){
                if($_REQUEST['pullTracker']['tipoNotificacao'][$i] == 0){
                   $retorno['listaNotificacoesHabilitadas'][] = $notificacao->habilitaNotificacao($_REQUEST['pullTracker']['idDestino'],$i,0,$_REQUEST['pullTracker']['persistentes'][$i]);
                }else{
                    $retorno['listaNotificacoesHabilitadas'][] = $notificacao->habilitaNotificacao($_REQUEST['pullTracker']['idDestino'],$i,1,$_REQUEST['pullTracker']['persistentes'][$i]);
                }
            }

            $usuarioNotificacao = new user();
            //Informações sobre o volume das notificações
            $usuarioNotificacao->update_user_meta($_REQUEST['pullTracker']['idDestino'], 'controleVolume', $_REQUEST['pullTracker']['controleVolume']);

            //Informações sobre o agrupamento de notificações
            $usuarioNotificacao->update_user_meta($_REQUEST['pullTracker']['idDestino'], 'controleAgrupamento', $_REQUEST['pullTracker']['controleAgrupamento']);

            //Informações sobre o tempo de vida das notificações
            $usuarioNotificacao->update_user_meta($_REQUEST['pullTracker']['idDestino'], 'controleTempoVida',  $_REQUEST['pullTracker']['controleTempoVida']);

            if($retorno['listaNotificacoesHabilitadas']){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Tipo de notificação atualizada com sucesso!';
            }

            echo json_encode(($retorno));

            break;

        case 'getListaNotificacoesHabilitadas':
            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Lista das Notificacoes Encontrada!';

            if($notificacoes = $notificacao->getListaNotificacoesHabilitadas($_REQUEST['pullTracker']['idDestino'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Lista das Notificacoes Encontrada!';
                $retorno['notificacoes'] = $notificacoes;

                $retorno['meta_notificacoes'] = $notificacao->getMetaDadosConf($_REQUEST['pullTracker']['idDestino']);
            }
            echo json_encode(($retorno));

            break;

        case 'getListaTiposNotificacoes':
            $notificacao = new notificacao();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Nenhuma Lista das Notificacoes Encontrada!';

            if($notificacoes = $notificacao->getListaTiposNotificacoes()){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Lista das Notificacoes Encontrada!';
                $retorno['listaTipos'] = $notificacoes;
            }
            echo json_encode(($retorno));

            break;

          case 'adicionarUsuarioGrupo':

            $capability = new capability();
            $retorno['status'] = 'false';
			$retorno['mensagem'] = 'Usuário não adicionado no grupo !';


            if($usergroup = $capability-addUserPerfil( $userJwt->empresa,$_REQUEST['idUsuario'],$_REQUEST['perfil'])){

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Usuário adicionado no grupo com sucesso !';
                $retorno['usergroup'] = $usergroup;
            }

                echo json_encode(($retorno));


            break;


        case "loadUserRoles":

            $capability = new capability();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma role encontrada!';
            $retorno['roles'] = array();

            if($usergroup = $capability->loadUserRoles($_REQUEST['userId'])){

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Roles encontradas!';
                $retorno['roles'] = $usergroup;

            }

            echo json_encode(($retorno));

            break;


        case 'loadMenusEmpresa':

            $empresas = new empresas();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum menu encontrado!';
            $retorno['menus'] = array();

            if($menusEmpresa = $empresas->loadUserRoles($userJwt->empresa)){

                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Menus encontrados!';
                $retorno['menus'] = $menusEmpresa;

            }

            echo json_encode(($retorno));

            break;

        case "icomon_importar_excel":
            require_once('includes/clientes/icomon/icomon.class.php');
            $icomon = new Icomon();
            echo json_encode($icomon->importarExcell());
            break;


        case "icomon_importar_csv":
            require_once('includes/clientes/icomon/icomon.class.php');
            $icomon = new Icomon();
            echo json_encode($icomon->importarCsv());
            break;

        case "icomon_importar_usuarios":
            require_once('includes/clientes/icomon/icomon.class.php');
            $icomon = new Icomon();
            echo json_encode($icomon->importarUsuarios());
            break;

        case "icomon_importar_defeitos":
            require_once('includes/clientes/icomon/icomon.class.php');
            $icomon = new Icomon();
            echo json_encode($icomon->importarDefeitos());
            break;

/*

        case "jamef_inclusao_agenda":
            $jamef = new Jamef();
            echo json_encode($jamef->inclusaoAgenda($_REQUEST["idTarefa"],$_REQUEST["idUser"]));
            break;

        case "jamef_consulta_agenda":

            $jamef = new Jamef();
            print_r($jamef->consultaAgendaCadastro());
            break;

        case "jamef_consulta_agenda_alteracao":

            $jamef = new Jamef();
            print_r($jamef->consultaAgendaAlteracao());
            break;

        case "atualizaTarefaJamef":

            $jamef = new Jamef();
            print_r($jamef->atualizaAgenda($_REQUEST["idTarefa"],$_REQUEST["idUser"]));
            break;

*/
        case 'incluirTarefaModelo':

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Erro ao incluir a tarefa!';

            $modeloTarefa = [
                "nome" =>  $_REQUEST['nome'],
                "tipo" =>  $_REQUEST['opcaoEscolhida'],
                "usuario" =>  $_REQUEST['usuario'],
                "grupos" =>  json_encode($_REQUEST['grupos']),
                "perfis" =>  json_encode($_REQUEST['perfis']),
                "prazo" =>  $_REQUEST['prazo'],
                "executar" =>  $_REQUEST['executar'],
                "instrucoes" =>  $_REQUEST['instrucoes'],
                "foto" =>  $_REQUEST['foto'],
                "assinatura" =>  $_REQUEST['assinatura'],
                "comentario" =>  $_REQUEST['comentario'],
                "formulario" =>  json_encode($_REQUEST['formulario']),
                "empresa" =>  $userJwt->empresa,
                "id" =>  $_REQUEST['pullTracker']['tarefaID'],
                "meta" =>  $_REQUEST['meta'],
                "tipoPrazo" =>  $_REQUEST['tipoPrazo']
            ];

            $confResponsavel = 0;
            if($_REQUEST['opcaoEscolhida'] == 'grupo'){
                if($_REQUEST['gruposAdm'] == 1){
                    //Significa que a tarefa será para um grupo e será enviada apenas aos ADMs
                    //Responsavel recebe codigo 1
                    $confResponsavel = 1;
                }else
                if($_REQUEST['gruposQlqrUser'] == 1){
                    //Significa que a tarefa será para um grupo e será enviada para TODOS os USUARIOS do GRUPO
                    //Responsavel recebe codigo 2
                    $confResponsavel = 2;
                }else{
                    //Significa que a tarefa será para um grupo e será enviada para USUARIOS com PERFIL ESPECIFICOS
                    //Responsavel recebe codigo 3
                    $confResponsavel = 3;
                }
            }
            if($_REQUEST['opcaoEscolhida'] == 'perfil'){
                if($_REQUEST['perfilQlqrUser'] == 1){
                    //Significa que a tarefa será um tipo de PERFIL ESPECIFICO de USUARIO
                    //Responsavel recebe codigo 4
                    $confResponsavel = 4;
                }else{
                    //Significa que a tarefa será para um tipo de PERFIL ESPECIFICO de USUARIO em GRUPOS ESPECIFICOS
                    //Responsavel recebe codigo 5
                    $confResponsavel = 5;
                    $_REQUEST['grupos'] = $_REQUEST['perfilGrupos'];
                }
            }
            if($_REQUEST['opcaoEscolhida'] == 'posDefinido'){
                $confResponsavel = 6;
            }

            if($_REQUEST['opcaoEscolhida'] == 'agente'){
                $confResponsavel = 7;
            }

            $modeloTarefa["confResponsavel"] =  $confResponsavel;

            if($_REQUEST['pullTracker']['tarefaID']!=''){
                if($modeloTarefaClass->updateTarefaModelo($modeloTarefa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefa modelo atualizado com sucesso!';
                }
            }else{
                if($tarefaModelo = $modeloTarefaClass->incluirTarefaModelo($modeloTarefa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefa modelo salvo com sucesso!';
                    $retorno['tarefaModelo'] = $tarefaModelo;
                }
            }

            echo json_encode(($retorno));


            break;

        case 'carregarTarefaModelo':


            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Tarefas modelos não encontrados !';

            if($tarefasModelo = $modeloTarefaClass->carregarTarefaModelo($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefas modelos encontrados';
                    $retorno['tarefasModelo'] = $tarefasModelo;
                }

            echo json_encode($retorno);

            break;

        case 'carregarTarefaModeloPorID':


            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Tarefas modelos não encontrados !';

            if($tarefasModelo = $modeloTarefaClass->carregarTarefaModeloPorID($_REQUEST['pullTracker']['tarefaID'])){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Tarefas modelos encontrados';
                    $retorno['tarefasModelo'] = $tarefasModelo;
                }

            echo json_encode(($retorno));

            break;

        case 'apagarTarefaModelo':

            $retorno['status'] = 'Ok';
            $retorno['mensagem'] = 'Tarefa modelo excluído com sucesso!';
            $modeloTarefaClass->apagarTarefaModelo($_REQUEST['tarefaModelo']);

            echo json_encode($retorno);

        break;

        /***** TASK/508 - Reagendamento Tarefa pelo Acompanhamento de Tarefas *****/
        case 'reagendarTarefa':

            $retorno['status'] = 'Ok';
            $retorno['mensagem'] = 'Reagendamento não concluido!';

            $tarefa = new indicadoresTarefa();
            $resultado = $tarefa->reAgendarTarefas($_REQUEST['idTarefa'], $_REQUEST['motivo'], $_REQUEST['tipoDoNovoPrazo'], $_REQUEST['valorNovoPrazo'], $userJwt->empresa);

            if($resultado){
                $retorno['mensagem'] = 'Tarefa Reagendada com Sucesso!';
                $retorno['dadosNovaTarefa'] = $resultado;
            }else{
                $retorno['mensagem'] = mysqli_error();
                $retorno['status'] = 'ERRO';
            }

            echo json_encode($retorno);

        break;

        /***** TASK/508 - Reagendamento Tarefa pelo Acompanhamento de Tarefas *****/
        /***** TASK 451 - QRCode */

        case 'carregarQRCode':

            $qrcodes = new QRCode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'QRCode\'s não encontrados !';

            if($qrcodeList = $qrcodes->listar($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'QRCode\'s encontrados';
                $retorno['qrcode'] = $qrcodeList;
            }
            echo json_encode($retorno);
            break;

        case 'incluirQRCode':

            $qrcodes = new QRCode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Erro ao incluir o QRCode!';

            if($qrcode = $qrcodes->save($_REQUEST)){
                $retorno['status'] = 'ok';
                $retorno['qrcode'] = $qrcode;
                $retorno['mensagem'] = 'QR salvo com sucesso!';
            }

            echo json_encode(($retorno));


            break;

        case 'carregarQRCodeID':

            $qrcodes = new QRCode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'QRCode não encontrado!';

            if($qrcode = $qrcodes->carregar($_REQUEST['pullTracker']['id'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'QRCode encontrado';
                $retorno['qrcode'] = $qrcode;
            }

            echo json_encode(($retorno));

            break;

        case 'apagarQRCode':

            $qrcodes = new QRCode();

            //$retorno['status'] = 'false';
            $retorno['mensagem'] = 'QRCode excluído com sucesso!';

            /**
             * Corigindo nome da Variavel que vem do painel
             */
            $qrcodes->deletaQRCodeDasCategorias($_REQUEST['idQRCode']);

            $data = [
                "id" => $_REQUEST['idQRCode'],
                "ativo" => '0'
            ];

            if($qrcode = $qrcodes->save($data)){
                //$retorno['status'] = 'Ok';

            }

            echo json_encode($retorno);

            break;


        /***** FIM TASK 451 - QRCode */
        /***** INICIO TASK 454 - QRCode */
        case 'apagarQRCodePublico':
        $qrcodes = new QRCode();

        $retorno['status'] = 'false';
        $retorno['mensagem'] = 'QRCode não pode ser excluído!';
        $qrcodes->deletaQRCodeDasCategorias($_REQUEST['idQRCode']);
        /*$data = [
            "id" => $_REQUEST['id'],
            "ativo" => '0'
        ];*/

        $editarIdQRCode = false;
        $editarIdCategoria = false;
        //if(is_numeric($_REQUEST['idQRCode']) && is_numeric($_REQUEST['idCategoria'])){
        if(is_numeric($_REQUEST['idQRCode'])){
            //$retorno['mensagem'] = 'Formulário não foi excluído com sucesso!';
            $idQRCode = $_REQUEST['idQRCode'];
            //$idCategoria = $_REQUEST['idCategoria'];
            //$qrcodes->update(0,'empresa','qrcode',$editarId);
            //$qrcodes->update($userJwt->empresa,'originalId','qrcode',$editarId);
            //$qrcodes->removeCategoriaQRCode($idQRCode);
            $qrcodes->excluirQRCode($idQRCode);
            $retorno['status'] = 'ok';
            $retorno['mensagem'] = 'QRCode excluído com sucesso!';
        }
        else{
            //$retorno['mensagem'] = 'alguns deles não são numericos! '.$_REQUEST['idQRCode'].' '.$_REQUEST['idCategoria'];
        }
        /*if($r = $qrcodes->saveQrPublico($data)){
            $retorno['status'] = 'ok';
            $retorno['mensagem'] = 'QRCode excluído com sucesso!';
        }*/

        echo json_encode($retorno);

        break;

            case 'carregarQRCodePublico':

                $qrcodes = new QRCode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'QRCode\'s não encontrados !';

                if($qrcodeList = $qrcodes->listarQrCodePublico($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'QRCode\'s encontrados';
                    $retorno['qrcode'] = $qrcodeList;
                }
                echo json_encode($retorno);
            break;


            case 'incluirQRCodePublico':

                $qrcodes = new QRCode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Erro ao incluir o QRCode!';

                if($qrcode = $qrcodes->saveQrPublico($_REQUEST)){
                    $retorno['status'] = 'ok';
                    $retorno['qrcode'] = $qrcode;
                    $retorno['mensagem'] = 'QR salvo com sucesso!';
                }

                echo json_encode(($retorno));

            break;

            case 'apagarQRCodePublico2':
            $qrcodes = new QRCode();

            $retorno['status'] = 'ok';
            $retorno['mensagem'] = 'QRCode excluído com sucesso!';

            $data = [
                "id" => $_REQUEST['id'],
                "ativo" => '0'
            ];

            if($r = $qrcodes->saveQrPublico($data)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'QRCode excluído com sucesso!';
            }

            echo json_encode($retorno);

            break;

        /***** FIM TASK 454 - QRCode */

        case 'pegaUsuariosEmTarefaInativos':
                    $sqlPegarInitRastreamento = "SELECT DISTINCT
                    registros.idUser,
                    empresa,
                    registros.location_type,
                    FROM_UNIXTIME((registros.`timestamp` / 1000)) hora_do_registro,
                    convert_tz(utc_timestamp(), '+00:00', '-3:00') hora_atual,
                    convert_tz(utc_timestamp(), '+00:00', '-3:01') hora_atual_1_minutos_atras
            FROM registros,
                    (SELECT idUser,
                            users.empresa,
                            dia,
                            max(registros.`timestamp`) maxTime
                    FROM registros, users
                    WHERE     dia = '2017-09-13'
                            AND users.id = idUser
                            AND registros.`timestamp` < now()
                    GROUP BY idUser, empresa, dia) AS temp,
                    rotas,
                    tarefas
            WHERE     temp.idUser = registros.idUser
                    AND temp.maxTime = registros.`timestamp`
                    AND temp.idUser = rotas.user
                    AND rotas.id = tarefas.rota
                    AND tarefas.dataTarefa = temp.dia
                    AND tarefas.status NOT IN ('pendente', 'concluida', 'malsucedida')";
            //echo($sqlPegarInitRastreamento);
            $item = $db->execute($sqlPegarInitRastreamento);


            if(mysqli_error()){
            echo('ERRO -> '.mysqli_error());
            }
            $numeroDeUsuario = 0;
            $numeroDeUsuarioParaNotificar = 0;
            while($temp = mysqli_fetch_assoc($item)){
            //var_dump($temp);
            $data1 = new DateTime($temp[hora_atual_1_minutos_atras]);
            //print_r($data1->format('Y:m:d - h:i:s '));

            $data2 = new DateTime($temp[hora_do_registro]);
            //print_r($data2->format('|| Y:m:d - h:i:s '));

            if($data2 < $data1){
            echo('Usuario Para Notificar -> '.$temp[idUser].' ultimo ponto recebido em '.$data2->format('Y|m|d - h:i:s '));
            $numeroDeUsuarioParaNotificar++;
            echo('<br>');
            }
            $numeroDeUsuario++;
            }
            echo('<br><br> Numero de Usuarios Em Tarefas '.$numeroDeUsuario.'. Numero A Notificar '.$numeroDeUsuarioParaNotificar);

            break;

            case 'responderFormularios':
                $formulario = new formulario();

				$retorno['status'] = 'false';
				$retorno['mensagem'] = 'Nenhuma rota encontrada';
                $retorno['respostasSalvas'] = array();


               foreach($_POST['json']['itens'] as $j){
                 if($j['data']){


                    $dataReg = date('Y-m-d H:i:s', ($j['data']/1000));
                    $resp = $formulario->createResposta($j['id'],$j['idTarefa'],$j['idForm'],$dataReg,$j['parent']);
                    $retorno['respostasSalvas'][]=$resp;
                    $retorno['status'] = 'OK';
				    $retorno['mensagem'] = 'Resposta salva com sucesso!';
                    $data['idResposta'] = $resp;
                    /*
                        Teste Se o Formulario irá gerar uma Atividade Automatica a partir de suas respostas
                            Este teste é feito observando um campo da Tarefa "automatica"
                            Se esse campo for diferente de 0 quer dizer que essa tarefa foi gerada automaticamente
                            O valor do campo é o id da RESPOSTA que originalmente gerou esta tarefa
                    */
                    $sql = 'SELECT T.automatica FROM `tarefas` T WHERE automatica = '.$resp.'';
                    $verificaTarefaIniciada =  mysqli_fetch_array($db->execute($sql));

                    if($verificaTarefaIniciada){
                         foreach($j['respostas'] as $r){
                              $formulario->createMetaResposta($resp,$r['idPergunta'],$r['valor'],$r['tipo'],$r['slug']);
                         }

                    }else{
                        $data['instrucao'] = '\n';
                        foreach($j['respostas'] as $r){
                            $formulario->createMetaResposta($resp,$r['idPergunta'],$r['valor'],$r['tipo'],$r['slug']);

                            $sql = 'SELECT P.texto as texto, P.tipo as tipo, P.lista as lista FROM form_questions P WHERE id = '.$r['idPergunta'];
                            $pergunta = mysqli_fetch_array($db->execute($sql));

                            if($pergunta['tipo'] == 'tarefaAutomatica'){
                                $sql = 'SELECT * FROM modelo_tarefa WHERE id = '.$pergunta['lista'];
                                $data['modelo'] = mysqli_fetch_array($db->execute($sql));
                            }else{
                                if($pergunta['tipo'] != ''){
                                    if($pergunta['tipo'] == 'multiplaEscolha'){
                                        $arrayValores = json_decode($r['valor']);
                                        for($i=0;$i<count($arrayValores);$i++){
                                            $sql = 'SELECT titulo FROM form_listas_itens WHERE apagado = 0 and id = '.$arrayValores[$i];
                                            $titulo = mysqli_fetch_array($db->execute($sql));
                                            if($i!=0)
                                                $r['titulo'] .= ',';
                                            $r['titulo'] .= $titulo[0].' ';
                                        }
                                        $data['instrucao'] .= " ".$pergunta['texto'].' : '.$r['titulo'].'\n'." ";
                                    }else{
                                        $data['instrucao'] .= " ".$pergunta['texto'].' : '.$r['valor'].'\n'." ";
                                    }
                                }
                            }

                        }

                    }

                 }
               }
                echo safe_json_encode(arrayToUTF8($retorno));
            break;


        case 'listarEmpresas':

            $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma empresa encontrada !';

            if($listaEmpresas = $empresas->listarEmpresas()){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Empresas encontradas!';
                    $retorno['empresas'] = $listaEmpresas;
                }

            echo json_encode($retorno);

            break;


        case 'virarEmpresa':

            $empresas = new empresas();
            $retorno['status'] = 'ok';
            $payload = [
                "iss"       => Jwtauth::_iss,
                "exp"       => time() + Jwtauth::_tempoDeVida,
                "userId"    => $_REQUEST['idUser'],
                "empresa"   => $_REQUEST['idEmpresa'],
                "isEmpresa" => 1
            ];
            $retorno['token'] = Jwtauth::generateWebToken($payload);
            $token = $empresas->virarEmpresa($_REQUEST['idUser'],$retorno['token']);
            $retorno['status'] = 'ok';
            $retorno['empresa'] = $token;
            $retorno['metaDadosEmpresa'] = $empresas->getMetaDadosEmpresa($_REQUEST['idEmpresa']);
            echo json_encode($retorno);
            break;


        case 'buscarMenusEmpresa':

            $empresas = new empresas();
            $metas = $empresas->get_empresa_meta($userJwt->empresa,'menu');
            $retorno['metas']= json_decode($metas);
            echo json_encode(($retorno));

        break;

        case 'menusEmpresa':

        $empresas = new empresas();
        $menus = $_REQUEST['menus'];
        $meta = $empresas->update_empresa_meta($userJwt->empresa, $_REQUEST['metakey'],$menus);
        $retorno['metas']= $meta;
        echo json_encode(($retorno));

        break;

        case 'cadastrarEmpresa':

            $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Erro ao cadastrar a empresa!';
            $senha= md5($_REQUEST['senha']);
            $token = md5($_REQUEST['criado']);
            $status =1;

            if($_REQUEST['pullTracker']['editarId']!=''){
                if($empresas->EmailRepetido($_REQUEST['email'],
                        $_REQUEST['pullTracker']['editarId'])){
                    $retorno['status'] = 'Email Repetido';
                    $retorno['mensagem'] = 'Este e-mail já está cadastrado em nosso sistema.';
                }else{
                    // if(empty($_REQUEST['senha'])){

                    $senha = empty($_REQUEST['senha']) ? "" : $senha;
                    $token = empty($_REQUEST['senha']) ? "" : $token;
                    if($empresas->atualizarEmpresa(
                            $userJwt->id,
                            $_REQUEST['nome'],
                            $_REQUEST['isAdmin'],$status,
                            $_REQUEST['email'],
                            $senha, $token,
                            $_REQUEST['criado'],
                            $_REQUEST['maxUsers'],$_REQUEST['ativo'],
                            $_REQUEST['pullTracker']['editarId'],
                            $_REQUEST['menus'])) {
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Empresa atualizada com sucesso!2';
                            //$idEmpresa = $empresas->buscarEmpresa($_REQUEST['pullTracker']['editarId']);
                            //$retorno['idEmpresa'] = $idEmpresa['empresa'];
                        }
                        // if($empresas->verificarEmpresa(
                        //         $userJwt->id,
                        //         $_REQUEST['nome'],
                        //         $_REQUEST['isAdmin'],$status,$_REQUEST['email'],
                        //         $_REQUEST['criado'],$_REQUEST['maxUsers'],
                        //         $_REQUEST['ativo'],$_REQUEST['pullTracker']['editarId'],
                        //         $_REQUEST['menus'])){
                        //     $retorno['status'] = 'ok';
                        //     $retorno['mensagem'] = 'Empresa atualizada com sucesso!1';
                        //     //$idEmpresa = $empresas->buscarEmpresa($_REQUEST['pullTracker']['editarId']);
                        //     //$retorno['idEmpresa'] = $idEmpresa['empresa'];
                        //     }
                    // }else {
                    //     //bugfix-246:: erro ocorre no banco de dados
                    //     if($empresas->atualizarEmpresa(
                    //             $userJwt->id,
                    //             $_REQUEST['nome'],
                    //             $_REQUEST['isAdmin'],$status,$_REQUEST['email'],
                    //             $senha,$token,$_REQUEST['criado'],
                    //             $_REQUEST['maxUsers'],$_REQUEST['ativo'],
                    //             $_REQUEST['pullTracker']['editarId'],
                    //             $_REQUEST['menus'])) {
                    //         $retorno['status'] = 'ok';
                    //         $retorno['mensagem'] = 'Empresa atualizada com sucesso!2';
                    //         //$idEmpresa = $empresas->buscarEmpresa($_REQUEST['pullTracker']['editarId']);
                    //         //$retorno['idEmpresa'] = $idEmpresa['empresa'];
                    //     }
                    // }
                }

            }else{
                if($empresas->EmailRepetido($_REQUEST['email'],false)){
                    $retorno['status'] = 'Email Repetido';
                    $retorno['mensagem'] = 'Este e-mail já está cadastrado em nosso sistema.';
                } else {
                    if($empresas->cadastrarEmpresa(
                            $userJwt->id,
                            $_REQUEST['nome'],
                            $_REQUEST['isAdmin'],$status,$_REQUEST['email'],
                            $senha,$token,$_REQUEST['criado'],
                            $_REQUEST['maxUsers'],$_REQUEST['ativo'],
                            $_REQUEST['menus'])){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Empresa cadastrada com sucesso!';
                        //$idEmpresa = $empresas->buscarEmpresa($empresas);
                        //$retorno['idEmpresa'] = $idEmpresa['empresa'];

                    }
                }
            }

            echo json_encode(($retorno));

            break;

        case 'grupoPadraoEmpresa':

            $empresas = new empresas();

            if($rempresa = $empresas->buscarGrupoPadraoEmpresa($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['empresa'] = $rempresa;
            }else{
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não há grupo padrão para essa empresa';
            }

            echo json_encode($retorno);
        break;

        case 'visualizarEmpresa':

           $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma empresa encontrada !';

            if($_REQUEST['pullTracker']['grupos']!=''){
                $in = '(' . implode(',', $_REQUEST['pullTracker']['grupos']) .')';
            }else{
                $in = '';
            }

            if($agenteEmpresa = $empresas->visualizarEmpresa($_REQUEST['pullTracker']['id'],$in)){
                    $retorno['status'] = 'ok';
                    $retorno['detalhes'] = $agenteEmpresa;
                }


            echo json_encode($retorno);

            break;

            case 'visualizarDetalhesEmpresa':

            $empresas = new empresas();

             $retorno['status'] = 'false';
             $retorno['mensagem'] = 'Nenhuma empresa encontrada !';

             if($agenteEmpresa = $empresas->visualizarDetalhesEmpresa($_REQUEST['pullTracker']['id'])){
                     $retorno['status'] = 'ok';
                     $retorno['detalhes'] = $agenteEmpresa;
                 }


             echo json_encode(($retorno));

             break;


        case 'statusEmpresa':

            $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma resposta encontrada !';

            if($status = $empresas->statusEmpresa($_REQUEST['pullTracker']['id'])){
                $retorno['status'] = 'ok';
                $retorno['statusEmpresa'] = $status;
            }

            echo json_encode(arrayToUTF8($retorno));
            break;

        case 'ativarEmpresa':

            $empresas = new empresas();
            if($empresas->EmailRepetido($_REQUEST['email'],$_REQUEST['pullTracker']['id'])){
                $retorno['status'] = 'Email Repetido';
                $retorno['mensagem'] = 'Este e-mail já está cadastrado em nosso sistema.';
            }
            else{
                $empresas->ativarEmpresa($userJwt->id, 
                        $_REQUEST['pullTracker']['id']);
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Empresa e agentes ativados com sucesso!';
            }
            echo json_encode(arrayToUTF8($retorno));
            break;

        case 'desativarEmpresa':

            $empresas = new empresas();

            $empresas->desativarEmpresa($userJwt->id, 
                $_REQUEST['dataDesativar'], 
                $_REQUEST['pullTracker']['id']);
            $retorno['status'] = 'ok';
            $retorno['mensagem'] = 'Empresa e agentes desativados com sucesso!';

            echo json_encode(arrayToUTF8($retorno));
            break;

        case 'listarVersoes':

            $empresas = new empresas();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma versão encontrada !';
            if($versao = $empresas->listarVersoes()){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Versões encontradas !';
                $retorno['versoes'] = $versao;
            }
            echo json_encode($retorno);
            break;

        case 'listarIdiomas':

            $idioma = new idioma();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum Idioma encontrado!';
            if($listaIdiomas = $idioma->listarIdiomas()){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Idiomas encontrados!';
                $retorno['listaIdiomas'] = $listaIdiomas;
            }
            echo json_encode($retorno);
            break;

        case 'salvaMetaDadoDeTraducao':

            $user = new user();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma Alteração Feita!';
            if($user->update_user_meta($_REQUEST['userId'],'language',$_REQUEST['siglaIdioma'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Preferencia de Idioma Atualizado com Sucesso!';
            }
            echo json_encode($retorno);
            break;

        case 'salvarVersao':

            $empresas = new empresas();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Erro ao salvar a versão!';

            if($versao = $empresas->salvarVersao($_REQUEST['versao'],$_REQUEST['plataforma'],$_REQUEST['dataCriacao'])){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Versão salva com sucesso!';
            }


            echo json_encode($retorno);
            break;


        case 'apagarVersao':

            $retorno['status'] = 'Ok';
            $retorno['mensagem'] = 'Versão excluída com sucesso!';
            $empresas = new empresas();
            $empresas->apagarVersao($_REQUEST['versao']);

            echo json_encode($retorno);

        break;


        case 'informacoesEmpresa':

            $empresas = new empresas();

            $retorno['status'] = 'false';
            $in = '(' . implode(',', $_REQUEST['pullTracker']['grupos']) .')';
            if($informacoes = $empresas->informacoesEmpresa($_REQUEST['pullTracker']['empresaId'],$in)){
                $retorno['status'] = 'ok';
                $retorno['informacoes'] = $informacoes;
            }


            echo json_encode($retorno);
            break;

        case 'verificarIdEmpresa':

           $empresas = new empresas();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhum agente encontrado !';

            if($lista = $empresas->verificarIdEmpresa($_REQUEST['pullTracker']['empresaId'])){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Empresas encontradas!';
                    $retorno['listaAgentes'] = $lista;
                }

            echo json_encode($retorno);

            break;

        case "registerFolhaPonto":
            $retorno['status'] = 'fail';
            $retorno['mensagem'] = 'Não foi possível salvar a batida do ponto!';

            $registroFolhaPonto = new RegistroFolhaPonto();

            $retorno['idOnApp'] = $_REQUEST['idOnApp'];
            $retorno['timeServer'] = time() - ( $_REQUEST['timeEnvio'] - $_REQUEST['timeBatida'] );
            $retorno['timeEnvio'] = $_REQUEST['timeEnvio'];


            $registro =  $registroFolhaPonto->registrarBatida(
                $_REQUEST['idUsuario'],
                $_REQUEST['idRegistro'],
                $_REQUEST['timeBatida'],
                $_REQUEST['timeEnvio'],
                $_REQUEST['tipo'],
                $_REQUEST['origem']
            );

           // $retorno['registro'] = $this->notificar($_REQUEST['idUsuario'],'Notificação',$registro['msg']);
           $retorno['registro'] = $registro;

            if($registro['id']){
                $retorno['id'] = $registro['id'];
                $retorno['status'] = 'ok';
                $retorno['mensagem']  = 'registro ponto feito com sucesso!';
            }
            echo json_encode($retorno);
            break;
        case "get_server_time_hash":
            header("Content-type: text/html; charset=utf-8");
            if($_REQUEST['arquivo']){
                echo file_get_contents('/var/log/ntpdate/'.$_REQUEST['arquivo']);
            }else{
                $thelist = "Arquivos:<ul>";
                if ($handle = opendir('/var/log/ntpdate/')) {
                    while (false !== ($file = readdir($handle)))
                    {
                        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) != 'chain')
                        {
                            $thelist .= "\n<li><a href=\"/?pullTracker[act]=get_server_time_hash&arquivo=$file\">$file</a></li>";
                        }
                    }
                    closedir($handle);
                }
                echo $thelist."</ul>";
            }
            break;
        case "get_folha_ponto_chain":
            echo file_get_contents('/var/log/ntpdate/block_folha_ponto.chain');
            break;
        case 'validateHashFolhaPonto':
            $registroFolhaPonto = new RegistroFolhaPonto();
            $registroFolhaPonto->validateHashFolhaPonto($_REQUEST['id']);
            break;
        case "espelho_folha":
            $retorno = array("status"=>"false","msg"=>"Nenhum dado encontrado!");

            if($_REQUEST['pullTracker']['agenteFilterID'] && $_REQUEST['pullTracker']['inicio'] && $_REQUEST['pullTracker']['fim']){

                $registroFolhaPonto = new RegistroFolhaPonto();
                $retorno = $registroFolhaPonto->espelhoFolhaPonto(
                        $_REQUEST['pullTracker']['agenteFilterID'] ,
                        $_REQUEST['pullTracker']['inicio'] ,
                        $_REQUEST['pullTracker']['fim'],
                        $_REQUEST['pullTracker']['diasConfig']
                    );

            }

            echo json_encode($retorno);
            break;

        case 'toogle_registro_ponto' :

            $registroFolhaPonto = new RegistroFolhaPonto();
            $retorno = $registroFolhaPonto->toogleRegistroPonto(
                    $userJwt->empresa,
                    $_REQUEST['pullTracker']['user_id'],
                    json_decode($_REQUEST['pullTracker']['registro_ponto'] )
            );

            echo json_encode(array("batida"=> $retorno));
            break;

        case 'espelho_folha_afd':
            $registroFolhaPonto = new RegistroFolhaPonto();
            $empresas = new empresas();
            $retorno = $registroFolhaPonto->gerarArquivoAFD($userJwt->empresa);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            header('Content-Disposition: attachment; filename=REP'.str_pad($empresas->get_empresa_meta($userJwt->empresa,'REP'),17,'9').'.txt');
            header('Content-Type: application/octet-stream');

            if (function_exists('mb_strlen')) {
                $size = mb_strlen($retorno, '8bit');
            } else {
                $size = strlen($retorno);
            }
            header('Content-length: ' . $size);
            echo $retorno;
            die();
            break;

        case "enviarErrorReport":

            // Recebe mensagem de erro em caso de falha na sincronização
            $pathDir = dirname(__FILE__)."/reports";
            if(!is_dir($pathDir)){
                mkdir($pathDir);
            }
            $fileName = time();
            $myfile = fopen($pathDir."/".$fileName, "w") or die("Unable to open file $fileName!");

            ob_start();

            //print_r($_REQUEST);
            echo json_encode($_REQUEST);

            $text = ob_get_contents();

            ob_end_clean();

            fwrite($myfile, $text);
            fclose($myfile);

            $retorno = array();
            $retorno["arquivo"] = $fileName;


            echo json_encode($retorno);

            break;


            /********* INICIO FOLHA PONTO **********/

        case "registrarBatida":

                $registroFolhaPonto = new RegistroFolhaPonto();

                $idUsuario      = $_REQUEST["idUsuario"] ? $_REQUEST["idUsuario"]  : 161   ;
                $idRegistro     = $_REQUEST["idRegistro"]? $_REQUEST["idRegistro"] : 7892979   ;
                $timeBatida     = $_REQUEST["timeBatida"]? $_REQUEST["timeBatida"] : 1504122794;
                $timeEnvio      = $_REQUEST["timeEnvio"] ? $_REQUEST["timeEnvio"]  : 1504122918;
                $tipo           = $_REQUEST["tipo"]      ? $_REQUEST["tipo"]       : 1 ;

                $retorno = $registroFolhaPonto->registrarBatida($idUsuario,$idRegistro,$timeBatida,$timeEnvio,$tipo );

                echo json_encode($retorno);

                break;

            /********* FIM FOLHA PONTO **********/


        case 'registerPositionTeste':

            $geo = new geo();
            $user = new user();

             /*
            {
                "location": {
                    "event": "motionchange",
                    "is_moving": false,
                    "uuid": "cb0ea42b-64f7-4ec6-b769-d0dbe03f5a97",
                    "timestamp": "2017-09-15T14:08:56.470Z",
                    "odometer": 11453,
                    "coords": {
                        "latitude": -20.4638882,
                        "longitude": -45.4288993,
                        "accuracy": 21.6,
                        "speed": -1,
                        "heading": -1,
                        "altitude": -1
                    },
                    "activity": {
                        "type": "still",
                        "confidence": 100
                    },
                    "battery": {
                        "is_charging": true,
                        "level": 0.63
                    },
                    "extras": {}
                },
                "teste": "teste"
            }
            */
            $string = file_get_contents( 'php://input' );
            if(!$json = json_decode($string)){
                $json = json_decode(json_encode($_REQUEST));
            }

            $dataHoraPonto = strtotime($json->location->timestamp) * 1000;
            if(!$dataHoraPonto){
                $dataHoraPonto = preg_replace('( [(][^\)]*[)]*)','', $json->location->timestamp);
                $dataHoraPonto = strtotime($dataHoraPonto) * 1000;
            }

            $array = array(
                'longitude' => $json->location->coords->longitude,
                'latitude' => $json->location->coords->latitude,
                'accuracy' => $json->location->coords->accuracy,
                'speed' => $json->location->coords->speed,
                'heading' => $json->location->coords->heading,
                'altitude' => $json->location->coords->altitude,
                'timestamp' => $dataHoraPonto,
                'location_type' => $json->location->event ? $json->location->event : 'background',
                'pullTracker' => array(
                    'id' => $json->pullTracker->id,
                ),
                'bateria' => $json->location->battery->level * 100,
                'provider' => json_encode($json->location->activity),
                'uuid' => $json->location->uuid,
            );


            $userBase = mysqli_fetch_assoc($user->getOne('*',$array['pullTracker']['id'],'users'));


            // verifica se o ponto ja existe

            $pontoBase = mysqli_fetch_assoc($geo->getOne("*",$array['pullTracker']['id'],"registros"," WHERE uuid = '".$json->location->uuid."' and idUser="));
            if($pontoBase && $pontoBase["id"] && $pontoBase['location_type'] != 'initRastreamento' && $pontoBase['location_type'] != 'stopRastreamento'){
                $retorno = array("status" => "ok", "idOnApp" => "","id"=>$pontoBase["id"]);
                $db->desconecta();
                echo json_encode($retorno);
                die();


                // se for um ponto de init ou stop atualiza para com as cordenadas enviadas
            }else if($pontoBase && $pontoBase["id"] && ($pontoBase['location_type'] == 'initRastreamento' || $pontoBase['location_type'] == 'stopRastreamento')){

                foreach($array as $key => $valor){
                    if(!in_array($key,array("location_type","uuid","idUser"))){
                        $geo->update($valor,$key,"registros",$pontoBase["id"]);
                    }
                }

                $retorno = array("status" => "ok", "idOnApp" => "","id"=>$pontoBase["id"]);
                $db->desconecta();
                echo json_encode($retorno);
                die();
            }


            $retorno = $geo->registerPosition($array['pullTracker']['id'],$array['latitude'],$array['longitude'],$array['timestamp'],json_encode($array));


            if($retorno['status']!='ok'){
                http_response_code(405);
                print_r($json);
                print_r($array);
            }else{
                if(!strtotime($userBase['lastseen']) || strtotime($userBase['lastseen']) < $dataHoraPonto / 1000){
                    $user->updateUser($array['longitude'],'longitude',$array['pullTracker']['id']);
                    $user->updateUser($array['latitude'],'latitude',$array['pullTracker']['id']);
                    $user->updateUser(date('Y-m-d H:i:s',$dataHoraPonto / 1000 ),'lastseen',$array['pullTracker']['id']);
                    $user->updateUser($array['bateria'],'bateria',$array['pullTracker']['id']);
                }
            }
            echo json_encode($retorno);

            break;


        case 'relatoriosServerSideHorasRastreadas':

                 //Array de Datas

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado';

                $relatorio = new Relatorio();
                if($retorno['relatorioHorasRastreadas'] = $relatorio->horasRastreadas($userJwt->empresa,$_REQUEST['pullTracker']['inicio'],$_REQUEST['pullTracker']['fim'],$_REQUEST['pullTracker']['agenteFilterID'],$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['pullTracker']['gruposUser'],$_REQUEST['pullTracker']['filtroDesativados'])){

                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                }

                echo json_encode($retorno);


                break;

            case 'relatoriosServerSideTarefasAgente':

                //Array de Datas

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado Para Esta Data';

                $relatorio = new Relatorio();
                if($retorno['relatorioTarefasAgente'] = $relatorio->tarefasPorAgente($userJwt->empresa,$_REQUEST['pullTracker']['inicio'],$_REQUEST['pullTracker']['fim'],$_REQUEST['pullTracker']['agenteFilterID'],$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['pullTracker']['gruposUser'],$_REQUEST['pullTracker']['filtroDesativados'])){

                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                }

                echo json_encode($retorno);


                break;

            case 'relatoriosServerSideKmRodados':

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado Para Esta Data';

                $relatorio = new Relatorio();
                if($retorno['relatorioKmRodados'] = $relatorio->kmRodados($userJwt->empresa,$_REQUEST['pullTracker']['inicio'],$_REQUEST['pullTracker']['fim'],$_REQUEST['pullTracker']['agenteFilterID'],$_REQUEST['pullTracker']['grupoAtivo'],$_REQUEST['pullTracker']['gruposUser'],$_REQUEST['pullTracker']['filtroDesativados'])){

                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                }

                echo json_encode($retorno);


                break;

            case 'getKmEsperadoServerSide':

                $relatorio = new relatorio();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Valor Encontrado nessa Data!';
                $kmEmtarefa = $relatorio->getKmEsperadoDia($_REQUEST['idUser'],$_REQUEST['rota'],$_REQUEST['data']);
                // if($kmEmtarefa){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Quantide de Km Encontrada';
                        $retorno['kmEmTarefa'] = floatval(round($kmEmtarefa, 2));
                // }

                echo json_encode($retorno);

            break;

            case 'relatoriosKmEntreTarefas':

                //Array de Datas

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado';

                $relatorio = new Relatorio();
                if($retorno['relatorioHorasRastreadas'] = $relatorio->relatorioKmEntreTarefas($userJwt->empresa,$_REQUEST['dataInicio'],$_REQUEST['dataFim'],$_REQUEST['agenteEspecifico'])){

                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                }

                echo json_encode($retorno);


                break;

            case 'getRelatorioTarefasExpandidoSS':
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado';

                $relatorio = new Relatorio();
                if($tarefasPorAgente = $relatorio->relatorioTarefasExpandido($_REQUEST['rota'],$_REQUEST['data'])){
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                    $retorno['tarefas'] = $tarefasPorAgente['tarefas'];
                    $retorno['nome'] = $tarefasPorAgente['nome'];
                }

                echo json_encode($retorno);
                break;

            case 'testeDeNotPainel':

                $notificacao = new notificacao();
                $notificacao->criarNotificacaoAgenteEspecifico(11,'Tarefa Encadeada de ....',533,0);

                break;

            case 'detalhesHorasRastreadasKmRodados':

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Dado encontrado Para Esta Data';

                $relatorio = new Relatorio();
                if($retorno['detalhes'] = $relatorio->detalhesHorasRastreadasKmRodados($_REQUEST['pullTracker']['agenteFilterID'], $_REQUEST['pullTracker']['rota'], $_REQUEST['pullTracker']['data'])){

                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados';

                }

                echo json_encode($retorno);


                break;

                case 'detalhesHorasRastreadasKmRodadosPeriodo':

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhum Dado encontrado Para Esta Data';

                    $relatorio = new Relatorio();
                    if($retorno['detalhes'] = $relatorio->relatorioDeDespesas($_REQUEST['pullTracker']['agenteFilterID'], $_REQUEST['pullTracker']['rota'], $_REQUEST['pullTracker']['dataInit'], $_REQUEST['pullTracker']['dataEnd'])){

                        $retorno['status'] = 'OK';
                        $retorno['mensagem'] = 'Dados Encontrados';

                    }

                    echo json_encode($retorno);


                    break;

            case 'doMatriz':

                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Erro ao savar Matriz de dados.';

                if($matrizSalva = $matriz->salvarMatrizEmpresa($userJwt->empresa,$_REQUEST['titulo'],$_REQUEST['colunas'],json_decode($_REQUEST['dados']),$_REQUEST['idMatriz'],$_REQUEST['dadosForm'])){
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Matriz de dados salva com sucesso!';
                    $retorno['post'] = $matrizSalva;
                }
                echo safe_json_encode($retorno);

                break;

            case 'loadListaMatrizEmpresa':

                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma matriz de dados encontrada.';
                $retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $matriz->loadListaMatrizEmpresa($userJwt->empresa)){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Listas encontradas!';
                    $retorno['post'] = $listas;
                }
                echo safe_json_encode($retorno);

                break;

            case 'loadMatrizEmpresa':

                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma matriz de dados encontrada.';
                $retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $matriz->loadMatrizEmpresa($userJwt->empresa,$_REQUEST['idMatriz'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Listas encontradas!';
                    $retorno['post'] = $listas;
                }
                echo safe_json_encode($retorno);
                break;

            case 'loadColunasMatrizEmpresa':

                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma matriz de dados encontrada.';
                $retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $matriz->loadColunasMatrizEmpresa($userJwt->empresa,$_REQUEST['idMatriz'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Listas encontradas!';
                    $retorno['post'] = $listas;
                }
                echo safe_json_encode($retorno);
                break;

            case 'loadColunaMatrizEmpresa':
                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma matriz de dados encontrada.';
                $retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $matriz->loadColunaMatrizEmpresa($_REQUEST['idMatriz'],$_REQUEST['idCampo'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Coluna encontrada!';
                    $retorno['post'] = $listas;
                }
                echo safe_json_encode($retorno);
                break;

            case 'loadValoresMatrizEmpresa':
                $matriz = new MatrizDados();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum valor encontrado.';
                $retorno['post'] = $_REQUEST;
                $gp = false;
                $usr = false;
                if($listas = $matriz->loadValoresMatrizEmpresa($userJwt->empresa,$_REQUEST['idMatriz'],$_REQUEST['idCampo'],$_REQUEST['valor'])){
                    $retorno['status'] = 'true';
                    $retorno['mensagem'] = 'Valores encontrados!';
                    $retorno['post'] = $listas;
                }
                echo safe_json_encode($retorno);

                break;
            case 'hasInitToday':
                    $registro = new RegistroFolhaPonto();
                    $hoje = date('Y-m-d');
                    echo safe_json_encode($registro->getStatusUsuarioDia($_REQUEST['user'], $hoje));
                break;

            case 'apagaMetaFormulario':
                $formulario = new formulario();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma Imagem Encontrada';

                $user = new user();

                if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                    if($formulario->delete_formulario_meta($_REQUEST['pullTracker']['idForm'],'foto')){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Imagem Deletada';
                    }
                }else{
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Usuario Não autorizado';
                }

                echo json_encode($retorno);
            break;

            case 'verificaRespostaPergunta':
                    $formulario = new formulario();
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhuma Resposta Encontrada';
                    $retorno['respostas'] = '';

                    $user = new user();

				    if($user->getUserByTokenPainel($_REQUEST['idUsuario'],$_REQUEST['token'])){
                        $retorno['respostas'] = $formulario->contaQuantidadeResposta($_REQUEST['pullTracker']['idPergunta']);
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Respostas Encontradas';
                    }else{
                        $retorno['status'] = 'false';
                        $retorno['mensagem'] = 'Usuario Não autorizado';
                    }

                    echo json_encode($retorno);
                break;

            case 'verificaFormTemCabecalho':
                    $formulario = new formulario();
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nenhuma Resposta Encontrada';

                    $user = new user();

                    $retorno['perguntasIncapacitantes'] = $formulario->verificaPerguntasParacabecalho($_REQUEST['pullTracker']['IdForm']);
				    if($retorno['perguntasIncapacitantes'] == 0){
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'Formulario sem perguntas que impedem';
                    }else{
                        $retorno['status'] = 'false';
                        $retorno['mensagem'] = 'Esse formulario possui perguntas do tipo Formulario e não pode ser utilizado como um cabeçalho';
                    }

                    echo json_encode($retorno);
                break;

            case 'whirpool_import_os':

                if(!$_POST['lista_os']){
                    echo json_encode([
                        "status" => "false",
                        "mensagem" => "parametro lista_os obrigatório"
                    ]);
                    die();
                }

                $retorno = $whirlpoll->sincronizarOS($_POST['lista_os'],$_POST['dados_os'] , $_POST['dados_cliente'] , $_POST['appliances'] );
                echo json_encode($retorno);

                break;

            case 'whirpool_get_dados':

                if(!$_REQUEST['tarefas']){
                    echo json_encode([
                        "status" => "false",
                        "mensagem" => "parametro tarefas obrigatório"
                    ]);
                    die();
                }


                $retorno = $whirlpoll->getDadosOS(json_decode($_REQUEST['tarefas']));
                //$retorno = $whirlpoll->post_save_tarefa_concluida("45506597",2027);
                echo json_encode($retorno);
                die();

                break;

            case 'whirpool_send_OS':

                if(! $_REQUEST['tarefa'] || !$_REQUEST['user']){
                    echo json_encode([
                        "status" => "false",
                        "mensagem" => "parametro tarefas e usuário obrigatório"
                    ]);
                    die();
                }

                $retorno = $whirlpoll->post_save_tarefa_concluida($_REQUEST['tarefa'] ,$_REQUEST['user']);// "45506597",2027);
                echo json_encode($retorno);

                break;

            case 'whirpool_print_os':

                if(!$_REQUEST['tarefa']){
                    echo json_encode([
                        "status" => "false",
                        "mensagem" => "parametro tarefa obrigatório"
                    ]);
                    die();
                }

                $pdf = $whirlpoll->generatePDF($_REQUEST['tarefa']);
                $pdf->Output();
                die();

                break;

            case 'tegma_resync_task':

                if(!$_REQUEST['tarefa']){
                     $retorno = [
                        "status" => "Error",
                         "mensagem" => "Campo tarefa obrigatório"
                     ];
                } else {
                    $retorno = [
                        "status" => "ok",
                        "tarefas"=> []
                    ];

                    /**
                     * Impedindo que multiplas linhas sejas inseridas na tabela por causa de uma mesma tarefa possuir mais de um chassi
                     */
                    $listaTarefasJaReprocessadas = [];

                    foreach($_REQUEST['tarefa'] as $tarefa) {

                        if ( in_array($tarefa['idTarefa'], $listaTarefasJaReprocessadas) ) {
                            continue;
                        }

                        $tegma->execute('UPDATE tegma_integracao_chassi SET status = 5 WHERE idTarefa = ' . $tarefa['idTarefa']);
                        $retorno['tarefas'][] = $tegma->post_save_tarefa_concluida($tarefa['idTarefa'],$userJwt->id);
                        $listaTarefasJaReprocessadas[] = $tarefa['idTarefa'];
                    }
                }
                echo json_encode($retorno);
                die();
                break;

            case 'tegmaBuscaChassis':
                $retorno = [
                    "status" => 'ok',
                    'respostas' => $tegma->tegmaBuscaChassis($_REQUEST['pullTracker'])
                ];
                echo json_encode($retorno);
                break;

            case 'testeGeometria':

                $poligono = array( [0,0],[100,0],[100,100],[0,100] );
                print_r($poligono);
                $ponto = [99,101];
                print_r($ponto);

                if(isInside($poligono, count($poligono), $ponto)){
                    echo 'Dentro';
                }else{
                    echo 'Fora';
                }

                break;


            case 'teste':
                // do_action('post_save_tarefa_concluida', array(46608764,2924));

                break;

            case 'indicadoresTarefa':

                $exportar = ($_REQUEST['exportar'] == false || $_REQUEST['exportar'] == 'false') ? false : $_REQUEST['exportar'];

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Indicadores Encontrados';

                $tarefa = new indicadoresTarefa();
                if($indicadores = $tarefa->get_indicadores_tarefa($_REQUEST, $exportar)){
                    $retorno = $indicadores;
                }

                if(!$exportar){
                    echo json_encode($retorno);
                }

                break;

            case 'atualizaIndicadores':

                $tarefa = new indicadoresTarefa();
                $tarefa->atualiza_tabela_Indicadores();

                break;

            case 'atualizaIndicadores_tarefaPorId':

                $tarefa = new indicadoresTarefa();
                $tarefa->atualiza_tabela_Indicadores($_REQUEST['idTarefa']);
                // $tarefa->atualiza_tabela_Indicadores();

                break;

            case 'atualiza_indicadores_inicio':

                $tarefa = new indicadoresTarefa();
                $tarefa->atualiza_indicadores_inicio($_REQUEST['idTarefa']);

                break;

            case 'atualiza_indicadores_fim':

                $tarefa = new indicadoresTarefa();
                $tarefa->atualiza_indicadores_fim($_REQUEST['idTarefa']);

                break;

            /**
             * TASK/533- Kpi de médias
             */
            case 'montaKpiMedias':

                $gruposParticipantes = $_REQUEST['grupos'];
                $grupoEspecifico = $_REQUEST['grupoEspecifico'];
                $agenteEspecifico = $_REQUEST['agenteEspecifico'];
                $empresa = $userJwt->empresa;
                $dataInicio = $_REQUEST['dataInicio'];
                $dataFim = $_REQUEST['dataFim'];
                // $exportar = ($_REQUEST['exportar'] == false || $_REQUEST['exportar'] == 'false') ? false : $_REQUEST['exportar'];



                $kpiMedias = new KpiMedias();
                if($indicadores = $kpiMedias->montaKpi($gruposParticipantes, $empresa, $dataInicio, $dataFim, $grupoEspecifico, $agenteEspecifico)){
                    $retorno = $indicadores;
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Dados Encontrados Encontrados';
                }else{
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Nennhum dado encontrado';
                }

                // if(!$exportar){
                    echo json_encode($retorno);
                // }

                break;


            case 'testeRegimeHorario':
                $regimeTeste =  new RegimeHorario();

//                 echo('Array Após de Inicializar
// ');
                $regimeTeste->inicializaRegimeSemanal();
                // print_r(($regimeTeste->regimeSemanal));

//                 echo('Array Após de Carregar Empresa
// ');
                $regimeTeste->carregaRegimebyIdEmpresa(7);
                // print_r($regimeTeste->primeiraHoraUtilDoDia('18/07/2018'));

                print_r( $regimeTeste->verificaQuantidadeHorasUteisNoIntervalo('11/07/2018 20:11:50', '18/07/2018 05:00:00'));


                // $teste = explode(':', '26:05');
                // print_r($teste);

                break;



            //--------------------      --------------------      --------------------//
            //Requisições relativas à categorias


            //Categorias de Formulário//
            case 'loadCategoriasFormulario':
                $form = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma categoria encontrada.';

                if($categorias = $form->categoriasFormularios($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Categorias encontradas com sucesso!';
                    $retorno['categorias'] = $categorias;
                }

                echo json_encode($retorno);
            break;

            case 'loadChecklistItensFormulario':
                $forms = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum formulario encontrado.';

                if($formsCat = $forms->loadFormulariosCategorias($userJwt->empresa)){
                    $retorno['mensagem'] = 'Categorias preenchidas com sucesso!';
                    $retorno['status'] = 'ok';
                    $retorno['result'] = $formsCat;

                }

                echo json_encode($retorno);
            break;

            case 'criarCategoriaFormulario':
            $forms = new formulario();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Não foi possível criar a categoria!';
            $idVerifica = $_REQUEST['editarId'];
            if($_REQUEST['editarId']!=''){ //testa se esta editando

                if($forms->categoriaExiste($_REQUEST['nome'],$userJwt->empresa)&&($idVerifica !=  $forms->categoriaExiste($_REQUEST['nome'],$userJwt->empresa))){ //verifica se o usuario alterou o nome e se a categoria já existe
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $_REQUEST['editarId'];
                    $forms->update($_REQUEST['nome'],'nome','form_categorias',$id);
                    $retorno['mensagem'] = 'Categoria alterada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }else{
                if($forms->categoriaExiste($_REQUEST['nome'],$userJwt->empresa)){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $forms->criarCategoria($_REQUEST['nome'],$userJwt->empresa);
                    $retorno['mensagem'] = 'Categoria criada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }
            //#PR-566 deleta todos os itens e insere todos que vem da requisicao
             $forms->deletaCategoriaFormulario($id);

            if(is_array($_REQUEST['itens'])){
                $forms->enterCategorias($_REQUEST['itens'],'form_meta_categorias',$id);
            }
            echo json_encode($retorno);
            break;

            case 'montarCategoriaFormulario':
                $forms = new formulario();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível encontrar formulários!';

                if($catForm = $forms->montarCategoriaFormulario($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Formulários encontrados com sucesso!';
                    $retorno['forms'] = $catForm;
                }

                echo json_encode($retorno);
            break;


            //Categorias de QRCode//

            /*
            case 'loadCategoriasQRCode2':

            $qrcodes = new QRCode();

            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'QRCode\'s não encontrados !';

            if($qrcodeList = $qrcodes->listar($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'QRCode\'s encontrados';
                $retorno['qrcode'] = $qrcodeList;
            }
            echo json_encode($retorno);
            break;*/



            case 'loadCategoriasQRCode':
                $qrcode = new qrcode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma categoria encontrada.';

                if($categorias = $qrcode->categoriasQRCode($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Categorias encontradas com sucesso!';
                    $retorno['categorias'] = $categorias;
                }

                echo json_encode($retorno);
            break;


            case 'loadChecklistItensQRCode':
                $qrcode = new QRCode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum qrcode encontrado.';

                if($qrcodesCat = $qrcode->loadIDTituloQRCode($userJwt->empresa)){
                    $retorno['mensagem'] = 'Categorias preenchidas com sucesso!';
                    $retorno['status'] = 'ok';
                    $retorno['result'] = $qrcodesCat;
                }

                echo json_encode($retorno);
            break;


            case 'criarCategoriaQRCode':
            $qrcode = new QRCode();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Não foi possível criar a categoria!';
            $idVerifica = $_REQUEST['editarId'];
            if($_REQUEST['editarId']!=''){ //testa se esta editando

                if($qrcode->categoriaExiste($_REQUEST['nome'],$userJwt->empresa)&&($idVerifica !=  $qrcode->categoriaExiste($_REQUEST['nome'],$userJwt->empresa))){ //verifica se o usuario alterou o nome e se a categoria já existe
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $_REQUEST['editarId'];
                    $qrcode->update($_REQUEST['nome'],'nome','qrcode_categorias',$id);
                    if($categorias = $qrcode->buscaQrCodesCategoriaPublico($id , $userJwt->empresa)){ //busca os QRCodes públicos pertencentes a esta categoria

                        $listaQRCodes = $qrcode->categoriasMerge($categorias, array($_REQUEST['itens']));    //faz um merge entre todos os QRCodes da categoria                ;
                        $qrcode->deletaCategoriaQRCode($id);             //exclui todos os itens selecionados
                        if(is_array($_REQUEST['itens'])){
                            $qrcode->enterCategoriasQRCode($listaQRCodes,'qrcode_meta_categorias',$id); //insere todos os QRCodes.
                        }
                    }else{ //se não houver QRCodes públicos
                        $qrcode->deletaCategoriaQRCode($id);
                        if(is_array($_REQUEST['itens'])){
                            $qrcode->enterCategoriasQRCode($_REQUEST['itens'],'qrcode_meta_categorias',$id);
                        }
                    }
                    $retorno['mensagem'] = 'Categoria alterada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }else{
                if($qrcode->categoriaExiste($_REQUEST['nome'],$userJwt->empresa)){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $qrcode->criarCategoria($_REQUEST['nome'],$userJwt->empresa);
                    $qrcode->deletaCategoriaQRCode($id);
                    if(is_array($_REQUEST['itens'])){
                        $qrcode->enterCategoriasQRCode($_REQUEST['itens'],'qrcode_meta_categorias',$id);
                    }
                    $retorno['mensagem'] = 'Categoria criada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }




            echo json_encode($retorno);
            break;

            case 'montarCategoriaQRCode':

                $qrcode = new qrcode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível encontrar QR Codes!';

                if($catQrcode = $qrcode->montarCategoriaQRcode($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Formulários encontrados com sucesso!';
                    $retorno['categorias'] = $catQrcode;
                }
                echo json_encode($retorno);
            break;

            //Categorias de QRCode Público//
            //case 'categoriasQRCodes':
            case 'loadCategoriasQRCodePublico':
            $qrcode = new qrcode();
            // $qrcode->atualizaStatusQRCodePublico($userJwt->empresa);
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Nenhuma categoria encontrada.';

            if($categorias = $qrcode->categoriasQRCodePublico($userJwt->empresa)){
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Categorias encontradas com sucesso!';
                $retorno['categorias'] = $categorias;
            }

            echo json_encode($retorno);
        break;

            //case 'loadQRCodesIDTitulo':
            case 'loadChecklistItensQRCodePublico':
                $qrcode = new QRCode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum qrcode encontrado.';

                if($qrcodesCat = $qrcode->loadIDTituloQRCodePublico($userJwt->empresa)){
                    $retorno['mensagem'] = 'Categorias preenchidas com sucesso!';
                    $retorno['status'] = 'ok';
                    $retorno['result'] = $qrcodesCat;
                }

                echo json_encode($retorno);
            break;

            case 'criarCategoriaQRCodePublico':
                $qrcode = new QRCode();
                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível criar a categoria!';
                $idVerifica = $_REQUEST['editarId'];
                if($_REQUEST['editarId']!=''){ //testa se esta editando

                    if($qrcode->categoriaExistePublico($_REQUEST['nome'],$userJwt->empresa)&&($idVerifica !=  $qrcode->categoriaExistePublico($_REQUEST['nome'],$userJwt->empresa))){ //verifica se o usuario alterou o nome e se a categoria já existe
                        $retorno['status'] = 'false';
                        $retorno['mensagem'] = 'Esta categoria já existe';
                    }else{
                        $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                        $id = $_REQUEST['editarId'];
                        $qrcode->update($_REQUEST['nome'],'nome','qrcode_categorias',$id);
                        if($categorias = $qrcode->buscaQrCodesCategoria($id , $userJwt->empresa)){ //busca os QRCodes que não são públicos pertencentes a esta categoria

                            $listaQRCodes = $qrcode->categoriasMerge($categorias, array($_REQUEST['itens']));    //faz um merge entre todos os QRCodes da categoria                ;
                            $qrcode->deletaCategoriaQRCode($id);             //exclui todos os itens selecionados
                            if(is_array($_REQUEST['itens'])){
                                $qrcode->enterCategoriasQRCode($listaQRCodes,'qrcode_meta_categorias',$id); //insere todos os QRCodes.
                            }
                        }else{ //se não houver QRCodes que não são públicos
                            $qrcode->deletaCategoriaQRCode($id);
                            if(is_array($_REQUEST['itens'])){
                                $qrcode->enterCategoriasQRCode($_REQUEST['itens'],'qrcode_meta_categorias',$id);
                            }
                        }
                        $retorno['mensagem'] = 'Categoria alterada com sucesso!';
                        $retorno['status'] = 'ok';
                    }
                }else{
                    if($qrcode->categoriaExistePublico($_REQUEST['nome'],$userJwt->empresa)){
                        $retorno['status'] = 'false';
                        $retorno['mensagem'] = 'Esta categoria já existe';
                    }else{
                        $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                        $id = $qrcode->criarCategoriaPublico($_REQUEST['nome'],$userJwt->empresa);
                        $qrcode->deletaCategoriaQRCode($id);
                        if(is_array($_REQUEST['itens'])){
                            $qrcode->enterCategoriasQRCode($_REQUEST['itens'],'qrcode_meta_categorias',$id);
                        }
                        $retorno['mensagem'] = 'Categoria criada com sucesso!';
                        $retorno['status'] = 'ok';
                    }
                }

                // if(is_array($_REQUEST['exitItens'])){
                //     foreach($_REQUEST['exitItens'] as $u){
                //     $qrcode->exitCatQRCode($u,$id);
                //     }
                // }

                // if(is_array($_REQUEST['itens'])){
                //     foreach($_REQUEST['itens'] as $u){
                //     $qrcode->enterCategorias($u,$id);
                //     }
                // }
                // $qrcode->deletaCategoriaQRCode($id);
                // if(is_array($_REQUEST['itens'])){
                //     $qrcode->enterCategoriasQRCode($_REQUEST['itens'],'qrcode_meta_categorias',$id);
                // }
                echo json_encode($retorno);
            break;

            case 'montarCategoriaQRCodePublico':

                $qrcode = new qrcode();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível encontrar QR Codes!';

                if($catQrcode = $qrcode->montarCategoriaQRcodePublico($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Formulários encontrados com sucesso!';
                    $retorno['categorias'] = $catQrcode;
                }
                echo json_encode($retorno);
            break;

            case 'buscaCategoriasFilho':
                $groupClass = new Grupo();
                $retorno['status'] = 'ok';
                $retorno['mensagem'] = 'Grupos Filhos';
                $retorno['gruposfilhos'] = $groupClass->getChildIdGrupo($_REQUEST['grupo']);
                echo json_encode($retorno);

            break;

            /**
             * TASK/577 - Exclusão de Grupo
             *
             * Função para excluir o grupo
             */
            case 'deleteGrupo':

                $grupoObject = new Grupo();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível excluir o Grupo!';

                if($gruposExcluidos = $grupoObject->excluirGrupoAndFilhos($_REQUEST['idGrupo'])){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'O Grupo foi excluído com sucesso!';
                    $retorno['idGrupo'] = $gruposExcluidos;
                }
                echo json_encode($retorno);

            break;

            //Categorias de Modelos de Tarefa

            case 'loadCategoriasModeloTarefa':
                $modelTF = new ModeloTarefa();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhuma categoria encontrada.';

                if($categorias = $modelTF->categoriasModeloTarefa($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Categorias encontradas com sucesso!';
                    $retorno['categorias'] = $categorias;
                }

                echo json_encode($retorno);
            break;


            case 'loadChecklistItensModeloTarefa':
                $modelTF = new ModeloTarefa();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Nenhum Modelo de Tarefa encontrado.';

                if($modelTFCategoria = $modelTF->loadModeloTarefa($userJwt->empresa)){
                    $retorno['mensagem'] = 'Categorias preenchidas com sucesso!';
                    $retorno['status'] = 'ok';
                    $retorno['result'] = $modelTFCategoria;

                }

                echo json_encode($retorno);
            break;

            case 'criarCategoriaModeloTarefa':
            $modelTF = new ModeloTarefa();
            $retorno['status'] = 'false';
            $retorno['mensagem'] = 'Não foi possível criar a categoria!';
            $idVerifica = $_REQUEST['editarId'];
            if($_REQUEST['editarId']!=''){ //testa se esta editando

                if($modelTF->categoriaExisteModeloTarefa($_REQUEST['nome'],$userJwt->empresa)&&($idVerifica !=  $modelTF->categoriaExisteModeloTarefa($_REQUEST['nome'],$userJwt->empresa))){ //verifica se o usuario alterou o nome e se a categoria já existe
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $_REQUEST['editarId'];
                    $modelTF->update($_REQUEST['nome'],'nome','modelo_tarefa_categorias',$id);
                    $retorno['mensagem'] = 'Categoria alterada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }else{
                if($modelTF->categoriaExisteModeloTarefa($_REQUEST['nome'],$userJwt->empresa)){
                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Esta categoria já existe';
                }else{
                    $_REQUEST['itens'] = json_decode($_REQUEST['itens']);
                    $id = $modelTF->criarCategoriaModeloTarefa($_REQUEST['nome'],$userJwt->empresa);
                    $retorno['mensagem'] = 'Categoria criada com sucesso!';
                    $retorno['status'] = 'ok';
                }
            }
             $modelTF->deletaCategoriaModeloTarefa($id);

            if(is_array($_REQUEST['itens'])){
                $modelTF->enterCategorias($_REQUEST['itens'],'modelo_tarefa_meta_categorias',$id);
            }
            echo json_encode($retorno);
        break;

            case 'montarCategoriaModeloTarefa':
                $modelTF = new ModeloTarefa();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível encontrar o Modelo de Tarefa!';

                if($modelTFCategoria = $modelTF->montarCategoriaModeloTarefa($userJwt->empresa)){
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'Modelos de Tarefa encontrados com sucesso!';
                    $retorno['modelTF'] = $modelTFCategoria;
                }

                echo json_encode($retorno);
            break;

            case 'apagaModeloTarefa':
                $modelTF = new ModeloTarefa();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possível excluir o modelo de tarefa';

                $editarId = false;
                if(is_numeric($_REQUEST['editarId'])){
                    $editarId = $_REQUEST['editarId'];
                    $modelTF->update(0,'empresa','modelo_tarefa',$editarId);
                    //$modelTF->update($userJwt->empresa,'originalId','modelo de tarefa',$editarId);
                    $modelTF->removeCategoriaModeloTarefa($editarId);
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Modelo de Tarefa excluído com sucesso!';
                }

                echo json_encode($retorno);

            break;
                case 'relatorioPDFTarefa':

                $retorno = 'Não foi possivel gerar o PDF';

                if(CPFL_ID == $userJwt->empresa) {
                    $cpfl = new cpfl();
                    if($cpfl->buscaTarefaRelatorioCPFL($_REQUEST['tarefa'],false)) {
                        $retorno['status'] = 'ok';
                        $retorno['mensagem'] = 'PDF gerado com sucesso';
                    }
                }

                echo json_encode($retorno);


            break;
            case 'relatorioCPFemail':

                $cpfl = new cpfl();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi possivel enviar o PDF';

                if($cpfl->enviaPDFrespostaCPFL($_REQUEST['tarefa'],$_REQUEST['user'])) {
                    $retorno['status'] = 'ok';
                    $retorno['mensagem'] = 'PDF enviado com sucesso';
                }

                echo json_encode($retorno);

            break;

            case 'comparando_form_repostas_antigas':
                $Form_resposta = new Form_resposta();

                $retorno['status'] = 'false';
                $retorno['mensagem'] = 'Não foi sincronizar nenhuma resposta';

                if($retorno['idsOnApp'] = $Form_resposta->comparaRespostasDoApp(json_decode($_REQUEST['formRespostas']))){
                    $retorno['status'] = 'OK';
                    $retorno['mensagem'] = 'Respostas divergentes encontradas!';
                }

                echo json_encode($retorno);
                break;

            case 'salvando_form_repostas_antigas':
                    $Form_resposta = new Form_resposta();

                    $retorno['status'] = 'false';
                    $retorno['mensagem'] = 'Não foi sincronizar nenhuma resposta';

                    $respostas          = $_REQUEST['respostas'] ? json_decode($_REQUEST['respostas']) : null;
                    $metarespostas      = $_REQUEST['metaRespostas'] ? json_decode($_REQUEST['metaRespostas']) : null;

                    if($retorno['idsOnApp'] = $Form_resposta->salvaRespostasDoApp($respostas, $metarespostas)){
                        $retorno['status'] = 'OK';
                        $retorno['mensagem'] = 'Respostas divergentes encontradas!';
                    }

                    echo json_encode($retorno);
                    break;
    }

    $db->desconecta();
?>
