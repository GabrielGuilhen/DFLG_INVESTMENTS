<?php

require_once __DIR__ . '/../app/core/Autoload.php';
require_once __DIR__ . '/../app/config/Config.php';

use app\core\Router;

$router = new Router();

$router->get('/', 'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

//Autenticacao
$router->get('/login', 'AutenticacaoController@login');
$router->post('/logar', 'AutenticacaoController@logar');
$router->get('/logout', 'AutenticacaoController@logout');

$router->get('/cadastrar', 'AutenticacaoController@cadastrar');
$router->post('/cadastrar/salvar', 'AutenticacaoController@salvar');

// Meta Routes
$router->get('/metas', 'MetaController@listarTodas');
$router->get('/metas/cadastrar', 'MetaController@criar');
$router->post('/metas/salvar', 'MetaController@salvar');
$router->get('/metas/editar', 'MetaController@editar');
$router->post('/metas/atualizar', 'MetaController@atualizar');
$router->post('/metas/progredir', 'MetaController@progredir');
$router->post('/metas/excluir', 'MetaController@excluir');

// Transacao Routes
$router->get('/transacoes', 'TransacaoController@listarTodas');
$router->get('/transacoes/cadastrar', 'TransacaoController@criar');
$router->post('/transacoes/salvar', 'TransacaoController@salvar');
$router->get('/transacoes/editar', 'TransacaoController@editar');
$router->post('/transacoes/atualizar', 'TransacaoController@atualizar');
$router->post('/transacoes/excluir', 'TransacaoController@excluir');

// Parcelamento Routes
$router->get('/parcelamentos', 'ParcelaController@listarTodos');
$router->get('/parcelamentos/cadastrar', 'ParcelaController@criar');
$router->post('/parcelamentos/salvar', 'ParcelaController@salvar');
$router->get('/parcelamentos/editar', 'ParcelaController@editar');
$router->post('/parcelamentos/atualizar', 'ParcelaController@atualizar');
$router->post('/parcelamentos/excluir', 'ParcelaController@excluir');

// Perfil Routes
$router->get('/perfil', 'PerfilController@visualizar');
$router->get('/perfil/editar', 'PerfilController@editar');
$router->get('/perfil/excluir', 'PerfilController@excluir');
$router->post('/perfil/excluir', 'PerfilController@excluirConta');
$router->post('/perfil/atualizar', 'PerfilController@atualizar');
$router->post('/perfil/preferencias', 'PerfilController@atualizarPreferencias');

// Administracao
$router->get('/admin/usuarios', 'AdministradorController@usuarios');
$router->post('/admin/usuarios/bloquear', 'AdministradorController@bloquear');
$router->post('/admin/usuarios/desbloquear', 'AdministradorController@desbloquear');
$router->post('/admin/usuarios/promover', 'AdministradorController@promover');
$router->post('/admin/usuarios/excluir', 'AdministradorController@excluir');
$router->get('/admin/usuarios/administrador/cadastrar', 'AdministradorController@criarAdministrador');
$router->post('/admin/usuarios/administrador/salvar', 'AdministradorController@salvarAdministrador');
$router->get('/admin/usuarios/editar', 'AdministradorController@editar');
$router->post('/admin/usuarios/atualizar', 'AdministradorController@atualizar');

// Ranking
$router->get('/ranking', 'RankingController@index');
$router->get('/relatorios', 'RelatorioController@index');
$router->get('/limites', 'LimiteCategoriaController@index');
$router->post('/limites/salvar', 'LimiteCategoriaController@salvar');

// Calculadoras Routes
$router->get('/calculadoras', 'CalculadoraController@index');
$router->get('/calculadoras/juros-compostos', 'CalculadoraController@jurosCompostos');
$router->post('/calculadoras/juros-compostos/calcular', 'CalculadoraController@jurosCompostosCalcular');
$router->get('/calculadoras/aporte-meta', 'CalculadoraController@aporteMeta');
$router->post('/calculadoras/aporte-meta/calcular', 'CalculadoraController@aporteMetaCalcular');
$router->get('/calculadoras/tempo-meta', 'CalculadoraController@tempoMeta');
$router->post('/calculadoras/tempo-meta/calcular', 'CalculadoraController@tempoMetaCalcular');
$router->get('/calculadoras/independencia-financeira', 'CalculadoraController@independenciaFinanceira');
$router->post('/calculadoras/independencia-financeira/calcular', 'CalculadoraController@independenciaFinanceiraCalcular');
$router->get('/calculadoras/aposentadoria', 'CalculadoraController@aposentadoria');
$router->post('/calculadoras/aposentadoria/calcular', 'CalculadoraController@aposentadoriaCalcular');
$router->get('/calculadoras/reserva-emergencia', 'CalculadoraController@reservaEmergencia');
$router->post('/calculadoras/reserva-emergencia/calcular', 'CalculadoraController@reservaEmergenciaCalcular');
$router->get('/calculadoras/valor-real', 'CalculadoraController@valorReal');
$router->post('/calculadoras/valor-real/calcular', 'CalculadoraController@valorRealCalcular');
$router->get('/calculadoras/comparador-cenarios', 'CalculadoraController@comparadorCenarios');
$router->post('/calculadoras/comparador-cenarios/calcular', 'CalculadoraController@comparadorCenariosCalcular');
$router->get('/calculadoras/rendimento-investimento', 'CalculadoraController@rendimentoInvestimento');
$router->post('/calculadoras/rendimento-investimento/calcular', 'CalculadoraController@rendimentoInvestimentoCalcular');
$router->get('/calculadoras/juros-simples', 'CalculadoraController@jurosSimples');
$router->post('/calculadoras/juros-simples/calcular', 'CalculadoraController@jurosSimplesCalcular');

$router->run();