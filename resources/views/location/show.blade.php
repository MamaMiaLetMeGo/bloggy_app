@extends('layouts.app')

@section('title', 'CharlesGendron.com - Posts from ' . $location)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        {{-- Subscribe Section - Moved to top --}}
        <div class="p-6 bg-gray-50 border-b"> {{-- Changed border-t to border-b --}}
            <h2 class="text-xl font-semibold mb-4">Get Movement Notifications</h2>
            <form action="{{ route('location.subscribe') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Subscribe to Updates
                </button>
            </form>
        </div>

        {{-- Map Section --}}
        <div class="p-6">
            <h1 class="text-3xl font-bold mb-4">Live Location Tracker</h1>
            
            <div class="h-[600px] md:h-[800px]">
                <iframe 
                    src="https://share.garmin.com/mistie" 
                    frameborder="0" 
                    class="w-full h-full"
                    allowfullscreen
                    style="min-height: 600px;"
                ></iframe>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    Echo.channel('location-updates')
        .listen('LocationUpdated', (e) => {
            console.log('Location updated:', e.locationData);
            // Update your UI here
        });
</script>
@endpush
@endsection