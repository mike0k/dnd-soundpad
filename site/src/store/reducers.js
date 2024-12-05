import { combineReducers } from 'redux';

import ErrorReducer from './reducer/ErrorReducer';
import LayoutReducer from './reducer/LayoutReducer';
import MediaReducer from './reducer/MediaReducer';
import PlayerReducer from './reducer/PlayerReducer';
import PlaylistReducer from './reducer/PlaylistReducer';
import RoomReducer from './reducer/RoomReducer';
import SessionReducer from './reducer/SessionReducer';
import UserReducer from './reducer/UserReducer';

export default combineReducers({
    error: ErrorReducer,
    layout: LayoutReducer,
    media: MediaReducer,
    player: PlayerReducer,
    playlist: PlaylistReducer,
    room: RoomReducer,
    session: SessionReducer,
    user: UserReducer,
});
