import { CLEAR_ERROR, SET_ERROR } from '../types';

export const clear = () => {
    return {
        type: CLEAR_ERROR,
    };
};

export const set = (errors) => {
    if (typeof errors === 'undefined') {
        return clear();
    } else {
        return {
            type: SET_ERROR,
            payload: errors,
        };
    }
};
