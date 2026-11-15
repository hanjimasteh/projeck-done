<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-lg-4">
        <div class="wizard-steps" role="list">
            @foreach ($steps as $step)
                @php
                    $isActive = $step['key'] === $currentStep;
                @endphp

                <a href="{{ route($step['route']) }}"
                   role="listitem"
                   class="wizard-step {{ $isActive ? 'is-active' : '' }} {{ $step['done'] ? 'is-done' : '' }}">
                    <span class="wizard-step-number">
                        @if ($step['done'])
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </span>
                    <span class="wizard-step-label">{{ $step['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
