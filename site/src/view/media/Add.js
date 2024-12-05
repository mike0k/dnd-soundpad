import React from 'react';

import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogTitle from '@material-ui/core/DialogTitle';
import ToggleButton from '@material-ui/lab/ToggleButton';
import ToggleButtonGroup from '@material-ui/lab/ToggleButtonGroup';

import { FaPlus, FaGoogleDrive, FaVimeoV, FaYoutube } from 'react-icons/fa';

import * as UMedia from 'util/Media';

const Add = ({ open, onClose }) => {
    const [name, setName] = React.useState();
    const [type, setType] = React.useState();
    const [url, setUrl] = React.useState();

    const onSave = async () => {
        const valid = await UMedia.add({ name, type, url });
        if (valid) {
            onClose();
            setName();
            setType();
            setUrl();
        }
    };

    return (
        <Dialog open={open} onClose={onClose} fullWidth>
            <DialogTitle>Add media</DialogTitle>
            <DialogContent style={style.DialogContent}>
                <Grid container spacing={2} direction='column' alignItems='center'>
                    <Grid item style={style.row}>
                        <ToggleButtonGroup
                            style={style.ToggleButtonGroup}
                            size='large'
                            value={type}
                            exclusive
                            onChange={(e, type) => setType(type)}>
                            <ToggleButton value='gDrive'>
                                <FaGoogleDrive style={style.icon} />
                            </ToggleButton>
                            <ToggleButton value='vimeo'>
                                <FaVimeoV style={style.icon} />
                            </ToggleButton>
                            <ToggleButton value='youtube'>
                                <FaYoutube style={style.icon} />
                            </ToggleButton>
                        </ToggleButtonGroup>
                    </Grid>

                    <Grid item style={style.row}>
                        <TextField
                            autoFocus
                            label='URL'
                            defaultValue={url}
                            onChange={(e) => setUrl(e.target.value)}
                            fullWidth
                            disabled={!type}
                        />
                    </Grid>

                    <Grid item style={style.row}>
                        <TextField
                            label='Playlist Name'
                            defaultValue={name}
                            onChange={(e) => setName(e.target.value)}
                            fullWidth
                            disabled={!(type && url)}
                        />
                    </Grid>

                    <Grid item style={style.row}>
                        <Button
                            variant='contained'
                            color='primary'
                            onClick={onSave}
                            fullWidth
                            disabled={!(type && url && name)}>
                            <FaPlus />
                        </Button>
                    </Grid>
                </Grid>
            </DialogContent>
            <DialogActions />
        </Dialog>
    );
};

const style = {
    DialogContent: {
        minWidth: 300,
    },
    icon: {
        fontSize: '2rem',
    },
    row: {
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
    ToggleButtonGroup: {
        alignItems: 'center',
        justifyContent: 'center',
    },
};

export default Add;
