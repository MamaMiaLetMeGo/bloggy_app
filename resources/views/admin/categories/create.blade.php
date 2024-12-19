@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Category</h1>
            </div>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-8 divide-y divide-gray-200">
            @csrf

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
                                       value="{{ old('name') }}"
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
                                       value="{{ old('slug') }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                                       required>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">URL-friendly version of the name. Use lowercase letters, numbers, and hyphens only.</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description with TinyMCE --}}
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea name="description" 
                                          id="description" 
                                          rows="3">{{ old('description') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Brief description of the category.</p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
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
                                        <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <span>Upload a file</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 2MB
                                    </p>
                                </div>
                                <img id="imagePreview" class="absolute inset-0 w-full h-full object-cover rounded-md hidden">
                            </div>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Meta Title --}}
                        <div class="sm:col-span-4">
                            <label for="meta_title" class="block text-sm font-medium text-gray-700">
                                Meta Title
                            </label>
                            <div class="mt-1">
                                <input type="text" 
                                       name="meta_title" 
                                       id="meta_title" 
                                       maxlength="60"
                                       value="{{ old('meta_title') }}"
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">60 characters remaining.</p>
                            @error('meta_title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Meta Description --}}
                        <div class="sm:col-span-6">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700">
                                Meta Description
                            </label>
                            <div class="mt-1">
                                <textarea name="meta_description" 
                                          id="meta_description" 
                                          rows="3"
                                          maxlength="160"
                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('meta_description') }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Maximum 160 characters.</p>
                            @error('meta_description')
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
                        Create Category
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
            'removeformat | image | help',
        automatic_uploads: true,
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/admin/categories/upload-image');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            xhr.setRequestHeader('X-CSRF-Token', token);
            
            xhr.upload.onprogress = (e) => {
                progress((e.loaded / e.total) * 100);
            };
            
            xhr.onload = () => {
                if (xhr.status !== 200) {
                    reject({ message: `HTTP Error: ${xhr.status}`, remove: true });
                    return;
                }
                const json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location !== 'string') {
                    reject('Invalid JSON response: ' + xhr.responseText);
                    return;
                }
                resolve(json.location);
            };
            
            xhr.onerror = () => {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }),
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });

    // Existing image upload and slug generation scripts
    // Constants for image validation
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB
    const MIN_WIDTH = 200;
    const MIN_HEIGHT = 200;
    const MAX_WIDTH = 2000;
    const MAX_HEIGHT = 2000;

    // Preview meta title and description lengths
    const metaTitleInput = document.getElementById('meta_title');
    const metaDescInput = document.getElementById('meta_description');

    function updateCharCount(input, maxLength) {
        const charCount = input.value.length;
        const remainingChars = maxLength - charCount;
        const helpText = input.nextElementSibling;
        helpText.textContent = `${remainingChars} characters remaining`;
        if (remainingChars < 10) {
            helpText.classList.add('text-yellow-600');
        } else {
            helpText.classList.remove('text-yellow-600');
        }
    }

    metaTitleInput.addEventListener('input', () => updateCharCount(metaTitleInput, 60));
    metaDescInput.addEventListener('input', () => updateCharCount(metaDescInput, 160));

    // Auto-generate meta title from name if empty
    document.getElementById('name').addEventListener('input', function() {
        if (!metaTitleInput.value) {
            metaTitleInput.value = this.value;
            updateCharCount(metaTitleInput, 60);
        }
    });

    // Image validation and preview functions
    async function validateImage(file) {
        if (file.size > MAX_FILE_SIZE) {
            throw new Error('File size must be less than 2MB');
        }

        return new Promise((resolve, reject) => {
            const img = new Image();
            img.src = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(img.src);
                if (img.width < MIN_WIDTH || img.height < MIN_HEIGHT) {
                    reject(new Error(`Image dimensions must be at least ${MIN_WIDTH}x${MIN_HEIGHT}px`));
                } else if (img.width > MAX_WIDTH || img.height > MAX_HEIGHT) {
                    reject(new Error(`Image dimensions must not exceed ${MAX_WIDTH}x${MAX_HEIGHT}px`));
                } else {
                    resolve();
                }
            };
            img.onerror = () => reject(new Error('Invalid image file'));
        });
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'mt-2 text-sm text-red-600';
        errorDiv.textContent = message;
        const dropZone = document.getElementById('dropZone');
        dropZone.parentNode.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }

    async function processFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            showError('Please upload an image file');
            return;
        }

        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center';
        loadingOverlay.innerHTML = '<div class="text-blue-600">Processing...</div>';
        document.getElementById('dropZone').appendChild(loadingOverlay);

        try {
            await validateImage(file);

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                document.querySelector('#dropZone svg').classList.add('hidden');
            };
            reader.readAsDataURL(file);

        } catch (error) {
            showError(error.message);
            const input = document.getElementById('image');
            input.value = ''; // Clear the input
        } finally {
            loadingOverlay.remove();
        }
    }

    // Image input change handler
    document.getElementById('image')?.addEventListener('change', function(e) {
        processFile(e.target.files[0]);
    });

    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight(e) {
            dropZone.classList.add('border-blue-500', 'border-2');
        }

        function unhighlight(e) {
            dropZone.classList.remove('border-blue-500', 'border-2');
        }

        dropZone.addEventListener('drop', async function(e) {
            const file = e.dataTransfer.files[0];
            if (file) {
                await processFile(file);
                if (!document.getElementById('imagePreview').classList.contains('hidden')) {
                    // Only update input if validation passed
                    const input = document.getElementById('image');
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                }
            }
        });
    }
</script>
@endpush 