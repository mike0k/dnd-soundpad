import React from 'react';
import { useSelector } from 'react-redux';

import * as URoom from 'util/Room';
import Offline from './Offline';
import Host from './Host';
import Join from './Join';

const Room = () => {
    useSelector((state) => state.room);

    return (
        <React.Fragment>
            {URoom.getStatus() === 'offline' && <Offline />}
            {URoom.getStatus() === 'host' && <Host />}
            {URoom.getStatus() === 'join' && <Join />}
        </React.Fragment>
    );
};

//const style = {};

export default Room;
