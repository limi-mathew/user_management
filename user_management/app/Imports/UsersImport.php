<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
//use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class UsersImport implements ToModel,WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'Last Name'  => $row['0']
           /* 'First Name'  => $row['1'],
            'Email'   => $row['2'],
            'Phone Number'   => $row['3'],
            'City'    => $row['4'],
            'Country'   => $row['5']
            /*'Last Name'  => $row['last_name'],
            'First Name'  => $row['first_name'],
            'Email'   => $row['email'],
            'Phone Number'   => $row['phonenumber'],
            'City'    => $row['city'],
            'Country'   => $row['country']*/
        ]);
        
    }
    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'ISO-8859-1',
            'delimiter' => "\t"
        ];
    }
}
