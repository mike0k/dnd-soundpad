import { CLEAR_PLAYER, MERGE_PLAYER, SET_PLAYER } from '../types';
import * as R from 'ramda';

const initialState = {
    items: {},
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_PLAYER:
            return R.clone(initialState);
        case MERGE_PLAYER:
            return R.mergeDeepRight(state, action.payload);
        case SET_PLAYER:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
