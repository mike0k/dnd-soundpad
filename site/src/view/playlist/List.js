import React from 'react';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

import * as UTool from 'util/Tool';

const PlaylistList = ({ items }) => {
    return (
        <List>
            {typeof items !== 'undefined' &&
                UTool.map(items, (item) => {
                    return (
                        <ListItem button key={item.id}>
                            <ListItemText primary={item.name} />
                        </ListItem>
                    );
                })}
        </List>
    );
};

//const style = {};

export default PlaylistList;
