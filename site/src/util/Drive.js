import { gapi } from 'gapi-script';

export const init = () => {
    gapi.load('client:auth2', initClient);
};

const initClient = () => {
    gapi.client
        .init({
            apiKey: REACT_APP_GOOGLE_DRIVE_API_KEY,
            clientId: REACT_APP_GOOGLE_DRIVE_CLIENT_ID,
            discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/drive/v3/rest'],
            scope: 'https://www.googleapis.com/auth/drive.metadata.readonly',
        })
        .then(
            function () {
                // Listen for sign-in state changes.
                gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

                // Handle the initial sign-in state.
                updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
            },
            function (error) {}
        );
};

const updateSigninStatus = (isSignedIn) => {
    if (isSignedIn) {
        // Set the signed in user
        setSignedInUser(gapi.auth2.getAuthInstance().currentUser.je.Qt);
        // list files if user is authenticated
        listFiles();
    } else {
        // prompt user to sign in
        handleAuthClick();
    }
};

const listFiles = (searchTerm = null) => {
    setIsFetchingGoogleDriveFiles(true);
    gapi.client.drive.files
        .list({
            pageSize: 100,
            fields: 'nextPageToken, files(id, name, mimeType, modifiedTime)',
            q: searchTerm,
        })
        .then(function (response) {
            setIsFetchingGoogleDriveFiles(false);
            setListDocumentsVisibility(true);
            const res = JSON.parse(response.body);
            setDocuments(res.files);
        });
};

const handleAuthClick = (event) => {
    gapi.auth2.getAuthInstance().signIn();
};
