<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Official/Announcements/Index', [
            'announcements' => Announcement::with('creator:id,name')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        Announcement::create([
            'title'      => $validated['title'],
            'content'    => $validated['content'],
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }
}
