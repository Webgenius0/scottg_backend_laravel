@extends('web.backend.app')

@section('title', 'Blog Page')

@push('style')
    <style>
        .ck-editor__editable_inline {
            min-height: 200px;
        }

        .dropify-wrapper {
            background-color: #f5f8fa;
            /* Light background */
            border: 2px dashed #007bff;
            /* Custom border */
            border-radius: 15px;
            /* Rounded corners */
            transition: all 0.3s ease;
        }

        .dropify-wrapper:hover {
            border-color: #0056b3;
            /* Darker border on hover */
            background-color: #e6f7ff;
            /* Change background on hover */
        }

        .dropify-wrapper .dropify-message {
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .dropify-wrapper .dropify-message p {
            font-size: 18px;
            /* Larger font */
            margin: 10px 0;
        }

        .dropify-wrapper .dropify-preview .dropify-render img {
            max-width: 100%;
            /* Responsive image */
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Blog</h4>
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="{{ route('admin.blogs.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            name="title" id="title" placeholder="Enter title here"
                                            value="{{ old('title') }}">
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="blog_category" class="form-label">Blog Category</label>
                                        <input type="text" class="form-control @error('blog_category') is-invalid @enderror"
                                            name="blog_category" id="blog_category" placeholder="Enter blog category here"
                                            value="{{ old('blog_category') }}">
                                        @error('blog_category')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="content" class="form-label">Content</label>
                                        <textarea class="form-control @error('content') is-invalid @enderror" name="content" id="content">{{ old('content') }}</textarea>
                                        @error('content')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="image" class="form-label">Image</label>
                                        <input type="file"
                                            class="dropify form-control @error('image') is-invalid @enderror" name="image"
                                            id="image" data-default-file="{{ old('image') }}">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /Account -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#content'), {
                    height: '500px'
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
@endpush
