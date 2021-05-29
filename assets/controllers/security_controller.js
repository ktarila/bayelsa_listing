import { Controller } from 'stimulus';


export default class extends Controller {
    static targets = ['password', 'show', 'hide'];

    connect() {
       // console.log('security controller') 
    }

    togglePasswordShow() {
        let password_el = this.passwordTarget;
        console.log(password_el);
        let input_attr = password_el.type;
        console.log(input_attr);
        if (input_attr === "password")
        {
            this.passwordTarget.type = "text";

        } else {
            this.passwordTarget.type = "password";
        }
        this.hideTarget.classList.toggle('hidden');
        this.showTarget.classList.toggle('hidden');
        
    }

}