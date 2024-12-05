import React from 'react';
import * as R from 'ramda';

import { makeStyles } from '@material-ui/core/styles';
import Divider from '@material-ui/core/Divider';
import Drawer from '@material-ui/core/Drawer';
import Grid from '@material-ui/core/Grid';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Typography from '@material-ui/core/Typography';
import { MdCheck, MdRemove } from 'react-icons/md';

import * as URoom from 'util/Room';
import * as UPlaylist from 'util/Playlist';
import * as UTool from 'util/Tool';

const Access = ({ open, setOpen }) => {
    const classes = useStyles();
    const [access, setAccess] = React.useState([]);
    const user = URoom.getUser(open);

    React.useEffect(() => {
        if (open !== false) {
            const user = URoom.getUser(open);
            if (user !== false && typeof user.access !== 'undefined') {
                setAccess([...user.access]);
            }
        }
    }, [open]);

    const onClose = () => {
        URoom.setUserAccess(open, access);
        setOpen(false);
    };

    const sorted = (accessLvl) => {
        const groups = [];
        UTool.map(UPlaylist.get().groups, (item) => {
            if (item.access === accessLvl) {
                groups.push(item);
            }
        });

        return R.sortBy(R.prop('sort'), groups);
    };
    const access1 = sorted(1);
    const access2 = sorted(2);

    const toggleAccess = (id) => {
        const index = access.indexOf(id);
        if (index !== -1) {
            access.splice(index, 1);
        } else {
            access.push(id);
        }
        setAccess([...access]);
    };

    return (
        <Drawer open={open !== false} onClose={onClose} className={classes.drawer}>
            <Grid container direction='column' className={classes.container}>
                <Grid item className={classes.row}>
                    <Typography variant='h6'>{user !== false && user.name}</Typography>
                </Grid>
                <Grid item className={classes.grow}>
                    <Divider />
                    <Typography className={classes.label} variant='subtitle1'>
                        Share
                    </Typography>
                    <List style={{ paddingTop: 0 }}>
                        {UTool.map(access1, (item, i) => {
                            const hasAccess = access.includes(item.id);
                            return (
                                <ListItem
                                    button
                                    key={i}
                                    dense
                                    onClick={() => toggleAccess(item.id)}>
                                    <ListItemIcon>
                                        {hasAccess ? <MdCheck /> : <MdRemove />}
                                    </ListItemIcon>
                                    <ListItemText primary={item.name} />
                                </ListItem>
                            );
                        })}
                        {access1.length === 0 && (
                            <ListItem dense>
                                <ListItemIcon>
                                    <MdRemove />
                                </ListItemIcon>
                                <ListItemText primary='No Limited Groups' />
                            </ListItem>
                        )}
                    </List>

                    <Divider />
                    <Typography className={classes.label} variant='subtitle1'>
                        Public
                    </Typography>
                    <List style={{ paddingTop: 0 }}>
                        {UTool.map(access2, (item, i) => (
                            <ListItem key={i} dense>
                                <ListItemIcon>
                                    <MdCheck />
                                </ListItemIcon>
                                <ListItemText primary={item.name} />
                            </ListItem>
                        ))}
                        {access2.length === 0 && (
                            <ListItem dense>
                                <ListItemIcon>
                                    <MdRemove />
                                </ListItemIcon>
                                <ListItemText primary='No Public Groups' />
                            </ListItem>
                        )}
                    </List>
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

export default Access;
