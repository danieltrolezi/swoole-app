import WebSocketClient from './WebSocketClient.js';

class Interface {
    constructor() {
        this.roomInput = document.getElementById('roomInput');
        this.messageInput = document.getElementById('messageInput');
        this.websocketClient = new WebSocketClient('ws://localhost:9090');
    }

    init() {
        document.getElementById('joinButton').addEventListener('click', () => this.joinRoom());
        document.getElementById('leaveButton').addEventListener('click', () => this.leaveRoom());
        document.getElementById('sendButton').addEventListener('click', () => this.sendMessage());
        document.getElementById('broadcastButton').addEventListener('click', () => this.broadcastMessage());
    }

    getRoom() {
        return this.roomInput.options[this.roomInput.selectedIndex].value;
    }

    getMessage() {
        return this.messageInput.value.trim();
    }

    joinRoom() {
        this.websocketClient.joinRoom(this.getRoom());
    }

    leaveRoom() {
        this.websocketClient.leaveRoom(this.getRoom());
    }

    sendMessage() {
        const messageText = this.getMessage();
        if (messageText !== '') {
            this.websocketClient.sendMessage(this.getRoom(), messageText);
            this.messageInput.value = '';
        } else {
            alert('Please enter a message before submitting.');
        }
    }

    broadcastMessage() {
        const messageText = this.getMessage();
        if (messageText !== '') {
            this.websocketClient.broadcastMessage(messageText);
            this.messageInput.value = '';
        } else {
            alert('Please enter a message before submitting.');
        }
    }
}

export default Interface;