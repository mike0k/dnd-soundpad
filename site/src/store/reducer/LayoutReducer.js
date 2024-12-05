import { CLEAR_LAYOUT, MERGE_LAYOUT, SET_LAYOUT } from '../types';
import * as R from 'ramda';

const initialState = {
    account: false,
    sidebar: false,
    sidebarTab: 'player',
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_LAYOUT:
            return R.clone(initialState);
        case MERGE_LAYOUT:
            return R.mergeDeepRight(state, action.payload);
        case SET_LAYOUT:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
