# DFLG Investments – Registro de decisões do projeto

Última atualização: 17/10/2026

Este arquivo é a memória oficial do projeto. Quem for retomar o trabalho (pessoa ou IA, em um chat novo) deve ler este arquivo primeiro, junto com o documento do PI e o código.

## 1. Regras de trabalho

1. O **documento do PI é a fonte da verdade**. O sistema deve ser 100% baseado nele.
2. **Capítulos 1 e 2 estão congelados.** O capítulo 3 (diagramas) também, em princípio.
3. Só se altera o documento quando algo está realmente errado, e **quem define o que está errado é apenas o responsável pelo projeto**. A IA só aponta e pergunta, nunca decide sozinha.
4. **Nada de inventar** requisito, regra ou funcionalidade que não esteja no documento. Se algo estiver ambíguo ou ausente, perguntar antes de implementar.
5. Toda entrega de código cita o RF, a RN ou o UC a que corresponde.
6. Separar sempre o que vem direto do documento do que é suposição.
7. Não presumir: leitura de código é estática até ser executada. Bugs apontados por leitura ainda precisam ser confirmados rodando.

## 2. Decisões fechadas

### 2.1 Correções do documento (a aplicar pelo grupo)

| # | Onde | Alteração | Motivo |
|---|---|---|---|
| 1 | Seção 3.2.1 (cenário Registrar Ganho) | UC-09 e RF 09 viram **UC-07 e RF 07** (título, campo Requisito Funcional, passo 1 do fluxo, campo Casos de Uso Estendidos). | O Quadro 2 e a Figura 5 usam UC-07/RF 07. RF 09/UC-09 é streak. |
| 2 | Seção 3.2.1 | RN 05, FE 01 e campo Valor passam a dizer **"maior que zero"**. | O Quadro 3 define RN 05 como maior que zero, e o sistema já implementa assim. |
| 3 | Seção 2.3.2 | RF14 vira **RF04** (perfil). | RF 04 é Gerenciar Perfil. |
| 4 | Seção 2.3.1 | Referência à seção 3.3 vira **2.3.3**. | A justificativa do XP está na 2.3.3. |
| 5 | Quadro 3, RN 13 | Nova redação (ver 2.2). | Permitir compra já em andamento. |
| 6 | Seção 3.4 (Figura 7) | **Refazer o DER** e manter o diagrama de classes consistente com ele. | A seção 3.3 afirma que as classes correspondem ao banco da 3.4. |

O detalhamento de cada item está no documento "DFLG Investments – Alterações necessárias no documento".

### 2.2 RN 13 (parcelamentos) – decisão e nova redação

Texto novo (versão curta; a versão longa acrescenta a última frase):

> Todo parcelamento deve possuir uma quantidade de parcelas maior que 1, um valor de parcela maior que zero e a data de vencimento da primeira parcela informada, podendo esta ser anterior, igual ou posterior à data atual.
>
> *(opcional)* As parcelas com vencimento até a data atual são consideradas pagas.

### 2.3 Modelo de parcelamento (decisão B)

Seguir o **diagrama de classes** (opção 2): o parcelamento nasce de um registro financeiro.

- **`tag`:** rótulo curto e livre que identifica a compra (ex.: "Celular"), mostrado na listagem.
- **`lancar_futuros`:** o usuário informa o valor da parcela, a quantidade e a data da primeira parcela. O registro de origem é a primeira parcela. Com o indicador ligado, o sistema gera uma despesa para cada parcela restante, uma por mês. Desligado, existe só a primeira. Em compras já em andamento (primeira parcela no passado), o sistema gera todas as parcelas da série, inclusive as vencidas.
- **Vínculo para editar e excluir:** `registro_financeiro` ganha uma coluna opcional apontando para o registro de origem. Excluir o parcelamento apaga as despesas geradas. Editar valor, quantidade ou data refaz só as parcelas futuras (data posterior a hoje) e preserva as vencidas.
- **Parcelas pagas:** deixa de ser campo digitado. É derivado das datas (parcela com data até hoje conta como paga).
- **Saldo:** passa a considerar apenas registros com data até hoje. Isso vale para saldo, totais e dashboard.
- Essa coluna de origem precisa entrar no DER refeito e como auto-associação de `registro_financeiro` no diagrama de classes.

### 2.4 Direção geral do modelo de dados (decisão C)

O documento está certo e o modelo é que se ajusta. O modelo de dados será ampliado para comportar:

- XP concedido uma única vez (perfil completo, preferências da conta, primeiro acesso aos relatórios);
- marcos de streak (7, 30, 100 e 1.000 dias, com 25, 100, 300 e 5.000 XP);
- ranking semanal, mensal e geral (exige histórico de XP por período).

As opções de modelagem ainda serão apresentadas e escolhidas (ver pendências).

### 2.5 Demais decisões

- **RN 13 (D):** o código passa a respeitar o documento (mais de 1 parcela, valor da parcela maior que zero, primeira parcela sem restrição de data).
- **XP:** registrar receitas, despesas, metas ou parcelamentos **não gera XP** (seções 2.3.1 e 2.3.3).
- **Regra da frequência do XP:** acesso diário vale 5 XP por dia e o simulador vale 10 XP por dia (RN 07). As duas podem ocorrer no mesmo dia, então o controle não pode ser um único "uma ação com XP por dia".

## 3. Pendências (decidir antes de implementar)

| Item | Descrição |
|---|---|
| **A** | Banco × diagrama de classes. O código tem `status`, `perfil` e `foto` em `usuarios`, e `tipo` e `limite_orcamento` em `categorias`, que o diagrama não prevê. O diagrama tem `meta_categorias`, `simulacoes_investimento` e `nivel_permissao`, que o banco não tem. Decidir em conjunto. |
| **C** | Escolher como guardar XP único, marcos de streak e histórico de XP para o ranking. |
| **DER** | Desenhar a proposta e aprovar. Só depois de A e C. Lacunas já identificadas: `simulacoes_investimento` ausente no DER; `maior_streak` e `data_ultimo_xp` ausentes em `usuario_comum` na Figura 7; coluna de registro de origem (2.3). |
| **Itens do documento** | A seção 2.3 remete à seção 2.1 para falar de redes sociais, mas a 2.1 não trata disso. A RN 11 tem verbo repetido. A Lista de Ilustrações não inclui a Figura 7. O texto do RF dentro do cenário UC difere do RF 07 do Quadro 2. |

## 4. Estado do sistema (levantamento de 17/10/2026, por leitura do DFLG_V2)

| RF | Estado | Observação |
|---|---|---|
| RF 01 | Parcial | Cadastro comum, RN 02 e RN 15 ok. Faltam cadastro de admin por outro admin (RN 01) e nível de permissão. |
| RF 02 | Não iniciado | `adminRequired()` existe, sem uso. Sem painel. Login não checa `status = bloqueado`. |
| RF 03 | Feito | Falta bloquear login de usuário bloqueado. |
| RF 04 | Não iniciado | Colunas existem; não há tela nem rota. |
| RF 05 | Parcial e incorreto | Fórmula de nível ok (nível × 100). Ações do Quadro 5 quase ausentes. XP indevido ao registrar transação. |
| RF 06 | Feito | CRUD, tipos e RN 08 ok. |
| RF 07 | Parcial | CRUD, categorias, filtros, RN 05 e RN 06 ok. Dashboard sem saldo, metas, evolução ou gráficos. |
| RF 08 | Parcial | Calculadora funciona; taxas fixas no código; não grava simulação; sem XP (RN 07); sem exigir login. |
| RF 09 | Quebrado | Ver bugs abaixo. |
| RF 10 | Não iniciado | |
| RF 11 | Não iniciado | |
| RF 12 | Não iniciado | Só existe a coluna `limite_orcamento`. |
| RF 13 | Parcial | CRUD feito, mas o modelo será refeito (decisão B). |
| RF 14 | Não iniciado | UC-07.5 "Transferir Saldo Meta" não existe. |
| RF 15 | Não iniciado | |
| RNF 04 | Violado | `ConnectionFactory` imprime o erro bruto do banco. |
| RNF 07 | Não iniciado | Modo claro/escuro. |

## 5. Bugs conhecidos (por leitura, ainda não executados)

1. `DashboardController::verificaStreak()` usa `$usuarioComum` e `$ontem`, que não existem no escopo. Provável erro fatal no dashboard. A lógica correta já existe em `GamificacaoService::verificarEAtualizarStreak()`.
2. `UsuarioComum::arrayParaObjeto` passa os argumentos na ordem errada: `data_ultimo_xp` vai para `ultimoAcesso`, `visibilidade_ranking` vai para `dataUltimoXp`, e `ultimo_acesso` nunca é lido.
3. `TransacaoService` concede 10 XP ao registrar transação, o que contradiz as seções 2.3.1 e 2.3.3.
4. `/calculadora` não exige login (viola RN 11 e RNF 02).
5. Exclusões são feitas por GET, sem proteção CSRF.
6. `URL_BASE` aponta para `DFLG_V1/DFLG` e a pasta é `DFLG_V2`.
7. A senha do admin de teste está em comentário no `script.sql` (aceitável em dev).
8. `ALTER TABLE usuario_comum ADD COLUMN ultimo_acesso` está solto no fim do `script.sql`; deveria estar no `CREATE TABLE`.

## 6. Próximos passos

1. Corrigir os bugs 1 a 4 da seção 5. São correções que só deixam o código fiel ao documento, sem decisão de modelo.
2. Decidir A e C, depois propor e aprovar o DER.
3. Refazer o módulo de parcelamento conforme a seção 2.3.
4. Implementar os RFs restantes, um por vez, conforme o documento.
5. Documentar cada entrega com o RF/RN/UC correspondente.

## 7. Como retomar em um chat novo

Anexe: este arquivo, o PDF do documento do PI e o zip mais recente do projeto. Peça para a IA ler os três e confirmar as regras de trabalho (seção 1) antes de começar. A IA deve apontar o que estiver diferente do que está aqui, e nunca decidir sozinha.
