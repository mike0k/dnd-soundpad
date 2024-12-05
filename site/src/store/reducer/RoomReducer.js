import { CLEAR_ROOM, MERGE_ROOM, SET_ROOM } from '../types';
import * as R from 'ramda';

const initialState = {
    active: false,
    id: '',
    key: '',
    groups: {},
    isOp: false,
    op: '',
    playlist: {},
    play: {},
    users: {},
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_ROOM:
            return R.clone(initialState);
        case MERGE_ROOM:
            return R.mergeDeepRight(state, action.payload);
        case SET_ROOM:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
