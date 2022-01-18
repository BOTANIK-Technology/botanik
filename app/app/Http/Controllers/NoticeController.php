<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view(
            'notice',
            [
                'notices' => Notice::getNotice( \Auth::user() )
            ]
        );
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function delete(Request $request)
    {
        Notice::find($request->id)->delete();
        return $this->index();
    }

    /**
     * @return array
     */
    public function getNoticeEvent(): ?array
    {
        $result  = [];
        $notices = Notice::getNotice(auth()->user() , true);

        if ($notices->isEmpty())
            return null;

        foreach ($notices as $notice) {
            if ($notice->seen == false) {
                $result[$notice->id] = $notice;
            }
        }

        Notice::makeSeen($notices);

        return $result;
    }
}
