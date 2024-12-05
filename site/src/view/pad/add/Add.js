import React from 'react';

import Card from '@material-ui/core/Card';
import IconButton from '@material-ui/core/IconButton';
import Drawer from '@material-ui/core/Drawer';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';

import { FaGoogleDrive, FaYoutube } from 'react-icons/fa';
import { BsMusicPlayerFill } from 'react-icons/bs';

import AddIcon from '@material-ui/icons/Add';

import * as UMedia from 'util/Media';
import * as UPlaylist from 'util/Playlist';
import * as UTool from 'util/Tool';
import AddDrive from './AddDrive';
import AddDriveLinked from './AddDriveLinked';
import AddMedia from './AddMedia';

const Add = () => {
    const [type, setType] = React.useState('');
    const [open, setOpen] = React.useState(false);

    const onClose = () => {
        setOpen(false);
        setTimeout(() => {
            setType('');
        }, 300);
    };

    const onAdd = (data) => {
        let media = {};
        if (typeof data.mediaId !== 'undefined') {
            media = UMedia.getItem(data.mediaId);
        } else {
            media = UMedia.add({
                name: data.name,
                type: data.type,
                url: data.url,
            });
        }

        if (typeof media !== 'undefined' && media !== false) {
            UPlaylist.add({
                name: media.name,
                url: media.url,
            });
        }
    };

    const types = [
        {
            id: 'media',
            icon: <BsMusicPlayerFill />,
            name: 'Your Archive',
        },
        // {
        //     id: 'drive',
        //     icon: <FaGoogleDrive />,
        //     name: 'Google Drive',
        // },
        {
            id: 'driveLinked',
            icon: <FaGoogleDrive />,
            name: 'Google Drive',
        },
        // {
        //     id: 'vimeo',
        //     icon: <FaVimeoV />,
        //     name: 'Vimeo',
        // },
        {
            id: 'youtube',
            icon: <FaYoutube />,
            name: 'YouTube',
        },
    ];

    return (
        <React.Fragment>
            <Card style={style.card}>
                <IconButton style={style.add} color='primary' onClick={() => setOpen(true)}>
                    <AddIcon />
                </IconButton>
            </Card>
            <Drawer open={open} onClose={onClose}>
                <div style={style.drawer}>
                    {type === '' && (
                        <List style={style.list}>
                            {UTool.map(types, (item) => (
                                <ListItem button key={item.id} onClick={() => setType(item.id)}>
                                    <ListItemIcon>{item.icon}</ListItemIcon>
                                    <ListItemText primary={item.name} />
                                </ListItem>
                            ))}
                        </List>
                    )}

                    {type === 'drive' && <AddDrive onAdd={onAdd} />}
                    {type === 'driveLinked' && <AddDriveLinked onAdd={onAdd} />}
                    {type === 'media' && <AddMedia onAdd={onAdd} />}
                </div>
            </Drawer>
        </React.Fragment>
    );
};

const style = {
    add: {
        width: '100%',
        height: '100%',
        borderRadius: 0,
        padding: 28,
    },
    card: {
        padding: 0,
    },
    drawer: {
        width: 600,
        height: '100%',
    },
    icon: {
        fontSize: '2rem',
    },
    list: {
        width: '100%',
    },
    ToggleButtonGroup: {
        alignItems: 'center',
        justifyContent: 'center',
    },
};

export default Add;
