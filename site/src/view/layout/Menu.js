import React from 'react';
import { Link } from 'react-router-dom';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import SwipeableDrawer from '@material-ui/core/SwipeableDrawer';

import Header from './Header';
import Login from './Login';

const Menu = () => {
    const [open, setOpen] = React.useState(false);

    return (
        <React.Fragment>
            <Header onToggle={() => setOpen(!open)} />
            <SwipeableDrawer
                open={open}
                onClose={() => setOpen(false)}
                onOpen={() => setOpen(true)}>
                <List style={style.list}>
                    <ListItem button component={Link} to={'/'}>
                        <ListItemText primary={'Dashboard'} />
                    </ListItem>
                    <ListItem button component={Link} to={'/playlist'}>
                        <ListItemText primary={'Manage Playlists'} />
                    </ListItem>
                    <ListItem button component={Link} to={'/media'}>
                        <ListItemText primary={'Manage Media'} />
                    </ListItem>

                    <Login />
                </List>
            </SwipeableDrawer>
        </React.Fragment>
    );
};

const style = {
    toggle: {
        position: 'fixed',
        bottom: 0,
        left: 0,
    },
    list: {
        width: 300,
    },
};

export default Menu;
