import React from 'react';

import Container from '@material-ui/core/Container';
import { makeStyles } from '@material-ui/core/styles';

import Account from './Account';
import Menu from './Menu';
import Player from '../player/Player';
import Sidebar from './Sidebar';
import Theme from './Theme';

const Layout = ({ children }) => {
    const classes = useStyles();
    return (
        <Theme>
            <Player />
            <Container maxWidth={false} className={classes.container}>
                {children}
            </Container>
            <Sidebar />
            <Account />
            <Menu />
        </Theme>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        paddingLeft: 250 + theme.spacing(2),
        paddingRight: 300 + theme.spacing(2),
        minWidth: 360,
        height: '100vh',
    },
    [theme.breakpoints.down('sm')]: {
        container: {
            paddingRight: theme.spacing(2),
        },
    },
}));

export default Layout;
