import React from 'react';

export const map = (obj, callback) => {
    if (typeof obj === 'object') {
        if (Array.isArray(obj)) {
            let i = -1;
            return obj.map((item, key) => {
                i++;
                return callback(item, key, i);
            });
        } else {
            return Object.keys(obj).map((key, i) => {
                const item = obj[key];
                return callback(item, key, i);
            });
        }
    }
    return <React.Fragment />;
};

export const random = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
};

export const randomId = (min, max) => {
    return '_' + Math.random().toString(36).substr(2, 9);
};

export const shuffleArray = (arr) => {
    let i, j, temp;
    for (i = arr.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }
    return arr;
};

export const themeColors = () => {
    let rootStyle = getComputedStyle(document.body);
    return {
        themeColor1: rootStyle.getPropertyValue('--theme-color-1').trim(),
        themeColor2: rootStyle.getPropertyValue('--theme-color-2').trim(),
        themeColor3: rootStyle.getPropertyValue('--theme-color-3').trim(),
        themeColor4: rootStyle.getPropertyValue('--theme-color-4').trim(),
        themeColor5: rootStyle.getPropertyValue('--theme-color-5').trim(),
        themeColor6: rootStyle.getPropertyValue('--theme-color-6').trim(),
        themeColor1_10: rootStyle.getPropertyValue('--theme-color-1-10').trim(),
        themeColor2_10: rootStyle.getPropertyValue('--theme-color-2-10').trim(),
        themeColor3_10: rootStyle.getPropertyValue('--theme-color-3-10').trim(),
        themeColor4_10: rootStyle.getPropertyValue('--theme-color-3-10').trim(),
        themeColor5_10: rootStyle.getPropertyValue('--theme-color-3-10').trim(),
        themeColor6_10: rootStyle.getPropertyValue('--theme-color-3-10').trim(),
        primaryColor: rootStyle.getPropertyValue('--primary-color').trim(),
        foregroundColor: rootStyle.getPropertyValue('--foreground-color').trim(),
        separatorColor: rootStyle.getPropertyValue('--separator-color').trim(),
    };
};

export const toDuration = (sec_num) => {
    sec_num = parseInt(sec_num);
    let hours = Math.floor(sec_num / 3600);
    let minutes = Math.floor((sec_num - hours * 3600) / 60);
    let seconds = sec_num - hours * 3600 - minutes * 60;
    let str = '';

    if (hours > 0) {
        if (hours < 10) {
            hours = '0' + hours;
        }
        str += hours + ':';
    }
    if (minutes < 10) {
        minutes = '0' + minutes;
    }
    str += minutes + ':';
    if (seconds < 10) {
        seconds = '0' + seconds;
    }
    str += seconds;

    return str;
};
