<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CoquiTtsController extends Controller
{
    public function index()
    {
        return view('coqui_tts');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string|max:5000',
            'voice' => 'nullable|string|max:100'
        ]);

        $text = $data['text'];
        $voice = $data['voice'] ?? 'vi-VN-HoaiMyNeural';

        $outputDir = storage_path('app/public/tts');
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = 'edge_tts_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.mp3';
        $outputPath = $outputDir . DIRECTORY_SEPARATOR . $filename;

        // Use absolute path to Python in virtual environment
        $python = 'D:\\Download\\apps\\laragon\\www\\sumotech\\.venv\\Scripts\\python.exe';
        $script = storage_path('scripts/edge_tts_generate.py');

        $process = new Process([
            $python,
            $script,
            '--text',
            $text,
            '--out',
            $outputPath,
            '--voice',
            $voice
        ]);

        // Set environment variables for virtual environment
        $process->setEnv([
            'VIRTUAL_ENV' => 'D:\\Download\\apps\\laragon\\www\\sumotech\\.venv',
            'PATH' => 'D:\\Download\\apps\\laragon\\www\\sumotech\\.venv\\Scripts;' . getenv('PATH'),
        ]);

        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'success' => false,
                'error' => $process->getErrorOutput() ?: $process->getOutput() ?: 'TTS process failed'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'audio_url' => asset('storage/tts/' . $filename)
        ]);
    }
}
