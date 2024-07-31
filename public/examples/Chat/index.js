import Interface from './src/Interface.js';

class Application {
    constructor() {
        if (Application.instance) {
            return Singleton.instance;
        }

        Application.instance = this;

        this.interface = new Interface();
        this.interface.init();  
    }
} 

new Application();

