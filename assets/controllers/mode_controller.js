import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [ "dark", "light" ];
    connect() {
        this.setMode();
    }

    toggleDarkMode() {
        if (localStorage.theme === 'dark')
        {
            localStorage.theme = 'light';

        } else {
            localStorage.theme = 'dark';
        }
        this.setMode();
    }

    setMode()
    {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkTarget.classList.add('hidden');
            this.lightTarget.classList.remove('hidden');
        } else {
            document.documentElement.classList.remove('dark');
            this.darkTarget.classList.remove('hidden');
            this.lightTarget.classList.add('hidden');
        }

    }

}