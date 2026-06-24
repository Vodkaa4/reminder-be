<x-filament-panels::page.simple>
    <div class="fi-custom-login-wrapper">
        @include('filament.pages.auth.styles')
    
        <div class="login-card-custom">
            <!-- Left Side: Login Form -->
            <div class="left-col">
                <div class="mb-8 text-center lg:text-left">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                        {{ __('Welcome back') }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Please sign in to your account
                    </p>
                </div>
    
                <!-- Login Form with Livewire Handling -->
                <form wire:submit.prevent="authenticate" class="grid gap-y-8">
                    {{ $this->form }}
    
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </form>
    
                <p class="mt-8 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} PT Eksonindo MPI. All rights reserved.
                </p>
            </div>
    
            <!-- Right Side: Image -->
            <div class="right-col">
                <img src="{{ asset('images/login.png') }}" alt="Login Illustration">
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
