import React from 'react';
import ReactPlayer from 'react-player';
import Moment from 'moment';

import * as UPlayer from 'util/Player';
import * as UPlaylist from 'util/Playlist';
import * as UUser from 'util/User';

const Item = ({ item }) => {
    const refEl = React.useRef();
    const user = UUser.get();
    const [ready, setReady] = React.useState(false);

    let play = false;
    if (ready && item.play > 0 && (item.status === 'play' || item.status === 'ready')) {
        play = true;
        if (!item.loop && item.played >= item.play) {
            play = false;
        }
    }

    let volume = item.volume / 100;
    volume = (volume * user.volume) / 100;

    const defaultOpt = {
        url: item.url,
        loop: item.loop,
        playing: play,
        volume: volume,
        muted: user.mute,

        onReady: () => {
            setReady(true);
            if (item.status !== 'play' || !item.loop) {
                UPlayer.setItem(item.id, { status: 'ready', play: 0 });
            }
        },
        onPlay: () => {
            console.log('onPlay', item.name);
            const now = Moment().unix();
            const then = item.started;
            if (then !== false && then < now) {
                const seek = now - then;
                if (seek > 5) {
                    refEl.current.seekTo(seek);
                }
            }
            UPlayer.setItem(item.id, {
                status: 'play',
                played: 0,
                //started: now,
            });
        },
        onDuration: (seconds) => {
            if (item.duration === 0) {
                UPlayer.setItem(item.id, { duration: seconds });
                if (UPlaylist.getItem(item.id) !== false) {
                    UPlaylist.setItem(item.id, { duration: seconds });
                }
            }
        },
        onProgress: ({ playedSeconds, loadedSeconds }) => {
            UPlayer.setItem(item.id, { playedSeconds });
        },
        onPause: () => {
            console.log('onPause', item.name);
            UPlayer.pause(item.id);
            refEl.current.seekTo(0);
        },
        onEnded: () => {
            console.log('onEnded', item.name);
            UPlayer.pause(item.id);
            refEl.current.seekTo(0);
        },
        onError: () => {
            //UPlayer.remove(item.id);
        },
    };

    return (
        <div key={item.id} style={style.playerContainer}>
            {<ReactPlayer ref={refEl} {...defaultOpt} />}
        </div>
    );
};

const style = {
    playerContainer: {
        display: 'none',
    },
};

export default Item;
