import { CLEAR_USER, MERGE_USER, SET_USER } from '../types';
import * as R from 'ramda';

import { randomId } from 'util/Tool';
import { uniqueNamesGenerator, adjectives, colors, animals } from 'unique-names-generator';

const id = randomId();
const name = uniqueNamesGenerator({
    dictionaries: [colors, adjectives, animals],
    length: 3,
    separator: ' ',
});

const initialState = {
    id,
    loggedIn: false,
    name: name,
    mute: false,
    token: false,
    volume: 75,
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_USER:
            return R.clone(initialState);
        case MERGE_USER:
            return R.mergeDeepRight(state, action.payload);
        case SET_USER:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
