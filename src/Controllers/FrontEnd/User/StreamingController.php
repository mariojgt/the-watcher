<?php

namespace Mariojgt\TheWatcher\Controllers\FrontEnd\User;

use Pusher\Pusher;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class StreamingController extends Controller
{
    /**
     * @return [blade view]
     */
    public function index()
    {
        // Example on the fly change the inertia template
        // Inertia::setRootView('layouts.news');
        return Inertia::render('Frontend/Streaming/Index', [
            'title' => 'Login',
        ]);
    }

    public function watch()
    {
        // Example on the fly change the inertia template
        // Inertia::setRootView('layouts.news');
        return Inertia::render('Frontend/Streaming/Watch', [
            'title' => 'Login',
        ]);
    }

    // Pusher autorization
    public function pusherAuth()
    {
        $app_id      = "960185";
        $app_key     = "0be1b784902a6fb92390";
        $app_secret  = "9ae1d43ccdee1fc963e5";
        $app_cluster = "mt1";

        $pusher = new Pusher($app_key, $app_secret, $app_id, ['cluster' => $app_cluster]);

        $auth = $pusher->socket_auth(Request('channel_name'), Request('socket_id'));

        return $auth;
    }

    /**
     * Recive the streaming video
     *
     * @param Request $request
     *
     * @return [type]
     */
    public function reciveStreamingVideo(Request $request)
    {
        // Get the video and save it whats is changed
        $video = $request->file('file');

        $target_dir = public_path("streaming/");

        $target_file = $target_dir . 'test.webm';
        // Create a new file or increment the saved file
        if (file_exists($target_file)) {
            // Second option is this
            $myfile = fopen($target_file, "a") or die("Unable to open file!");

            fwrite($myfile, file_get_contents($video));
            fclose($myfile);
        } else {
            move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
        }

        return response()->json(['success' => true]);
    }
}
