@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card text-white bg-dark text-center">
                    <img class="card-img-top" src="{{ asset('images/default-profile-picture.png') }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $user->name }} {{ $user->surname }}</h5>
                        @if( !$user->isCurrent)
                            <div id="friend-button-container" data-user-id="{{ $user->id }}">
                                @if($user->send_status === $relationshipStatuses['approved'] || $user->receive_status === $relationshipStatuses['approved'])
                                    <button type="button" class="delete-friend btn btn-outline-light">{{ __('Remove Friend') }}</button>
                                @elseif($user->receive_status === $relationshipStatuses['pending'])
                                    <button type="button" class="btn btn-outline-info" disabled>{{ __('Pending') }}</button>
                                @elseif($user->send_status === $relationshipStatuses['pending'])
                                    <button type="button" class="reject-friend btn btn-outline-danger">{{ __('Reject') }}</button>
                                    <button type="button" class="approve-friend btn btn-outline-success ml-2">{{ __('Approve') }}</button>
                                @elseif($user->receive_status === $relationshipStatuses['rejected'])
                                    <button type="button" class="btn btn-outline-warning" disabled>{{ __('Rejected') }}</button>
                                    <button type="button" class="add-friend btn btn-outline-primary ml-2">{{ __('Add Friend') }}</button>
                                @else
                                    <button type="button" class="add-friend btn btn-outline-primary">{{ __('Add Friend') }}</button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card text-white bg-dark">
                    <div class="card-header">{{ __('Posts') }}</div>
                    <div class="card-body"></div>
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
    <script>
        $(document).ready(function() {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;
            const routes = JSON.parse(document.getElementById('routes').innerHTML);
            const friendButtonContainer = document.getElementById('friend-button-container');
            const userId = friendButtonContainer.dataset.userId;

            /**
             * Send request for update friend status.
             * @param {string} url
             * @param {string} button
             */
            function sendFriendAction(url, button) {
                axios.post(url, {user_id: userId})
                    .then(() => {
                        friendButtonContainer.innerHTML = button;
                    })
                    .catch((error) => {
                        const data = error.response.data;
                        console.error(data);
                        if (data.message) {
                            if (confirm(data.message + '\nPlease refresh the page.')) {
                                location.reload();
                            }
                        }
                    });
            }

            $(document)
                .on('click', '.add-friend', function() {
                    sendFriendAction(routes.friends_add, '<button type="button" class="btn btn-outline-info" disabled>Pending</button>');
                })
                .on('click', '.delete-friend', function() {
                    sendFriendAction(routes.friends_delete, '<button type="button" class="add-friend btn btn-outline-primary">Add Friend</button>');
                })
                .on('click', '.approve-friend', function() {
                    sendFriendAction(routes.friends_approve, '<button type="button" class="delete-friend btn btn-outline-light">Remove Friend</button>');
                })
                .on('click', '.reject-friend', function() {
                    sendFriendAction(routes.friends_reject, '<button type="button" class="add-friend btn btn-outline-primary">Add Friend</button>');
                });
        });
    </script>
@endsection
