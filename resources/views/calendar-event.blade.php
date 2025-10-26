<div>
  <div class="flex flex-wrap gap-2 bg-[#e1e7ff] justify-center items-center p-4 ">
    <button wire:click="goToPreviousMonth" type="button"
      class="inline-flex items-center justify-center">
      <svg class="w-8 h-8" fill="none" viewBox="0 0 20 20">
        <path d="M12 15l-5-5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <span class="inline-flex items-center px-4 py-2 text-3xl font-bold text-gray-800 dark:text-white">
      {{ $currentMonth->format('F Y') }}
    </span>

    <button wire:click="goToNextMonth" type="button"
      class="inline-flex items-center justify-center">
      <svg class="w-8 h-8" fill="none" viewBox="0 0 20 20">
        <path d="M8 5l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    
    <button wire:click="goToCurrentMonth" type="button"
      class="inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
      Current Month
    </button>
  </div>
  <x-modal name="simpleModal" wire:model='showModal'>
      <x-card title="Seminar Details" class="w-full flex flex-col justify-center">
        <div class="flex flex-col justify-center px-12">
          <h1>
            <strong>Title:</strong> {{ $title }}
          </h1>
          <h1>
            <strong>Date:</strong> {{ $date }}
          </h1>
          <h1>
          <strong>Time:</strong> 
            {{ \Carbon\Carbon::parse($startTime)->format('g:i A') }} - 
            {{ \Carbon\Carbon::parse($endTime)->format('g:i A') }}
          </h1>
        </div>
        <hr class="border-t border-gray-300 w-[85%] mx-auto" />
        <div class="px-12">
          <h1 class=" text-gray-700 my-4 text-start">
            <strong>Seminar Attendees</strong>
          </h1>

          <div class="flex justify-start">
            @foreach ($userNames as $user)
              <div class="hs-tooltip inline-block">
                <img
                  class="hs-tooltip-toggle relative inline-block w-11 h-11 rounded-full ring-2 ring-white hover:z-10 dark:ring-neutral-900 object-cover"
                  src="{{ ($user['avatar'] ?? false) ? asset('storage/' . $user['avatar']) : asset('images/ilend-logo.png') }}"
                  alt="Avatar"
                >
                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 inline-block absolute invisible z-20 py-1.5 px-2.5 bg-gray-900 text-xs text-white rounded-lg dark:bg-neutral-700" role="tooltip">
                  {{ $user['name'] }}
                </span>
              </div>
            @endforeach
          </div>
          <ul class="mt-2">
            @foreach ($userNames as $user)
              <li>{{ $user['name'] }}</li>
            @endforeach
          </ul>
        </div>
        <x-slot name="footer" class="flex justify-end gap-x-4">
          <x-button flat label="Close" x-on:click="close" />
        </x-slot>
      </x-card>
  </x-modal>
</div>