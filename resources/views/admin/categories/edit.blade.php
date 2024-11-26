@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Category: {{ $category->name }}</h1>
                <p class="mt-2 text-sm text-gray-700">
                    @if($category->posts_count > 0)
                        This category contains {{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}.
                    @else
                        This category has no posts yet.
                    @endif
                </p>
            </div>
            <div class="flex">
                <a href="{{ $category->url }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Category
                </a>
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
                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50" 
                                       readonly>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">URL-friendly version of the name. This is auto-generated.</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Color --}}
                        <div class="sm:col-span-2">
                            <label for="color" class="block text-sm font-medium text-gray-700">
                                Color
                            </label>
                            <div class="mt-1">
                                <input type="color" 
                                       name="color" 
                                       id="color" 
                                       value="{{ old('color', $category->color) }}"
                                       class="h-9 p-0 block w-full border-gray-300 rounded-md">
                            </div>
                            @error('color')
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
                                          rows="3">{{ old('description', $category->description) }}</textarea>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Brief description of the category.</p>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Icon --}}
                        <div class="sm:col-span-4">
                            <label for="icon" class="block text-sm font-medium text-gray-700">
                                Icon
                            </label>
                            <div class="mt-1">
                                <select name="icon" 
                                        id="icon" 
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Select an icon</option>
                                    @foreach($icons ?? [] as $value => $label)
                                        <option value="{{ $value }}" {{ old('icon', $category->icon) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('icon')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
                        <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Category Image</label>
                            <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <div class="flex flex-col items-center">
                                        <img id="imagePreview" 
                                             src="{{ $category->image ? Storage::disk('public')->url($category->image) : '' }}" 
                                             alt="{{ $category->name }}" 
                                             class="h-32 w-32 object-cover rounded-lg mb-4 {{ $category->image ? '' : 'hidden' }}">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 {{ $category->image ? 'hidden' : '' }}" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload a file</span>
                                                <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, WEBP up to 2MB</p>
                                    </div>
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if(session('image_error'))
                                <p class="mt-2 text-sm text-red-600">{{ session('image_error') }}</p>
                            @endif
                        </div>

                        {{-- Featured Toggle --}}
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
                                    <label for="is_featured" class="font-medium text-gray-700">Feature this category</label>
                                    <p class="text-gray-500">Featured categories are highlighted on the blog homepage.</p>
                                </div>
                            </div>
                            @error('is_featured')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SEO Section --}}
                        <div class="sm:col-span-6">
                            <div class="bg-gray-50 px-4 py-5 sm:rounded-lg sm:p-6">
                                <div class="md:grid md:grid-cols-3 md:gap-6">
                                    <div class="md:col-span-1">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900">SEO</h3>
                                        <p class="mt-1 text-sm text-gray-500">Search engine optimization settings.</p>
                                    </div>
                                    <div class="mt-5 space-y-6 md:mt-0 md:col-span-2">
                                        <div>
                                            <label for="meta_title" class="block text-sm font-medium text-gray-700">
                                                Meta Title
                                            </label>
                                            <div class="mt-1">
                                                <input type="text" 
                                                       name="meta_title" 
                                                       id="meta_title" 
                                                       value="{{ old('meta_title', $category->meta_title) }}"
                                                       maxlength="60"
                                                       class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Maximum 60 characters.</p>
                                            @error('meta_title')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="meta_description" class="block text-sm font-medium text-gray-700">
                                                Meta Description
                                            </label>
                                            <div class="mt-1">
                                                <textarea name="meta_description" 
                                                          id="meta_description" 
                                                          rows="3"
                                                          maxlength="160"
                                                          class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('meta_description', $category->meta_description) }}</textarea>
                                            </div>
                                            <p class="mt-2 text-sm text-gray-500">Maximum 160 characters.</p>
                                            @error('meta_description')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

    // Initialize character counts
    updateCharCount(metaTitleInput, 60);
    updateCharCount(metaDescInput, 160);

    // Auto-generate meta title from name if empty
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-');
        document.getElementById('slug').value = slug;
        
        if (!metaTitleInput.value) {
            metaTitleInput.value = this.value;
            updateCharCount(metaTitleInput, 60);
        }
    });

    // Loading state elements
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50';
    loadingOverlay.innerHTML = `
        <div class="bg-white rounded-lg px-4 py-3 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <p class="text-gray-700">Processing image...</p>
            </div>
        </div>
    `;

    // Error toast notification
    function showError(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-lg z-50';
        toast.innerHTML = `
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">${message}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-auto pl-3">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // Image validation function
    async function validateImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.src = URL.createObjectURL(file);
            
            img.onload = function() {
                URL.revokeObjectURL(img.src);
                const width = img.naturalWidth;
                const height = img.naturalHeight;
                
                if (width < MIN_WIDTH || height < MIN_HEIGHT) {
                    reject(`Image must be at least ${MIN_WIDTH}x${MIN_HEIGHT}px`);
                } else if (width > MAX_WIDTH || height > MAX_HEIGHT) {
                    reject(`Image must be no larger than ${MAX_WIDTH}x${MAX_HEIGHT}px`);
                } else {
                    resolve();
                }
            };
            
            img.onerror = () => reject('Invalid image file');
        });
    }

    // Handle file processing
    async function processFile(file) {
        if (!file) return;

        try {
            // Show loading state
            document.body.appendChild(loadingOverlay);

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                throw new Error('File is too large. Maximum size is 2MB.');
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                throw new Error('Please upload an image file.');
            }

            // Validate dimensions
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
            // Remove loading state
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
</script>
@endpush