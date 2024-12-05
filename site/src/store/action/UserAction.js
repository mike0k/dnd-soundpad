import { CLEAR_USER, MERGE_USER, SET_USER } from '../types';

export const clear = () => {
    return {
        type: CLEAR_USER,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_USER,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_USER,
        payload: data,
    };
};
