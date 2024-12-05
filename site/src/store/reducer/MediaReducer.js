import { CLEAR_MEDIA, MERGE_MEDIA, SET_MEDIA } from '../types';
import * as R from 'ramda';

const initialState = {
    items: {
        // test1: {
        //     id: 'test1',
        //     name: 'Youtube Rain',
        //     type: 'youtube',
        //     url:
        //         'https://www.youtube.com/watch?v=qTBHE2_dJeI&list=PLLfnxMdjx5KNTQWKlrVhxGEx7YcLjBTGp&index=2',
        // },

        test2: {
            id: 'test2',
            name: 'Wildmount',
            type: 'drive',
            url: 'https://docs.google.com/uc?export=download&id=1Q-JFT-DVDni9CltDw4ZkSkzDKov7o1sG',
        },
    },
};

export default function (state = initialState, action) {
    switch (action.type) {
        case CLEAR_MEDIA:
            return R.clone(initialState);
        case MERGE_MEDIA:
            return R.mergeDeepRight(state, action.payload);
        case SET_MEDIA:
            return { ...state, ...action.payload };
        default:
            return state;
    }
}
