import React from 'react';
import { useSelector } from 'react-redux';
import * as R from 'ramda';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import FaceIcon from '@material-ui/icons/Face';

import * as ULayout from 'util/Layout';
import * as UTool from 'util/Tool';
import * as UUser from 'util/User';
import Access from './Access';

const User = () => {
    const users = useSelector((state) => state.room.users);
    const user = UUser.get();
    const [open, setOpen] = React.useState(false);

    const onClick = (id) => {
        if (user.id === id) {
            ULayout.set({ account: true });
        } else {
            setOpen(id);
        }
    };

    const sorted = () => {
        const items = [];
        UTool.map(users, (item) => {
            items.push(item);
        });

        return R.sortBy(R.prop('name'), items);
    };

    return (
        <React.Fragment>
            <List style={{ paddingTop: 0 }}>
                {UTool.map(sorted(), (item, id, i) => (
                    <ListItem
                        button
                        key={i}
                        dense
                        selected={user.id === item.id}
                        onClick={() => onClick(item.id)}>
                        <ListItemIcon>
                            <FaceIcon />
                        </ListItemIcon>
                        <ListItemText primary={item.name} />
                    </ListItem>
                ))}
            </List>
            <Access open={open} setOpen={setOpen} />
        </React.Fragment>
    );
};

export default User;
