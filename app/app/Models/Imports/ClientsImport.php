<?php

namespace App\Models\Imports;

use App\Http\Constants;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow
{
    use Importable, SkipsFailures;

    private $importedRecordsCount = 0;

    /**
     * @param array $row
     *
     * @return Client
     */
    public function model(array $row)
    {
        ++$this->importedRecordsCount;
        $address = isset($row['plz_ort']) ? explode(' ', $row['plz_ort']) : null;
        $postalCode  = isset($address[0]) && is_numeric($address[0]) ? $address[0] : "";
        $city  = isset($address[1]) ? $address[1] : "";

        return new Client([
            'user_id' => Auth::id(),
            'internal_id' => $row['bemerkung'] ? trim($row['bemerkung']) : null,
            'status' => 1,
            'first_name' => $row['vorname'] ?? 'Undefined',
            'last_name' => $row['name'] ?? 'Undefined',
            'email' => $row['e_mail'] ?? '',
            'telephone' => $row['tel'] ?? '',
            'mobile' => "",
            'address' => $row['strasse'],
            'postal_code' => $postalCode,
            'city' => $city,
            'country' => $row['staat'] ?? Constants::DEFAULT_COUNTRY,
            'cellagon_id' => $row['kd_lfnr'] ?? 0,
            'batch_id' => session('batch_id')
        ]);
    }

    public function rules(): array
    {
        return [
            'kd_lfnr' => ['required', Rule::unique('clients', 'cellagon_id')],
            'bemerkung' => ['required', Rule::unique('clients', 'internal_id')],
            'name' => 'required',
            'vorname' => 'required',
        ];
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function getImportedRecordsCount() {
        return $this->importedRecordsCount;
    }
}
