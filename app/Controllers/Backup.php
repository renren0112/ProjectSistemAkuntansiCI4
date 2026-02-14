<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Backup extends BaseController
{
    public function index()
    {
        $data = ['title' => 'Backup & Restore Database'];
        return view('pengaturan/backup', $data);
    }

    // 1. PROSES BACKUP (DOWNLOAD .SQL)
    public function download()
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        $Date = date('Y-m-d_H-i-s');
        $filename = 'Backup_DB_Akuntansi_' . $Date . '.sql';

        $sqlScript = "-- BACKUP DATABASE SISTEM AKUNTANSI\n";
        $sqlScript .= "-- Waktu: " . date('d F Y H:i:s') . "\n\n";
        $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            // 1. Structure
            $sqlScript .= "-- Struktur tabel `$table` --\n";
            $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
            
            $query = $db->query("SHOW CREATE TABLE `$table`");
            $row = $query->getRowArray();
            
            // Fix: Kadang key array beda case
            $createTable = isset($row['Create Table']) ? $row['Create Table'] : $row['eate Table'];
            $sqlScript .= $createTable . ";\n\n";

            // 2. Data
            $query = $db->table($table)->get();
            $results = $query->getResultArray();
            $count = count($results);

            if ($count > 0) {
                $sqlScript .= "-- Data untuk tabel `$table` --\n";
                foreach ($results as $row) {
                    $sqlScript .= "INSERT INTO `$table` VALUES(";
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = "NULL";
                        } else {
                            $values[] = "'" . $db->escapeString($value) . "'";
                        }
                    }
                    $sqlScript .= implode(', ', $values);
                    $sqlScript .= ");\n";
                }
                $sqlScript .= "\n";
            }
        }

        $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $this->response->download($filename, $sqlScript);
    }

    // 2. PROSES RESTORE (UPLOAD .SQL)
    public function restore()
    {
        $file = $this->request->getFile('file_backup');

        if ($file && $file->isValid() && $file->getExtension() === 'sql') {
            $sql = file_get_contents($file->getTempName());
            
            // Hapus komentar SQL agar tidak error saat parsing
            $lines = explode("\n", $sql);
            $cleanSql = "";
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '') continue;
                $cleanSql .= $line;
            }

            $db = \Config\Database::connect();
            
            // Jalankan query satu per satu
            $queries = explode(';', $cleanSql);
            
            $db->transStart();
            $db->query('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $db->query($query);
                }
            }
            
            $db->query('SET FOREIGN_KEY_CHECKS=1');
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return redirect()->to('/backup')->with('error', 'Gagal merestore database. File mungkin korup.');
            }

            return redirect()->to('/backup')->with('success', 'Database berhasil dipulihkan!');
        }

        return redirect()->to('/backup')->with('error', 'Silakan upload file .sql yang valid.');
    }
}