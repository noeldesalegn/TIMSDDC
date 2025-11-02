<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class AdminNewsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $status = $request->string('status')->toString(); // active|inactive|all
        $perPage = (int) $request->query('per_page', 10);

        $query = News::query();
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('body', 'like', "%$q%");
            });
        }
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $news = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.news.index', compact('news', 'q', 'status', 'perPage'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        News::create($data);
        return redirect()->route('admin.news.index')->with('success', 'News post created.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'body' => ['required','string'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $news->update($data);
        return redirect()->route('admin.news.index')->with('success', 'News post updated.');
    }

    public function toggle(News $news)
    {
        $news->is_active = !$news->is_active;
        $news->save();
        return back()->with('success', 'News status updated.');
    }
}
