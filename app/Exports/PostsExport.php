<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostsExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Post::withTrashed()->get();
    }

    public function map($row): array
    {
        return [
            $row->id, 
            $row->title,
            $row->description,
            $row->status == 0 ? 'Inactive' : 'Active',
            $row->createdUser?->name,
            $row->updatedUser?->name,
            $row->deletedUser?->name,
            $row->created_at,
            $row->updated_at,
            $row->deleted_at
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Status',
            'Created user name',
            'Updated user name',
            'Deleted user name',
            'Created at',
            'Updated at',
            'Deleted at'
        ];
    }
}
