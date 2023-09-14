<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::all();
    }

    public function map($row): array
    {
        return [
            $row->id, 
            $row->name,
            $row->email,
            $row->profile,
            $row->type == '0' ? 'Admin' : 'User',
            $row->phone,
            $row->address,
            $row->dob,
            $row->createdBy->name,
            $row->updatedBy?->name,
            $row->created_at,
            $row->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'email',
            'profile',
            'type',
            'phone',
            'address',
            'dateOfBirth',
            'createdUserName',
            'updatedUserName',
            'createdAt',
            'updatedAt'
        ];
    }
}
