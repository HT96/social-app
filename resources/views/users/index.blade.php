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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usersContainer = document.getElementById('users-container');
            const search = document.getElementById('search');

            /**
             * Render the each of users.
             * @param {Object} user
             * @return {string}
             */
            function renderUser(user) {
                return `<a href="${usersContainer.dataset.itemUrl}/${user.id}">` +
                    `<div class="alert alert-light" role="alert">` +
                    `<h5 class="alert-heading">${user.name} ${user.surname}</h5>` +
                    `</div>` +
                    `</a>`;
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
                    console.error(error);
                });
        });
    </script>
@endsection
