@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div id="friend-requests-card" class="card text-white bg-dark mb-3" style="display: none">
                    <div class="card-header">
                        {{ __('Friend Requests') }}
                    </div>
                    <div class="card-body">
                        <div id="friend-requests-container" data-url="{{ route('users/list') }}" data-item-url="{{ url('users/show') }}/">
                        </div>
                    </div>
                </div>
                <div class="card text-white bg-dark">
                    <div class="card-header">
                        {{ __('Friends') }}
                        <form id="search-form" class="form-inline float-right">
                            <input id="search" name="search" value="{{ request('search', '') }}" class="form-control mr-sm-2" type="search"  placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">{{ __('Search') }}</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div id="friends-container" data-url="{{ route('users/list') }}" data-item-url="{{ url('users/show') }}/">
                        </div>
                        <div class="d-flex justify-content-center">
                            <div id="friends-container-loader" class="spinner-border" role="status">
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
            const friendRequestsCard = document.getElementById('friend-requests-card');
            const search = document.getElementById('search');

            const friendRequestsList = new UsersList('friend-requests-container', relationshipStatuses);
            friendRequestsList.init({only_incoming_requests: 1})
                .then((data) => {
                    if (data.length) {
                        friendRequestsCard.style.display = '';
                    }
                });

            const friendsList = new UsersList('friends-container', relationshipStatuses);
            friendsList.init({
                only_friends: 1,
                search: search.value
            });

            /**
             * Recount friend requests
             */
            function recountFriendRequests() {
                const count = document.querySelectorAll('#friend-requests-container .item-row').length;
                const friendRequestsCount = document.querySelector('.incoming-friend-requests-count');
                if (count) {
                    friendRequestsCount.innerText = count;
                } else {
                    friendRequestsCard.style.display = 'none';
                    friendRequestsCount.style.display = 'none';
                }
            }

            $(friendRequestsList.container)
                .on('click', '.approve-friend', function() {
                    const userId = this.dataset.id;
                    friendsList.sendFriendAction(routes.friends_approve, {user_id: userId})
                        .then(() => {
                            $(this).closest('.item-row').remove();
                            recountFriendRequests();
                            friendsList.init({
                                only_friends: 1,
                                search: search.value
                            });
                        });
                })
                .on('click', '.reject-friend', function() {
                    const userId = this.dataset.id;
                    friendsList.sendFriendAction(routes.friends_reject, {user_id: userId})
                        .then(() => {
                            $(this).closest('.item-row').remove();
                            recountFriendRequests();
                        });
                });

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                friendsList.init({
                    only_friends: 1,
                    search: search.value
                });
            });

            $(friendsList.container)
                .on('click', '.delete-friend', function() {
                    const userId = this.dataset.id;
                    friendsList.sendFriendAction(routes.friends_delete, {user_id: userId})
                        .then(() => {
                            $(this).closest('.item-row').remove();
                        });
                });
        });
    </script>
@endsection
