import React from 'react';
import { useSelector } from 'react-redux';

import { makeStyles } from '@material-ui/core/styles';
import Drawer from '@material-ui/core/Drawer';
import Grid from '@material-ui/core/Grid';
import BottomNavigation from '@material-ui/core/BottomNavigation';
import BottomNavigationAction from '@material-ui/core/BottomNavigationAction';
import { useTheme } from '@material-ui/core/styles';
import useMediaQuery from '@material-ui/core/useMediaQuery';

import { MdPlayArrow } from 'react-icons/md';
import { GoRadioTower } from 'react-icons/go';

import Room from '../room/Room';
import Playing from '../playing/Playing';
import * as ULayout from 'util/Layout';

const Sidebar = ({ children }) => {
    const classes = useStyles();
    const layout = useSelector((state) => ({
        toggle: state.layout.sidebar,
        tab: state.layout.sidebarTab,
    }));
    let open = true;

    const theme = useTheme();
    const mobile = useMediaQuery(theme.breakpoints.down('sm'));
    if (mobile) {
        open = layout.toggle;
    }

    const onClose = () => {
        if (mobile) {
            ULayout.set({ sidebar: false });
        }
    };

    return (
        <Drawer
            classes={{ paper: classes.drawer }}
            open={open}
            variant={mobile ? 'temporary' : 'persistent'}
            anchor='right'
            onClose={onClose}>
            <Grid container direction='column' className={classes.container}>
                <Grid item className={classes.grow}>
                    {layout.tab === 'player' && <Playing />}
                    {layout.tab === 'room' && <Room />}
                </Grid>
                <Grid item className={classes.row}>
                    <BottomNavigation
                        className={classes.bottomNav}
                        value={layout.tab}
                        onChange={(event, newValue) => {
                            ULayout.set({ sidebarTab: newValue });
                        }}
                        showLabels>
                        <BottomNavigationAction
                            label='Player'
                            value='player'
                            icon={<MdPlayArrow />}
                        />
                        <BottomNavigationAction
                            label='Channel'
                            value='room'
                            icon={<GoRadioTower />}
                        />
                    </BottomNavigation>
                </Grid>
            </Grid>
        </Drawer>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        width: 300,
        height: '100%',
    },
    drawer: {
        border: 'none',
    },
    grow: {
        width: '100%',
        flexGrow: 1,
        borderLeft: '1px solid ' + theme.palette.action.disabledBackground,
    },
    row: {
        width: '100%',
        borderLeft: '1px dashed ' + theme.palette.secondary.main,
    },
    bottomNav: {
        height: 'auto',
        backgroundColor: theme.palette.secondary.main,
        minHeight: 64,
    },
}));

export default Sidebar;
