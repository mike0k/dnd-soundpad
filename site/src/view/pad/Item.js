import React from 'react';
import { useSelector } from 'react-redux';

import Card from '@material-ui/core/Card';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import Slider from '@material-ui/core/Slider';
import Typography from '@material-ui/core/Typography';

import { MdPlayArrow, MdPause, MdLoop, MdClose } from 'react-icons/md';

import * as UPlayer from 'util/Player';
import * as UPlaylist from 'util/Playlist';
import * as URoom from 'util/Room';
import * as UTool from 'util/Tool';
import LongPress from 'util/LongPress';

const Item = ({ item }) => {
    const { player, status } = useSelector((state) => ({
        player: state.player.items[item.id],
        status:
            typeof state.player.items[item.id] !== 'undefined'
                ? state.player.items[item.id].status
                : 'load',
    }));
    const [volume, setVolume] = React.useState(item.volume);
    const [loading, setLoading] = React.useState(false);
    const [confirmDelete, setConfirmDelete] = React.useState(0);
    const room = URoom.get();

    let hasAccess = item.isOp || URoom.hasGroupAccess(item.group);
    let isPlaying = false;
    let isLoading = loading;
    let playedSeconds = 0;
    if (status === 'play') {
        isPlaying = true;
        playedSeconds = player.playedSeconds;
        isLoading = false;
        if (loading) {
            setLoading(false);
        }
    }
    if (status === 'load') {
        isLoading = true;
        if (loading) {
            setLoading(false);
        }
    }

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
    const onVolume = (e, val) => {
        setVolume(val);
        UPlaylist.setItem(item.id, { volume: val });
    };

    const onPlayShort = () => {
        setLoading(true);
        if (room.active && hasAccess) {
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

    isLoading = false;

    return (
        <Card style={style.card}>
            <Grid container>
                <Grid item style={style.playContainer}>
                    <IconButton
                        {...onPlay}
                        disabled={isLoading}
                        style={style.play}
                        color={room.active && hasAccess ? 'primary' : 'default'}>
                        {isLoading && <CircularProgress />}
                        {!isLoading && (isPlaying ? <MdPause /> : <MdPlayArrow />)}
                    </IconButton>
                </Grid>
                <Grid item style={style.grow}>
                    <Grid container>
                        <Grid item style={style.titleOuter}>
                            <Typography
                                variant='subtitle1'
                                color='textSecondary'
                                style={style.title}>
                                {item.name}
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
                                disabled={!item.isOp}
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
                        onChange={onVolume}
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
        whiteSpace: 'nowrap',
        position: 'absolute',
        top: 0,
        bottom: 0,
        left: 0,
        right: 0,
    },
    titleOuter: {
        flexGrow: 1,
        overflow: 'hidden',
        position: 'relative',
    },
};

export default Item;
