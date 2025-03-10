<?php

namespace App\Http\Controllers;

// use App\Models\Report;
use App\Models\Device;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $result = Device::query()->select('uuid')->get();
        return view('home', ['devices' => $result]);
    }

    public function testing()
    {
        return view('cms.form');
    }

    // public function reports()
    // {
    //     $data['reports'] = Report::query()->where('user_id', auth()->id())->latest()->paginate(6);
    //     return view('cms.reports', $data);
    // }

    // public function report($id)
    // {
    //     $report = Report::query()->where('user_id', auth()->id())->where('id', $id)->first();
    //     $data['report'] = $report;
    //     return view('cms.last', $data);
    // }

    // public function last()
    // {
    //     $report = Report::query()->where('user_id', auth()->id())->latest()->first();
    //     $data['report'] = $report;
    //     return view('cms.last', $data);
    // }

    public function submitTesting(Request $request)
    {
        $validate_arr = [
            'age' => 'required|numeric|min:1',
            'smoke' => 'required|numeric|min:1|max:20',
            'alcohol' => 'required|numeric|min:1|max:2',
            'drink' => 'required|numeric|min:1|max:3',
            'coffee' => 'required|numeric|min:1|max:4',
        ];
        /*$data = $request->validate($validate_arr);
        $data['user_id'] = auth()->id();
        $report = Report::query()->create($data);
        $client = new \GuzzleHttp\Client();
        $link = 'https://web-ml-hqdmy72nua-ue.a.run.app/30days/' . $request->smoke . '/' . $request->coffee
            . '/' . $request->drink . '/' . $request->alcohol . '/' . $request->age;
        $res = $client->get($link);
        $arr = json_decode($res->getBody());
        $update_data['avgbase'] = $arr->avgbase;
        $update_data['morning'] = $arr->morning;
        $update_data['noon'] = $arr->noon;
        $update_data['evening'] = $arr->evening;
        $update_data['data_response'] = json_encode($arr);
        $update_data['hr'] = json_encode($arr->HR);
        $update_data['time'] = json_encode($arr->time);
        $report->update($update_data);*/

        $client = new \GuzzleHttp\Client();
        $link = 'https://web-ml-hqdmy72nua-ue.a.run.app/30days/' . $request->smoke . '/' . $request->coffee
            . '/' . $request->drink . '/' . $request->alcohol . '/' . $request->age;
        $res = $client->get($link);
        $arr = json_decode($res->getBody());

        $list = collect([
            [
                'age' => $request->age,
                'smoke' => $request->smoke,
                'alcohol' => $request->alcohol,
                'drink' => $request->drink,
                'coffee' => $request->coffee,
                'user_id' => auth()->id(),
                'avgbase' => $arr->avgbase,
                'morning' => $arr->morning,
                'noon' => $arr->noon,
                'evening' => $arr->evening,
                'data_response' => json_encode($arr),
                'hr' => json_encode($arr->HR),
                'time' => json_encode($arr->time),
            ]
        ]);

        (new FastExcel($list))->export('file.csv');


        return redirect()->route('report.last');
    }
}
