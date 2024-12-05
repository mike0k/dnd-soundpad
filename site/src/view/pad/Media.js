import React from 'react';
import { useSelector } from 'react-redux';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

import * as UPlaylist from 'util/Playlist';
import * as UTool from 'util/Tool';

const PadMedia = () => {
    const items = useSelector((state) => state.media.items);

    const onAdd = (id) => {
        UPlaylist.add(id);
    };

    return (
        <List>
            {UTool.map(items, (item) => {
                return (
                    <ListItem button key={item.id} onClick={() => onAdd(item.id)}>
                        <ListItemText primary={item.name} />
                    </ListItem>
                );
            })}
        </List>
    );
};

//const style = {};

export default PadMedia;
