<?php

class AdsImportService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        // AUTO-PATCH DATABASE: Secara otomatis menambahkan kolom result_type jika belum ada
        try {
            $checkCol = $this->db->query("SHOW COLUMNS FROM ad_performance_daily LIKE 'result_type'");
            if ($checkCol->fetch() === false) {
                $this->db->exec("ALTER TABLE ad_performance_daily ADD COLUMN result_type VARCHAR(150) NULL AFTER results");
            }
        } catch (Exception $e) {
            // Abaikan jika error (biasanya karena kurang privilege ALTER, tapi seharusnya aman)
        }
    }

    public function processPendingImports() {
        $stmt = $this->db->prepare("SELECT * FROM ad_report_imports WHERE import_status = 'uploaded' ORDER BY created_at ASC LIMIT 5");
        $stmt->execute();
        $pendingImports = $stmt->fetchAll();

        foreach ($pendingImports as $import) {
            $this->processSingleImport($import);
        }
    }

    private function processSingleImport($import) {
        $this->updateImportStatus($import['id'], 'processing');

        $filePath = dirname(APP_PATH) . '/public/' . ltrim($import['file_path'], '/');
        
        if (!file_exists($filePath)) {
            $this->updateImportStatus($import['id'], 'failed', 'File fisik tidak ditemukan: ' . $filePath);
            return;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            $this->updateImportStatus($import['id'], 'failed', 'Format tidak didukung. Harap gunakan format CSV.');
            return;
        }

        $successRows = 0;
        $failedRows = 0;
        $totalRows = 0;

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            
            // Deteksi Delimiter pintar
            $firstLine = fgets($handle);
            $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
            rewind($handle); 

            $headers = fgetcsv($handle, 10000, $delimiter);
            
            if (!$headers) {
                $this->updateImportStatus($import['id'], 'failed', 'Gagal membaca header CSV.');
                fclose($handle);
                return;
            }

            $map = $this->mapMetaHeaders($headers);

            if ($map['spend'] === false || $map['impressions'] === false) {
                $this->updateImportStatus($import['id'], 'failed', 'Kolom esensial (Spend / Impressions) tidak ditemukan.');
                fclose($handle);
                return;
            }

            // =======================================================================
            // PASS 1: SMART OVERWRITE ENGINE (Mencegah Duplikasi)
            // =======================================================================
            $campaignDates = [];
            while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                if (empty(array_filter($data))) continue;
                $campName = $map['campaign_name'] !== false ? trim($data[$map['campaign_name']] ?? '') : '';
                if (empty($campName)) continue;

                $rawDate = $map['date'] !== false ? $data[$map['date']] : '';
                $reportDate = !empty($rawDate) ? date('Y-m-d', strtotime($rawDate)) : date('Y-m-d');
                
                // Simpan kombinasi Campaign dan Tanggal yang ada di file CSV ini
                $campaignDates[$campName][$reportDate] = true;
            }

            // Hapus data lama yang bersinggungan agar data terbaru bisa menimpa dengan rapi
            foreach ($campaignDates as $camp => $datesArr) {
                $dates = array_keys($datesArr);
                $placeholders = implode(',', array_fill(0, count($dates), '?'));
                $sqlDel = "DELETE FROM ad_performance_daily WHERE brand_id = ? AND campaign_name = ? AND report_date IN ($placeholders)";
                $params = array_merge([$import['brand_id'], $camp], $dates);
                $stmtDel = $this->db->prepare($sqlDel);
                $stmtDel->execute($params);
            }

            // Reset Pointer File untuk mulai insert
            rewind($handle);
            fgetcsv($handle, 10000, $delimiter); // Skip header

            // =======================================================================
            // PASS 2: INSERT NEW DATA
            // =======================================================================
            $this->db->beginTransaction();
            try {
                while (($data = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
                    if (empty(array_filter($data))) continue;
                    $campName = $map['campaign_name'] !== false ? trim($data[$map['campaign_name']] ?? '') : '';
                    if (empty($campName)) continue;

                    $totalRows++;
                    
                    $spend = (float)($data[$map['spend']] ?? 0);
                    $impressions = (int)($data[$map['impressions']] ?? 0);
                    $clicks = (int)($map['clicks'] !== false ? $data[$map['clicks']] : 0);
                    $reach = (int)($map['reach'] !== false ? $data[$map['reach']] : 0);
                    
                    $rawResults = $map['results'] !== false ? $data[$map['results']] : 0;
                    $results = is_numeric($rawResults) ? (float)$rawResults : 0;

                    // Ekstrak Keterangan Tipe Hasil / Konversi
                    $resultType = $map['result_type'] !== false ? trim($data[$map['result_type']] ?? '') : '';

                    // Ekstrak Metrik Video
                    $v25  = (int)($map['video_25'] !== false ? $data[$map['video_25']] : 0);
                    $v50  = (int)($map['video_50'] !== false ? $data[$map['video_50']] : 0);
                    $v75  = (int)($map['video_75'] !== false ? $data[$map['video_75']] : 0);
                    $v95  = (int)($map['video_95'] !== false ? $data[$map['video_95']] : 0);
                    $v100 = (int)($map['video_100'] !== false ? $data[$map['video_100']] : 0);

                    // Kalkulasi Otomatis
                    $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
                    $cpc = $clicks > 0 ? ($spend / $clicks) : 0;
                    $cpm = $impressions > 0 ? ($spend / $impressions) * 1000 : 0;
                    $cpr = $results > 0 ? ($spend / $results) : 0;
                    $frequency = $reach > 0 ? ($impressions / $reach) : 0;

                    $rawDate = $map['date'] !== false ? $data[$map['date']] : '';
                    $reportDate = !empty($rawDate) ? date('Y-m-d', strtotime($rawDate)) : date('Y-m-d');

                    // Insert beserta Kolom Tipe Hasil
                    $sql = "INSERT INTO ad_performance_daily 
                            (brand_id, ad_account_id, report_date, campaign_name, adset_name, ad_name, 
                             spend, impressions, reach, clicks, results, result_type, ctr, cpc, cpm, cost_per_result, frequency, 
                             video_25, video_50, video_75, video_95, video_100, import_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtPerf = $this->db->prepare($sql);
                    $stmtPerf->execute([
                        $import['brand_id'], $import['ad_account_id'], $reportDate, $campName,
                        $map['adset_name'] !== false ? trim($data[$map['adset_name']]) : null,
                        $map['ad_name'] !== false ? trim($data[$map['ad_name']] ?? '') : null,
                        $spend, $impressions, $reach, $clicks, $results, $resultType,
                        $ctr, $cpc, $cpm, $cpr, $frequency, 
                        $v25, $v50, $v75, $v95, $v100, $import['id']
                    ]);

                    $successRows++;
                }
                
                $this->db->commit();
                if ($successRows === 0) {
                    $this->updateImportStatus($import['id'], 'failed', 'Semua baris dilewati. Gagal mengekstrak nama kampanye.', $totalRows, $successRows, $failedRows);
                } else {
                    $this->updateImportStatus($import['id'], 'done', null, $totalRows, $successRows, $failedRows);
                }

            } catch (Exception $e) {
                $this->db->rollBack();
                $this->updateImportStatus($import['id'], 'failed', 'Error DB: ' . $e->getMessage(), $totalRows, $successRows, $failedRows);
            }
            fclose($handle);
        }
    }

    private function mapMetaHeaders($headers) {
        return [
            'date'          => $this->findIndex($headers, ['awal pelaporan', 'reporting starts', 'hari', 'tanggal', 'date']),
            'campaign_name' => $this->findIndex($headers, ['nama kampanye', 'campaign name']),
            'adset_name'    => $this->findIndex($headers, ['nama set iklan', 'ad set name']),
            'ad_name'       => $this->findIndex($headers, ['nama iklan', 'ad name']),
            'spend'         => $this->findIndex($headers, ['jumlah yang dibelanjakan', 'amount spent']),
            'impressions'   => $this->findIndex($headers, ['impresi', 'tayangan', 'impressions']),
            'reach'         => $this->findIndex($headers, ['jangkauan', 'reach']),
            'clicks'        => $this->findIndex($headers, ['klik tautan', 'link clicks', 'clicks', 'klik']),
            'results'       => $this->findIndex($headers, ['hasil', 'results']),
            
            // FIX: Tambahkan deteksi Jenis Keuntungan / Indikator Hasil
            'result_type'   => $this->findIndex($headers, ['jenis keuntungan', 'indikator hasil', 'result type', 'action indicator']),
            
            'video_25'      => $this->findIndex($headers, ['video diputar hingga 25%', 'video plays at 25%']),
            'video_50'      => $this->findIndex($headers, ['video diputar hingga 50%', 'video plays at 50%']),
            'video_75'      => $this->findIndex($headers, ['video diputar hingga 75%', 'video plays at 75%']),
            'video_95'      => $this->findIndex($headers, ['video diputar hingga 95%', 'video plays at 95%']),
            'video_100'     => $this->findIndex($headers, ['video diputar hingga 100%', 'video plays at 100%']),
        ];
    }

    private function findIndex($headers, $keywords) {
        foreach ($headers as $idx => $header) {
            $cleanHeader = strtolower(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', trim($header)));
            foreach ($keywords as $kw) {
                if (strpos($cleanHeader, $kw) !== false) {
                    return $idx;
                }
            }
        }
        return false;
    }

    private function updateImportStatus($id, $status, $errorLog = null, $total = 0, $success = 0, $failed = 0) {
        $sql = "UPDATE ad_report_imports SET import_status = ?, error_log = ?, total_rows = ?, success_rows = ?, failed_rows = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$status, $errorLog, $total, $success, $failed, $id]);
    }
}