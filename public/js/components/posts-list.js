/** Class representing list of posts. */
class PostsList {
    /**
     * Create a instance.
     * @param {string} containerId
     * @throws {Error}
     */
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if ( !this.container) {
            throw Error(`#${containerId} element not found`);
        }
        this.loader = document.getElementById(containerId + '-loader');
    }

    /**
     * Get posts list.
     * @param {Object} params
     * @return {Promise}
     */
    init(params = {}) {
        if (this.loader) {
            this.loader.style.display = '';
        }
        return axios.get(this.container.dataset.url, {params: params})
            .then((response) => {
                this.appendToContainer(response.data);
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
     * Append the rendered posts into container.
     * @param {Array.<Object>} posts
     */
    appendToContainer(posts) {
        let res = '';
        for (let post of posts) {
            res += this.renderPost(post);
        }
        this.container.innerHTML = res;
    }

    /**
     * Render the posts.
     * @param {Object} post
     * @return {string}
     */
    renderPost(post) {
        const createdDate = new Date(post.created_at);
        return `<div class="item-row alert alert-light" role="alert">
                    <div>
                        <h5><a href="${this.container.dataset.userUrl + post.user_id}">${post.name} ${post.surname}</a></h5>
                        <small>${createdDate.toLocaleString()}</small>
                    </div>
                    <hr>
                    <h5 class="alert-heading text-center">${post.title}</h5>
                    <p>${post.text}</p>
                </div>`;
    }
}
