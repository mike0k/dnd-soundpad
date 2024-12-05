import React from 'react';
import { useSelector } from 'react-redux';
import * as R from 'ramda';
import { Scrollbars } from 'react-custom-scrollbars';

import Divider from '@material-ui/core/Divider';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import IconButton from '@material-ui/core/IconButton';
import { MdPlayArrow, MdPause } from 'react-icons/md';

import { makeStyles } from '@material-ui/core/styles';
import * as UPlaylist from 'util/Playlist';
import * as URoom from 'util/Room';
import * as UTool from 'util/Tool';
import Add from './add/Add';
import Item from './Item';

const PadPlaylist = () => {
    const classes = useStyles();
    const playlist = useSelector((state) => ({
        items: state.playlist.items,
        group: state.playlist.group,
        roomItems: state.room.playlist,
        isOp: state.room.isOp,
    }));
    const room = URoom.get();
    const group = UPlaylist.getGroup(playlist.group);

    const sorted = () => {
        const items = [];
        UTool.map(playlist.items, (item) => {
            items.push(item);
        });

        return R.sortBy(R.prop('sort'), items);
    };

    const onPlayGroup = () => {
        UPlaylist.playGroup(playlist.group);
    };

    return (
        <Grid container direction='column' className={classes.container}>
            <Grid item className={classes.row}>
                <Grid container className={classes.header}>
                    <Grid item style={{ flexGrow: 1 }}>
                        <Typography variant='h6'>{group.name}</Typography>
                    </Grid>
                    <Grid item>
                        <IconButton color='default' size='small' onClick={onPlayGroup}>
                            {group.play === 1 ? <MdPause /> : <MdPlayArrow />}
                        </IconButton>
                    </Grid>
                </Grid>
                <Divider />
            </Grid>
            <Grid item className={classes.grow}>
                <Scrollbars>
                    <Grid container spacing={5} className={classes.list}>
                        {UTool.map(sorted(), (item) => {
                            let show = playlist.group !== item.group ? false : true;
                            if (show && room.active) {
                                if (!item.isOp && !URoom.hasGroupAccess(item.group)) {
                                    show = false;
                                }
                            }
                            return (
                                <Grid
                                    item
                                    key={item.id}
                                    xs={12}
                                    lg={6}
                                    className={show ? classes.show : classes.hide}>
                                    {typeof item !== 'undefined' && <Item item={item} />}
                                </Grid>
                            );
                        })}
                        <Grid
                            item
                            xs={12}
                            lg={6}
                            className={group !== false ? classes.show : classes.hide}>
                            <Add />
                        </Grid>
                    </Grid>
                </Scrollbars>
            </Grid>
        </Grid>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        width: '100%',
        height: '100%',
        paddingBottom: theme.spacing(8),
    },
    list: {
        paddingTop: theme.spacing(2),
        width: '100%',
        //height: '100%',
    },
    hide: {
        display: 'none',
    },
    show: {},
    header: {
        padding: theme.spacing(2),
    },
    grow: {
        width: '100%',
        flexGrow: 1,
    },
    row: {
        width: '100%',
    },
}));

export default PadPlaylist;
