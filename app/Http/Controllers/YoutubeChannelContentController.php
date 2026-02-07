<?php

namespace App\Http\Controllers;

use App\Models\YoutubeChannel;
use App\Models\YoutubeChannelContent;
use Illuminate\Http\Request;

class YoutubeChannelContentController extends Controller
{
    public function index(YoutubeChannel $youtubeChannel)
    {
        $contents = $youtubeChannel->contents()->orderByDesc('published_at')->paginate(20);

        return view('youtube_channel_contents.index', compact('youtubeChannel', 'contents'));
    }

    public function create(YoutubeChannel $youtubeChannel)
    {
        return view('youtube_channel_contents.create', compact('youtubeChannel'));
    }

    public function store(Request $request, YoutubeChannel $youtubeChannel)
    {
        $validated = $request->validate([
            'video_id' => 'required|string|max:255|unique:youtube_channel_contents,video_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:2048',
            'thumbnail_url' => 'nullable|string|max:2048',
            'duration_seconds' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'views_count' => 'nullable|integer|min:0',
            'likes_count' => 'nullable|integer|min:0',
            'comments_count' => 'nullable|integer|min:0',
        ]);

        $validated['youtube_channel_id'] = $youtubeChannel->id;

        YoutubeChannelContent::create($validated);

        return redirect()->route('youtube-channels.contents.index', $youtubeChannel)
            ->with('success', 'Video created successfully.');
    }

    public function show(YoutubeChannel $youtubeChannel, int $content)
    {
        $content = $youtubeChannel->contents()->findOrFail($content);

        return view('youtube_channel_contents.show', compact('youtubeChannel', 'content'));
    }

    public function edit(YoutubeChannel $youtubeChannel, int $content)
    {
        $content = $youtubeChannel->contents()->findOrFail($content);

        return view('youtube_channel_contents.edit', compact('youtubeChannel', 'content'));
    }

    public function update(Request $request, YoutubeChannel $youtubeChannel, int $content)
    {
        $content = $youtubeChannel->contents()->findOrFail($content);

        $validated = $request->validate([
            'video_id' => 'required|string|max:255|unique:youtube_channel_contents,video_id,' . $content->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string|max:2048',
            'thumbnail_url' => 'nullable|string|max:2048',
            'duration_seconds' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'views_count' => 'nullable|integer|min:0',
            'likes_count' => 'nullable|integer|min:0',
            'comments_count' => 'nullable|integer|min:0',
        ]);

        $content->update($validated);

        return redirect()->route('youtube-channels.contents.index', $youtubeChannel)
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(YoutubeChannel $youtubeChannel, int $content)
    {
        $content = $youtubeChannel->contents()->findOrFail($content);
        $content->delete();

        return redirect()->route('youtube-channels.contents.index', $youtubeChannel)
            ->with('success', 'Video deleted successfully.');
    }
}
