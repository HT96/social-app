@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-white bg-dark">
                    <div class="card-header">{{ __('Users') }}</div>

                    <div class="card-body">
                        <div id="users-container" data-url="{{ route('users/list') }}">
                            <div class="d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
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
    <script>
        $(document).ready(function() {
            const routes = JSON.parse(document.getElementById('routes').innerHTML);
            const relationshipStatuses = JSON.parse(document.getElementById('relationship-statuses').innerHTML);
            const usersContainer = document.getElementById('users-container');
            const search = document.getElementById('search');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

            /**
             * Render the friend button each of users.
             * @param {Object} user
             * @return {string}
             */
            function renderFriendButton(user) {
                if (user.receive_status === relationshipStatuses.pending) {
                    return `<button type="button" class="btn btn-outline-dark float-right" disabled>Pending</button>`;
                }
                if (user.send_status === relationshipStatuses.pending) {
                    return `<button type="button" class="approve-friend btn btn-outline-success ml-2 float-right" data-id="${user.id}">Approve</button>
                        <button type="button" class="reject-friend btn btn-outline-danger float-right" data-id="${user.id}">Reject</button>`;
                } else if (user.send_status === relationshipStatuses.approved || user.receive_status === relationshipStatuses.approved) {
                    return `<button type="button" class="delete-friend btn btn-outline-secondary float-right" data-id="${user.id}">Remove Friend</button>`;
                }
                return `<button type="button" class="add-friend btn btn-outline-primary float-right" data-id="${user.id}">Add Friend</button>`;
            }

            /**
             * Render the each of users.
             * @param {Object} user
             * @return {string}
             */
            function renderUser(user) {
                return `<div class="alert alert-light" role="alert">
                    <div class="row">
                        <div class="col-md-8 col-sm-8">
                            <h5 class="alert-heading"><a href="#${user.id}">${user.name} ${user.surname}</a></h5>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            ${renderFriendButton(user)}
                        </div>
                    </div>
                </div>`;
            }

            /**
             * Render the users container.
             * @param {Array} users
             */
            function renderUsersContainer(users) {
                let res = '';
                for (let user of users) {
                    res += renderUser(user);
                }
                usersContainer.innerHTML = res;
            }

            axios.get(usersContainer.dataset.url, {
                params: {
                    search: search? search.value: ''
                }
            })
                .then((response) => {
                    renderUsersContainer(response.data);
                })
                .catch((error) => {
                    console.error(error.response.data);
                });

            /**
             * Handling a friend button click.
             * @param {HTMLElement} button
             * @param {string} url
             * @param {number|null} status
             */
            function handleFriendButtonClick(button, url, status = null) {
                const userId = button.dataset.id;
                axios.post(url, {
                    user_id: userId
                })
                    .then((response) => {
                        button.parentNode.innerHTML = renderFriendButton({
                            id: userId,
                            receive_status: status
                        });
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
