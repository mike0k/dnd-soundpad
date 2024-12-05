import { store } from '../store/store';
import * as ErrorAction from '../store/action/ErrorAction';

export const clear = () => {
    store.dispatch(ErrorAction.clear());
};

export const clearItem = (items) => {
    const errors = get();
    let valid = false;
    for (let key in items) {
        const item = items[key];
        if (errors[item]) {
            delete errors[item];
            valid = true;
        }
    }
    if (valid) {
        store.dispatch(ErrorAction.set(errors));
    }
};

export const get = () => {
    return store.getState().error;
};
