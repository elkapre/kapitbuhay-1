<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Auth\SignInResult\SignInResult;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Contract\Firestore;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Kreait\Firebase\Value\Uid;
use App\Mail\EmailSender;
use Exception;
use Illuminate\Support\Facades\Mail;

class VawAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $firestore = app('firebase.firestore');
        $storage = app('firebase.storage');

        $database = $firestore->database();
        $userRef = $database->collection('barangay_accounts');
        //$idRef = $userRef->where('brgyValidIDFront', '=', 'empty');
        $civilianUsers = $userRef->documents();

        $bucket = $storage->getBucket();

        foreach($civilianUsers as $uid)
        {
            $uuid = $uid['brgyUID'];
            $brgyLogo = $bucket->object('barangay-vaw/'. $uuid .'/credentials/Barangay-Logo.png');
            $brgyIDFront = $bucket->object('barangay-vaw/'.$uuid.'/credentials/Barangay-Valid-ID-Front.png');
            $brgyIDBack = $bucket->object('barangay-vaw/'.$uuid.'/credentials/Barangay-Valid-ID-Back.png');

            $urlLogo = $brgyLogo->signedUrl(
                # This URL is valid for 15 minutes
                new \DateTime('15 min'),
                [
                    'version' => 'v4',
                ]
            );

            $urlIDFront = $brgyIDFront->signedUrl(
                # This URL is valid for 15 minutes
                new \DateTime('15 min'),
                [
                    'version' => 'v4',
                ]
            );

            $urlIDBack = $brgyIDBack->signedUrl(
                # This URL is valid for 15 minutes
                new \DateTime('15 min'),
                [
                    'version' => 'v4',
                ]
            );

            $civilianUsers = $database->collection('barangay_accounts')->document($uuid);

            $civilianUsers->update([
                        ['path' => 'brgyLogo', 'value' => $urlLogo],
                        ['path' => 'brgyValidIDFront', 'value' => $urlIDFront],
                        ['path' => 'brgyValidIDBack', 'value' => $urlIDBack]
                    ]);
        };

        $civilianUsers = $userRef->documents();
        return view('pages.manage_VawAccounts', [
            'account' => $civilianUsers,
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
        //
        session(['viewVawID' => $request->input('vawID')]);

        return redirect('manage_accountsvawprofileview');
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
        $userRef = $database->collection('barangay_accounts');
        $idRef = $userRef->where($userRef->id(), '=', $id);
        $civilianUsers = $idRef->documents();

        return view('pages.manage_VictimAccounts', [
            'account' => $civilianUsers,
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
