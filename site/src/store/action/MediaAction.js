import { CLEAR_MEDIA, MERGE_MEDIA, SET_MEDIA } from '../types';

export const clear = () => {
    return {
        type: CLEAR_MEDIA,
    };
};

export const merge = (data) => {
    return {
        type: MERGE_MEDIA,
        payload: data,
    };
};

export const set = (data) => {
    return {
        type: SET_MEDIA,
        payload: data,
    };
};
