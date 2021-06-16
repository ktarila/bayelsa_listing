import { Controller } from 'stimulus';
import debounce from 'lodash/debounce';
var stringSimilarity = require("string-similarity");


export default class extends Controller {

    static targets = ["category", 'searchtext'];
    connect() {
        console.log("Category Control");
        this.autofilter = debounce(this.autofilter, 300).bind(this);

    }

    autofilter() {
        // autosave logic here...
        console.log('calling filter');
        let value = this.searchtextTarget.value.toLowerCase();
        for (let card of this.categoryTargets) {
            let category = card.dataset.category.toLowerCase();
            if (value === "") {
                card.classList.remove("hidden");
            } else {
                var similarity = stringSimilarity.compareTwoStrings(value, category);
                if (similarity < 0.2) {
                    card.classList.add("hidden");
                } else {
                    card.classList.remove("hidden");
                }
            }

        }

    }


}