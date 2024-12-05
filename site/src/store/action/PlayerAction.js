import { CLEAR_PLAYER, MERGE_PLAYER, SET_PLAYER } from '../types';

export const clear = () => {
    return {
        type: CLEAR_PLAYER,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_PLAYER,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_PLAYER,
        payload: data,
    };
};
