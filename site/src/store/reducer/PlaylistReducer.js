import { CLEAR_PLAYLIST, MERGE_PLAYLIST, SET_PLAYLIST } from '../types';
import * as R from 'ramda';
import { randomId } from 'util/Tool';

const playlistId = randomId();
const groupId = randomId();

const initialState = {
    id: playlistId,
    name: 'Playlist',
    group: groupId,
    groups: {
        [groupId]: {
            id: groupId,
            access: 0,
            name: 'Group 1',
            sort: 0,
        },
    },
    items: {},
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_PLAYLIST:
            return R.clone(initialState);
        case MERGE_PLAYLIST:
            return R.mergeDeepRight(state, action.payload);
        case SET_PLAYLIST:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
