
<div class="flex basis-full flex-col items-center space-y-12 rounded-lg border border-gray-200 bg-white px-5 py-20 text-center shadow dark:border-gray-700 dark:bg-gray-800">
	<div class="flex w-full justify-center">
		<x-link.button :href="$previous" icon="chevron-left" :disabled="$previous === '#' ? true : false" />
		<x-link.button href="" :text="$question . ' / ' . $totalKuesioner" class="px-4" />
		<x-link.button :href="$next" icon="chevron-right" :disabled="$next === '#' ? true : false" />
	</div>
	<h5 class="max-w-3xl text-2xl font-medium tracking-tight text-gray-900 dark:text-white">{{ $kuesioner->question }}
	</h5>
	@php
		for ($i=0; $i < $totalKuesioner; $i++) { 
      if (!isset($data['question'.$i+1])) {
        $selected[$i+1] = 0;
      } else {
        $selected[$i+1] = $data['question'.$i+1];
      }
    }
	@endphp
	<div class="flex space-x-5">
		@for ($i = 1; $i <= 4; $i++)
			<?php
			  $opacityClass = $selected[$question] == $i ? '' : 'opacity-100';
			?>
			<a href="{{ route('kuesioner', [...$data, ...['question' . $question => $i]]) }}" data-tooltip-target="rate{{ $i }}" data-tooltip-style="light" data-tooltip-placement="bottom" class="{{ $opacityClass }} transform transition duration-100 hover:scale-125 hover:opacity-100">
				<img src="{{ asset('assets/' . $i . '.svg') }}" class="h-20 w-20">
			</a>
			<div id="rate{{ $i }}" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 opacity-0 shadow-sm">
				{{ rateLabel($i) }}
				<div class="tooltip-arrow" data-popper-arrow></div>
			</div>
		@endfor
	</div>
</div>
