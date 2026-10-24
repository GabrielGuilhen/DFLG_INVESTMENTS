<?php

namespace app\database;

use PDO;

class DatabaseInitializer
{

    public function init(PDO $connection)
    {
        $connection->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $connection->exec("USE " . DB_NAME);

        $scriptPath = __DIR__ . '/scripts/script.sql';

        if (file_exists($scriptPath)) {
            $sql = file_get_contents($scriptPath);

            try {
                $connection->exec($sql);
                $this->migrarColunasAntigas($connection);
                $this->migrarColunasParcelamento($connection);
            } catch (\Exception $e) {
                error_log("Erro ao inicializar banco de dados: " . $e->getMessage());
            }
        }
    }

    public function migrate(PDO $connection): void
    {
        $connection->exec(
            "CREATE TABLE IF NOT EXISTS limites_categoria (" .
            "usuario_id INT NOT NULL, categoria_id INT NOT NULL, limite_mensal DECIMAL(10,2) NOT NULL, " .
            "PRIMARY KEY (usuario_id, categoria_id), " .
            "FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE, " .
            "FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
        $this->migrarColunasAntigas($connection);
        $this->migrarColunasParcelamento($connection);
    }

    private function migrarColunasAntigas(PDO $connection): void
    {
        $colunas = $connection->query(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS " .
            "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuario_comum'"
        )->fetchAll(PDO::FETCH_COLUMN);

        if (in_array('data_ultimo_xp', $colunas, true) && !in_array('ultimo_acesso', $colunas, true)) {
            $connection->exec(
                "ALTER TABLE usuario_comum CHANGE COLUMN data_ultimo_xp ultimo_acesso DATE NULL"
            );
        } elseif (!in_array('ultimo_acesso', $colunas, true)) {
            $connection->exec(
                "ALTER TABLE usuario_comum ADD COLUMN ultimo_acesso DATE NULL AFTER maior_streak"
            );
        }
    }

    private function migrarColunasParcelamento(PDO $connection): void
    {
        $parcelamentoColunas = $this->getColunas($connection, 'parcelamentos');
        $registroColunas = $this->getColunas($connection, 'registro_financeiro');

        if (!in_array('tag', $parcelamentoColunas, true)) {
            $connection->exec("ALTER TABLE parcelamentos ADD COLUMN tag VARCHAR(50) NULL");
        }
        if (!in_array('valor_parcela', $parcelamentoColunas, true)) {
            $connection->exec("ALTER TABLE parcelamentos ADD COLUMN valor_parcela DECIMAL(10,2) NULL");
        }
        if (!in_array('lancar_futuros', $parcelamentoColunas, true)) {
            $connection->exec("ALTER TABLE parcelamentos ADD COLUMN lancar_futuros BOOLEAN NOT NULL DEFAULT TRUE");
        }
        if (!in_array('parcelamento_id', $registroColunas, true)) {
            $connection->exec("ALTER TABLE registro_financeiro ADD COLUMN parcelamento_id INT NULL");
        }
    }

    private function getColunas(PDO $connection, string $tabela): array
    {
        $stmt = $connection->prepare(
            "SELECT COLUMN_NAME FROM information_schema.COLUMNS " .
            "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tabela"
        );
        $stmt->execute(['tabela' => $tabela]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
