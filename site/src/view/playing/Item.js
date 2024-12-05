import React from 'react';
import { useSelector } from 'react-redux';

import Card from '@material-ui/core/Card';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import Typography from '@material-ui/core/Typography';

import { MdPlayArrow, MdPause } from 'react-icons/md';

import * as UPlaylist from 'util/Playlist';
import * as UPlayer from 'util/Player';
import * as URoom from 'util/Room';
import * as UTool from 'util/Tool';

const Item = ({ player }) => {
    const room = useSelector((state) => state.room);
    const playlist = UPlaylist.getItem(player.id);
    let isloading = player.status === 'load';
    let isPlaying = player.status === 'play';
    let isdisabled = false;

    if (room.active && player.stream) {
        isdisabled = !URoom.hasGroupAccess(playlist.group);
    }

    const onPause = () => {
        if (!isloading && !isdisabled) {
            UPlayer.toggle(player.id);
        }
    };

    return (
        <Card style={style.card}>
            <Grid container>
                <Grid item style={style.playContainer}>
                    <IconButton
                        onClick={onPause}
                        disabled={isloading || isdisabled}
                        style={style.play}
                        color={player.stream ? 'primary' : 'default'}>
                        {isloading && <CircularProgress />}
                        {!isloading && (isPlaying ? <MdPause /> : <MdPlayArrow />)}
                    </IconButton>
                </Grid>
                <Grid item style={style.grow}>
                    <Typography variant='subtitle1' color='textSecondary' style={style.title}>
                        {player.name.length > 40
                            ? player.name.substring(0, 37) + '...'
                            : player.name}
                    </Typography>
                    <Typography variant='subtitle2' color='textSecondary'>
                        {UTool.toDuration(player.playedSeconds)} /{' '}
                        {UTool.toDuration(player.duration)}
                    </Typography>
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
