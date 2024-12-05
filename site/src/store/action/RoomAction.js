import { CLEAR_ROOM, MERGE_ROOM, SET_ROOM } from '../types';

export const clear = () => {
    return {
        type: CLEAR_ROOM,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_ROOM,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_ROOM,
        payload: data,
    };
};
