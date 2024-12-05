import React from 'react';
import { useSelector } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';

import { makeStyles } from '@material-ui/core/styles';
import Divider from '@material-ui/core/Divider';
import Grid from '@material-ui/core/Grid';
import Typography from '@material-ui/core/Typography';

import PlayerItem from './Item';
import * as UTool from 'util/Tool';

const Player = () => {
    const player = useSelector((state) => state.player);
    const classes = useStyles();

    return (
        <Grid container direction='column' className={classes.container}>
            <Grid item className={classes.row}>
                <Grid container className={classes.header}>
                    <Grid item style={{ flexGrow: 1 }}>
                        <Typography variant='h6' align='center'>
                            PLAYING
                        </Typography>
                    </Grid>
                </Grid>

                <Divider />
            </Grid>
            <Grid item className={classes.grow}>
                <Scrollbars>
                    {UTool.map(player.items, (item, key, i) => {
                        return (
                            <React.Fragment key={i}>
                                {typeof item !== 'undefined' && item.status === 'play' && (
                                    <PlayerItem player={item} />
                                )}
                            </React.Fragment>
                        );
                    })}
                </Scrollbars>
            </Grid>
        </Grid>
    );
};

const useStyles = makeStyles((theme) => ({
    header: {
        padding: theme.spacing(2),
    },
    container: {
        width: '100%',
        height: '100%',
        //paddingBottom: theme.spacing(8),
    },
    grow: {
        width: '100%',
        flexGrow: 1,
    },
    row: {
        width: '100%',
    },
}));

export default Player;
