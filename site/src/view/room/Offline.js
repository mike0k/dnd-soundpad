import React from 'react';
import { useSelector } from 'react-redux';

import { makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Divider from '@material-ui/core/Divider';
import Grid from '@material-ui/core/Grid';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import Typography from '@material-ui/core/Typography';
import { GoRadioTower } from 'react-icons/go';
import FaceIcon from '@material-ui/icons/Face';

import * as ULayout from 'util/Layout';
import * as URoom from 'util/Room';

const Offline = () => {
    const classes = useStyles();
    const name = useSelector((state) => state.user.name);

    const onAccount = () => {
        ULayout.set({ account: true });
    };

    const onHost = () => {
        URoom.host();
    };

    return (
        <React.Fragment>
            <Grid
                container
                direction='column'
                justify='flex-start'
                alignItems='center'
                className={classes.container}>
                <Grid item className={classes.grow}>
                    <Typography variant='h6' align='center' paragraph>
                        OFFLINE
                    </Typography>
                    <Divider />
                    <List className={classes.list}>
                        <ListItem dense button selected={true} onClick={onAccount}>
                            <ListItemIcon>
                                <FaceIcon />
                            </ListItemIcon>
                            <ListItemText primary={name} />
                        </ListItem>
                    </List>
                </Grid>
                <Grid item className={classes.row}>
                    <Button
                        variant='outlined'
                        color='primary'
                        startIcon={<GoRadioTower />}
                        fullWidth
                        onClick={onHost}>
                        Host Stream
                    </Button>
                </Grid>
            </Grid>
        </React.Fragment>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        height: '100%',
        paddingTop: theme.spacing(2),
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
        padding: theme.spacing(1),
    },
}));

export default Offline;
