import React from 'react';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';

import * as UTool from 'util/Tool';
import * as UMedia from 'util/Media';

const AddMedia = ({ onAdd }) => {
    const media = UMedia.get().items;

    const onClick = (item) => {
        onAdd({ mediaId: item.id });
    };

    return (
        <List>
            {UTool.map(media, (item) => (
                <ListItem button key={item.id} onClick={() => onClick(item)}>
                    <ListItemIcon>{item.icon}</ListItemIcon>
                    <ListItemText primary={item.name} secondary={item.url} />
                </ListItem>
            ))}
        </List>
    );
};

// const style = {
//     row: {
//         width: '100%',
//         display: 'flex',
//         justifyContent: 'center',
//     },
// };

export default AddMedia;
