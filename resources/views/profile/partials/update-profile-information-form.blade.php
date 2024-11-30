<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Profile Image') }}
            </label>
            <div class="mt-2 flex items-center space-x-6">
                <div class="flex-shrink-0">
                    @if(auth()->user()->profile_image)
                        <img src="{{ Storage::url(auth()->user()->profile_image) }}" 
                             alt="{{ auth()->user()->name }}" 
                             class="h-16 w-16 object-cover rounded-full">
                    @else
                        <div class="h-16 w-16 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-xl font-medium text-white">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <input type="file" 
                           name="profile_image" 
                           id="profile_image"
                           accept="image/*"
                           class="hidden">
                    <label for="profile_image" 
                           class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Change Image') }}
                    </label>
                    @if(auth()->user()->profile_image)
                        <button type="button" 
                                onclick="document.getElementById('remove_image').value = '1'"
                                class="ml-2 text-sm text-red-600 hover:text-red-800">
                            {{ __('Remove') }}
                        </button>
                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                    @endif
                </div>
            </div>
            @error('profile_image')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                {{ __('Name') }}
            </label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="{{ old('name', $user->name) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                {{ __('Email') }}
            </label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', $user->email) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                required
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        {{ __('Your email address is unverified.') }}
                        
                        <button form="send-verification" class="text-sm text-blue-600 hover:text-blue-800 underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
