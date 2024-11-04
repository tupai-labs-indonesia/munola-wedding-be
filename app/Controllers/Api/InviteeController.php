<?php

namespace App\Controllers\Api;

use App\Middleware\UuidMiddleware;
use App\Repositories\InviteeContract;
use App\Repositories\InviteeRepositories;
use App\Repositories\LikeContract;
use App\Request\InsertCommentRequest;
use App\Request\InsertInviteeRequest;
use App\Response\JsonResponse;
use Core\Auth\Auth;
use Core\Database\DB;
use Core\Routing\Controller;
use Core\Http\Request;
use Core\Http\Respond;
use Kamu\Aman;
use Throwable;

class InviteeController extends Controller
{
    private $json;
    public function __construct(JsonResponse $json)
    {
        $this->json = $json;
    }

    public function get(Request $request): JsonResponse
    {
        $valid = $this->validate($request, [
            'next' => ['nullable', 'int'],
            'per' => ['required', 'int', 'max:10']
        ]);

        if ($valid->fails()) {
            return $this->json->errorBadRequest($valid->messages());
        }

        return $this->json->successOK(InviteeRepositories::getAll(
            $valid->per,
            ($valid->next ?? 0)
        ));
    }
    public function create(InsertInviteeRequest $request): JsonResponse
    {
        $name = $request->name;
        $invitation = null;
        $whatsapp = null;

        $name = urlencode($name);
        $inviteeName = str_replace(' ', '+', $request->name);
        $inviteeName = str_replace('&', '~', $inviteeName);
        $invitation_url = "{$request->base_url}?to={$inviteeName}";
        $invitation = str_replace('%20', '+', urlencode($invitation_url));
        $dahlia = "JEMPUTAN%20-%20UNDANGAN%20PERNIKAHAN%0A%2AVinni%20dan%20Fikri%2A%0A%0AAssalaamu%27alaikum%20Warahmatullaahi%20Wabarakaatuh.%0A%0AKepada%20Yth.%0A%0ABapak%2FIbu%20%2A{$name}%2A%0A%0ADengan%20memohon%20Rahmat%20%26%20Ridho%20Allah%20Subhanahu%20wa%20Ta%27ala%2C%20kami%20mengundang%20Bapak%2FIbu%2FSaudara%2Fi%2C%20untuk%20berkenan%20hadir%20dan%20memberikan%20Do%27a%20Restu%20atas%20Pernikahan%20Putra%20Putri%20kami%3A%C2%A0%0A%0AVinniola%20Hijriani%20Nur%20Asy%20Syam%2C%20S.T%0APutri%20dari%20Bapak%20Herrisyam%20dan%20Ibu%20Surani%0A%0A%26%0A%0AMunawir%20Fikri%20Al-akbari%0APutra%20dari%20Bapak%20Dr.%20H.%20M.%20Rizal%20Akbar%2C%20S.Si%2C%20M.Phil%20dan%20Ibu%20Hj.%20Lestary%20Fitriany%2C%20S.T.%2C%20M.E%0A%0AYang%20akan%20dilangsungkan%20pada%3A%0A%0AHari%2FTanggal%3A%20Senin%2C%202%20Desember%202024%0AWaktu%3A%2011.00%20-%20Selesai%0ATempat%3A%20Kediaman%20Mempelai%20Wanita%20%28Jl.%20Mawar%20Gg.%20Dahlia%2C%20Tanjung%20Palas%2C%20Dumai%20Timur%2C%20Dumai%2C%20Riau%2028816%29%0A%0ABerikut%20link%20untuk%20info%20lengkap%20dari%20acara%20kami%3A%C2%A0%0A%2A{$invitation}%2A%0A%0AMerupakan%20suatu%20kehormatan%20dan%20kebahagiaan%20bagi%20kami%20apabila%20Bapak%2FIbu%2FSaudara%2Fi%20berkenan%20hadir%20untuk%20memberikan%20doa%20restu%20kepada%20kedua%20mempelai.%0A%0AAtas%20kehadiran%20dan%20doa%20restunya%2C%20kami%20ucapkan%20terima%20kasih.%0A%0AWassalamu%27alaikum%20Warahmatullahi%20Wabarakatuh.%0A%0AHormat%20Kami%2C%0A%0A%2AHerrisyam%20%26%20Surani%2A%0A%2ADr.%20H.%20M.%20Rizal%20Akbar%2C%20S.Si%2C%20M.Phil%20%26%20Hj.%20Lestary%20Fitriany%2C%20S.T.%2C%20M.E%2A";
        $kakap = "JEMPUTAN%20-%20RESEPSI%20PERNIKAHAN%0A%2AFikri%20dan%20Vinni%2A%0A%0AAssalaamu%27alaikum%20Warahmatullaahi%20Wabarakaatuh.%0A%0AKepada%20Yth.%0A%0ABapak%2FIbu%20%2A{$name}%2A%0A%0ADengan%20memohon%20Rahmat%20%26%20Ridho%20Allah%20Subhanahu%20wa%20Ta%27ala%2C%20kami%20mengundang%20Bapak%2FIbu%2FSaudara%2Fi%2C%20untuk%20berkenan%20hadir%20dan%20memberikan%20Do%27a%20Restu%20atas%20Pernikahan%20Putra%20Putri%20kami%3A%C2%A0%0A%0AMunawir%20Fikri%20Al-akbari%0APutra%20dari%20Bapak%20Dr.%20H.%20M.%20Rizal%20Akbar%2C%20S.Si%2C%20M.Phil%20dan%20Ibu%20Hj.%20Lestary%20Fitriany%2C%20S.T.%2C%20M.E%0A%0A%26%0A%0AVinniola%20Hijriani%20Nur%20Asy%20Syam%2C%20S.T%0APutri%20dari%20Bapak%20Herrisyam%20dan%20Ibu%20Surani%0A%0AYang%20akan%20dilangsungkan%20pada%3A%0A%0AHari%2FTanggal%3A%20Kamis%2C%205%20Desember%202024%0AWaktu%3A%2011.00%20-%20Selesai%0ATempat%3A%20Kediaman%20Mempelai%20Pria%20%28Jl.%20Kakap%20No.7%20Komp.%20Yaktapena%2C%20Dumai%20Barat%2C%20Dumai%2C%20Riau%2028824%29%0A%0ABerikut%20link%20untuk%20info%20lengkap%20dari%20acara%20kami%3A%C2%A0%0A%2A{$invitation}%2A%0A%0AMerupakan%20suatu%20kehormatan%20dan%20kebahagiaan%20bagi%20kami%20apabila%20Bapak%2FIbu%2FSaudara%2Fi%20berkenan%20hadir%20untuk%20memberikan%20doa%20restu%20kepada%20kedua%20mempelai.%0A%0AAtas%20kehadiran%20dan%20doa%20restunya%2C%20kami%20ucapkan%20terima%20kasih.%0A%0AWassalamu%27alaikum%20Warahmatullahi%20Wabarakatuh.%0A%0AHormat%20Kami%2C%0A%0A%2ADr.%20H.%20M.%20Rizal%20Akbar%2C%20S.Si%2C%20M.Phil%20%26%20Hj.%20Lestary%20Fitriany%2C%20S.T.%2C%20M.E%2A%0A%2AHerrisyam%20%26%20Surani%2A";
        $phoneNumber = $this->convertPhoneNumber($request->phone_number);

        switch($request->type){
            case 'kakap':
                $whatsapp = $kakap;
                break;
                case 'dahlia':
                $whatsapp = $dahlia;
                break;
            default:
                break;
        }

        $invitee = InviteeRepositories::create([
            "name" => $request->name,
            "phone_number" => $phoneNumber,
            "invitation_link" => $invitation_url,
            "whatsapp_link" => "http://api.whatsapp.com/send?text=" . $whatsapp,
        ]);

        return $this->json->success(
            $invitee,
            Respond::HTTP_CREATED
        );
    }

    function convertPhoneNumber($phoneNumber) {
        if (strpos($phoneNumber, '0') === 0) {
            return '62' . substr($phoneNumber, 1);
        }
        return $phoneNumber;
    }

}
