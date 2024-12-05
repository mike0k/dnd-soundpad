import React from 'react';

import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import { makeStyles } from '@material-ui/core/styles';

import { GoRadioTower } from 'react-icons/go';

import Volume from './Volume';
import * as ULayout from 'util/Layout';

const Header = ({ onToggle }) => {
    const classes = useStyles();

    const onSidebar = () => {
        const sidebar = ULayout.get().sidebar;
        ULayout.set({ sidebar: !sidebar });
    };

    return (
        <AppBar position='fixed' color='secondary' className={classes.appBar}>
            <Toolbar>
                <IconButton onClick={onToggle}>
                    <MenuIcon />
                </IconButton>
                <Typography variant='h6' className={classes.grow}>
                    Soundpad
                </Typography>
                <Volume />
                <IconButton onClick={onSidebar} className={classes.sidebarBtn}>
                    <GoRadioTower />
                </IconButton>
            </Toolbar>
        </AppBar>
    );
};

const useStyles = makeStyles((theme) => ({
    appBar: {
        top: 'auto',
        bottom: 0,
        right: 300,
        left: 0,
        width: 'auto',
        minWidth: 360,
    },
    grow: {
        flexGrow: 1,
    },
    sidebarBtn: {
        display: 'none',
    },
    [theme.breakpoints.down('sm')]: {
        appBar: {
            right: 0,
        },
        sidebarBtn: {
            display: 'flex',
        },
    },
}));

export default Header;
