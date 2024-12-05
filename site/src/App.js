import React from 'react';
import { MemoryRouter as Router, Route, Switch } from 'react-router-dom';
//import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';

import Layout from 'view/layout/Layout';
import * as UPlaylist from 'util/Playlist';
import * as URoom from 'util/Room';

const VDash = React.lazy(() => import('view/dash'));
const VMedia = React.lazy(() => import('view/media/Media'));
const VPad = React.lazy(() => import('view/pad/Pad'));
const VPlaylist = React.lazy(() => import('view/playlist/Playlist'));

const App = () => {
    const [init, setInit] = React.useState(true);

    if (init) {
        setInit(false);
        UPlaylist.clean();
        URoom.loadRoom();
    }

    return (
        <Router>
            <Layout>
                <Switch>
                    <Route path={'/media'} render={(props) => <VMedia {...props} />} />
                    <Route path={'/playlist'} render={(props) => <VPlaylist {...props} />} />
                    <Route path={'/test'} render={(props) => <VDash {...props} />} />
                    <Route render={(props) => <VPad {...props} />} />
                </Switch>
            </Layout>
        </Router>
    );
};

export default App;
