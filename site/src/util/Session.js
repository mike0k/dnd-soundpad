import { store } from '../store/Store';
import * as SessionAction from '../store/action/SessionAction';

import * as UPlaylist from './Playlist';
import * as UUser from './User';
import { randomId } from './Tool';

//const clear = () => store.dispatch(SessionAction.clear());
export const get = () => store.getState().session;
export const set = (data) => store.dispatch(SessionAction.set(data));
//const update = (data) => store.dispatch(SessionAction.merge(data));

export const add = () => {
    const id = randomId();
    UPlaylist.set({ id });
    const playlist = UPlaylist.get();
    set({ active: id, items: { ...get().items, [id]: playlist } });
};

export const remove = (id) => {
    const item = getItem(id);
    if (item !== false) {
        let { active, items } = get();
        delete items[id];
        if (active === id) {
            const key = Object.keys(items)[0];
            active = key;
        }
        set({ active, items });
    }
};

export const load = (id) => {
    const item = getItem(id);
    if (item !== false) {
        UPlaylist.clear();
        UPlaylist.set(item);
    }
};

const getItem = (id) => {
    const items = get().items;
    if (typeof items[id] !== 'undefined') {
        return items[id];
    }
    return false;
};

export const updateCurrent = () => {
    const playlist = UPlaylist.get();
    const id = playlist.id;
    set({ active: id, items: { ...get().items, [id]: playlist } });
    UUser.save();
};
