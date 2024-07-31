import ChatUI from './src/ChatUI.js';

class Application {
    constructor() {
        if (Application.instance) {
            return Singleton.instance;
        }

        Application.instance = this;

        this.chatUI = new ChatUI();
        this.chatUI.init();  
    }
} 

new Application();

