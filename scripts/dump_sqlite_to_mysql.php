<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php scripts/dump_sqlite_to_mysql.php <sqlite-path> <output-sql-path>\n");
    exit(1);
}

$dbPath = $argv[1];
$outPath = $argv[2];

if (!is_file($dbPath)) {
    fwrite(STDERR, "SQLite database not found: {$dbPath}\n");
    exit(1);
}

$pdo = new PDO('sqlite:'.$dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = $pdo
    ->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")
    ->fetchAll(PDO::FETCH_COLUMN);

function qi(string $identifier): string
{
    return '`'.str_replace('`', '``', $identifier).'`';
}

function mapType(array $column): string
{
    $name = strtolower((string) $column['name']);
    $type = strtolower((string) $column['type']);

    if ((int) $column['pk'] === 1 && $name === 'id') {
        return 'bigint unsigned NOT NULL AUTO_INCREMENT';
    }

    if (str_contains($type, 'varchar')) {
        return preg_match('/varchar\((\d+)\)/i', $type, $match) ? 'varchar('.$match[1].')' : 'varchar(255)';
    }

    if (str_contains($type, 'char')) {
        return $type;
    }

    if (str_contains($type, 'text')) {
        return 'text';
    }

    if (str_contains($type, 'decimal') || str_contains($type, 'numeric')) {
        return preg_match('/decimal\((\d+),(\d+)\)/i', $type, $match)
            ? 'decimal('.$match[1].','.$match[2].')'
            : 'decimal(10,2)';
    }

    if (str_contains($type, 'double') || str_contains($type, 'float') || str_contains($type, 'real')) {
        return 'double';
    }

    if (str_contains($type, 'tinyint')) {
        return 'tinyint';
    }

    if (str_contains($type, 'smallint')) {
        return 'smallint';
    }

    if (str_contains($type, 'bigint')) {
        return 'bigint unsigned';
    }

    if (str_contains($type, 'int')) {
        return str_ends_with($name, '_id') ? 'bigint unsigned' : 'int';
    }

    if (str_contains($type, 'datetime')) {
        return 'datetime';
    }

    if (str_contains($type, 'timestamp')) {
        return 'timestamp NULL';
    }

    if (str_contains($type, 'date')) {
        return 'date';
    }

    if (str_contains($type, 'json')) {
        return 'json';
    }

    if (str_contains($type, 'blob')) {
        return 'blob';
    }

    return str_ends_with($name, '_id') ? 'bigint unsigned' : 'varchar(255)';
}

function defaultSql(mixed $default): ?string
{
    if ($default === null) {
        return null;
    }

    $value = (string) $default;
    if (strcasecmp($value, 'NULL') === 0) {
        return 'DEFAULT NULL';
    }

    if (preg_match('/^CURRENT_(TIMESTAMP|DATE|TIME)$/i', $value)) {
        return 'DEFAULT '.strtoupper($value);
    }

    if (is_numeric($value)) {
        return 'DEFAULT '.$value;
    }

    return "DEFAULT '".str_replace("'", "''", trim($value, "'"))."'";
}

function qv(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    if (is_resource($value)) {
        $value = stream_get_contents($value);
    }

    return "'".str_replace(
        ["\\", "'", "\r", "\n", "\0"],
        ["\\\\", "''", "\\r", "\\n", ""],
        (string) $value
    )."'";
}

$handle = fopen($outPath, 'wb');
if (!$handle) {
    fwrite(STDERR, "Unable to open output file: {$outPath}\n");
    exit(1);
}

fwrite($handle, "-- LMS Al Azhar SMP SQL dump\n");
fwrite($handle, "-- Generated from Laravel SQLite database on ".date('Y-m-d H:i:s P')."\n");
fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($handle, "SET time_zone = \"+00:00\";\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($handle, "SET NAMES utf8mb4;\n\n");

foreach ($tables as $table) {
    $columns = $pdo->query('PRAGMA table_info('.qi($table).')')->fetchAll(PDO::FETCH_ASSOC);
    $indexes = $pdo->query('PRAGMA index_list('.qi($table).')')->fetchAll(PDO::FETCH_ASSOC);
    $definitions = [];
    $primary = [];

    foreach ($columns as $column) {
        $line = '  '.qi((string) $column['name']).' '.mapType($column);

        if (!((int) $column['pk'] === 1 && strtolower((string) $column['name']) === 'id')) {
            $line .= ((int) $column['notnull'] === 1) ? ' NOT NULL' : ' NULL';
            $default = defaultSql($column['dflt_value']);
            if ($default !== null) {
                $line .= ' '.$default;
            }
        }

        $definitions[] = $line;

        if ((int) $column['pk'] > 0) {
            $primary[(int) $column['pk']] = (string) $column['name'];
        }
    }

    ksort($primary);
    if ($primary !== []) {
        $definitions[] = '  PRIMARY KEY ('.implode(', ', array_map('qi', $primary)).')';
    }

    foreach ($indexes as $index) {
        $indexName = (string) $index['name'];
        if (str_starts_with($indexName, 'sqlite_autoindex')) {
            continue;
        }

        $indexColumns = $pdo->query('PRAGMA index_info('.qi($indexName).')')->fetchAll(PDO::FETCH_ASSOC);
        if ($indexColumns === []) {
            continue;
        }

        $columnSql = implode(', ', array_map(fn (array $row) => qi((string) $row['name']), $indexColumns));
        $definitions[] = '  '.((int) $index['unique'] === 1 ? 'UNIQUE KEY ' : 'KEY ').qi($indexName).' ('.$columnSql.')';
    }

    fwrite($handle, 'DROP TABLE IF EXISTS '.qi((string) $table).";\n");
    fwrite($handle, 'CREATE TABLE '.qi((string) $table)." (\n".implode(",\n", $definitions)."\n");
    fwrite($handle, ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n");

    $rows = $pdo->query('SELECT * FROM '.qi((string) $table))->fetchAll(PDO::FETCH_ASSOC);
    if ($rows === []) {
        continue;
    }

    $columnNames = array_keys($rows[0]);
    $columnSql = implode(', ', array_map('qi', $columnNames));

    foreach (array_chunk($rows, 100) as $chunk) {
        $values = [];
        foreach ($chunk as $row) {
            $values[] = '('.implode(', ', array_map(fn (string $column) => qv($row[$column]), $columnNames)).')';
        }

        fwrite($handle, 'INSERT INTO '.qi((string) $table).' ('.$columnSql.") VALUES\n".implode(",\n", $values).";\n");
    }

    fwrite($handle, "\n");
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

echo $outPath.PHP_EOL;
