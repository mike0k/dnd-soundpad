import React from 'react';

import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import ListItemText from '@material-ui/core/ListItemText';
import TextField from '@material-ui/core/TextField';

import * as UTool from 'util/Tool';

const ListDocuments = ({ docs = [], onSearch, GUser }) => {
    const [search, setSearch] = React.useState();

    const onChange = (e) => {
        setSearch(e.target.value);
        onSearch(e.target.value);
    };

    return (
        <React.Fragment>
            <TextField
                autoFocus
                margin='dense'
                label='Search'
                type='text'
                fullWidth
                defaultValue={search}
                onChange={onChange}
            />
            <List>
                {UTool.map(docs, (doc) => (
                    <ListItem button key={doc.id}>
                        <ListItemText primary={doc.name} />
                    </ListItem>
                ))}
            </List>
        </React.Fragment>
    );
};

export default ListDocuments;
