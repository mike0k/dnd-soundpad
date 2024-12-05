const data = {
    google: {
        apiKey: 'AIzaSyBKvNK1LjjjmRnUE2hcwX5ki4heKWYhjag',
    },
    url: {
        domain: 'https://life.animitemedia.com/',
        api: 'https://life.animitemedia.com/api/',
        site: 'https://life.animitemedia.com/',
        img: 'https://life.animitemedia.com/img/',
    },
};

if (process.env.NODE_ENV === 'development') {
    data.google.apiKey = 'AIzaSyCJzzCI2jXQrIENxgv2PEXErdbJIlxdzHY';

    data.url.domain = 'localhost';
    data.url.api = 'http://localhost/life/api/';
    data.url.site = 'http://localhost:8001/';
    data.url.img = 'http://localhost:8001/img/';
}

export const google = data.google;
export const url = data.url;
const config = data;
export default config;
