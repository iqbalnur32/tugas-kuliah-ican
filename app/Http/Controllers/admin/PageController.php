<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\support\facades\Validator;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::orderBy('id', 'asc');

        if ($request->keyword != '') {
            $pages = $pages->where('name', 'like', '%' . $request->keyword . '%');
        }

        $pages = $pages->paginate(10);

        return view('admin.pages.list', [
            'pages' => $pages
        ]);
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ], [
            'name.required' => 'Harap isi nama terlebih dahulu.',
            'slug.required' => 'Harap isi slug terlebih dahulu.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page sukses ditambahkan';

        session()->flash('succes', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function edit($id)
    {

        $page = Page::find($id);

        if ($page == null) {
            session()->flas('error' . 'Page tidak ditemukan');
            return redirect()->route('pages.index');
        }

        return view('admin.pages.edit', [
            'page' => $page
        ]);
    }

    public function update(Request $request, $id)
    {

        $page = Page::find($id);

        if ($page == null) {
            session()->flash('error', 'Page tidak ditemukan');
            return response()->json([
                'status' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required'
        ], [
            'name.required' => 'Harap isi nama terlebih dahulu.',
            'slug.required' => 'Harap isi slug terlebih dahulu.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page sukses terupdate';

        session()->flash('succes', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function destroy($id)
    {
        $page = Page::find($id);

        if ($page == null) {
            session()->flash('error', 'Page tidak ditemukan');

            return response()->json([
                'status' => true,
            ]);
        }

        $page->delete();

        $message = 'Page sukses terhapus';

        session()->flash('succes', $message);

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
