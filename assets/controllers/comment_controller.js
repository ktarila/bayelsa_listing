import { Controller } from 'stimulus';


export default class extends Controller {
    connect() {
        // console.log("Comment Control");

    }

    catch_parent() {
        // Add search value parameter to form and redirect
        event.preventDefault();
        let el = event.target;
        console.log(el);
        const data = new URLSearchParams();
        for (const pair of new FormData(el)) {
            data.append(pair[0], pair[1]);
        }
        let url = window.location.href;
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

                // clear text area
                document.getElementById('comment_content').value="";


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