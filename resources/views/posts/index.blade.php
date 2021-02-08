@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-white bg-dark">
                    <div class="card-header">
                        <h5>{{ __('Posts') }}</h5>
                        <hr>
                        <form id="add-post-form" action="{{ route('posts/add') }}" method="POST">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="title">{{ __('Title') }}</label>
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="public">{{ __('Who can see post?') }}</label>
                                    <select name="public" id="public" class="form-control">
                                        <option value="">{{ __('Friends') }}</option>
                                        <option value="1">{{ __('Public') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="text" id="text" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Publish') }}</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="posts-container" data-url="{{ route('posts/list') }}" data-user-url="{{ url('users/show') }}/">
                        </div>
                        <div class="d-flex justify-content-center">
                            <div id="posts-container-loader" class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ asset('js/components/posts-list.js') }}"></script>
    <script>
        $(document).ready(function() {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

            const postsList = new PostsList('posts-container');
            postsList.init();

            const $form = $('#add-post-form');
            $form.on('submit', function(e) {
                e.preventDefault();
                axios.post($form.attr('action'), $form.serialize())
                    .then(() => {
                        postsList.init();
                        $form[0].reset();
                    })
                    .catch((error) => {
                        console.error(error.response.data);
                    });
            });

        });
    </script>
@endsection
