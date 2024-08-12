import WebSocketClient from './WebSocketClient.js';

class Interface {
    constructor() {
        this.websocketClient = new WebSocketClient('ws://localhost:9090');
        this.apiUrl = 'http://localhost:8080';
    }

    init() {
        document.getElementById('asyncTaskButton').addEventListener('click', () => this.startAsyncTask());
        document.getElementById('blockingCoroutinesButton').addEventListener('click', () => this.startBlockingCoroutines());
        document.getElementById('nonBlockingCoroutinesButton').addEventListener('click', () => this.startNonBlockingCoroutines());
    }

    startAsyncTask() {
        this.call('/examples/async-task');
    }

    startBlockingCoroutines() {
        this.call('/examples/blocking-coroutines');
    }

    startNonBlockingCoroutines() {
        this.call('/examples/non-blocking-coroutines');
    }

    call(path) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.apiUrl + path, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Request sent successfully!');
                    console.log('Response:', xhr.responseText);
                } else {
                    console.error('Error sending message:', xhr.status);
                }
            }
            
        };

        xhr.send();
    }
}

export default Interface;