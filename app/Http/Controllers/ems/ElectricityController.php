<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Models\Electricity;
use Illuminate\Http\Request;
use App\Exports\ElectricityExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ElectricityController extends Controller
{
    // public function index(Request $request, $export = false)
    // {
    //     $this->authorize("view", new Electricity());

    //     $data['locations']              =       config("asset.locations");
    //     $data['users']                  =       User::whereHas("roles", function ($query) {
    //         $query->whereIn("name", ['unitChecker', 'unitCheckerAdmin']);
    //     })->pluck("name", "id")->toArray();
    //     $electricities                  =       Electricity::with('user');

    //     $electricities = $this->filter($request, $electricities);

    //     if ($export) {
    //         return $electricities;
    //     }

    //     $data['electricities']          =       $electricities;

    //     return view("electricity.index", $data);
    // }
    public function index(Request $request, $export=false)
    {
        if ($request->ajax()) {
            return DataTables::of($categories)
                ->addColumn('action', function ($category) {
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-warning btn-sm">Edit</a>
                    <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
    }

    public function filter($request, $electricities)
    {
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $electricities->whereBetween("date", [$request->from_date, $request->to_date]);
        }

        if (!empty($request->user_id)) {
            $electricities->where("user_id", $request->user_id);
        }

        if (!empty($request->location)) {
            $electricities->where("location", $request->location);
        }

        return $electricities->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize("create", new Electricity());
        $data['electricity']            =       new Electricity();
        $data['submitRoute']            =       route("electricity.store");
        $data['locations']              =       config("asset.locations");
        $data['method']                 =       "POST";
        return view("electricity.form", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize("create", new Electricity());
        $electricityExists          =   Electricity::whereDate("date", $request->date)->where("location", $request->location)->exists();
        if ($electricityExists) {
            return back()->with("failure", "Entry already exists");
        }
        $electricity                =   new Electricity();
        $electricity->start_unit    =   $request->start_unit;
        $electricity->location      =   $request->location;
        $electricity->end_unit      =   $request->end_unit;
        $electricity->user_id       =   auth()->user()->id;
        $electricity->date          =   $request->date;

        $previousDayElectricity = Electricity::where('location', $electricity->location)
            ->whereDate('date', '<', $electricity->date)
            ->orderBy('date', 'desc')
            ->first();

        if ($previousDayElectricity) {
            $wasteUnits = $electricity->start_unit - $previousDayElectricity->end_unit;
            $electricity->waste_units = $wasteUnits;
        }

        $electricity->save();
        saveLogs("Electricity units added", $electricity);

        return redirect(route('electricity.index'))->with("success", "Electricity Units Submitted");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize("update", new Electricity());
        $data['electricity']        =       Electricity::with("activity.user")->findOrFail($id);
        $data['locations']          =       config("asset.locations");
        $data['submitRoute']        =       route("electricity.update", ['electricity' => $id]);
        $data['method']             =       "PUT";
        return view("electricity.form", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize("update", new Electricity());
        $electricity                =   Electricity::findOrFail($id);
        // $electricity->location      =   $request->location;
        $electricity->start_unit    =   $request->start_unit;
        $electricity->end_unit      =   $request->end_unit;
        $electricity->user_id       =   auth()->user()->id;
        $previousDayElectricity = Electricity::where('location', $electricity->location)
            ->whereDate('date', '<', $electricity->date)
            ->orderBy('date', 'desc')
            ->first();
        if ($previousDayElectricity) {
            $wasteUnits = $electricity->start_unit - $previousDayElectricity->end_unit;
            $electricity->waste_units = $wasteUnits;
        }
        $electricity->update();

        saveLogs("Electricity units updated", $electricity);

        return redirect(route('electricity.index'))->with("success", "Electricity Units Submitted");
    }

    public function export(Request $request)
    {
        if (in_array(strtolower(auth()->user()->email), User::$developers)) {
            $electricityUnits   = $this->index($request, true);

            return Excel::download(new ElectricityExport($electricityUnits), 'electricity.xlsx');
        }
    }

    public function dashboard(Request $request)
    {
        $this->authorize("dashboard", new Electricity());
        if (empty($request->from_date) && empty($request->to_date)) {
            $fromDate   =   now()->subDays(7);
            $toDate     =   now();
        } else {
            $fromDate   =   $request->from_date;
            $toDate     =   $request->to_date;
        }
        if (empty($request->location)) {
            $location   =   "Office";
        } else {
            $location   =   $request->location;
        }
        $electricities      =   Electricity::whereBetween("date", [$fromDate, $toDate])->where("location", $location)
            ->with("user")->orderBy("date")->get();
        $readings =   [];
        foreach ($electricities as $electricity) {
            $readings[Carbon::parse($electricity->date)->format("d-M")] = $electricity->total_units;
        }
        $data['location']   =   $location;
        $data['locations']  =   config("asset.locations");
        $data['labels']     =   array_keys($readings);
        $data['values']     =   array_values($readings);
        return view("electricity.dashboard", $data);
    }

    public function emailNotifier($parameter_check)

    {

        $electricity = Electricity::whereDate('date', Carbon::today())->whereIn("location", config("asset.locations"))
            ->get();
        if (($parameter_check == "start_unit" && $electricity->isEmpty()) || ($parameter_check == "end_unit" && $electricity->whereNull($parameter_check)->isNotEmpty())) {

            $user_ids = User::havingRole("unitChecker");

            $data['users'] = User::whereIn('id', $user_ids)->pluck('email', 'email');

            $subject      = "Submit Electricity Reading";

            $message = "Please submit electricity reading ";

            $email                  =       User::whereHas("roles", function ($query) {
                $query->whereIn("name", ['unitChecker', 'unitCheckerAdmin']);
            })->pluck("email", "email")->toArray();
            send_email("email.electricityNotification", $data, $subject, $message, $email, null);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
}
