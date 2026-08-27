<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Tampilkan form edit kandidat dengan data lama sudah terisi.
     */
    public function edit($id)
    {
        $candidate = Candidate::with('event')->findOrFail($id);
        $event = $candidate->event;

        return view('edit-candidate', compact('candidate', 'event'));
    }

    /**
     * Update data kandidat (nama, nomor, biodata, foto).
     * Jika foto baru diupload, foto lama dihapus dari storage.
     */
    public function update(Request $request, $id)
    {
        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:150',

            'biodata' => 'nullable|string',
            'photo'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $photoName = $candidate->photo_url;

        if ($request->hasFile('photo')) {

            $photo = $request->file('photo');
            $photoName = uniqid() . '.' . $photo->extension();

            $img = Image::make($photo)
                ->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // simpan ke storage
            Storage::put('public/candidates/' . $photoName, (string) $img->encode());

            // hapus foto lama
            if ($candidate->photo_url) {
                Storage::delete('public/candidates/' . $candidate->photo_url);
            }
        }

        $candidate->update([
            'name'     => $request->name,

            'biodata'  => $request->biodata,
            'photo_url' => $photoName,
        ]);

        return redirect()->route('admin.event', $candidate->event_id)
            ->with('success', 'Data kandidat berhasil diperbarui!');
    }
}