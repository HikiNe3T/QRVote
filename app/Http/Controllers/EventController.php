<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendAccessCode;
use Intervention\Image\ImageManagerStatic as Image;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Traits\EventHelpers;

class EventController extends Controller
{
    use EventHelpers;

    public function create()
    {
        return view('create-event');
    }

    public function store(Request $request)
    {
        // =========================
        // VALIDASI DASAR
        // =========================
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => [
        'required',
        'date_format:H:i',
        function ($attribute, $value, $fail) use ($request) {
            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i');

            // Jika tanggal mulai adalah hari ini,
            // jam mulai tidak boleh sudah lewat
            if ($request->start_date === $today && $value < $currentTime) {
                $fail(
                    'Jam mulai tidak boleh memilih waktu yang sudah lewat. ' .
                    'Sekarang sudah pukul ' . $currentTime . '.'
                );
            }
        },
    ],

    'end_time' => [
        'required',
        'date_format:H:i',
        function ($attribute, $value, $fail) use ($request) {
            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i');

            // Jika tanggal selesai adalah hari ini,
            // jam selesai tidak boleh sudah lewat
            if ($request->end_date === $today && $value < $currentTime) {
                $fail(
                    'Jam selesai tidak boleh memilih waktu yang sudah lewat. ' .
                    'Sekarang sudah pukul ' . $currentTime . '.'
                );
            }

            // Jika mulai dan selesai di hari yang sama,
            // jam selesai tidak boleh sebelum jam mulai
            if (
                $request->start_date === $request->end_date &&
                $value <= $request->start_time
            ) {
                $fail(
                    'Jam selesai harus lebih besar dari jam mulai ' .
                    '(' . $request->start_time . ') dan tidak boleh melewati pukul 00:00.'
                );
            }
        },
    ],
            'candidates' => 'required|array|min:1',
            'candidates.*' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'voting_type' => 'required|in:public,private',
        ]);

        // =========================
        // FLAG BOOLEAN (BIAR RAPI)
        // =========================
        $categoriesEnabled = $request->boolean('categories_enabled');
        $juaraHarapanEnabled = $request->boolean('juara_harapan_enabled');
        $pemenangKategoriEnabled = $request->boolean('pemenang_kategori_enabled');

        // =========================
        // VALIDASI KATEGORI
        // =========================
        $categories = [];

        if ($categoriesEnabled) {
            $categories = json_decode($request->categories_data, true);

            if (!$categories || count($categories) === 0) {
                return back()->withErrors(['Kategori tidak boleh kosong'])->withInput();
            }

            $total = array_sum(array_column($categories, 'weight'));

            if ($total != 100) {
                return back()->withErrors(['Total bobot kategori harus 100%'])->withInput();
            }
        }

        // =========================
        // VALIDASI JUARA HARAPAN
        // =========================
        if ($juaraHarapanEnabled) {

            $totalKandidat = count($request->candidates);
            $jumlah = $request->juara_harapan_count;

            if ($totalKandidat <= 4) {
                return back()->withErrors(['Minimal kandidat harus lebih dari 4']);
            }

            if (!$jumlah || $jumlah <= 0) {
                return back()->withErrors(['Jumlah juara harapan wajib diisi']);
            }

            if ($jumlah > ($totalKandidat - 3)) {
                return back()->withErrors(['Jumlah juara harapan terlalu banyak']);
            }
        }

        // =========================
        // VALIDASI KATEGORI PENENTU
        // =========================
        $determiningIndex = $request->input('determining_category_id');

        if ($pemenangKategoriEnabled) {
            if (!$categoriesEnabled) {
                return back()->withErrors(['Aktifkan kategori penilaian terlebih dahulu'])->withInput();
            }

            // JANGAN pakai !$value -> "0" (kategori pertama) dianggap kosong
            if ($determiningIndex === null || $determiningIndex === '' || !is_numeric($determiningIndex)) {
                return back()->withErrors(['Pilih kategori penentu'])->withInput();
            }

            $determiningIndex = (int) $determiningIndex;

            if (!isset($categories[$determiningIndex])) {
                return back()->withErrors(['Kategori penentu tidak valid'])->withInput();
            }
        }


        // =========================
        // UPLOAD IMAGE
        // =========================
        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->extension();

            $img = Image::make($image)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            $img->save(storage_path('app/public/events/' . $imageName), 80);
        }

        DB::beginTransaction();

        try {

            // =========================
            // CREATE EVENT
            // =========================
            $event = Event::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imageName,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'creator_id' => Auth::id(),
                'voting_type' => $request->input('voting_type'),

                // BOOLEAN FLAGS
                'categories_enabled' => $categoriesEnabled,
                'juara_harapan_enabled' => $juaraHarapanEnabled,
                'pemenang_kategori_enabled' => $pemenangKategoriEnabled,

                'juara_harapan_count' => $juaraHarapanEnabled
                    ? $request->juara_harapan_count
                    : 0,

                'determining_category_id' => null
            ]);

            // =========================
            // SIMPAN KATEGORI
            // =========================
            $categoryIds = [];

            if ($categoriesEnabled) {
                foreach ($categories as $cat) {

                    $category = Category::create([
                        'event_id' => $event->id,
                        'name' => $cat['name'],
                        'weight' => $cat['weight']
                    ]);

                    $categoryIds[] = $category->id;
                }
            }

            // =========================
            // SET KATEGORI PENENTU
            // =========================
            if ($pemenangKategoriEnabled && $categoriesEnabled) {
                if (!isset($categoryIds[$determiningIndex])) {
                    throw new \Exception('Kategori penentu tidak valid');
                }

                $event->update([
                    'determining_category_id' => $categoryIds[$determiningIndex],
                ]);
            }


            // =========================
            // SIMPAN KANDIDAT
            // =========================
            foreach ($request->candidates as $index => $name) {
                Candidate::create([
                    'event_id' => $event->id,
                    'name' => $name,
                    'number' => $index + 1
                ]);
            }

            // =========================
            // PARTICIPANTS (PRIVATE)
            // =========================
            if ($request->input('voting_type') === 'private') {

                foreach ($request->participants ?? [] as $p) {

                    $data = json_decode($p, true);
                    if (!$data) continue;

                    Participant::create([
                        'event_id' => $event->id,
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'access_code' => $data['code'],
                        'has_voted' => 0
                    ]);

                    // EMAIL
                    if (!empty($data['email'])) {
                        Mail::to($data['email'])
                            ->send(new SendAccessCode($data['code']));
                    }

                    // WA
                    if (!empty($data['phone'])) {
                        $this->sendWhatsApp(
                            $this->formatPhone($data['phone']),
                            "Kode akses voting kamu: " . $data['code']
                        );
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([
                'Gagal menyimpan event: ' . $e->getMessage()
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Event berhasil dibuat!');
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->creator_id !== Auth::id()) {
            abort(403);
        }

        // Mencegah hapus jika event sedang berjalan atau sudah selesai
        if (in_array($event->status, ['active'])) {
            return back()->with('error', 'Event tidak dapat dihapus saat sedang berlangsung atau sudah selesai.');
        }

        $event->delete();

        return back()->with('success', 'Event dihapus');
    }

    public function checkCode(Request $request, $eventId)
    {
        $participant = Participant::where('event_id', $eventId)
            ->where('access_code', $request->code)
            ->first();

        if (!$participant) {
            return back()->with('error', 'Kode salah');
        }

        if ($participant->has_voted) {
            return back()->with('error', 'Kode sudah digunakan');
        }

        session([
            'participant_id' => $participant->id
        ]);

        return redirect()->route('event.vote', $eventId);
    }

    public function admin($id)
    {
        $event = Event::with(['candidates', 'participants'])
            ->findOrFail($id);

        $totalVote = $event->participants
            ->where('has_voted', 1)
            ->count();

        return view('admin-event', compact('event', 'totalVote'));
    }

    public function toggleVoting(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $event->voting_type = $request->type;
        $event->save();

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Mencegah edit jika event sedang berjalan atau sudah selesai
        if (in_array($event->status, ['active', 'finished'])) {
            return response()->json([
                'success' => false,
                'message' => 'Detail event tidak dapat diubah saat event sedang berlangsung atau sudah selesai.'
            ], 403);
        }

        $event->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $event = Event::findOrFail($id);

        // Mencegah hapus jika event belum dimulai (upcoming) atau sedang berlangsung (active)
        if (in_array($event->status, ['upcoming', 'active'])) {
            return back()->with('error', 'Event tidak dapat dihapus saat belum dimulai atau sedang berlangsung.');
        }

        $event->delete();

        return redirect()->route('dashboard')->with('success', 'Event berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $event->status = $request->active ? 'active' : 'inactive';
        $event->save();

        return response()->json(['success' => true]);
    }

    public function generateQrEvent($id)
    {
        $event = Event::findOrFail($id);
        $png = $this->generateQrPngGD(url('/event/' . $event->id));

        return response($png, 200, ['Content-Type' => 'image/png']);
    }

    public function downloadQrEvent($id)
    {
        $event = Event::findOrFail($id);
        $png = $this->generateQrPngGD(url('/event/' . $event->id));

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="qr-event-' . str()->slug($event->name) . '.png"',
        ]);
    }

    public function generateQrCandidate($id)
    {
        $candidate = Candidate::findOrFail($id);
        $png = $this->generateQrPngGD(url('/vote/' . $candidate->id));

        return response($png, 200, ['Content-Type' => 'image/png']);
    }

    public function downloadCandidatesPDF($id)
    {
        $event = Event::with('candidates')->findOrFail($id);

        $qrBase64 = [];
        foreach ($event->candidates as $candidate) {
            $url = url('/vote/' . $candidate->id);
            $png = $this->generateQrPngGD($url, 300);
            $qrBase64[$candidate->id] = base64_encode($png);
        }

        $pdf = Pdf::loadView('pdf.candidates', [
            'event' => $event,
            'candidates' => $event->candidates,
            'qrBase64' => $qrBase64,
        ])->setPaper('a4');

        return $pdf->download('lanyard-' . str()->slug($event->name) . '.pdf');
    }


}