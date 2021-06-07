// mydropzone_controller.js

import { Controller } from 'stimulus';
import Dropzone from "dropzone";

export default class extends Controller {
    static targets = ["drop", "input"];

    connect() {
        Dropzone.autoDiscover = false;
        this.initializeDropzone();
    }

    initializeDropzone() {
        // console.log(this.dropTarget);
        this.inputTarget.classList.toggle("hidden");
        let dropurl = this.dropTarget.dataset.dropurl;

        let dropzone = new Dropzone(this.dropTarget, {
            paramName: 'file',
            url: dropurl,
            addRemoveLinks: true,
            maxFilesize: 1, // MB
            autoProcessQueue: true,
            acceptedFiles: 'image/*',
            success: function(file, response) {
                file._removeLink.href = "/upload/remove";
                file._removeLink.setAttribute('data-id', response.id);
                file._removeLink.setAttribute('data-token', response.token);
            }
        });

        dropzone.on("removedfile", function(file) {
            let remove_url = file._removeLink.href;
            let data = {};
            data['id'] = file._removeLink.dataset.id;
            data['token'] = file._removeLink.dataset.token;
            fetch(remove_url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
        });
    }





}