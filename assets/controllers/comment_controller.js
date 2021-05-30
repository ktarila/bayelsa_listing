import { Controller } from 'stimulus';


export default class extends Controller {
    connect() {
        // console.log("Comment Control");

    }

    catch_parent() {
        // Add search value parameter to form and redirect
        event.preventDefault();
        console.log("comment form submit");
        let el = event.target;
        console.log(el);
        const data = new URLSearchParams();
        for (const pair of new FormData(el)) {
            data.append(pair[0], pair[1]);
        }
        let url = window.location.href;
        console.log(url);
        console.log(data.toString());
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

            }).catch(function(err) {
                // There was an error
                console.warn('Something went wrong.', err);
            });
    }

    nextPage(e) {
        event.preventDefault(e);
        let data = this.get_search_data();

        let url = e.target.href;
        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(function(response) {
            // The API call was successful!
            return response.text();
        }).then(function(html) {

            // Convert the HTML string into a document object
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');

            // Get new ads
            let ad_container = doc.getElementById('advert-container');
            // append new add to document
            // console.log(ad_container);
            let children = (ad_container.children);
            for (let child of children) {
                // update has tags
                var containsTag = child.querySelectorAll('.has-tag');
                for (var i = containsTag.length - 1; i >= 0; i--) {
                    let text = containsTag[i].innerHTML;
                    containsTag[i].innerHTML = text.replace(/#(\w+)/g, '<a class="hover:text-deep-orange-600 text-blue-500 dark:text-blue-400" href="/tag/$1">#$1</a>');;
                }
                document.getElementById('advert-container').appendChild(child);
            }
            // replace loadmore button
            let load_more = doc.getElementById('paginate-container');
            document.getElementById('paginate-container').innerHTML = load_more.innerHTML;

        }).catch(function(err) {
            // There was an error
            console.warn('Something went wrong.', err);
        });
    }

}