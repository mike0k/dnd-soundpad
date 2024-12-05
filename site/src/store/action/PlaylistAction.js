import { CLEAR_PLAYLIST, MERGE_PLAYLIST, SET_PLAYLIST } from '../types';

export const clear = () => {
    return {
        type: CLEAR_PLAYLIST,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_PLAYLIST,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_PLAYLIST,
        payload: data,
    };
};
