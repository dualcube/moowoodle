import { useEffect, useState } from "react";
import axios from "axios";
import { getApiLink } from "../../services/apiService";
import './Log.scss';

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
    <div className="section-log-container">
      <h2>LOG</h2>
      <div className="button-section">
        <button onClick={handleDownloadLog} class="download-btn">
          Download
        </button>

        <button className="button-clear" onClick={handleClearLog}>
          <span class="text">Clear</span>
          <span class="icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
            >
              <path d="M24 20.188l-8.315-8.209 8.2-8.282-3.697-3.697-8.212 8.318-8.31-8.203-3.666 3.666 8.321 8.24-8.206 8.313 3.666 3.666 8.237-8.318 8.285 8.203z"></path>
            </svg>
          </span>
        </button>
            </div>
      <div className="log-container-wrapper">
        <div>
            {
                data.map((log) => {
                    return <p>{log}</p>;
                })
            }
        </div>
      </div>
        <div className="click-to-copy">
        <button class="Btn">
          <span class="svgIcon">
            <svg fill="white" viewBox="0 0 384 512" height="1em" xmlns="http://www.w3.org/2000/svg"><path d="M280 64h40c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128C0 92.7 28.7 64 64 64h40 9.6C121 27.5 153.3 0 192 0s71 27.5 78.4 64H280zM64 112c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H320c8.8 0 16-7.2 16-16V128c0-8.8-7.2-16-16-16H304v24c0 13.3-10.7 24-24 24H192 104c-13.3 0-24-10.7-24-24V112H64zm128-8a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"></path></svg>
          </span>
        </button>
        </div>
    </div>
  );
};

export default Log;