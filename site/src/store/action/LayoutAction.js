import { CLEAR_LAYOUT, MERGE_LAYOUT, SET_LAYOUT } from '../types';

export const clear = () => {
    return {
        type: CLEAR_LAYOUT,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_LAYOUT,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_LAYOUT,
        payload: data,
    };
};
