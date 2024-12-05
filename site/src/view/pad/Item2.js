import React from 'react';
import { useSelector } from 'react-redux';

import Card from '@material-ui/core/Card';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import Slider from '@material-ui/core/Slider';
import Typography from '@material-ui/core/Typography';

import { MdPlayArrow, MdPause, MdLoop, MdClose } from 'react-icons/md';

import * as UMedia from 'util/Media';
import * as UPlayer from 'util/Player';
import * as UPlaylist from 'util/Playlist';
import * as URoom from 'util/Room';
import * as UTool from 'util/Tool';
import LongPress from 'util/LongPress';

const Item = ({ item }) => {
    const pLoacl = useSelector((state) => state.player.items[item.id]);
    const room = useSelector((state) => state.room);
    const roomItem = URoom.getItem(item.id);
    const pRoom = useSelector((state) => state.player.items[roomItem.id]);
    const [volume, setVolume] = React.useState(item.volume);
    const [loading, setLoading] = React.useState(true);
    const [confirmDelete, setConfirmDelete] = React.useState(0);
    let hasGroupAccess = URoom.hasGroupAccess(item.group);
    let isloading = loading;
    let isPlaying = false;
    let isPlayingStream = room.active;
    let playedSeconds = 0;

    const onChangeVolume = (e, val) => {
        setVolume(val);
        UPlaylist.setItem(item.id, { volume: val });
    };
    const onDelete = () => {
        if (confirmDelete === 1) {
            UPlaylist.remove(item.id);
        } else {
            setConfirmDelete(1);
            setTimeout(() => {
                setConfirmDelete(0);
            }, 1000);
        }
    };
    const onLoop = () => {
        UPlaylist.setItem(item.id, { loop: !item.loop });
    };

    const onPlayShort = () => {
        setLoading(true);
        if (pRoom !== false && room.active && (room.isOp || hasGroupAccess)) {
            UPlayer.toggle(item.id, 'room');
        } else {
            UPlayer.toggle(item.id, 'local');
        }
    };
    const onPlayLong = () => {
        setLoading(true);
        UPlayer.toggle(item.id, 'local');
    };
    const onPlay = LongPress(onPlayShort, onPlayLong);

    if (room.active && typeof pRoom !== 'undefined' && pRoom.status === 'load') {
        isloading = true;
    } else if (typeof pLoacl !== 'undefined' && pLoacl.status === 'load') {
        isloading = true;
    } else {
        isloading = false;
    }

    if (typeof pRoom !== 'undefined' && pRoom.status === 'play') {
        isPlaying = true;
        isPlayingStream = true;
        playedSeconds = pRoom.playedSeconds;
    } else if (typeof pLoacl !== 'undefined' && pLoacl.status === 'play') {
        isPlaying = true;
        isPlayingStream = false;
        playedSeconds = pLoacl.playedSeconds;
    }
    if (isloading && loading) {
        setLoading(false);
    }

    return (
        <Card style={style.card}>
            <Grid container>
                <Grid item style={style.playContainer}>
                    <IconButton
                        {...onPlay}
                        disabled={isloading}
                        style={style.play}
                        color={
                            isPlayingStream && (room.isOp || hasGroupAccess) ? 'primary' : 'default'
                        }>
                        {isloading && <CircularProgress />}
                        {!isloading && (isPlaying ? <MdPause /> : <MdPlayArrow />)}
                    </IconButton>
                </Grid>
                <Grid item style={style.grow}>
                    <Grid container>
                        <Grid item style={style.grow}>
                            <Typography
                                variant='subtitle1'
                                color='textSecondary'
                                style={style.title}>
                                {item.id}
                                {typeof item.name !== 'undefined' &&
                                    (item.name.length > 40
                                        ? item.name.substring(0, 37) + '...'
                                        : item.name)}
                            </Typography>
                        </Grid>
                        <Grid item>
                            <Typography
                                variant='subtitle2'
                                color='textSecondary'
                                style={style.duration}>
                                {UTool.toDuration(playedSeconds)} /{' '}
                                {UTool.toDuration(item.duration)}
                            </Typography>
                        </Grid>
                        <Grid item>
                            <IconButton
                                onClick={onDelete}
                                disabled={hasGroupAccess}
                                color={confirmDelete === 1 ? 'primary' : 'default'}
                                size='small'>
                                <MdClose />
                            </IconButton>
                            <IconButton
                                onClick={onLoop}
                                color={item.loop ? 'primary' : 'default'}
                                size='small'>
                                <MdLoop />
                            </IconButton>
                        </Grid>
                    </Grid>

                    <Slider
                        value={volume}
                        onChange={onChangeVolume}
                        step={5}
                        min={0}
                        max={100}
                        valueLabelDisplay='auto'
                    />
                </Grid>
            </Grid>
        </Card>
    );
};

const style = {
    card: {
        padding: '8px 16px',
    },
    controls: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    duration: {
        lineHeight: 1.7,
    },
    grow: {
        flexGrow: 1,
    },
    play: {
        marginRight: 16,
    },
    playContainer: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    title: {
        maxHeight: 30,
        overflow: 'hidden',
    },
};

export default Item;
