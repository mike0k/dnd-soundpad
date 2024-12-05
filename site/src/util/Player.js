import { store } from '../store/Store';
import * as PlayerAction from '../store/action/PlayerAction';
import * as R from 'ramda';

import * as UPlaylist from './Playlist';
import * as URoom from './Room';
import * as UTool from './Tool';

export const clear = () => store.dispatch(PlayerAction.clear());
export const get = () => store.getState().player;
const set = (data) => store.dispatch(PlayerAction.set(data));
const update = (data) => store.dispatch(PlayerAction.merge(data));

export const play = (id) => {
    const item = getItem(id);
    if (item.status !== 'play') {
        setItem(id, { status: 'play', play: 0 });
    }
};
export const pause = (id) => {
    const item = getItem(id);
    if (item.status !== 'ready') {
        UPlaylist.pause(id);
        URoom.pause(id);
        setItem(id, {
            play: 0,
            //played: item.played + 1,
            played: 1,
            status: 'ready',
            started: false,
        });
    }
};

export const add = (data) => {
    const id = UTool.randomId();
    const item = {
        ...data,
        id,
    };
    update({ items: { [id]: item } });
};

export const init = () => {
    const items = get().items;
    UTool.map(items, (item, key) => {
        if (item.status === 'unload') {
            items[key].status = 'load';
        }
    });
    update({ items });
};

export const load = () => {
    const playlist = loadPlaylist(false);
    const room = loadRoom(false);

    set({
        items: {
            ...playlist,
            ...room,
        },
    });
};

export const loadItem = (id, data = {}) => {
    const loaded = getItem(id);
    let item = loaded !== false ? R.clone(loaded) : {};
    if (loaded === false) {
        item = {
            duration: 0,
            loop: false,
            name: id,
            play: 0,
            played: 0,
            playedSeconds: 0,
            started: false,
            stream: false,
            type: 'back',
            volume: 75,
            url: '',
            ...data,
            id,
            status: 'load',
        };
    } else {
        item = {
            ...loaded,
            ...data,
            id: loaded.id,
            played: loaded.played,
            status: loaded.status,
        };
    }

    if (item.play === 0) {
        item.played = item.play;
    } else if (item.play > item.played) {
        item.played = item.play - 1;
    } else if (item.played > item.play) {
        item.played = item.play;
    }

    return item;
};

export const loadPlaylist = (doUpdate = true) => {
    const playlist = UPlaylist.get().items;
    const room = URoom.get();
    const items = {};
    UTool.map(playlist, (item) => {
        const playerItem = loadItem(item.id, {
            loop: item.loop,
            name: item.name,
            play: item.play,
            started: item.started,
            stream: item.stream,
            url: item.url,
            volume: item.volume,
        });
        if (room.active) {
            //playerItem.status = 'unload';
        }

        items[playerItem.id] = playerItem;
    });

    if (typeof playlist['undefined'] !== 'undefined') {
        let clean = playlist;
        delete clean['undefined'];
        set({ items: clean });
    }

    if (doUpdate) {
        return update({ items });
    }
    return items;
};

export const loadRoom = (doUpdate = true) => {
    const { playlist } = URoom.get();
    const items = {};
    UTool.map(playlist, (item) => {
        const playerItem = loadItem(item.id, {
            loop: item.loop,
            name: item.name,
            play: item.play,
            started: item.started,
            stream: true,
            url: item.url,
            volume: item.volume,
        });

        if (playerItem.play === 2) {
            playerItem.play = 1;
            playerItem.played = 0;
        }

        items[playerItem.id] = playerItem;
    });

    if (doUpdate) {
        return update({ items });
    }
    return items;
};

export const getItem = (id) => {
    const items = get().items;
    if (typeof items[id] !== 'undefined') {
        return items[id];
    }
    return false;
};

export const remove = (id) => {
    let items = get().items;
    if (typeof items[id] !== 'undefined') {
        delete items[id];
        set({ items });
    }
};

export const setItem = (id, data = {}) => {
    return update({ items: { [id]: { ...data } } });
};

export const toggle = (id, type = false) => {
    const room = URoom.get();
    const player = getItem(id);

    if (player !== false) {
        const rItem = URoom.getItem(id);
        const pItem = UPlaylist.getItem(id);

        if (rItem !== false && rItem.play > 0) {
            return URoom.pause(id);
        } else if (pItem !== false && pItem.play > 0) {
            return UPlaylist.pause(id);
        }

        if (type === false) {
            if (room.active && room.isOp) {
                type = 'room';
            } else {
                type = 'local';
            }
        }

        if (type === 'room') {
            if (URoom.hasGroupAccess(pItem.group)) {
                return URoom.play(id);
            }
        } else {
            setItem(id, { played: 0 });
            return UPlaylist.play(id);
        }
    }
};
