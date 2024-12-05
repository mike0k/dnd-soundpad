import React from 'react';
import { useSelector } from 'react-redux';

import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

import * as UUser from 'util/User';

const Login = () => {
    const user = useSelector((state) => ({
        loggedIn: state.user.loggedIn,
    }));
    const onLogin = () => {
        if (user.loggedIn) {
            UUser.logout();
        } else {
            UUser.login();
        }
    };

    return (
        <React.Fragment>
            <ListItem button onClick={onLogin}>
                <ListItemText primary={user.loggedIn ? 'Logout' : 'Login'} />
            </ListItem>
        </React.Fragment>
    );
};

export default Login;
