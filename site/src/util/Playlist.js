import { store } from '../store/Store';
import * as PlaylistAction from '../store/action/PlaylistAction';
import * as Moment from 'moment';

import { randomId } from './Tool';

import * as UPlayer from './Player';
import * as URoom from './Room';
import * as USession from './Session';
import * as UTool from './Tool';

export const clear = (data) => {
    const res = store.dispatch(PlaylistAction.clear(data));
    USession.updateCurrent();
    return res;
};
export const get = () => store.getState().playlist;
export const set = (data) => {
    const res = store.dispatch(PlaylistAction.set(data));
    USession.updateCurrent();
    return res;
};
export const update = (data) => {
    const res = store.dispatch(PlaylistAction.merge(data));
    USession.updateCurrent();
    return res;
};

export const add = (data) => {
    if (typeof data.url !== 'undefined' && data.url !== '') {
        let id = randomId();
        id = typeof data.id !== 'undefined' ? data.id : id;
        const item = {
            id,
            duration: 0,
            loop: false,
            isOp: true,
            name: '',
            play: 0,
            stream: false,
            volume: 75,
            group: get().group,
            sort: Object.keys(get().items).length,
            started: false,
            url: '',
            ...data,
        };

        if (item.mediaId !== '' && item.mediaId !== null) {
            update({ items: { [id]: item } });
            UPlayer.loadPlaylist();
            return item;
        }
    }
    return false;
};

export const addGroup = () => {
    const groups = get().groups;
    const id = randomId();
    const item = {
        id,
        access: 0,
        name: 'Group ' + (Object.keys(groups).length + 1),
        play: 0,
        sort: Object.keys(get().groups).length,
    };

    update({ groups: { [id]: item } });
};

export const clean = () => {
    UTool.map(get().items, (item) => {
        if (typeof item !== 'undefined') {
            let valid = true;
            valid = getGroup(item.group) === false ? false : valid;
            if (!valid) {
                remove(item.id);
            }
        }
    });
};

export const getGroup = (id) => {
    const items = get().groups;
    if (typeof items[id] !== 'undefined') {
        return items[id];
    }
    return false;
};

export const getItem = (id) => {
    const items = get().items;
    if (typeof items[id] !== 'undefined') {
        return items[id];
    }
    return false;
};

export const loadRoom = () => {
    const { playlist } = URoom.get();
    const items = {};
    UTool.map(playlist, (rItem) => {
        let pItem = getItem(rItem.id);
        if (pItem !== false) {
            pItem = {
                ...pItem,
                loop: rItem.loop,
                play: rItem.play,
                volume: rItem.volume,
            };
        } else {
            pItem = add({
                loop: rItem.loop,
                group: rItem.group,
                isOp: false,
                name: rItem.name,
                play: rItem.play,
                started: rItem.started,
                stream: true,
                url: rItem.url,
                volume: rItem.volume,
                id: rItem.id,
            });
        }
        items[pItem.id] = pItem;
    });

    //update({ items: items });
};

export const pause = (id) => {
    if (id !== 'undefined') {
        setItem(id, { play: 0, stream: URoom.get().active, started: false });
    } else {
        UTool.map(get().items, (item) => {
            setItem(item.id, { play: 0, stream: URoom.get().active, started: false });
        });
    }
};

export const play = (id) => {
    const item = getItem(id);
    if (item !== false) {
        update({
            items: {
                [item.id]: {
                    play: 1,
                    stream: false,
                    started: Moment().unix(),
                },
            },
        });
    }
};

export const playGroup = (id) => {
    const group = getGroup(id);
    if (group !== false) {
        let playGroup = 1;
        if (group.play === 1) {
            playGroup = 0;
        }
        setGroup(group.id, { play: playGroup });
        UTool.map(get().items, (item) => {
            if (item.group === group.id) {
                if (URoom.get().active && URoom.hasGroupAccess(id)) {
                    if (playGroup === 1) {
                        URoom.play(item.id);
                    } else {
                        URoom.pause(item.id);
                    }
                } else {
                    if (playGroup === 1) {
                        play(item.id);
                    } else {
                        pause(item.id);
                    }
                }
            }
        });
    }
};

export const remove = (id) => {
    let items = get().items;
    if (typeof items[id] !== 'undefined') {
        delete items[id];
        set({ items });
    }
};

export const removeGroup = (id) => {
    let { groups, items } = get();
    if (Object.keys(groups).length > 1) {
        if (typeof groups[id] !== 'undefined') {
            delete groups[id];
        }
        UTool.map(items, (item) => {
            if (item.group === id) {
                delete items[item.id];
            }
        });

        set({ group: Object.keys(groups)[0], groups, items });
    }
};

export const setGroup = (id, data = {}) => {
    const group = getGroup(id);
    if (group !== false) {
        const room = URoom.get();
        const res = update({ groups: { [id]: { ...data } } });
        if (room.active && room.isOp) {
            URoom.setGroups();
        }
        return res;
    }
    return false;
};

export const setItem = (id, data = {}) => {
    const item = getItem(id);
    if (item !== false) {
        const room = URoom.get();
        const res = update({ items: { [id]: { ...data } } });
        if (room.active && (room.isOp || URoom.hasGroupAccess(item.group))) {
            URoom.setPlaylist();
        }
        return res;
    }
    return false;
};

export const viewGroup = (id) => {
    const item = getGroup(id);
    if (item !== false) {
        set({ group: id });
    }
};
