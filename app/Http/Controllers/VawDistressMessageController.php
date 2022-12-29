<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VawDistressMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $distressRef = $database->collection('sos-distress-message');
        $messageRef = $distressRef->documents();

        return view('pages.vaw_distressmessage', [
            'message' => $messageRef,
        ]);
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
        //$request->session()->put('viewDistressID', 'distressID');
        session(['viewDistressID' => $request->input('distressID')]);

        return redirect('vaw_reviewdistressmessage');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $firestore = app('firebase.firestore');

        $database = $firestore->database();
        $distressRef = $database->collection('sos-distress-message');
        $idRef = $distressRef->where('sosID', '=', $id);
        $messageRef = $idRef->documents();

        return view('pages.vaw_reviewdistressmessage', [
            'message' => $messageRef,
        ]);
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
