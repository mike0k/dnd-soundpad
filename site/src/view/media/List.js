import React from 'react';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';

import * as UTool from 'util/Tool';

const MediaList = ({ items }) => {
    const onPlay = (id) => {
        //UPlayer.que(id);
    };
    return (
        <List>
            {typeof items !== 'undefined' &&
                UTool.map(items, (item) => (
                    <ListItem button key={item.id} onClick={() => onPlay(item.id)}>
                        <ListItemText primary={item.name} secondary={item.url} />
                    </ListItem>
                ))}
        </List>
    );
};

// const style = {
//     addBtn: {
//         position: 'absolute',
//         top: 8,
//         right: 8,
//     },
// };

export default MediaList;
