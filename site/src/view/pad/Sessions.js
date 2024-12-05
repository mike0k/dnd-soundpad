import React from 'react';
import { useSelector } from 'react-redux';

import { makeStyles } from '@material-ui/core/styles';
import Drawer from '@material-ui/core/Drawer';
import Grid from '@material-ui/core/Grid';
import IconButton from '@material-ui/core/IconButton';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemSecondaryAction from '@material-ui/core/ListItemSecondaryAction';
import ListItemText from '@material-ui/core/ListItemText';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';
import { MdAdd, MdDelete } from 'react-icons/md';

import * as UPlaylist from 'util/Playlist';
import * as USession from 'util/Session';
import * as UTool from 'util/Tool';

import 'react-sortable-tree/style.css';

const Sessions = ({ open, setOpen }) => {
    const classes = useStyles();
    const session = useSelector((state) => state.session);
    const [name, setName] = React.useState(UPlaylist.get().name);
    const [confirmDelete, setConfirmDelete] = React.useState(0);

    const onDelete = (id) => {
        if (confirmDelete === id) {
            USession.remove(id);
        } else {
            setConfirmDelete(id);
            setTimeout(() => {
                setConfirmDelete(0);
            }, 1000);
        }
    };

    const onLoad = (id) => {
        USession.load(id);
    };

    const onNew = () => {
        USession.add();
    };

    const onSetName = (e) => {
        setName(e.target.value);
        UPlaylist.update({ name: e.target.value });
    };

    return (
        <Drawer open={open} onClose={() => setOpen(false)} className={classes.drawer}>
            <Grid container direction='column' className={classes.container}>
                <Grid item className={classes.row}>
                    <TextField
                        label='Name'
                        value={name}
                        onChange={onSetName}
                        variant='outlined'
                        fullWidth
                    />
                </Grid>
                <Grid item className={classes.grow}>
                    <List>
                        {UTool.map(session.items, (item) => (
                            <ListItem
                                key={item.id}
                                button
                                selected={session.active === item.id}
                                onClick={() => onLoad(item.id)}>
                                <ListItemText primary={item.name} />
                                <ListItemSecondaryAction>
                                    <IconButton
                                        edge='end'
                                        color={confirmDelete === item.id ? 'primary' : 'default'}
                                        onClick={() => onDelete(item.id)}>
                                        <MdDelete />
                                    </IconButton>
                                </ListItemSecondaryAction>
                            </ListItem>
                        ))}
                    </List>
                </Grid>
                <Grid item className={classes.row}>
                    <Button
                        variant='outlined'
                        color='default'
                        startIcon={<MdAdd />}
                        fullWidth
                        onClick={onNew}>
                        New Playlist
                    </Button>
                </Grid>
            </Grid>
        </Drawer>
    );
};

const useStyles = makeStyles((theme) => ({
    drawer: {
        width: 250,
    },
    container: {
        width: '100%',
        height: '100%',
        padding: theme.spacing(2) + 'px ' + theme.spacing(1) + 'px',
        margin: 0,
    },
    grow: {
        flexGrow: 1,
        width: '100%',
    },
    row: {
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
    title: {
        borderBottom: '1px dashed' + theme.palette.secondary.main,
        padding: theme.spacing(1),
    },
}));

export default Sessions;
