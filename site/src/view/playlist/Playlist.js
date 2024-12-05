import React from 'react';
import { useSelector } from 'react-redux';

import AddIcon from '@material-ui/icons/Add';
import Fab from '@material-ui/core/Fab';

import Add from './Add';
import PlaylistList from './List';

const Playlist = () => {
    const [addOpen, setAddOpen] = React.useState(false);

    const playlist = useSelector((state) => state.playlist);

    return (
        <React.Fragment>
            <PlaylistList items={playlist.items} />
            <Fab color='primary' onClick={() => setAddOpen(true)} style={style.addBtn}>
                <AddIcon />
            </Fab>
            <Add open={addOpen} onClose={() => setAddOpen(false)} />
        </React.Fragment>
    );
};

const style = {
    addBtn: {
        position: 'absolute',
        top: 8,
        right: 8,
    },
};

export default Playlist;
