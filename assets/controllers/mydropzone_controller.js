// mydropzone_controller.js

import { Controller } from 'stimulus';
import Dropzone from "dropzone";

export default class extends Controller {
    static targets = ["drop", "input"];

    connect() {
        Dropzone.autoDiscover = false;
        console.log('dropzone form');
        this.initializeDropzone();
    }

    initializeDropzone() {
        // console.log(this.dropTarget);
        this.inputTarget.classList.toggle("hidden");
        let dropurl = this.dropTarget.dataset.dropurl;
        console.log(dropurl);

        var dropzone = new Dropzone(this.dropTarget, {
            paramName: 'file',
            url: dropurl,
            addRemoveLinks: true,
            maxFilesize: 1, // MB
            autoProcessQueue: true,
            success: function(file, response) {
                console.log(file);
                console.log(response);
            }
        });
    }


}