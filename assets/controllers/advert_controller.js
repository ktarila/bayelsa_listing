import { Controller } from 'stimulus';
const serialize = require('form-serialize');

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "store" comes from the filename:
 * store_controller.js -> "store"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    // connect() {
    //     this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/store_controller.js';
    // }
    catch_click() {
        // console.log('clicked me')
        // console.log(this.element.getAttribute("data-href"))
        window.location = this.element.getAttribute('data-href');
    }

    catch_logout() {
        event.preventDefault();
        // console.log(this.element.getAttribute("data-href"))
        window.location = '/logout';
    }

    get_search_data() {
        let search_string = document.getElementById('searchtext').value;
        let json_data = this.getFormDataAsJson();
        json_data['search_value'] = search_string;

        return json_data;

    }

    catch_search() {
        // Add search value parameter to form and redirect
        event.preventDefault();
        let json_data = this.get_search_data();
        this.postData(json_data);

        // window.location = newUrl;
    }

    catch_filter() {
        // Add search value parameter to form and redirect
        event.preventDefault();
        this.catch_search();
        this.closeModals();

    }

    getFormDataAsJson() {
        // Add search value parameter to form and redirect
        let filter_form = document.getElementById("advert_filter");

        const data = new FormData(filter_form);

        const formJSON = Object.fromEntries(data.entries());
        formJSON['category[]'] = data.getAll('category[]');

        Object.defineProperty(formJSON, 'category',
            Object.getOwnPropertyDescriptor(formJSON, 'category[]'));
        delete formJSON['category[]'];

        return formJSON;

    }

    postData(data) {
        let url = window.location;
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
            })
            .then(function(html) {

                // Convert the HTML string into a document object
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');

                // replace ads
                let ad_container = doc.getElementById('advert-container');
                // console.log(ad_container.innerHTML);
                let children = (ad_container.children);
                for (let child of children) {
                    // update has tags
                    var containsTag = child.querySelectorAll('.has-tag');
                    for (var i = containsTag.length - 1; i >= 0; i--) {
                        let text = containsTag[i].innerHTML;
                        containsTag[i].innerHTML = text.replace(/#(\w+)/g, '<a class="hover:text-deep-orange-600 text-blue-500 dark:text-blue-400" href="/tag/$1">#$1</a>');;
                    }

                }
                document.getElementById('advert-container').innerHTML = ad_container.innerHTML;

                // replace loadmore button
                let load_more = doc.getElementById('paginate-container');
                document.getElementById('paginate-container').innerHTML = load_more.innerHTML;

            }).catch(function(err) {
                // There was an error
                console.warn('Something went wrong.', err);
            });
    }

    closeModals() {
        const body = document.querySelectorAll('body')
        const modal = document.querySelectorAll('.modal')
        for (var i = 0; i < modal.length; i++) {
            if (!modal[i].classList.contains("opacity-0")) {
                modal[i].classList.add("opacity-0");
            }
            if (!modal[i].classList.contains("pointer-events-none")) {
                modal[i].classList.add("pointer-events-none");
            }
        }
        for (var i = 0; i < body.length; i++) {
            if (!body[i].classList.contains("modal-active")) {
                body[i].classList.add("modal-active");
            }
        }
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