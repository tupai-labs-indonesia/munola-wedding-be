<?php

namespace App\Repositories;

use App\Models\Invitee;
use Core\Model\Model;
use Ramsey\Uuid\Uuid;

class InviteeRepositories
{
    public static function create(array $data): Model
    {
        return Invitee::create($data);
    }

    public static function getAll(int $limit, int $offset): Model
    {
        return Invitee::orderBy('id', 'DESC')
            ->limit(abs($limit))
            ->offset($offset)
            ->get();
    }
}
