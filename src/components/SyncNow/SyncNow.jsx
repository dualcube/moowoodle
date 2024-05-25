import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import Dialog from "@mui/material/Dialog";
import Popoup from "../PopupContent/PopupContent";
import { getApiLink } from "../../services/apiService";
import "./SyncNow.scss";

const SyncNow = (props) => {
  const { buttonKey, interval, proSetting, proSettingChanged, value, description, apilink, statusApiLink } = props;
 
  const [modelOpen, setModelOpen] = useState(false);
  const [syncStatus, setSyncStatus] = useState([]);
  const syncStart = useRef(true);
  const [handleClick, setHandleClick] = useState(false);

  console.log(syncStatus);

  const fetchSyncStatus = () => {
    axios({
      method: "post",
      url: getApiLink(statusApiLink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      // Sync data is not avialable or sync is over
      if ( ! response.data || ! response.data.length ) {
        setHandleClick(false);
        syncStart.current = false;
      }

      if (syncStart.current) {
        setSyncStatus(response.data);
        setTimeout(() => {
          setHandleClick(true);
          fetchSyncStatus();
        }, interval)
      }
    });
  }

  useEffect(() => {
    fetchSyncStatus();
  }, []);

  useEffect(() => {
    if (syncStart.current) {
      fetchSyncStatus();
    }
  }, [syncStart.current]);

  const handleSync = (event) => {
    event.preventDefault();

    if (proSettingChanged()) {
      setModelOpen(true);
      return;
    }

    // setHandleClick(true);
    // if (syncStart.current) {
    //   return;
    // }

    // syncStart.current = true;

    axios({
      method: "post",
      url: getApiLink(apilink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      if (response.data) {
        setSyncStatus(response.data);
        syncStart.current = false;
        setHandleClick(false);
      }
      fetchSyncStatus();
    });
  }

  return (
    <>
      <Dialog
        className="admin-module-popup"
        open={modelOpen}
        onClose={() => setModelOpen(false)}
        aria-labelledby="form-dialog-title"
      >
        <span
          className="admin-font font-cross"
          onClick={() => setModelOpen(false)}
        ></span>
        <Popoup />
      </Dialog>
      
      <div className="section-synchronize-now">
        <div className="button-section">
          <button className="synchronize-now-button" onClick={handleSync}>
            {value}
          </button>
          {handleClick && (
            <div class="loader">
              <div class="three-body__dot"></div>
              <div class="three-body__dot"></div>
              <div class="three-body__dot"></div>
            </div>
          )}
          
        </div>

        <p className="btn-description" dangerouslySetInnerHTML={{__html: description}}></p>
        {
            proSetting && <span className="admin-pro-tag">pro</span>
        }

        {syncStatus.length > 0 && (
          <>
            {syncStatus.map((status) => {
              return (
                <>
                  <div className="details-status-row">
                    {status.action}
                    <span className="status-icons">
                      <i class="admin-font font-icon-yes"></i>
                    </span>
                    <span
                      style={{
                        width: `${(status.current / status.total) * 100}%`,
                      }}
                      className="progress-bar"
                    ></span>
                  </div>
                </>
              );
            })}
          </>
        )}
        
      </div>
    </>
  );
};

export default SyncNow;
