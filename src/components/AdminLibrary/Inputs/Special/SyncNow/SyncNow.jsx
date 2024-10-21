import React, { useEffect, useState } from "react";
import axios from "axios";
import { getApiLink } from "../../../../../services/apiService";
import "./SyncNow.scss";

const SyncNow = (props) => {
  const { interval, proSetting, proSettingChanged, value, description, apilink, statusApiLink } = props;

  // it is true when sync start
  const [syncStarted, setSyncStarted] = useState(false);

  // state variable for store sync status
  const [syncStatus, setSyncStatus] = useState([]);

  // state variable for check button has clicked or not.
  const [buttonClicked, setButtonClicked] = useState(false);

  // fetch data in interval
  useEffect(() => {
    fetchSyncStatus();
  }, []);
  
  /**
   * Function for fetch sync status.
   */
  const fetchSyncStatus = (singleCall = false) => {
    axios({
      method: "post",
      url: getApiLink(statusApiLink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      const syncData = response.data;

      // Set loader based on response.
      setSyncStarted(syncData.running);

      // Set sync status from response.
      setSyncStatus(syncData.status);

      // Call recursively to fetch sync status.
      if (!singleCall) {
        setTimeout(() => {
          fetchSyncStatus();
        }, interval);
      }
    });
  }

  const handleSync = async (event) => {
    event.preventDefault();

    // Check it is a pro setting or not.
    if (proSettingChanged()) {
      return;
    }

    
    // Start loading
    setSyncStarted(true);
    
    // Set button click event to true.
    setButtonClicked(true);
    
    // Rest call for start sync.
    axios({
      method: "post",
      url: getApiLink(apilink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      if (response.data) {
        setSyncStarted(false);
      }

      // Fetch sync status for just one time
      fetchSyncStatus(true);
    });
  }

  // Render sync now setting
  return (
    <div className="section-synchronize-now">
      <div className="button-section">
        <button className="synchronize-now-button" onClick={handleSync}>
          {value}
        </button>
        {/* Render loader on sync start */}
        {syncStarted && (
          <div class="loader">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
          </div>
        )}
      </div>

      {syncStarted && <p className="sync-process">Synchronization started please wait.</p>}

      {/* Render description */}
      <p className="btn-description" dangerouslySetInnerHTML={{ __html: description }}></p>

      {/* Render pro tag */}
      {
        proSetting && <span className="admin-pro-tag">pro</span>
      }

      {/* Render sync status */}
      {
        syncStatus.length > 0 &&
        syncStatus.map((status) => {
          return (
            <div className="details-status-row sync-now">
              {status.action}
              <div className="status-meta">
                <span className="status-icons">
                  <i class="admin-font font-icon-yes"></i>
                </span>
                <span>{status.current} / {status.total}</span>
              </div>
              <span
                style={{
                  width: `${(status.current / status.total) * 100}%`,
                }}
                className="progress-bar"
              ></span>
            </div>
          );
        })
      }
    </div>
  );
};

export default SyncNow;
