class WebSocketClient {
    constructor(serverUrl) {
        this.serverUrl = serverUrl;
        this.websocket = new WebSocket(serverUrl);
        this.setupEventHandlers();
    }

    setupEventHandlers() {
        this.websocket.onopen = () => {
            console.log('Connected to WebSocket server.');
        };

        this.websocket.onclose = () => {
            console.log('Disconnected');
        };

        this.websocket.onmessage = (evt) => {
            console.log('Retrieved data from server: ' + evt.data);
        };

        this.websocket.onerror = (evt) => {
            console.log('Error occurred: ' + evt.data);
        };
    }

    joinRoom(room) {
        this.websocket.send(JSON.stringify({
            'action': 'join',
            'room': room
        }));
    }

    leaveRoom(room) {
        this.websocket.send(JSON.stringify({
            'action': 'leave',
            'room': room
        }));
    }

    sendMessage(room, message) {
        this.websocket.send(JSON.stringify({
            'action': 'message',
            'room': room,
            'message': message
        }));
    }

    broadcastMessage(message) {
        this.websocket.send(JSON.stringify({
            'action': 'broadcast',
            'message': message
        }));
    }
}

export default WebSocketClient;