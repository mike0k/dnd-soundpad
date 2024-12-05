import React from 'react';

import { MuiThemeProvider, createMuiTheme } from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import primary from '@material-ui/core/colors/deepOrange';
import secondary from '@material-ui/core/colors/grey';

const theme = createMuiTheme({
    palette: {
        //contrastThreshold: 2,
        type: 'dark',
        primary: {
            main: primary[600],
        },
        secondary: {
            main: secondary[800],
        },
    },
    typography: {
        fontFamily: ['Lato', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif'].join(','),
    },
});

const Theme = ({ children }) => {
    return (
        <MuiThemeProvider theme={theme}>
            <CssBaseline />
            {children}
        </MuiThemeProvider>
    );
};

export default Theme;
