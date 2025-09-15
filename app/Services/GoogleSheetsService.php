<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;

class GoogleSheetsService
{
    private GoogleSheets $service;
    private string $spreadsheetId;

    public function __construct()
    {
        $client = new GoogleClient();
        $client->setApplicationName('HafizApp Sheets Sync');
        $client->setScopes([GoogleSheets::SPREADSHEETS_READONLY]);
        // Pastikan path absolut ke credentials.json
        $client->setAuthConfig(base_path(config('services.google.sheets.credentials')));
        $client->setAccessType('offline');

        $this->service = new GoogleSheets($client);
        $this->spreadsheetId = (string) config('services.google.sheets.spreadsheet_id');
    }

    /** Ambil nilai range, return array baris (tiap baris = array kolom) */
    public function getValues(string $range): array
    {
        $resp = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        return $resp->getValues() ?? [];
    }
}