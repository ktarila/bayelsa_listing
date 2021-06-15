import { Controller } from 'stimulus';
import Masonry from 'masonry-layout';


export default class extends Controller {
    connect() {
        console.log("Category Control");
        this.initMasonory();

    }

    initMasonory() {
        var grid = document.querySelector('.mason-grid');
        var msnry = new Masonry(grid, {
            itemSelector: '.tile',
            columnWidth: 200
        });
    }

}