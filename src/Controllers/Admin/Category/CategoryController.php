<?php

namespace Mariojgt\TheWatcher\Controllers\Admin\Category;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Mariojgt\TheWatcher\Models\Admin;
use Mariojgt\TheWatcher\Models\Category;
use Illuminate\Validation\ValidationException;
use Mariojgt\Castle\Helpers\AutenticatorHandle;
use Mariojgt\TheWatcher\Resource\AdminResource;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Category/Index', []);
    }

    public function store(Request $request)
    {
        // Add the slug to the request
        $request->request->add(['slug' => Str::slug(Request('name'))]);
        // Validate the email to make sure it is unique
        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|email|unique:admins,categories',
        ]);
        dd(Request()->all());

        // Create the categorie
        $category              = new Category();
        $category->name        = Request('name');
        $category->slug        = Request('slug');
        $category->description = Request('description');
        // $category->media_id
        $category->save();

        return Redirect::back()
            ->with('success', 'Profile updated successfully');
    }
}
