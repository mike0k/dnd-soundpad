import React from 'react';

import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import TextField from '@material-ui/core/TextField';

import { FaPlus } from 'react-icons/fa';

const AddDrive = ({ onAdd }) => {
    const [name, setName] = React.useState('');
    const [url, setUrl] = React.useState('');

    const valid = url !== '' && name !== '';

    return (
        <Grid container spacing={2} direction='column' alignItems='center' style={style.container}>
            <Grid item style={style.row}>
                <TextField
                    autoFocus
                    label='URL'
                    defaultValue={url}
                    onChange={(e) => setUrl(e.target.value)}
                    fullWidth
                />
            </Grid>

            <Grid item style={style.row}>
                <TextField
                    label='Playlist Name'
                    defaultValue={name}
                    onChange={(e) => setName(e.target.value)}
                    fullWidth
                />
            </Grid>

            <Grid item style={style.row}>
                <Button
                    variant='contained'
                    color='primary'
                    onClick={onAdd}
                    fullWidth
                    disabled={valid}>
                    <FaPlus />
                </Button>
            </Grid>
        </Grid>
    );
};

const style = {
    container: {
        width: '100%',
        height: '100%',
    },
    row: {
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
};

export default AddDrive;
