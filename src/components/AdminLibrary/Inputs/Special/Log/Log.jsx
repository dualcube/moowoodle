import { useEffect, useState } from "react";
import axios from "axios";
import { getApiLink } from "../../../../../services/apiService";
import "./Log.scss"; 
const Log = (props) => {
  const { fetchApiLink, downloadApiLink, downloadFileName } = props;
  const [logData, setLogData] = useState([]);
  const [copied, setCopied] = useState(false); 
  
  useEffect(() => {
    axios({
      method: "post",
      url: getApiLink(fetchApiLink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: {
        logcount: 100,
      },
    }).then((response) => {
      setLogData(response.data);
    });
  }, []); 

  const handleDownloadLog = (event) => {
    event.preventDefault();
    const fileName = downloadFileName;
    axios({
      url: getApiLink(downloadApiLink),
      method: "POST",
      headers: {
        'X-WP-Nonce': appLocalizer.nonce
      },
      data: {
        file: fileName
      },
      responseType: "blob",
    }).then((response) => {
        // Create a blob from the response
        const blob = new Blob([response.data], {
          type: response.headers["content-type"],
        });        
        // Create a URL for the blob
        const url = window.URL.createObjectURL(blob);
        // Create a link element
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", fileName); // Set the file name      
        // Trigger the download
        document.body.appendChild(link);
        link.click();        
        // Clean up
        document.body.removeChild(link);
      })
      .catch((error) => {
        console.error("Error downloading file:", error);
      });
  }; 
  
  const handleClearLog = (event) => {
    event.preventDefault(); 
    axios({
      method: "post",
      url: getApiLink(fetchApiLink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: {
        logcount: 100,
        clear: true,
      },
    }).then((response) => {
      setLogData([]);
    });
  }; 
  
  const handleCopyToClipboard = (event) => {
    event.preventDefault();
    const logText = logData.map((log) => {
      const regex = /^([^:]+:[^:]+:[^:]+):(.*)$/;
      const match = log.match(regex);
      if (match) {
        const dateSection = match[1].trim();
        const content = match[2].trim();
        return `${dateSection} : ${content}`;
      } else {
        return log;
      }
    }).join('\n'); 
    
    navigator.clipboard.writeText(logText)
      .then(() => {
        setCopied(true);
      })
      .catch((error) => {
        setCopied(false);
        console.error("Error copying logs to clipboard:", error);
      });
    setTimeout(() => {
      setCopied(false);
    }, 10000);

  }; 

  return (
    <div className="section-log-container">
      <div className="button-section">
        <button onClick={handleDownloadLog} class="btn-purple download-btn">
          Download
        </button>        
        <button className="btn-purple button-clear" onClick={handleClearLog}>
          <span class="text">Clear</span>
          <i class="adminLib-close"></i>
        </button>
      </div>
      <div className="log-container-wrapper">
        <div className="wrapper-header">
          <p className="log-viewer-text">{appLocalizer.tab_name} - log viewer</p>
          <div className="click-to-copy">
            <button class="copy-btn" onClick={handleCopyToClipboard}>
              <i class="adminLib-vendor-form-copy"></i>

              <span className={!copied ? ('tooltip tool-clip') : ('tooltip')}>
                {!copied ? ('') : (<i className="adminLib-success-notification"></i>)}
                {!copied ? ('Copy to clipboard') : ('Copied')}
              </span>

            </button>
          </div>
        </div>
        <div className="wrapper-body">
          {logData.map((log, index) => {
            // Using regular expression to split at the first colon
            const regex = /^([^:]+:[^:]+:[^:]+):(.*)$/;
            const match = log.match(regex); 
            if (match) {
              const dateSection = match[1].trim();
              const content = match[2].trim(); 
              return (
                <div className="log-row" key={index}>
                  {/* Render date section in a span */}
                  <span className="log-creation-date">{dateSection} :</span>
                  {/* Render content in another span */}
                  <span className="log-details">{content}</span>
                </div>
              );
            } else {
              // Handle if the log doesn't match the expected format
              return null;
            }
          })}
        </div>
      </div>
    </div>
  );
};
export default Log;
