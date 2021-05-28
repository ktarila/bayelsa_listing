import { Controller } from 'stimulus';

export default class extends Controller {


    nextPage(e) {
        let url = e.target.href;
        console.log(url);
        fetch(url).then(function(response) {
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
        event.preventDefault(e);

    }

}