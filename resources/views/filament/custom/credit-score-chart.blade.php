@php
    $tier = match (true) {
        $creditScore >= 81 => 'Excellent',
        $creditScore >= 61 => 'Good',
        $creditScore >= 41 => 'Fair',
        default => 'Poor',
    };

    $scoreColorForTooltip = $creditScore >= 81
        ? '#4caf50'
        : ($creditScore >= 61
            ? '#2196f3'
            : ($creditScore >= 41
                ? '#ff9800'
                : '#f44336'));
@endphp

<x-filament::card>
    <div
        x-data="{
            creditScore: 0,
            targetScore: {{ $creditScore ?? 0 }},
            tier: '{{ $tier }}',
            scoreColorForTooltip: '{{ $scoreColorForTooltip }}',
            showTooltip: false,
            get angle() {
                return this.creditScore * 3.6;
            },
            get color() {
                return this.targetScore >= 81
                    ? '#4caf50'
                    : (this.targetScore >= 61
                        ? '#2196f3'
                        : (this.targetScore >= 41
                            ? '#ff9800'
                            : '#f44336'));
            },
            animate() {
                let step = 0;
                const interval = setInterval(() => {
                    if (step >= this.targetScore) {
                        this.creditScore = this.targetScore;
                        return clearInterval(interval);
                    }
                    step++;
                    this.creditScore = step;
                }, 10);
            }
        }"
        x-init="animate()"
        style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 1.5rem;"
    >
        <div
            style="position: relative; width: 256px; height: 256px;"
            @mouseenter="showTooltip = true"
            @mouseleave="showTooltip = false"
        >
            <div
                x-bind:style="`
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    background: conic-gradient(${color} ${angle}deg, #e0e0e0 0deg);
                    transition: background 0.3s ease;
                `"
            ></div>

            <div
                style="
                    position: absolute;
                    top: 2rem;
                    left: 2rem;
                    right: 2rem;
                    bottom: 2rem;
                    background: white;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                    font-weight: bold;
                "
                x-text="creditScore + '%'">
            </div>

            <div
                x-show="showTooltip"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                style="
                    position: absolute;
                    top: -65px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(0, 0, 0, 0.85);
                    color: white;
                    padding: 8px 12px;
                    border-radius: 8px;
                    font-size: 0.875rem;
                    white-space: nowrap;
                    pointer-events: none;
                    z-index: 10;
                    display: flex;
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 4px;
                "
            >
                <div style="
                    position: absolute;
                    bottom: -8px;
                    left: 50%;
                    transform: translateX(-50%) rotate(45deg);
                    width: 16px;
                    height: 16px;
                    background: rgba(0, 0, 0, 0.85);
                    border-radius: 2px;
                    z-index: -1;
                "></div>

                <div style="font-weight: bold; font-size: 1rem;">
                    <span x-text="tier"></span>
                </div>

                <div style="display: flex; align-items: center; gap: 6px;">
                    <div
                        x-bind:style="`width: 14px; height: 14px; background-color: ${scoreColorForTooltip}; border: 1px solid rgba(255,255,255,0.5);`"
                    ></div>
                    <span>Credit Score: <span x-text="creditScore"></span></span>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: black;">
                Credit Score: {{ $creditScore }}/100
            </h3>
        </div>
    </div>
</x-filament::card>
