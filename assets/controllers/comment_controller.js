import { Controller } from 'stimulus';
import Swal from 'sweetalert2';
import 'sweetalert2/src/sweetalert2.scss'

export default class extends Controller {
    connect() {
        // console.log("Comment Control");

    }

    replace_comment() {
        let link = event.target;
        event.preventDefault();
        if (event.target.tagName !== 'A') {
            link = event.target.parentNode;
        }
        if (link.tagName === 'A') {
            let comment_id = link.dataset.comment_id;
            let url = link.href;
            fetch(url).then(function(response) {
                // The API call was successful!
                return response.text();
            }).then(function(html) {

                // Convert the HTML string into a document object
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                // replace loadmore button
                let reply_comment = doc.getElementById(comment_id);
                document.getElementById(comment_id).innerHTML = reply_comment.innerHTML;

            }).catch(function(err) {
                // There was an error
                console.warn('Something went wrong.', err);
            });

        } else {
            console.log("something went wrong");
        }
    }

    delete_comment() {
        event.preventDefault();
        let form = event.target;
        Swal.fire({
            title: 'Are you sure?',
            text: "Are you sure you want to permanently delete this comment!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
            customClass: {
                popup: 'dark:bg-gray-800 dark:text-white',
                header: 'dark:text-white',
                title: 'dark:text-white',
                content: 'dark:text-white',
                confirmButton: 'bg-deep-orange-900',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = new URLSearchParams();
                for (const pair of new FormData(form)) {
                    data.append(pair[0], pair[1]);
                }
                let url = form.action;
                // Display the values
                fetch(url, {
                        method: 'POST',
                        body: data,
                    })
                    .then(function(response) {
                        // The API call was successful!
                        let comment_box = document.getElementById(form.dataset.commentbox);
                        comment_box.classList.add("hidden");
                    })
                    .catch(function(err) {
                        // There was an error
                        console.warn('Something went wrong.', err);
                    });

            }
        });
    }

    submit_reply() {
        event.preventDefault();
        let el = event.target;
        const data = new URLSearchParams();
        for (const pair of new FormData(el)) {
            data.append(pair[0], pair[1]);
        }
        let url = el.action;
        fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(function(response) {
                // The API call was successful!

                return response.text();
            })
            .then(function(html) {
                // Convert the HTML string into a document object
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');

                let singleComment = doc.getElementById("parent-container");
                let commentContainer = document.getElementById('comments-container');
                commentContainer.insertBefore(singleComment, commentContainer.firstChild);

                // clear text area
                el.reset();


            }).catch(function(err) {
                // There was an error
                console.warn('Something went wrong.', err);
            });
    }

    catch_parent() {
        // Add search value parameter to form and redirect
        event.preventDefault();
        let el = event.target;
        const data = new URLSearchParams();
        for (const pair of new FormData(el)) {
            data.append(pair[0], pair[1]);
        }
        let url = window.location.href;
        // Display the values
        fetch(url, {
                method: 'POST',
                body: data,
            })
            .then(function(response) {
                // The API call was successful!

                return response.text();
            })
            .then(function(html) {
                // Convert the HTML string into a document object
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');

                let singleComment = doc.getElementById("parent-container");
                let commentContainer = document.getElementById('comments-container');
                commentContainer.insertBefore(singleComment, commentContainer.firstChild);

                // clear text area
                el.reset();


            }).catch(function(err) {
                // There was an error
                console.warn('Something went wrong.', err);
            });
    }

    nextPage(e) {
        let url = e.target.href;
        fetch(url).then(function(response) {
            // The API call was successful!
            return response.text();
        }).then(function(html) {

            // Convert the HTML string into a document object
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');

            // Get new comments
            let comment_container = doc.getElementById('comments-container');
            // append new comment to document
            document.getElementById('comments-container').appendChild(comment_container);
            // replace loadmore button
            let load_more = doc.getElementById('paginate-container');
            document.getElementById('paginate-container').innerHTML = load_more.innerHTML;

        }).catch(function(err) {
            // There was an error
            console.warn('Something went wrong.', err);
        });
        event.preventDefault(e);
    }

}