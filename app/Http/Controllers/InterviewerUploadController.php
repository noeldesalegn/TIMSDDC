<?php

namespace App\Http\Controllers;

use App\Models\InterviewerUpload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class InterviewerUploadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', ''); // image|pdf|doc|all
        $perPage = (int) $request->query('per_page', 12);

        $query = InterviewerUpload::with(['uploader', 'taxpayer'])
            ->where('user_id', $user->id)
            ->where('status', '!=', 'deleted');

        if ($q) {
            $query->where('original_name', 'like', "%$q%");
        }
        if ($type) {
            $query->where(function ($sub) use ($type) {
                if ($type === 'image') $sub->where('mime', 'like', 'image/%');
                elseif ($type === 'pdf') $sub->where('mime', 'application/pdf');
                elseif ($type === 'doc') $sub->whereIn('mime', [
                    'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain', 'text/csv'
                ]);
            });
        }

        $uploads = $query->latest()->paginate($perPage)->withQueryString();

        return view('interviewer.upload', compact('uploads', 'q', 'type', 'perPage'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $maxSize = 10 * 1024 * 1024; // 10MB
        $allowed = [
            'application/pdf',
            'image/jpeg','image/png','image/gif','image/webp',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain','text/csv'
        ];

        $saved = [];

        // Handle multiple direct files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (!$file->isValid()) continue;
                if ($file->getSize() > $maxSize) continue;
                if (!in_array($file->getMimeType(), $allowed)) continue;
                $path = $file->store('interviewer_uploads', 'public');
                $saved[] = InterviewerUpload::create([
                    'user_id' => $user->id,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Handle zip archive
        if ($request->hasFile('zip')) {
            $zipFile = $request->file('zip');
            if ($zipFile->isValid() && $zipFile->getClientOriginalExtension() === 'zip') {
                $tmpPath = $zipFile->getRealPath();
                $zip = new ZipArchive();
                if ($zip->open($tmpPath) === TRUE) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        if (!$stat) continue;
                        $name = $stat['name'];
                        if (substr($name, -1) === '/') continue; // directory
                        $stream = $zip->getStream($name);
                        if (!$stream) continue;
                        $contents = stream_get_contents($stream);
                        fclose($stream);
                        if ($contents === false) continue;
                        $size = strlen($contents);
                        if ($size > $maxSize) continue;
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        $mime = $this->guessMime($ext);
                        if (!in_array($mime, $allowed)) continue;
                        $storedPath = 'interviewer_uploads/'.uniqid().'_'.$this->sanitizeFilename(basename($name));
                        Storage::disk('public')->put($storedPath, $contents);
                        $saved[] = InterviewerUpload::create([
                            'user_id' => $user->id,
                            'original_name' => basename($name),
                            'path' => $storedPath,
                            'mime' => $mime,
                            'size' => $size,
                            'meta' => ['from_zip' => $zipFile->getClientOriginalName()],
                        ]);
                    }
                    $zip->close();
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'saved' => count($saved),
                'items' => $saved,
            ]);
        }

        return back()->with('success', 'Files uploaded: '.count($saved));
    }

    public function download(InterviewerUpload $upload)
    {
        $this->authorizeOwner($upload);
        return Storage::disk('public')->download($upload->path, $upload->original_name);
    }

    public function destroy(InterviewerUpload $upload)
    {
        $this->authorizeOwner($upload);
        Storage::disk('public')->delete($upload->path);
        $upload->status = 'deleted';
        $upload->save();
        return back()->with('success', 'File deleted');
    }

    protected function authorizeOwner(InterviewerUpload $upload)
    {
        if (auth()->id() !== $upload->user_id) {
            abort(403);
        }
    }

    protected function guessMime(string $ext): string
    {
        return match ($ext) {
            'pdf' => 'application/pdf',
            'jpg','jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            default => 'application/octet-stream',
        };
    }

    public function taxpayerindex(Request $request, User $taxpayer)
    {
        // Ensure selected user is a taxpayer
        if ($taxpayer->role !== 'taxpayer') {
            abort(404);
        }

        $q = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', '');
        $perPage = (int) $request->query('per_page', 12);

        $query = InterviewerUpload::where('taxpayer_id', $taxpayer->id)
            ->where('status', '!=', 'deleted');

        if ($q) {
            $query->where('original_name', 'like', "%{$q}%");
        }

        if ($type) {
            $query->where(function ($sub) use ($type) {
                if ($type === 'image') {
                    $sub->where('mime', 'like', 'image/%');
                } elseif ($type === 'pdf') {
                    $sub->where('mime', 'application/pdf');
                } elseif ($type === 'doc') {
                    $sub->whereIn('mime', [
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/plain',
                        'text/csv'
                    ]);
                }
            });
        }

        $uploads = $query->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('interviewer.taxpayer.taxpayeruploads', compact(
            'taxpayer',
            'uploads',
            'q',
            'type',
            'perPage'
        ));
    }

    /**
     * STORE uploads FOR THAT TAXPAYER
     */
    public function taxpayerindexstore(Request $request, User $taxpayer)
    {
        if ($taxpayer->role !== 'taxpayer') {
            abort(404);
        }

        $interviewer = $request->user();

        $maxSize = 10 * 1024 * 1024;
        $allowed = [
            'application/pdf',
            'image/jpeg','image/png','image/gif','image/webp',
            'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain','text/csv'
        ];

        $saved = [];

        /** MULTIPLE FILES */
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if (
                    !$file->isValid() ||
                    $file->getSize() > $maxSize ||
                    !in_array($file->getMimeType(), $allowed)
                ) {
                    continue;
                }

                $path = $file->store('interviewer_uploads', 'public');

                $saved[] = InterviewerUpload::create([
                    'user_id'      => $interviewer->id,
                    'taxpayer_id'  => $taxpayer->id,
                    'original_name'=> $file->getClientOriginalName(),
                    'path'         => $path,
                    'mime'         => $file->getMimeType(),
                    'size'         => $file->getSize(),
                    'status'       => 'uploaded',
                ]);
            }
        }

        /** ZIP FILE */
        if ($request->hasFile('zip')) {
            $zipFile = $request->file('zip');

            if ($zipFile->isValid() && $zipFile->getClientOriginalExtension() === 'zip') {
                $zip = new ZipArchive();

                if ($zip->open($zipFile->getRealPath()) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        if (!$stat || str_ends_with($stat['name'], '/')) continue;

                        $stream = $zip->getStream($stat['name']);
                        if (!$stream) continue;

                        $contents = stream_get_contents($stream);
                        fclose($stream);

                        if (strlen($contents) > $maxSize) continue;

                        $storedPath = 'interviewer_uploads/' . uniqid() . '_' . basename($stat['name']);
                        Storage::disk('public')->put($storedPath, $contents);

                        $saved[] = InterviewerUpload::create([
                            'user_id'      => $interviewer->id,
                            'taxpayer_id'  => $taxpayer->id,
                            'original_name'=> basename($stat['name']),
                            'path'         => $storedPath,
                            'mime'         => mime_content_type(storage_path('app/public/'.$storedPath)),
                            'size'         => strlen($contents),
                            'status'       => 'uploaded',
                            'meta'         => ['from_zip' => $zipFile->getClientOriginalName()],
                        ]);
                    }
                    $zip->close();
                }
            }
        }

        return back()->with('success', 'Files uploaded: ' . count($saved));
    }

    public function taxpayerDownload(InterviewerUpload $upload, User $taxpayer)
    {
        if (auth()->id() !== $upload->user_id || $upload->taxpayer_id !== $taxpayer->id) {
            abort(403);
        }
        return Storage::disk('public')->download($upload->path, $upload->original_name);
    }

    public function taxpayerDestroy(InterviewerUpload $upload, User $taxpayer)
    {
        if (auth()->id() !== $upload->user_id || $upload->taxpayer_id !== $taxpayer->id) {
            abort(403);
        }
        Storage::disk('public')->delete($upload->path);
        $upload->status = 'deleted';
        $upload->save();
        return back()->with('success', 'File deleted');
    }


        protected function sanitizeFilename(string $name): string
    {
        return preg_replace('/[^A-Za-z0-9_\.-]/', '_', $name);
    }

}
