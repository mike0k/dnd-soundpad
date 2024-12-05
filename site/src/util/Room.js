import { db } from 'store/firebase';
import { store } from '../store/Store';
import * as RoomAction from '../store/action/RoomAction';
import * as Moment from 'moment';

import * as UPlayer from './Player';
import * as UPlaylist from './Playlist';
import * as UUser from './User';
import * as UTool from './Tool';

let _watchRoom = false;
let roomKey = false;

const clear = () => store.dispatch(RoomAction.clear());
export const get = () => store.getState().room;
const set = (data) => store.dispatch(RoomAction.set(data));
//const update = (data) => store.dispatch(RoomAction.merge(data));

export const getGroup = (id) => {
    const groups = get().groups;
    let group = false;
    if (typeof groups !== 'undefined' && typeof groups[id] !== 'undefined') {
        group = groups[id];
    }
    return group;
};

export const getItem = (id) => {
    const room = get();
    let item = false;
    if (typeof room.playlist[id] !== 'undefined') {
        item = room.playlist[id];
    }

    return item;
};

export const getStatus = () => {
    const room = get();
    let status = 'offline';
    if (room.active) {
        if (room.op === UUser.get().id) {
            status = 'host';
        } else {
            status = 'join';
        }
    }

    return status;
};

export const getUser = (id) => {
    const users = get().users;
    let user = false;
    if (typeof users !== 'undefined' && typeof users[id] !== 'undefined') {
        user = users[id];
    }
    return user;
};

export const hasGroupAccess = (groupId) => {
    if (get().isOp) {
        return true;
    }
    const user = UUser.get();
    const userRoom = getUser(user.id);
    const group = getGroup(groupId);
    let hasAccess = false;
    if (group !== false && userRoom !== false) {
        if (group.access === 2) {
            hasAccess = true;
        } else if (typeof userRoom.access !== 'undefined' && userRoom.access.includes(groupId)) {
            hasAccess = true;
        }
    }

    return hasAccess;
};

export const host = () => {
    const roomsRef = db('rooms');
    const roomId = UTool.randomId();
    const userId = UUser.get().id;

    const room = {
        id: roomId,
        groups: setGroups(false),
        op: userId,
        users: {
            [userId]: {
                id: userId,
                name: UUser.get().name,
                access: [],
            },
        },
        playlist: setPlaylist(false),
        play: {},
    };

    const roomRef = roomsRef.push(room);

    set({
        id: roomId,
        key: roomRef.key,
        active: true,
        op: userId,
    });
    watchRoom(roomRef);
};

export const hostEnd = () => {
    if (_watchRoom !== false) {
        _watchRoom.off('value');
        _watchRoom.remove();
        _watchRoom = false;
    }
    if (roomKey !== false) {
        roomKey = false;
    }
    clear();
};

export const join = (id) => {
    const roomRef = db('rooms').orderByChild('id').equalTo(id);
    //const roomRef = db('rooms').child(id);
    //leave();
    if (_watchRoom !== false) {
        _watchRoom.off('value');
        _watchRoom = false;
    }
    if (roomKey !== false) {
        roomKey = false;
    }
    return watchRoom(roomRef);
};

export const leave = () => {
    if (_watchRoom !== false) {
        _watchRoom.off('value');
        _watchRoom = false;
    }
    if (roomKey !== false) {
        const userId = UUser.get().id;
        db('rooms/' + roomKey + '/users/' + userId).remove();
        roomKey = false;
    }

    clear();
    UPlaylist.pause();
    UPlayer.load();
};

export const loadRoom = () => {
    const room = get();
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const channel = urlParams.get('channel');

    if (typeof channel !== 'undefined' && channel !== null) {
        join(channel).then(() => {
            // setTimeout(() => {
            //     window.location.replace(window.location.protocol + '//' + window.location.host);
            // }, 1000);
        });
    } else if (room.active === true && room.key !== '') {
        const roomRef = db('rooms').child(room.key);
        watchRoom(roomRef);
    }
};

export const pause = (id) => {
    const item = getItem(id);
    if (item !== false) {
        db('rooms/' + roomKey + '/playlist/' + item.id).update({
            play: 0,
            started: false,
        });
    }
};

export const play = (id) => {
    const item = getItem(id);
    if (item !== false) {
        db('rooms/' + roomKey + '/playlist/' + item.id).update({
            play: item.play === 1 ? 2 : 1,
            played: 0,
            started: Moment().unix(),
        });
    }
};

export const setGroups = (doUpdate = true) => {
    let groups = {};
    const data = UPlaylist.get().groups;
    UTool.map(data, (group) => {
        if (group.access === 1 || group.access === 2) {
            groups[group.id] = {
                id: group.id,
                access: group.access,
                name: group.name,
            };
        }
    });

    if (doUpdate) {
        return _watchRoom.child('groups').set(groups);
    }
    return groups;
};

export const setPlaylist = (doUpdate = true) => {
    let items = {};
    const data = UPlaylist.get().items;
    const room = get();
    UTool.map(data, (item) => {
        const roomItem = getItem(item.id);
        let play = 0;
        if (room.active && roomItem !== false) {
            play = roomItem.play;
            if (!room.isOp) {
                item = roomItem;
            }
        }
        items[item.id] = {
            id: item.id,
            duration: item.duration,
            group: item.group,
            name: item.name,
            loop: item.loop,
            play,
            sort: item.sort,
            started: item.started,
            url: item.url,
            volume: item.volume,
        };
    });

    if (doUpdate) {
        db('rooms/' + roomKey + '/playlist').update(items);
    }
    return items;
};

export const setUserAccess = (id, access) => {
    const room = get();
    if (roomKey !== false && room.isOp) {
        const user = getUser(id);
        if (user !== false) {
            db('rooms/' + roomKey + '/users/' + user.id + '/access').set([...access]);
        }
    }
};

// const setUserName = () => {
//     const user = UUser.get();
//     if (roomKey !== false) {
//         db('rooms/' + roomKey + '/users/' + user.id).update(user.name);
//     }
// };

const watchRoom = (roomRef) => {
    _watchRoom = roomRef;
    return new Promise((resolve, reject) => {
        return roomRef.on('value', (snap) => {
            let item = snap.val();
            let key = snap.key;

            if (item !== null && typeof item.id === 'undefined') {
                snap.forEach(function (data) {
                    if (typeof item.id === 'undefined') {
                        item = data.val();
                        key = data.key;
                    }
                });
            }

            if (item !== null && key !== 'rooms') {
                roomKey = key;
                const user = UUser.get();
                const isOp = user.id === item.op;
                const data = {
                    id: item.id,
                    key: snap.key,
                    active: true,
                    groups: item.groups,
                    isOp,
                    op: item.op,
                    playlist: item.playlist,
                    play: item.play,
                    users: item.users,
                };
                set(data);
                UPlaylist.loadRoom();
            } else {
                //leave();
            }
            resolve({ item, key });
        });
    }).then(({ item, key }) => {
        if (item !== null && key !== 'rooms') {
            const user = UUser.get();
            if (
                typeof item.users[user.id] === 'undefined' ||
                item.users[user.id].name !== user.name
            ) {
                item.users[user.id] = {
                    id: user.id,
                    name: user.name,
                    access: [],
                };
                db('rooms')
                    .child(key + '/users')
                    .update(item.users);
            }
        }
    });
};
