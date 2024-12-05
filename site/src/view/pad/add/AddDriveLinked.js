import React from 'react';
import { gapi } from 'gapi-script';
import { makeStyles } from '@material-ui/core/styles';
import { Scrollbars } from 'react-custom-scrollbars';
import { useSelector } from 'react-redux';

import Button from '@material-ui/core/Button';
import CircularProgress from '@material-ui/core/CircularProgress';
import Grid from '@material-ui/core/Grid';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import TextField from '@material-ui/core/TextField';
import Typography from '@material-ui/core/Typography';

import * as UTool from 'util/Tool';

const CLIENT_ID = process.env.REACT_APP_GOOGLE_DRIVE_CLIENT_ID;
const API_KEY = process.env.REACT_APP_GOOGLE_DRIVE_API_KEY;
const DISCOVERY_DOCS = ['https://www.googleapis.com/discovery/v1/apis/drive/v3/rest'];
const SCOPES = 'https://www.googleapis.com/auth/drive.metadata.readonly';

const AddDrive = ({ onAdd }) => {
    const classes = useStyles();
    const media = useSelector((state) => state.media.items);
    const [docs, setDocs] = React.useState([]);
    const [GUser, setGUser] = React.useState();
    const [init, setInit] = React.useState(true);
    const [loading, setLoading] = React.useState(false);
    const [search, setSearch] = React.useState(false);

    const mediaUrls = [];
    UTool.map(media, (item) => {
        mediaUrls.push(item.url);
    });

    const listFiles = (searchTerm = '') => {
        if (!loading) {
            let query = 'mimeType contains "audio/"';
            if (searchTerm !== '' && searchTerm.length >= 3) {
                query += ' and name contains "' + searchTerm + '"';
            } else {
                searchTerm = '';
            }
            if (search === false || search !== searchTerm) {
                setLoading(true);
                setSearch(searchTerm);
                gapi.client.drive.files
                    .list({
                        pageSize: 1000,
                        fields: 'nextPageToken, files(id, name, mimeType, modifiedTime)',
                        q: query,
                    })
                    .then(function (response) {
                        const res = JSON.parse(response.body);
                        setDocs(res.files);
                        setLoading(false);
                    });
            }
        }
    };

    const updateSigninStatus = (isSignedIn) => {
        if (isSignedIn) {
            // Set the signed in user
            setGUser(gapi.auth2.getAuthInstance().currentUser.je.Qt);
            // list files if user is authenticated
            listFiles();
        } else {
            // prompt user to sign in
            gapi.auth2.getAuthInstance().signIn();
        }
    };

    const initClient = () => {
        gapi.client
            .init({
                apiKey: API_KEY,
                clientId: CLIENT_ID,
                discoveryDocs: DISCOVERY_DOCS,
                scope: SCOPES,
            })
            .then(() => {
                // Listen for sign-in state changes.
                gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

                // Handle the initial sign-in state.
                updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
            });
    };

    const onSignout = () => {
        gapi.auth2.getAuthInstance().signOut();
        setDocs([]);
        setGUser(null);
    };

    const formatUrl = (id) => 'https://docs.google.com/uc?export=download&id=' + id;

    const onAddItem = (doc) => {
        const url = formatUrl(doc.id);
        if (!mediaUrls.includes(url)) {
            onAdd({
                name: doc.name,
                type: 'drive',
                url,
            });
        }
    };

    if (init) {
        setInit(false);
        gapi.load('client:auth2', initClient);
    }

    return (
        <Grid container direction='column' className={classes.container} spacing={2}>
            <Grid item>
                <TextField
                    autoFocus
                    margin='dense'
                    label='Search'
                    type='text'
                    variant='outlined'
                    fullWidth
                    onChange={(e) => listFiles(e.target.value)}
                />
            </Grid>

            {loading && (
                <Grid item className={classes.loading}>
                    <CircularProgress />
                </Grid>
            )}
            {!loading && (
                <Grid item className={classes.grow}>
                    <Scrollbars>
                        <List>
                            {UTool.map(docs, (doc) => {
                                const url = formatUrl(doc.id);
                                return (
                                    <ListItem
                                        button
                                        key={doc.id}
                                        onClick={() => onAddItem(doc)}
                                        selected={mediaUrls.includes(url)}>
                                        <ListItemText primary={doc.name} />
                                    </ListItem>
                                );
                            })}
                            {docs.length === 0 && (
                                <ListItem>
                                    <ListItemText primary='No Audio files found' />
                                </ListItem>
                            )}
                        </List>
                    </Scrollbars>
                </Grid>
            )}

            <Grid item>
                <Typography variant='body2' className={classes.username}>
                    {GUser?.Ad}
                </Typography>
                <Button color='default' onClick={onSignout} fullWidth variant='outlined'>
                    Sign Out
                </Button>
            </Grid>
        </Grid>
    );
};

const useStyles = makeStyles((theme) => ({
    container: {
        height: '100%',
        width: '100%',
        paddingLeft: theme.spacing(2),
    },
    grow: {
        width: '100%',
        flexGrow: 1,
    },
    loading: {
        width: '100%',
        flexGrow: 1,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    username: {
        textAlign: 'center',
    },
}));

export default AddDrive;
