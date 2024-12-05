import React from 'react';
import SortableTree from 'react-sortable-tree';
import { useSelector } from 'react-redux';

import { makeStyles } from '@material-ui/core/styles';
import Drawer from '@material-ui/core/Drawer';

import * as UPlaylist from 'util/Playlist';
import * as UTool from 'util/Tool';

import 'react-sortable-tree/style.css';

const GroupSort = ({ open, setOpen }) => {
    const classes = useStyles();
    const groups = useSelector((state) => state.playlist.groups);
    const [tree, setTree] = React.useState([]);
    const playlist = UPlaylist.get();

    React.useEffect(() => {
        if (open !== false) {
            const items = [];
            const itemsObj = {};
            const playlist = UPlaylist.get();
            UTool.map(playlist.groups, (item) => {
                itemsObj[item.id] = { title: item.name, id: item.id, type: 'group', children: [] };
            });
            UTool.map(playlist.items, (item) => {
                itemsObj[item.group].children.push({
                    title: item.name,
                    id: item.id,
                    type: 'track',
                });
            });
            UTool.map(itemsObj, (item) => {
                items.push(item);
            });
            setTree(items);
        }
    }, [open, groups]);

    const onSort = () => {
        setOpen(false);
        UTool.map(tree, (groupData, groupKey) => {
            if (typeof playlist.groups[groupData.id] !== 'undefined') {
                playlist.groups[groupData.id].sort = groupKey;
                const group = playlist.groups[groupData.id];
                UTool.map(groupData.children, (trackData, trackKey) => {
                    if (typeof playlist.items[trackData.id] !== 'undefined') {
                        playlist.items[trackData.id].group = group.id;
                        playlist.items[trackData.id].sort = trackKey;
                    }
                });
            }
        });
        UPlaylist.update({ items: playlist.items, groups: playlist.groups });
    };

    const canDrop = ({ node, nextParent }) => {
        if (node.type === 'track') {
            if (nextParent === null) {
                return false;
            }
        } else if (node.type === 'group') {
            if (nextParent !== null) {
                return false;
            }
        }

        return true;
    };

    return (
        <Drawer open={open} onClose={onSort} className={classes.drawer}>
            <div className={classes.sort}>
                {open && (
                    <SortableTree
                        treeData={tree}
                        onChange={(data) => setTree([...data])}
                        maxDepth={2}
                        innerStyle={{ paddingRight: 100 }}
                        canDrop={canDrop}
                    />
                )}
            </div>
        </Drawer>
    );
};

const useStyles = makeStyles((theme) => ({
    drawer: {
        width: 250,
    },
    container: {
        width: '100%',
        height: '100%',
        padding: theme.spacing(2) + 'px ' + theme.spacing(1) + 'px',
        minWidth: 300,
        margin: 0,
    },
    grow: {
        flexGrow: 1,
        width: '100%',
    },
    row: {
        width: '100%',
        display: 'flex',
        justifyContent: 'center',
    },
    title: {
        borderBottom: '1px dashed' + theme.palette.secondary.main,
        padding: theme.spacing(1),
    },

    sort: {
        height: '100%',
        width: '100%',
        minWidth: 300,
        position: 'relative',
        '& .rst__rowContents': {
            backgroundColor: theme.palette.secondary.main,
            minWidth: 0,
        },
        '& .rst__moveHandle': {
            backgroundColor: theme.palette.primary.dark,
        },
        '& .rst__nodeContent': {
            //width: 'calc(100% - 44px)',
            width: '100%',
        },
        '& .rst__rowTitle': {
            fontSize: theme.typography.pxToRem(10),
        },
        '& .rst__node div:nth-child(3)': {
            '& .rst__moveHandle': {
                backgroundColor: theme.palette.primary.main,
            },
        },
    },
}));

export default GroupSort;
