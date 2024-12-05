import React from 'react';
import { useSelector } from 'react-redux';
import * as R from 'ramda';
import { Scrollbars } from 'react-custom-scrollbars';

import { makeStyles } from '@material-ui/core/styles';
import Divider from '@material-ui/core/Divider';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Typography from '@material-ui/core/Typography';
import { MdLock, MdLockOpen, MdPublic, MdSort, MdAdd } from 'react-icons/md';
import { GoRadioTower } from 'react-icons/go';

import * as UPlaylist from 'util/Playlist';
import * as URoom from 'util/Room';
import * as UTool from 'util/Tool';
import Group from './Group';
import GroupSort from './GroupSort';
import Sessions from './Sessions';

const PadMedia = () => {
    const classes = useStyles();
    const playlist = useSelector((state) => ({
        name: state.playlist.name,
        group: state.playlist.group,
        groups: state.playlist.groups,
        roomGroups: state.room.groups,
        isOp: state.room.isOp,
    }));
    const [openGroup, setOpenGroup] = React.useState(false);
    const [openSort, setOpenSort] = React.useState(false);
    const [openSession, setOpenSession] = React.useState(false);

    const onAdd = () => {
        UPlaylist.addGroup();
    };

    const onView = (id) => {
        if (id === playlist.group) {
            setOpenGroup(true);
        } else {
            UPlaylist.viewGroup(id);
        }
    };

    const onViewRoom = (id) => {
        UPlaylist.viewGroup(id);
    };

    const sorted = () => {
        const groups = [];
        UTool.map(playlist.groups, (item) => {
            groups.push(item);
        });
        return R.sortBy(R.prop('sort'), groups);
    };

    const sortedRoom = () => {
        const groups = [];
        UTool.map(playlist.roomGroups, (item) => {
            if (!playlist.isOp && URoom.hasGroupAccess(item.id)) {
                groups.push(item);
            }
        });

        return R.sortBy(R.prop('name'), groups);
    };

    const accessIcons = [<MdLock />, <MdLockOpen />, <MdPublic />];

    return (
        <React.Fragment>
            <div className={classes.drawer}>
                <Grid
                    container
                    direction='column'
                    alignItems='center'
                    className={classes.container}>
                    <Grid item className={classes.row}>
                        <Grid container className={classes.header}>
                            <Grid item style={{ flexGrow: 1 }} onClick={() => setOpenSession(true)}>
                                <Typography variant='h6'>{playlist.name}</Typography>
                            </Grid>
                            <Grid item>
                                <IconButton
                                    color='default'
                                    size='small'
                                    onClick={() => setOpenSort(true)}>
                                    <MdSort />
                                </IconButton>
                            </Grid>
                        </Grid>
                    </Grid>
                    <Grid item className={classes.grow}>
                        <Divider />
                        <Scrollbars>
                            <List className={classes.list}>
                                {UTool.map(sortedRoom(), (item) => {
                                    return (
                                        <ListItem
                                            button
                                            key={item.id}
                                            onClick={() => onViewRoom(item.id)}
                                            selected={playlist.group === item.id}>
                                            <ListItemIcon>
                                                <GoRadioTower />
                                            </ListItemIcon>
                                            <ListItemText primary={item.name} />
                                        </ListItem>
                                    );
                                })}
                                {UTool.map(sorted(), (item) => {
                                    return (
                                        <ListItem
                                            button
                                            key={item.id}
                                            onClick={() => onView(item.id)}
                                            selected={playlist.group === item.id}>
                                            <ListItemIcon>{accessIcons[item.access]}</ListItemIcon>
                                            <ListItemText primary={item.name} />
                                        </ListItem>
                                    );
                                })}
                                <ListItem button onClick={onAdd}>
                                    <ListItemIcon>
                                        <MdAdd />
                                    </ListItemIcon>
                                    <ListItemText primary='Add Group' />
                                </ListItem>
                            </List>
                        </Scrollbars>
                    </Grid>
                </Grid>
            </div>
            <Group id={playlist.group} open={openGroup} setOpen={setOpenGroup} />
            <GroupSort open={openSort} setOpen={setOpenSort} />
            <Sessions open={openSession} setOpen={setOpenSession} />
        </React.Fragment>
    );
};

const useStyles = makeStyles((theme) => ({
    drawer: {
        backgroundColor: theme.palette.secondary.paper,
        position: 'fixed',
        top: 0,
        bottom: 0,
        left: 0,
        width: 250,
        borderRight: '1px solid ' + theme.palette.action.disabledBackground,
        paddingBottom: theme.spacing(8),
    },
    container: {
        width: 250,
        height: '100%',
    },
    header: {
        padding: theme.spacing(2),
    },
    grow: {
        width: '100%',
        flexGrow: 1,
    },
    list: {
        paddingTop: 0,
    },
    row: {
        width: '100%',
    },
}));

export default PadMedia;
