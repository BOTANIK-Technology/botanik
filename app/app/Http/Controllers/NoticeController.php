<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
        $notices = Notice::getNotice( \Auth::user() , true);

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
