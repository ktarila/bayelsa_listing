document.addEventListener('DOMContentLoaded', function() {
    // on dom ready --- equivalent of jquery document ready

    // add active link to nav sidebar
    var all_links = document.querySelectorAll('.n-link');
    // Loop through each link.
    for (var i = all_links.length - 1; i >= 0; i--) {
        let anchor_el = all_links[i].getElementsByTagName('a');
        for (let el of anchor_el) {
            // exclude query parameters
            if (el.href == window.location.href.split("?")[0]) {
                all_links[i].className += " bg-deep-orange-600 text-white";
            }

        }
    }

    function hashtag(text) {
        var repl = text.replace(/#(\w+)/g, '<a class="z-30 hover:text-deep-orange-600 text-blue-500 dark:text-blue-400" href="/tag/$1">#$1</a>');
        return repl;
    }

    //  Hashtags to div
    var containsTag = document.querySelectorAll('.has-tag');
    for (var i = containsTag.length - 1; i >= 0; i--) {
        let text = containsTag[i].innerHTML;
        containsTag[i].innerHTML = hashtag(text);
    }

    //  Modals
    var openmodal = document.querySelectorAll('.modal-open')
    for (var i = 0; i < openmodal.length; i++) {
        openmodal[i].addEventListener('click', function(event) {
            event.preventDefault()
            openModal(this.getAttribute('data-modalid'))
        })
    }

    const overlay = document.querySelectorAll('.modal-overlay')
    for (var i = 0; i < overlay.length; i++) {
        overlay[i].addEventListener('click', closeModals)

    }

    var closemodal = document.querySelectorAll('.modal-close')
    for (var i = 0; i < closemodal.length; i++) {
        closemodal[i].addEventListener('click', closeModals)
    }

    document.onkeydown = function(evt) {
        evt = evt || window.event
        var isEscape = false
        if ("key" in evt) {
            isEscape = (evt.key === "Escape" || evt.key === "Esc")
        } else {
            isEscape = (evt.keyCode === 27)
        }
        if (isEscape && document.body.classList.contains('modal-active')) {
            closeModals()
        }
    };


    function closeModals() {
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

    function openModal(modalid) {
        const body = document.querySelector('body')
        const modal = document.querySelector('#' + modalid)
        modal.classList.toggle('opacity-0')
        modal.classList.toggle('pointer-events-none')
        body.classList.toggle('modal-active')
    }

    // Simple lightbox
    // intance via constructor and selector
    // eslint-disable-next-line  no-unused-vars
    // console.log(window.SimpleLightbox)
    // var lightbox = new window.SimpleLightbox({elements: '.gallery a'});
    // console.log(lightbox)

});


window.openPanel = function openPanel(el) {
    // Declare all variables
    var clicked_pane_id = el.getAttribute('data-href');

    // Get all elements with class="tab-pane" and hide them
    var tabcontent = document.querySelectorAll(".tab-pane");
    for (var i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // // Get all elements with class="tablinks" and remove the class "active"
    var tablinks = document.querySelectorAll(".tab-link");
    for (var i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active-tab");
    }

    // // Show the current tab, and add an "active" class to the button that opened the tab
    var clicked_pane = document.querySelector(clicked_pane_id);
    if (clicked_pane !== null) {
        clicked_pane.style.display = "block";
    }
    el.classList.add("active-tab");
}

// // Tabs
var active_tab = document.querySelector('.active-tab');
if (active_tab !== null) {
    window.openPanel(active_tab);
}

// dropdown
/*Toggle dropdown list*/
window.toggleDD = function toggleDD(myDropMenu) {
    document.getElementById(myDropMenu).classList.toggle("invisible");
};
/*Filter dropdown options*/
window.filterDD = function filterDD(myDropMenu, myDropMenuSearch) {
    var input, filter, ul, li, a, i;
    input = document.getElementById(myDropMenuSearch);
    filter = input.value.toUpperCase();
    div = document.getElementById(myDropMenu);
    a = div.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        if (a[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}
// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.drop-button') && !event.target.matches('.drop-search')) {
        var dropdowns = document.getElementsByClassName("dropdownlist");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (!openDropdown.classList.contains('invisible')) {
                openDropdown.classList.add('invisible');
            }
        }
    }
}


module.exports = {
    openPanel,
    toggleDD,
    filterDD
}