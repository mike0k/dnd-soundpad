import React from 'react';
import { useSelector } from 'react-redux';
import * as R from 'ramda';

import PlayerItem from './Item';
import * as UPlayer from 'util/Player';
import * as UTool from 'util/Tool';

const Player = () => {
    // const user = useSelector((state) => ({
    //     mute: state.user.mute,
    //     volume: state.user.volume,
    // }));
    const player = useSelector((state) => state.player);
    const [init, setInit] = React.useState(true);

    const playlist = useSelector((state) => state.playlist.items);
    const [playlistChange, setPlaylistChange] = React.useState(R.clone(playlist));

    const room = useSelector((state) => state.room);
    const [roomChange, setRoomChange] = React.useState(R.clone(room));

    React.useEffect(() => {
        if (init) {
            setInit(false);
            UPlayer.init();
            UPlayer.load();
        } else {
            if (JSON.stringify(playlist) !== JSON.stringify(playlistChange)) {
                setPlaylistChange(R.clone(playlist));
                UPlayer.loadPlaylist();
            }
            if (JSON.stringify(room) !== JSON.stringify(roomChange)) {
                setRoomChange(R.clone(room));
                UPlayer.loadRoom();
            }
        }
    }, [init, playlist, room, playlistChange, roomChange]);

    return (
        <React.Fragment>
            {UTool.map(player.items, (item, key, i) => {
                return (
                    <React.Fragment key={i}>
                        {typeof item !== 'undefined' && <PlayerItem item={item} />}
                    </React.Fragment>
                );
            })}
        </React.Fragment>
    );
};

//const style = {};

export default Player;
