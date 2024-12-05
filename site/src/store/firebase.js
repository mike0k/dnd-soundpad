import firebase from 'firebase/app';
import 'firebase/auth';
import 'firebase/database';

import * as UUser from 'util/User';

const config = {
    apiKey: 'AIzaSyBlZo4rF5WaAIeOkAVLj5rhP5bBPD92dIE',
    authDomain: 'soundpad-9a752.firebaseapp.com',
    databaseURL: 'https://soundpad-9a752.firebaseio.com/',
    projectId: 'soundpad-9a752',
    //storageBucket: "fun-food-friends-eeec7.appspot.com",
    //messagingSenderId: "144750278413"
};

firebase.initializeApp(config);

export default firebase;

export const db = (ref) => {
    return firebase.database().ref(ref);
};

export const GLogin = () => {
    var provider = new firebase.auth.GoogleAuthProvider();
    provider.addScope('https://www.googleapis.com/auth/drive.metadata.readonly');

    return firebase
        .auth()
        .signInWithPopup(provider)
        .then(function (result) {
            // This gives you a Google Access Token. You can use it to access the Google API.
            var token = result.credential.accessToken;
            // The signed-in user info.
            var user = result.user;
            UUser.set({
                id: user.uid,
                loggedIn: true,
                name: user.displayName,
                token,
            });
            // ...
        })
        .catch(function (error) {
            // Handle Errors here.
            var errorCode = error.code;
            var errorMessage = error.message;
            // The email of the user's account used.
            var email = error.email;
            // The firebase.auth.AuthCredential type that was used.
            //var credential = error.credential;

            console.log('fail', email, errorCode, errorMessage);
            // ...
        });
};

export const GLogout = () => {
    firebase
        .auth()
        .signOut()
        .then(function () {
            UUser.clear();
        })
        .catch(function (error) {
            // An error happened.
        });
};

firebase.auth().onAuthStateChanged(function (firebaseUser) {
    if (firebaseUser == null) {
        if (UUser.get().loggedIn) {
            UUser.clear();
        }
    } else {
        UUser.set({
            loggedIn: true,
        });
    }
});
