@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-8 divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <div class="space-y-8 divide-y divide-gray-200">
                <div class="pt-8">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        {{-- Name --}}
                        <div class="sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Name
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $category->name) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="sm:col-span-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700">
                                Slug
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="slug" 
                                       id="slug" 
                                       value="{{ old('slug', $category->slug) }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                The URL-friendly version of the name. Must be unique.
                            </p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea id="description" 
                                          name="description" 
                                          rows="3" 
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                A brief description of the category.
                            </p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image --}}
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">
                                Category Image
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md relative" id="dropZone">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 2MB
                                    </p>
                                </div>
                                @if($category->image)
                                    <div class="absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-90" id="currentImage">
                                        <div class="text-center p-4">
                                            <img src="{{ Storage::disk('public')->url($category->image) }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="mx-auto h-32 w-32 object-cover rounded mb-4">
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                    onclick="document.getElementById('currentImage').remove()">
                                                Change Image
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Is Featured --}}
                        <div class="sm:col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                           name="is_featured" 
                                           id="is_featured" 
                                           value="1"
                                           {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_featured" class="font-medium text-gray-700">Featured Category</label>
                                    <p class="text-gray-500">Featured categories are displayed prominently on the home page.</p>
                                </div>
                            </div>
                            @error('is_featured')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}"
                       class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/ohrfrapuhu20w9tbmhnitg6kvecj2vouenborprjzguexqop/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // TinyMCE initialization
    tinymce.init({
        selector: 'textarea#description',
        height: 300,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });

    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', (e) => {
        if (!slugInput.value || slugInput.value === slugify(e.target.defaultValue)) {
            slugInput.value = slugify(e.target.value);
        }
    });

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('image');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropZone.classList.add('border-blue-300', 'ring-2', 'ring-blue-500', 'ring-opacity-50');
    }

    function unhighlight(e) {
        dropZone.classList.remove('border-blue-300', 'ring-2', 'ring-blue-500', 'ring-opacity-50');
    }

    dropZone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const file = dt.files[0];
        
        fileInput.files = dt.files;
        processFile(file);
    }

    fileInput.addEventListener('change', function(e) {
        processFile(this.files[0]);
    });

    // Constants for image validation
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const MIN_WIDTH = 200;
    const MIN_HEIGHT = 200;

    async function processFile(file) {
        if (!file) return;

        try {
            // Show loading state
            const currentImage = document.getElementById('currentImage');
            if (currentImage) {
                currentImage.remove();
            }

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                throw new Error('File size exceeds 2MB limit');
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                throw new Error('Please upload an image file');
            }

            // Create a preview
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                const img = new Image();
                img.src = reader.result;
                img.onload = function() {
                    // Validate dimensions
                    if (img.width < MIN_WIDTH || img.height < MIN_HEIGHT) {
                        alert(`Image dimensions must be at least ${MIN_WIDTH}x${MIN_HEIGHT}px`);
                        return;
                    }

                    // Show preview
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'absolute inset-0 flex items-center justify-center bg-gray-100 bg-opacity-90';
                    previewContainer.id = 'currentImage';
                    
                    const previewContent = document.createElement('div');
                    previewContent.className = 'text-center p-4';
                    
                    const previewImage = document.createElement('img');
                    previewImage.src = reader.result;
                    previewImage.alt = 'Preview';
                    previewImage.className = 'mx-auto h-32 w-32 object-cover rounded mb-4';
                    
                    const changeButton = document.createElement('button');
                    changeButton.type = 'button';
                    changeButton.className = 'inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
                    changeButton.textContent = 'Change Image';
                    changeButton.onclick = function() {
                        previewContainer.remove();
                    };
                    
                    previewContent.appendChild(previewImage);
                    previewContent.appendChild(changeButton);
                    previewContainer.appendChild(previewContent);
                    dropZone.appendChild(previewContainer);
                };
            };
        } catch (error) {
            alert(error.message);
        }
    }
</script>
@endpush