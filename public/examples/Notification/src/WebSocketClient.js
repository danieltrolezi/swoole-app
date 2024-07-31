class WebSocketClient {
    constructor(serverUrl) {
        this.serverUrl = serverUrl;
        this.websocket = new WebSocket(serverUrl);
        this.setupEventHandlers();
    }

    setupEventHandlers() {
        this.websocket.onopen = () => {
            console.log('Connected to WebSocket server.');
            this.joinPrivateRoom();
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

    joinPrivateRoom() {
        this.websocket.send(JSON.stringify({
            'action': 'join',
            'room': 'room-private'
        }));
    }
}

export default WebSocketClient;