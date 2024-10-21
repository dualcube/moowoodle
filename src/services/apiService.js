/**
 * Core API service module
 */

import axios from "axios";

/**
 * Get response from rest api.
 * @param {String} url 
 * @param {Object} headers 
 */
const getApiResponse = async ( url, headers ) => {
    try {
        const result = await axios.get( url, headers );
        return result.data;
    } catch ( error ) {
        console.error(`Error: fetching data on url ${ url } `);
        console.error(`ErrorObject: ${ error }`);
    }
};

/**
 * Send response to rest api.
 * @param {String} url 
 * @param {Object} data 
 * @param {Object} headers 
 */
const sendApiResponse = async ( url, data, headers = {} ) => {
    try {
        const result = await axios.post(url, data, { headers: { 'X-WP-Nonce': appLocalizer.nonce, ...headers } });
        return result.data;
    } catch ( error ) {
        console.error(`Error: sending data on url ${ url } `);
        console.error(`ErrorObject: ${ error }`);
    }
}

/**
 * Get the rest api url from endpoint.
 * @param {String} endpoint 
 * @param {String} namespace
 * @param {String} rootUrl 
 * @returns 
 */
const getApiLink = ( endpoint, namespace = null, rootUrl = null ) => {
    rootUrl = rootUrl || appLocalizer.apiUrl;
    namespace = namespace || 'moowoodle/v1';
    return `${rootUrl}/${namespace}/${endpoint}`;
}

export { getApiResponse, sendApiResponse, getApiLink };
