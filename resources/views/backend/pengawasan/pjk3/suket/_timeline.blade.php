@php
    $admin = (bool) ($admin ?? false);
    $compact = (bool) ($compact ?? true);
    $data = $suket->dataTimeline($admin);
    $steps = $data['steps'];
    $current = (int) $data['current'];
    $selesaiSemua = (bool) $data['selesai'];
    $jumlah = max(count($steps), 1);
    $posisiIsi = $selesaiSemua ? ($jumlah - 1) : $current;
    $lebarIsi = $jumlah <= 1 ? 0 : round(($posisiIsi / ($jumlah - 1)) * 100, 2);
@endphp
<div class="suket-tl {{ $compact ? 'suket-tl--sm' : 'suket-tl--lg' }}">
    <div class="suket-tl__bar" aria-hidden="true">
        <div class="suket-tl__fill" style="width: {{ $lebarIsi }}%"></div>
    </div>
    <ol class="suket-tl__steps">
        @foreach ($steps as $i => $step)
            @php
                $state = 'is-todo';
                if ($selesaiSemua || $i < $current) {
                    $state = 'is-done';
                } elseif ($i === $current) {
                    $state = 'is-current';
                }
                $caption = $compact ? $step['label'] : $step['label_full'];
            @endphp
            <li class="suket-tl__step {{ $state }}"@if ($admin) title="{{ $step['hint'] }}"@endif>
                <span class="suket-tl__dot"></span>
                <span class="suket-tl__label">{{ $caption }}</span>
                @if ($admin && ! $compact)
                    <span class="suket-tl__hint">{{ $step['hint'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</div>
