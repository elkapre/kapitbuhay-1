<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoliceCreateReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!session()->has('userID') && !session()->has('policeName')) {
            return redirect('loginpage');
        }
        else {
            $firestore = app('firebase.firestore');

            $database = $firestore->database();
            $viewdisID = session('viewDistressID');

            $recordIDs = $database->collection('sos-distress-message')->document($viewdisID);
            $messageRef = $recordIDs->snapshot();

            date_default_timezone_set('Asia/Singapore');
            $date = date('m/d/Y h:i:s a', time());

            return view('pages.police_createReports', [
                'message'   => $messageRef,
                'date'      => $date,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $firestore = app('firebase.firestore');

        //store user details in firestore
        $database = $firestore->database();

        $policeID = session('userID');
        $brgy = session('barangay');

        $incidentReportRef = $database->collection('record_IDs')->document('incidentReport_ID');
        $incidentRefID = $incidentReportRef->snapshot();

        if ($incidentRefID->exists())
        {
            $newIncidentID = $incidentRefID['id'] + 1;

            $incidentID_Records = $database->collection('record_IDs')->document('incidentReport_ID');
            $incidentID_Records->update([
                ['path' => 'id', 'value' => $newIncidentID]
            ]);
        }
        else
        {
            $data = [
                'id' => '100000',
            ];
            $newIncidentID = 100000;

            $database->collection('record_IDs')->document('incidentReport_ID')->set($data);
        }

        $data = [
            'distressMessageRefID'      => $request->input('sosRefID'),
            'incidentReportID'          => $newIncidentID,
            'sender_FullName'           => $request->input('sender_Name'),
            'sendDate'                  => $request->input('sendDate'),
            'sender_location'           => $request->input('sender_loc'),
            'sender_locLink'            => $request->input('sender_locLink'),
            'distressMessageContent'    => $request->input('distressMessageContent'),
            'reportDetails'             => $request->input('reportDetails'),
            'reportTimeDate'            => $request->input('incidentReportDate'),
            'reportCreator'             => $request->input('reportCreator'),
            'reportCreatorID'           => $policeID,
            'reportPosition'            => $request->input('position'),
            'barangay'                  => $brgy,
            'creatorType'               => 'police',
            'report_status'             => 'Ongoing',
        ];

        $database->collection('incident_reports')->document($newIncidentID)->set($data);

        return redirect('police_report');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}