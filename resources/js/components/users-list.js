/** Class representing list of users. */
class UsersList {
    /**
     * Create a instance.
     * @param {string} containerId
     * @param {Object} relationshipStatuses
     * @throws {Error}
     */
    constructor(containerId, relationshipStatuses) {
        this.container = document.getElementById(containerId);
        if ( !this.container) {
            throw Error(`#${containerId} element not found`);
        }
        this.loader = document.getElementById(containerId + '-loader');
        this.relationshipStatuses = relationshipStatuses;
    }

    /**
     * Get users list.
     * @param {Object} params
     * @return {Promise}
     */
    init(params) {
        if (this.loader) {
            this.loader.style.display = '';
        }
        // TODO load by chunk when scrolling
        return axios.get(this.container.dataset.url, {params: params})
            .then((response) => {
                this.renderUsersContainer(response.data);
                if (this.loader) {
                    this.loader.style.display = 'none';
                }
                return response.data;
            })
            .catch((error) => {
                console.error(error.response.data);
            });
    }

    /**
     * Render the users container.
     * @param {Array.<Object>} users
     */
    renderUsersContainer(users) {
        let res = '';
        for (let user of users) {
            res += this.renderUser(user);
        }
        this.container.innerHTML = res;
    }

    /**
     * Render the each of users.
     * @param {Object} user
     * @return {string}
     */
    renderUser(user) {
        return `<div class="item-row alert alert-light" role="alert">
                    <div class="row">
                        <div class="col-md-8 col-sm-8">
                            <h5 class="alert-heading">
                                <a href="${this.container.dataset.itemUrl + user.id}">${user.name} ${user.surname}</a>
                            </h5>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            ${this.renderFriendButton(user)}
                        </div>
                    </div>
                </div>`;
    }

    /**
     * Render the friend button for each of users.
     * @param {Object} user
     * @return {string}
     */
    renderFriendButton(user) {
        if (user.receive_status === this.relationshipStatuses.pending) {
            return `<button type="button" class="btn btn-outline-dark float-right" disabled>Pending</button>`;
        }
        if (user.send_status === this.relationshipStatuses.pending) {
            return `<button type="button" class="approve-friend btn btn-outline-success ml-2 float-right" data-id="${user.id}">Approve</button>
                    <button type="button" class="reject-friend btn btn-outline-danger float-right" data-id="${user.id}">Reject</button>`;
        } else if (user.send_status === this.relationshipStatuses.approved || user.receive_status === this.relationshipStatuses.approved) {
            return `<button type="button" class="delete-friend btn btn-outline-secondary float-right" data-id="${user.id}">Remove Friend</button>`;
        }
        return `<button type="button" class="add-friend btn btn-outline-primary float-right" data-id="${user.id}">Add Friend</button>`;
    }

    /**
     * Send request for update friend status.
     * @param {string} url
     * @param {Object} params
     * @return {Promise}
     */
    sendFriendAction(url, params) {
        return axios.post(url, params)
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
}
