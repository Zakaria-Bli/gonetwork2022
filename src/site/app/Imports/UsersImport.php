<?php

namespace App\Imports;

use App\Mail\ActivationLink;
use App\Models\Link;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach($rows as $key => $row) {
            if ($key > 0) {
                $uuid = (string) Str::uuid();
                $user = User::where('email', $row[1])->first();
                if ($user !== null) {
                    
                    Link::create(['user_id' => $user->id, 'token' => $uuid, 'sended' => false]);
                }
            }
        }
        $links = Link::where('sended', 0)->get();
        foreach($links as $link) {
            $url = url('/acceptInvitation?token='.$link->token);
            Mail::to(trim($link->user->email))->send(new ActivationLink($url));
        }
    }
}
