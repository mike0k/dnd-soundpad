import { db } from 'store/firebase';
import { store, persistor } from '../store/Store';
import * as UserAction from '../store/action/UserAction';
import { GLogin, GLogout } from 'store/firebase';

import * as UMedia from './Media';
import * as USession from './Session';

export const clear = () => store.dispatch(UserAction.clear());
export const get = () => store.getState().user;
export const set = (data) => store.dispatch(UserAction.set(data));
//const update = (data) => store.dispatch(UserAction.merge(data));

export const reset = () => {
    persistor.purge();
    window.location.reload();
};

export const load = () => {
    const user = get();
    if (user.loggedIn) {
        const userRef = db('users/' + user.id);
        return userRef.once('value').then((snap) => {
            if (snap.val()) {
                const data = snap.val();
                UMedia.set({ ...data.media });
                USession.set({ ...data.session });
                set({ ...data.user });
            }
        });
    }
};

export const login = async () => {
    await GLogin();
    load();
};

export const logout = () => {
    GLogout();
};

export const save = () => {
    const user = get();
    if (user.loggedIn) {
        const data = {
            user: {
                id: user.id,
                name: user.name,
                mute: user.mute,
                volume: user.volume,
            },
            media: UMedia.get(),
            session: USession.get(),
        };

        db('users/' + user.id).set(data);
    }
};
