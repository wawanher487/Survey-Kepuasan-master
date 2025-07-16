<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Feedback;
use App\Models\Kuesioner;
use App\Models\Responden;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IndexController extends Controller
{
    public function index()
    {
        $datakuesioners = Kuesioner::all();
        $dataAnswers = Answer::all();
        $dataRespondens = Responden::all();
        $dataFeedbacks = Feedback::all();

        $total = (object) [
            'kuesioner' => $datakuesioners->count(),
            'answer' => $dataAnswers->count(),
            'responden' => $dataRespondens->count(),
            'feedback' => $dataFeedbacks->count()
        ];

        $today = Carbon::now();
        $dateArray = [];
        for ($i = 0; $i <= 7; $i++) {
            $dateArray[] = $today->subDays($i)->toDateString();
        }
        $dateArray = array_reverse($dateArray);

        $dailyAnswers = [];
        foreach ($dateArray as $key => $date) {
            $dailyAnswers[$date] = [
                (object) [
                    'label' => 0,
                    'total' => Answer::where('answer', 1)->whereDate('created_at', $date)->count()
                ],
                (object) [
                    'label' => 1,
                    'total' => Answer::where('answer', 2)->whereDate('created_at', $date)->count()
                ],
                (object) [
                    'label' => 2,
                    'total' => Answer::where('answer', 3)->whereDate('created_at', $date)->count()
                ],
                (object) [
                    'label' => 3,
                    'total' => Answer::where('answer', 4)->whereDate('created_at', $date)->count()
                ],
            ];
        }

        $answers = (object) [
            'total' => $total->answer,
            'details' => [
                [
                    'label' => rateLabel(1),
                    'total' => $dataAnswers->where('answer', 1)->count(),
                    'percentage' => getPercentage($dataAnswers->where('answer', 1)->count(), $total->answer)
                ],
                [
                    'label' => rateLabel(2),
                    'total' => $dataAnswers->where('answer', 2)->count(),
                    'percentage' => getPercentage($dataAnswers->where('answer', 2)->count(), $total->answer)
                ],
                [
                    'label' => rateLabel(3),
                    'total' => $dataAnswers->where('answer', 3)->count(),
                    'percentage' => getPercentage($dataAnswers->where('answer', 3)->count(), $total->answer)
                ],
                [
                    'label' => rateLabel(4),
                    'total' => $dataAnswers->where('answer', 4)->count(),
                    'percentage' => getPercentage($dataAnswers->where('answer', 4)->count(), $total->answer)
                ],
            ],
            'daily' => $dailyAnswers
        ];

        return view('pages.public.index', compact('total', 'answers'));
    }

    public function kuesioner(Request $request)
    {
        try {
            $step = $request->get('step');
            $question = $request->get('question');

            if (!$step) {
                return redirect()->route('kuesioner', ['step' => 1]);
            }

            if ($step == 1) {
                $villages = Village::all();
                return view('pages.public.kuesioner', compact('step', 'villages'))
                    ->with('totalKuesioner', 0); // belum tau total
            }

            if ($step == 2) {
                $data = $request->all();

                $validator = Validator::make($data, [
                    'step' => 'required',
                    'name' => 'required|max:30',
                    'gender' => 'required',
                    'age' => 'required|numeric|min:1|max:122',
                    'education' => 'required',
                    'job' => 'required',
                    'village' => 'required',
                    'domicile' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator);
                }

                // Ambil kuesioner sesuai satuan kerja
                $kuesioners = Kuesioner::where('village_id', $data['village'])->get();
                $totalKuesioner = count($kuesioners);

                if ($totalKuesioner === 0) {
                    throw new \Error('Maaf, kuesioner belum tersedia');
                }

                // Cek apakah semua sudah diisi
                $semuaPertanyaanTerisi = true;
                for ($i = 1; $i <= $totalKuesioner; $i++) {
                    $questionKey = "question" . $i;
                    if (!isset($data[$questionKey]) || empty($data[$questionKey])) {
                        if ($question == $totalKuesioner + 1) {
                            throw new \Error('Isi semua kuesioner!');
                        }
                        $semuaPertanyaanTerisi = false;
                        break;
                    }
                }

                // Jika semua sudah diisi, lanjut step 3
                if ($semuaPertanyaanTerisi) {
                    $data['step'] = 3;
                    return redirect()->route('kuesioner', [
                        'step' => 3,
                        'data' => $data
                    ]);
                }

                // tampilkan pertanyaan ke-$question
                $kuesioner = $kuesioners[$question - 1];
                $data['question'] = $question - 1;
                $previous = $question == 1 ? '#' : route('kuesioner', $data);
                $data['question'] = $question + 1;
                $next = $question == $totalKuesioner ? '#' : route('kuesioner', $data);

                return view('pages.public.kuesioner', compact(
                    'kuesioner',
                    'step',
                    'next',
                    'previous',
                    'question',
                    'totalKuesioner',
                    'data'
                ));
            }

            if ($step == 3) {
                $data = $request->data;
                $step = $request->step;

                $kuesioners = Kuesioner::where('village_id', $data['village'])->get();

                return view('pages.public.kuesioner', [
                    'step' => 3,
                    'kuesioner' => $kuesioners,
                    'data' => $data
                ]);
            }

            return redirect()->route('kuesioner', ['step' => 1]);
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['message' => ['Terjadi kesalahan!', $th->getMessage()]]);
        }
    }

    public function store(Request $request)
    {
        try {
            $responden = Responden::create([
                'name' => $request->name,
                'gender' => $request->gender,
                'age' => $request->age,
                'education' => $request->education,
                'job' => $request->job,
                'village_id' => $request->village,
                'domicile' => $request->domicile,
            ]);

            if ($request->feedback) {
                Feedback::create([
                    'responden_id' => (int) $responden->id,
                    'feedback' => $request->feedback
                ]);
            }

            foreach ($request->answers as $answer) {
                $answerData = json_decode($answer, true);
                Answer::create([
                    'kuesioner_id' => (int) $answerData['idKuesioner'],
                    'responden_id' => (int) $responden->id,
                    'answer' => (int) $answerData['kuesionerAnswer']
                ]);
            }

            return redirect()
                ->route('index')
                ->with('success', 'Data berhasil disimpan!');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['message' => ['Terjadi kesalahan!', $th->getMessage()]]);
        }
    }
}
