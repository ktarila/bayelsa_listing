import { Controller } from 'stimulus';


export default class extends Controller {
    connect() {
        // console.log("Comment Control");

    }

    closeBanner()
    {
        let el = event.target;
        let banner_id = el.dataset.bannerid;
        document.getElementById(banner_id).classList.add('hidden');
    }

}