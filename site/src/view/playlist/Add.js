import React from 'react';

import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';

import * as UPlaylist from 'util/Playlist';

const Add = ({ open, onClose }) => {
    const [name, setName] = React.useState();

    const onSave = async () => {
        const valid = await UPlaylist.add({ name });
        if (valid) {
            onClose();
            setName('');
        }
    };

    return (
        <Dialog open={open} onClose={onClose}>
            <DialogTitle>Create a playlist</DialogTitle>
            <DialogContent style={style.DialogContent}>
                <TextField
                    autoFocus
                    label='Playlist Name'
                    defaultValue={name}
                    onChange={(e) => setName(e.target.value)}
                    fullWidth
                />
            </DialogContent>
            <DialogActions>
                <Button onClick={onClose} color='primary'>
                    Cancel
                </Button>
                <Button onClick={onSave} color='primary'>
                    Save
                </Button>
            </DialogActions>
        </Dialog>
    );
};

const style = {
    DialogContent: {
        minWidth: 300,
    },
};

export default Add;
