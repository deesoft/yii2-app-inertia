import { io } from "socket.io-client";

const IO_URL = import.meta.env.VUE_APP_SOCKET_URL;
class Bus {
    constructor() {
        this.events = {};
    }

    /**
     * Register event
     * @param {string} name 
     * @param {Function} fn 
     */
    on(name, fn) {
        const events = this.events;
        onMounted(() => {
            events[name] = events[name] || [];
            events[name].push(fn);
        });
        onUnmounted(() => {
            if (events[name]) {
                for (var i = 0; i < events[name].length; i++) {
                    if (events[name][i] === fn) {
                        events[name].splice(i, 1);
                        break;
                    }
                };
            }
        });
    }

    /**
     * Trigger event
     * @param {string} name 
     */
    emit(name) {
        const args = arguments.slice(1);
        var th = this;
        if (this.events[name]) {
            this.events[name].forEach((fn) => fn.apply(th, args));
        }
    }
}

class Socket {
    constructor() {
        this.socket = null;
        if (IO_URL) {
            const socket = io(IO_URL, {
                transports: ["websocket"],
            });
            socket.on("connect", () => {
                
            });
            this.socket = socket;
        }
    }

    on(name, fn) {
        if (this.socket) {
            var socket = this.socket;
            onMounted(() => {
                socket.on(name, fn);
            });
            onUnmounted(() => {
                socket.off(name, fn);
            })
        }
    }

    emit(name) {
        if (this.socket) {
            const args = arguments.slice(1);
            this.socket.emit(name, ...args);
        }
    }
}

export const $bus = new Bus();
export const $socket = new Socket();