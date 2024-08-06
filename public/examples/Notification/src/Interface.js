import WebSocketClient from './WebSocketClient.js';

class Interface {
    constructor() {
        this.websocketClient = new WebSocketClient('ws://localhost:9090');
    }

    init() {
        document.getElementById('asyncTaskButton').addEventListener('click', () => this.startAsyncTask());
        document.getElementById('blockedCoroutinesButton').addEventListener('click', () => this.startBlockedCoroutines());
    }

    startAsyncTask() {
        this.call('http://localhost:8080/examples/async-task');
    }

    startBlockedCoroutines() {
        this.call('http://localhost:8080/examples/blocked-coroutines');
    }

    call(url) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
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