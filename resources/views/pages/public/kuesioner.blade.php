@php
	$genders = [
	    (object) [
	        'value' => 'Laki-laki',
	        'label' => 'Laki-laki',
	    ],
	    (object) [
	        'value' => 'Perempuan',
	        'label' => 'Perempuan',
	    ],
	];
	
	$educations = [
	    (object) [
	        'value' => 'SD',
	        'label' => 'Sekolah Dasar (SD)',
	    ],
	    (object) [
	        'value' => 'SMP',
	        'label' => 'Sekolah Menengah Pertama (SMP)',
	    ],
	    (object) [
	        'value' => 'SMA',
	        'label' => 'Sekolah Menengah Atas (SMA)',
	    ],
	    (object) [
	        'value' => 'D',
	        'label' => 'Diploma Empat (D4)',
	    ],
	    (object) [
	        'value' => 'D3',
	        'label' => 'Diploma Tiga (D3)',
	    ],
	    (object) [
	        'value' => 'S1',
	        'label' => 'Sarjana (S1)',
	    ],
	    (object) [
	        'value' => 'S2',
	        'label' => 'Magister (S2)',
	    ],
	    (object) [
	        'value' => 'S3',
	        'label' => 'Doktor (S3)',
	    ],
	];
	
	$jobs = [
	    (object) [
	        'value' => 'Pelajar/Mahasiswa',
	        'label' => 'Pelajar/Mahasiswa',
	    ],
	    (object) [
	        'value' => 'PNS',
	        'label' => 'PNS',
	    ],
	    (object) [
	        'value' => 'TNI',
	        'label' => 'TNI',
	    ],
	    (object) [
	        'value' => 'Polisi',
	        'label' => 'Polisi',
	    ],
	    (object) [
	        'value' => 'Swasta',
	        'label' => 'Swasta',
	    ],
	    (object) [
	        'value' => 'Wirausaha',
	        'label' => 'Wirausaha',
	    ],
	    (object) [
	        'value' => 'Lainnya',
	        'label' => 'Lainnya',
	    ],
	];
	$domiciles = [
	    (object) [
	        'value' => 'Garut',
	        'label' => 'Garut',
	    ],
	    (object) [
	        'value' => 'LuarGarut',
	        'label' => 'LuarGarut',
	    ],
	];
@endphp
@extends('layouts.public')
@section('title', 'Kuesioner')
@section('content')
	<section class="bg-white dark:bg-gray-900">
		
		<div class="mx-auto flex flex-col space-y-5 max-w-screen-lg px-4 py-8">
			@if ($step == 1)
				<x-form.personal-info :genders="$genders" :educations="$educations" :jobs="$jobs" :total-kuesioner="$totalKuesioner" :villages="$villages" :domiciles="$domiciles"/>
			@elseif ($step == 2)
				<x-form.kuesioner :previous="$previous" :step="$step" :question="$question" :total-kuesioner="$totalKuesioner" :next="$next" :kuesioner="$kuesioner" :data="$data" />
			@elseif ($step == 3)
				<x-form.confirmation :kuesioner="$kuesioner" :data="$data" :step="$step" />
			@endif
		</div>
	</section>
@endsection
