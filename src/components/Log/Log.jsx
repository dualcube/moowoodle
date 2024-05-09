import { useEffect, useState } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";

const Log = (props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        axios({
            method: "post",
            url: getApiLink('fetch-log'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                logcount: 100
            },
        }).then((response) => {
            // const data = JSON.parse(response.data);
            setData(response.data);
        });
    }, [] );

    const handleDownloadLog = (event) => {
        event.preventDefault();
        axios({
            url: appLocalizer.log_url,
            method: 'GET',
            responseType: 'blob',
        }).then(response => {

            // Create a blob from the response
            const blob = new Blob([response.data], { type: response.headers['content-type'] });
            
            // Create a URL for the blob
            const url = window.URL.createObjectURL(blob);
            
            // Create a link element
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'error.txt'); // Set the file name
            
            // Trigger the download
            document.body.appendChild(link);
            link.click();
            
            // Clean up
            document.body.removeChild(link);
        }).catch(error => {
            console.error( 'Error downloading file:', error );
        });
    }

    const handleClearLog = (event) => {
        event.preventDefault();

        console.log('handle clear log');axios({
            method: "post",
            url: getApiLink('fetch-log'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
            data: {
                logcount: 100,
                clear: true,
            },
        }).then((response) => {
            // const data = JSON.parse(response.data);
            setData(response.data);
        });
    }

    return (
        <div>
            <h2>LOG</h2>
            <div>
                <button
                    className=""
                    onClick={handleDownloadLog}
                >Download</button>

                <button
                    className=""
                    onClick={handleClearLog}
                >Clear All</button>
            </div>
            <div>
                <p>
                    {
                        data.map((log) => {
                            return <p>{log}</p>;
                        })
                    }
                </p>
            </div>
        </div>
    );
}

export default Log;