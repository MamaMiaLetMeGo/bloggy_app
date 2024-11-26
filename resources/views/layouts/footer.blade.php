<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    About
                </h3>
                <p class="mt-4 text-base text-gray-500">
                    Writing feels good.  
                </p>
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    Navigation
                </h3>
                <ul class="mt-4 space-y-4">
                    <li>
                        <a href="{{ route('home') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Categories
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('admin.posts.index') }}" class="text-base text-gray-500 hover:text-gray-900">
                                Admin Dashboard
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- Connect -->
            <div>
                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
                    Connect
                </h3>
                <ul class="mt-4 space-y-4">
                    <li>
                        <a href="{{ route('contact.show') }}" class="text-base text-gray-500 hover:text-gray-900">
                            Contact Me
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                            RSS Feed
                        </a>
                    </li>
                </ul>
            </div> 
        </div>

        <div class="mt-8 border-t border-gray-200 pt-8">
            <p class="text-base text-gray-400 text-center">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>