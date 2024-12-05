import { CLEAR_SESSION, MERGE_SESSION, SET_SESSION } from '../types';

export const clear = () => {
    return {
        type: CLEAR_SESSION,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_SESSION,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_SESSION,
        payload: data,
    };
};
