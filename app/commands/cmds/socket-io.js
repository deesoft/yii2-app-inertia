import pg from 'pg'
import { Server } from "socket.io";

const PORT = process.env.SOCKET_PORT || 3005;
const { Pool } = pg;
const io = new Server(PORT, {
    cors: { origin: "*" },
    serveClient: false,
});

const pool = new Pool({
    host: process.env.DB_HOST,
    database: process.env.DB_NAME,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
})

io.on('connection', function (socket) {
    socket.io = this;
    socket.on('login', async (userId) => {
        const client = await pool.connect();
        const result = await client.query('SELECT * FROM "user" WHERE "id" = $1', [userId]);
        if (result.rows.length) {
            Object.keys(socket.rooms).forEach((name) => {
                if (name !== socket) {
                    socket.leave(name);
                }
            });
            socket.join(`user-${userId}`);
            socket.userId = userId;
            socket.emit('success-login', userId);
        }
        client.release();
    });

    socket.on('read_notif', async (notifId) => {
        const client = await pool.connect();
        const { rowCount } = await client.query('UPDATE notification SET "is_read"=true WHERE ("id"=$1 AND "user_id"=$2 AND "is_read"=false)', [notifId, socket.userId]);
        if (rowCount) {
            io.in(`user-${socket.userId}`).volatile.emit('notif_readed', notifId);
        }
        client.release();
    });
});


setInterval(async () => {
    const key = Date.now();
    const exp = Math.round(key / 1000);
    const client = await pool.connect();

    if (Math.random() < 0.01) {
        await client.query('DELETE FROM "socket_io" WHERE "expire" <= $1', [exp]);
    }

    const { rowCount } = await client.query('UPDATE socket_io SET "key"=$1 WHERE ("key" IS NULL)', [key]);
    if (rowCount) {
        const result = await client.query('SELECT * FROM "socket_io" WHERE "key" = $1', [key]);
        result.rows.forEach(row => {
            if (row.room) {
                io.in(row.room).volatile.emit(row.name, row.payload);
            } else {
                io.volatile.emit(row.name, row.payload);
            }
        });
        await client.query('DELETE FROM "socket_io" WHERE "key" = $1', [key]);
    }
    client.release();
}, 1000);