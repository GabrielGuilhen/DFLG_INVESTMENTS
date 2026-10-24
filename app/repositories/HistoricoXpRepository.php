<?php

namespace app\repositories;

use app\database\ConnectionFactory;
use app\models\HistoricoXp;
use PDO;

class HistoricoXpRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = ConnectionFactory::getConnection();
    }

    public function registrar(HistoricoXp $historicoXp): bool
    {
        $sql = "INSERT INTO historico_xp (usuario_id, acao, xp, data_concessao) " .
            "VALUES (:usuarioId, :acao, :xp, :dataConcessao)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $historicoXp->getUsuarioId());
        $stmt->bindValue(':acao', $historicoXp->getAcao());
        $stmt->bindValue(':xp', $historicoXp->getXp());
        $stmt->bindValue(':dataConcessao', $historicoXp->getDataConcessao());

        return $stmt->execute();
    }

    // com data: verifica se a ação já deu XP naquele dia; sem data: se já deu XP alguma vez
    public function existeConcessao(int $usuarioId, string $acao, ?string $data = null): bool
    {
        $sql = "SELECT COUNT(*) FROM historico_xp WHERE usuario_id = :usuarioId AND acao = :acao";

        if ($data) {
            $sql .= " AND data_concessao = :data";
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':usuarioId', $usuarioId);
        $stmt->bindValue(':acao', $acao);

        if ($data) {
            $stmt->bindValue(':data', $data);
        }

        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function getRanking(string $periodo = 'geral'): array
    {
        $filtro = '';
        if ($periodo === 'semanal') {
            $filtro = "AND h.data_concessao >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
        } elseif ($periodo === 'mensal') {
            $filtro = "AND h.data_concessao >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')";
        }

        $sql = "SELECT u.nickname, ucm.nivel, ucm.streak_atual, " .
            "COALESCE(SUM(h.xp), 0) AS xp_periodo, u.criado_em " .
            "FROM usuarios u " .
            "INNER JOIN usuario_comum ucm ON ucm.usuario_id = u.id " .
            "LEFT JOIN historico_xp h ON h.usuario_id = u.id {$filtro} " .
            "WHERE u.status = 'ativo' AND ucm.visibilidade_ranking = 1 " .
            "GROUP BY u.id, u.nickname, ucm.nivel, ucm.streak_atual, u.criado_em " .
            "ORDER BY ucm.nivel DESC, xp_periodo DESC, ucm.streak_atual DESC, u.criado_em ASC";

        return $this->connection->query($sql)->fetchAll();
    }
}