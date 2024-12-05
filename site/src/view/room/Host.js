import React from 'react';
import { Scrollbars } from 'react-custom-scrollbars';

import { makeStyles } from '@material-ui/core/styles';
import Button from '@material-ui/core/Button';
import Divider from '@material-ui/core/Divider';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';
import { IconButton, Card } from '@material-ui/core';

import { GoRadioTower, GoLink } from 'react-icons/go';

import * as URoom from 'util/Room';
import User from './User';

const Host = () => {
    const classes = useStyles();
    const room = URoom.get();
    const urlRef = React.useRef();
    const [confirmDelete, setConfirmDelete] = React.useState(0);

    const onStop = () => {
        if (confirmDelete === 1) {
            URoom.hostEnd();
        } else {
            setConfirmDelete(1);
            setTimeout(() => {
                setConfirmDelete(0);
            }, 1000);
        }
    };

    const onCopyLink = () => {
        urlRef.current.select();
        document.execCommand('copy');
    };
    const url = window.location.protocol + '//' + window.location.host + '?channel=' + room.id;

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
                        STREAMING
                    </Typography>
                    <Divider />
                    <Scrollbars>
                        <User />
                    </Scrollbars>
                </Grid>
                <Grid item className={classes.row}>
                    <Card className={classes.url}>
                        <input ref={urlRef} value={url} className={classes.urlText} readOnly />
                        <IconButton
                            color='default'
                            size='small'
                            onClick={onCopyLink}
                            className={classes.urlBtn}>
                            <GoLink />
                        </IconButton>
                    </Card>
                    <Button
                        variant='outlined'
                        color={confirmDelete === 1 ? 'primary' : 'default'}
                        startIcon={<GoRadioTower />}
                        fullWidth
                        onClick={onStop}>
                        Stop
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
    row: {
        width: '100%',
        padding: theme.spacing(1),
    },
    url: {
        display: 'flex',
        marginBottom: theme.spacing(2),
        padding: theme.spacing(1),
    },
    urlText: {
        backgroundColor: 'transparent',
        flexGrow: 1,
        marginRight: theme.spacing(2),
        color: theme.palette.text.primary,
        border: 'none !important',
    },
}));

export default Host;
