<x-guest-layout>
    <div class="mb-6 text-sm text-gray-600 dark:text-gray-400 text-center">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-emerald-custom dark:text-emerald-custom bg-emerald-custom/10 dark:bg-emerald-custom/20 px-4 py-3 rounded-lg text-center">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col items-center space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-gray-700 dark:text-gray-300 hover:text-emerald-custom dark:hover:text-emerald-custom transition-colors">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
