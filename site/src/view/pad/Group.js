import React from 'react';

import { makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Divider from '@material-ui/core/Divider';
import Drawer from '@material-ui/core/Drawer';
import Grid from '@material-ui/core/Grid';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';
import { MdLock, MdLockOpen, MdPublic } from 'react-icons/md';

import * as UPlaylist from 'util/Playlist';
import * as UTool from 'util/Tool';

const Group = ({ id, open, setOpen }) => {
    const classes = useStyles();
    const [confirmDelete, setConfirmDelete] = React.useState(0);
    const [name, setName] = React.useState();
    const [access, setAccess] = React.useState(0);

    React.useEffect(() => {
        if (open) {
            const group = UPlaylist.getGroup(id);
            if (group !== false) {
                setName(group.name);
                setAccess(group.access);
            }
        }
    }, [open, id]);

    const onClose = () => {
        setOpen(false);
        UPlaylist.setGroup(id, { name, access });
    };

    const onDelete = () => {
        if (confirmDelete === 1) {
            onClose();
            UPlaylist.removeGroup(id);
        } else {
            setConfirmDelete(1);
            setTimeout(() => {
                setConfirmDelete(0);
            }, 1000);
        }
    };

    const listAccess = [
        { id: 0, name: 'Private', desc: 'Only you can access this group', icon: <MdLock /> },
        {
            id: 1,
            name: 'Limited',
            desc: 'Only people you select on your stream can access this group',
            icon: <MdLockOpen />,
        },
        {
            id: 2,
            name: 'Public',
            desc: 'Everyone on your stream can access this group',
            icon: <MdPublic />,
        },
    ];

    return (
        <Drawer open={open} onClose={onClose} className={classes.drawer}>
            <Grid container direction='column' className={classes.container}>
                <Grid item className={classes.row}>
                    <TextField
                        label='Name'
                        value={name}
                        onChange={(e) => setName(e.target.value)}
                        variant='outlined'
                        fullWidth
                    />
                </Grid>
                <Grid item className={classes.grow}>
                    <Divider />
                    <Typography className={classes.label} variant='subtitle1'>
                        Sharing
                    </Typography>
                    <List style={{ paddingTop: 0 }}>
                        {UTool.map(listAccess, (item, i) => (
                            <ListItem
                                button
                                key={i}
                                dense
                                selected={access === item.id}
                                onClick={() => setAccess(item.id)}>
                                <ListItemIcon>{item.icon}</ListItemIcon>
                                <ListItemText primary={item.name} secondary={item.desc} />
                            </ListItem>
                        ))}
                    </List>
                </Grid>
                <Grid item className={classes.row}>
                    <Button
                        variant='outlined'
                        color={confirmDelete === 1 ? 'primary' : 'default'}
                        onClick={onDelete}
                        fullWidth>
                        Delete
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
        minWidth: 300,
    },
    grow: {
        flexGrow: 1,
        width: '100%',
        paddingTop: theme.spacing(2),
    },
    label: {
        paddingTop: theme.spacing(1),
    },
    row: {
        width: '100%',
    },
    title: {
        borderBottom: '1px dashed' + theme.palette.secondary.main,
        padding: theme.spacing(1),
    },
}));

export default Group;
