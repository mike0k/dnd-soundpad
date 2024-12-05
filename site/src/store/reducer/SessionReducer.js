import { CLEAR_SESSION, MERGE_SESSION, SET_SESSION } from '../types';
import * as R from 'ramda';

const initialState = {
    active: '',
    items: {},
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_SESSION:
            return R.clone(initialState);
        case MERGE_SESSION:
            return R.mergeDeepRight(state, action.payload);
        case SET_SESSION:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
