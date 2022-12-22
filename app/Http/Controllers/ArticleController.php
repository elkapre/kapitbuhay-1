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

class ArticleController extends Controller
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
        $articleRef = $database->collection('articles');
        $imageLink = $articleRef->documents();

        $bucket = $storage->getBucket();

        foreach($imageLink as $link)
        {
            $articleID = $link['article_id'];

            $articleImage = $bucket->object('articles/'. $articleID .'/Article-Image.png');

            $articleImageUrl = $articleImage->signedUrl(
                # This URL is valid for 15 minutes
                new \DateTime('15 min'),
                [
                    'version' => 'v4',
                ]
            );

            $civilianUsers = $database->collection('articles')->document($articleID);

            $civilianUsers->update([
                ['path' => 'image', 'value' => $articleImageUrl]
            ]);
        }

        $articleActive = $articleRef->where('Active', '=', 'isActive');
        $documentRef = $articleActive->documents();

        return view('pages.manage_articles', [
            'document' => $documentRef,
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
        //initialize firebase
        $storage = app('firebase.storage');
        $firestore = app('firebase.firestore');

        //initialize database of firestore
        $database = $firestore->database();
        $articleRefSnap= $database->collection('articles')->document('100000');
        $isArticleEmpty = $articleRefSnap->snapshot();

        $articleRef = $database->collection('articles');
        $article = $articleRef->documents();

        if ($isArticleEmpty->exists())
        {
            foreach ($article as $findlatestID)
            {
                $newArticleID = $findlatestID['article_id'];
            }
            $newArticleID += 1;
        }
        else
        {
            $newArticleID = 100000;
        }

        //store image in firebase storage
        $bucket = $storage->getBucket();

        //get the user-input image
        $imageArticle = $request->file('articleimage');

        //get the original name of the file
        $articleImageName = $imageArticle->getClientOriginalName();

        //store to temporary local folder
        $localfolder = public_path('storage-temp-folder') .'/';

        //create file path in storage
        $articlePath = [
            'name' => 'articles/' . $newArticleID . '/Article-Image.png',
        ];

        //upload barangay Logo in Storage
        if ($imageArticle->move($localfolder, $articleImageName)) {
            $imgfile = fopen($localfolder.$articleImageName, 'r');
            $bucket->upload($imgfile, $articlePath);
            //will remove from local laravel folder
            unlink($localfolder . $articleImageName);
        }

        //store article data in fields
        $data = [
            'article_id'        => $newArticleID,
            'Active'            => 'isActive',
            'article_title'     => $request->input('createArticleTitle'),
            'article_content'   => $request->input('createArticleContent'),
            'image'             => 'empty',
            'source_link'       => 'empty',
        ];

        //save in firestore database
        $database->collection('articles')->document($newArticleID)->set($data);




        return redirect('article');
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

        //initialize firebase
        $storage = app('firebase.storage');
        $firestore = app('firebase.firestore');

        //store image in firebase storage
        $bucket = $storage->getBucket();

        $title = str_replace(' ', '', $request->input('updateArticleTitle'));

        $database = $firestore->database();
        $articleRef = $database->collection('articles');
        $articleID = $articleRef->documents();

        foreach($articleID as $article_ID)
        {

            if($request->file('articleimage') == null)
            {
                if($article_ID['article_id'] == $title)
                {
                    $articleRef = $database->collection('articles')->document($article_ID['article_id']);
                    $articleRef->update([
                        ['path' => 'article_content', 'value' => $request->input('updateArticleContent')]
                    ]);
                    break;
                }
                elseif($article_ID['article_id'] != $title)
                {
                    $objectArticleID = $bucket->object('articles/' . $article_ID['article_id'] . '/Article-Image.png',);

                    $copiedObject = $objectArticleID->copy($bucket, [
                        'name' => 'articles/' . $title . '/Article-Image.png',
                    ]);

                    $articleImage = $bucket->object('articles/'. $title .'/Article-Image.png');

                    $articleImageUrl = $articleImage->signedUrl(
                        # This URL is valid for 15 minutes
                        new \DateTime('15 min'),
                        [
                            'version' => 'v4',
                        ]
                    );

                    //delete the old article data
                    $deletedoc = $database->collection('articles')->document($id)->delete();

                    $data = [
                        'article_id'        => $title,
                        'Active'            => 'isActive',
                        'article_title'     => $request->input('updateArticleTitle'),
                        'article_content'   => $request->input('updateArticleContent'),
                        'image'             => $articleImageUrl,
                        'source_link'       => 'empty',
                    ];

                    //save in firestore database
                    $database->collection('articles')->document($title)->set($data);
                    $objectArticleID->delete();
                    break;
                }
            }
            elseif ($request->file('articleimage') != null)
            {
                //get the user-input image
                $imageArticle = $request->file('articleimage');

                //get the original name of the file
                $articleImageName = $imageArticle->getClientOriginalName();

                //store to temporary local folder
                $localfolder = public_path('storage-temp-folder') .'/';

                //create file path in storage
                $articlePath = [
                    'name' => 'articles/' . $article_ID['article_id'] . '/Article-Image.png',
                ];

                if ($imageArticle->move($localfolder, $articleImageName))
                {
                    $imgfile = fopen($localfolder.$articleImageName, 'r');
                    $bucket->upload($imgfile, $articlePath);
                    //will remove from local laravel folder
                    unlink($localfolder . $articleImageName);
                }

                //upload barangay Logo in Storage
                if ($article_ID['article_id'] == $title)
                {
                    $articleRef = $database->collection('articles')->document($id);
                    $articleRef->update([
                        ['path' => 'article_content', 'value' => $request->input('updateArticleContent')]
                    ]);
                    break;
                }
                elseif ($article_ID['article_id'] != $title)
                {
                    $objectIDArticle = $bucket->object('articles/' . $article_ID['article_id'] . '/Article-Image.png',);

                    $copiedObject = $objectIDArticle->copy($bucket, [
                        'name' => 'articles/' . $title . '/Article-Image.png',
                    ]);

                    //delete the old article data
                    $deletedoc = $database->collection('articles')->document($id)->delete();
                    $objectIDArticle->delete();

                    $data = [
                        'article_id'        => $title,
                        'Active'            => 'isActive',
                        'article_title'     => $request->input('updateArticleTitle'),
                        'article_content'   => $request->input('updateArticleContent'),
                        'image'             => 'empty',
                        'source_link'       => 'empty',
                    ];

                    //save in firestore database
                    $database->collection('articles')->document($title)->set($data);

                    break;
                }
            }
        }

        return redirect('article');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $firestore = app('firebase.firestore');
        $database = $firestore->database();
        $deletedoc = $database->collection('articles')->document($id);
        $deletedoc->update([
            ['path' => 'Active', 'value' => 'notActive']
        ]);

        return redirect('article');
    }
}
