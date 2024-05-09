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
};

export default Log;