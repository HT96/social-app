@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-white bg-dark">
                    <div class="card-header">
                        {{ __('Users') }}
                        <form id="search-form" class="form-inline float-right">
                            <input id="search" name="search" value="{{ request('search', '') }}" class="form-control mr-sm-2" type="search"  placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">{{ __('Search') }}</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="users-container" data-url="{{ route('users/list') }}">
                        </div>
                        <div class="d-flex justify-content-center">
                            <div id="users-container-loader" class="spinner-border" role="status">
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
    <script id="routes" type="application/json">
        {!! json_encode([
            'friends_add' => route('friends/add'),
            'friends_delete' => route('friends/delete'),
            'friends_approve' => route('friends/approve'),
            'friends_reject' => route('friends/reject'),
        ]) !!}
    </script>
    <script id="relationship-statuses" type="application/json">{!! json_encode($relationshipStatuses) !!}</script>
    <script src="{{ asset('js/components/users-list.js') }}"></script>
    <script>
        $(document).ready(function() {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
            const routes = JSON.parse(document.getElementById('routes').innerHTML);
            const relationshipStatuses = JSON.parse(document.getElementById('relationship-statuses').innerHTML);
            const search = document.getElementById('search');

            const usersList = new UsersList('users-container', relationshipStatuses);
            usersList.init({
                search: search.value
            });

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                usersList.init({
                    search: search.value
                });
            });

            /**
             * Handling a friend button click.
             * @param {HTMLElement} button
             * @param {string} url
             * @param {number|null} status
             */
            function handleFriendButtonClick(button, url, status = null) {
                const userId = button.dataset.id;
                usersList.sendFriendAction(url, {user_id: userId})
                    .then(() => {
                        button.parentNode.innerHTML = usersList.renderFriendButton({
                            id: userId,
                            receive_status: status
                        });
                    });
            }

            $(usersList.container)
                .on('click', '.add-friend', function() {
                    handleFriendButtonClick(this, routes.friends_add, relationshipStatuses.pending);
                })
                .on('click', '.delete-friend', function() {
                    handleFriendButtonClick(this, routes.friends_delete);
                })
                .on('click', '.approve-friend', function() {
                    handleFriendButtonClick(this, routes.friends_approve, relationshipStatuses.approved);
                })
                .on('click', '.reject-friend', function() {
                    handleFriendButtonClick(this, routes.friends_reject, relationshipStatuses.rejected);
                });
        });
    </script>
@endsection
