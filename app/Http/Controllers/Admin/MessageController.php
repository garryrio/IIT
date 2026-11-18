<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $items = ContactMessage::query()
            ->when(array_key_exists($request->query('status'), ContactMessage::STATUSES), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->query('q'), function ($q, $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('subject', 'like', $like)->orWhere('message', 'like', $like));
            })
            ->latest()->paginate(15)->withQueryString();

        return view('admin.messages.index', compact('items'));
    }

    public function show(ContactMessage $message): View
    {
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function update(Request $request, ContactMessage $message): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(array_keys(ContactMessage::STATUSES))]]);
        $message->update($data);

        return back()->with('success', 'Status pesan diperbarui menjadi "'.ContactMessage::STATUSES[$data['status']].'".');
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Pesan telah dihapus.');
    }
}
