import { Controller } from 'stimulus';
import { useClickOutside } from 'stimulus-use'


export default class extends Controller {
    static targets = ["draweraside", "drawertransition"]

    connect() {
        useClickOutside(this)
        this.drawer()
    }

    initialize() {
    this.open = false
  }

    clickOutside(event) {
        // example to close a modal
        // this.toggleableTarget.classList.add('hidden')
        this.open = false
        this.drawer()

    }

    closeSidebar() {
        event.preventDefault()
        this.open = false;
       this.drawer();
    }

    resetOpen(){
       this.closeSidebar()
    }

    openSidebar(){
       this.open = true;
       this.drawer();
    }


    drawer() {
        if (this.open) {
            document.body.style.setProperty("overflow", "hidden");
            this.drawertransitionTarget.classList.remove('hidden');
            this.drawerasideTarget.classList.add('translate-x-0');
            this.drawerasideTarget.classList.remove('-translate-x-full');
        } else {
            document.body.style.removeProperty("overflow");
            this.drawertransitionTarget.classList.add('hidden');
            this.drawerasideTarget.classList.remove('translate-x-0');
            this.drawerasideTarget.classList.add('-translate-x-full');
        }


    }



}