import React from 'react';
import { useSelector } from 'react-redux';

import { makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Drawer from '@material-ui/core/Drawer';
import Grid from '@material-ui/core/Grid';
import TextField from '@material-ui/core/TextField';

import * as ULayout from 'util/Layout';
import * as UUser from 'util/User';
import * as URoom from 'util/Room';

const Account = () => {
    const classes = useStyles();
    const layout = useSelector((state) => ({
        toggle: state.layout.account,
        name: state.user.name,
    }));
    const [confirmDelete, setConfirmDelete] = React.useState(0);
    const [name, setName] = React.useState(layout.name);
    const [changed, setChanged] = React.useState(false);

    const onClose = () => {
        ULayout.set({ account: false });
        if (changed) {
            UUser.set({ name });
            const room = URoom.get();
            if (room.active) {
                URoom.loadRoom();
            }
        }
    };

    const onSetName = (e) => {
        setName(e.target.value);
        setChanged(true);
    };

    const onReset = () => {
        if (confirmDelete === 1) {
            UUser.reset();
        } else {
            setConfirmDelete(1);
            setTimeout(() => {
                setConfirmDelete(0);
            }, 1000);
        }
    };

    return (
        <Drawer classes={{ paper: classes.drawer }} open={layout.toggle} onClose={onClose}>
            <Grid container direction='column' className={classes.container} spacing={2}>
                <Grid item className={classes.grow}>
                    <TextField
                        label='Name'
                        value={name}
                        onChange={onSetName}
                        variant='outlined'
                        fullWidth
                    />
                </Grid>
                <Grid item className={classes.row}>
                    <Button
                        variant='outlined'
                        color={confirmDelete === 1 ? 'primary' : 'default'}
                        onClick={onReset}
                        fullWidth>
                        Reset
                    </Button>
                </Grid>
            </Grid>
        </Drawer>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        height: '100%',
        padding: theme.spacing(2) + 'px ' + theme.spacing(1) + 'px',
        margin: 0,
        width: 300,
    },
    grow: {
        flexGrow: 1,
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
    row: {
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
}));

export default Account;
