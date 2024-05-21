import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import Dialog from "@mui/material/Dialog";
import Popoup from "../PopupContent/PopupContent";
import { getApiLink } from "../../services/apiService";
import "./SyncNow.scss";

const SyncNow = (props) => {
  const { buttonKey, proSetting, proSettingChanged, value, description, apilink } = props;
 
  const [modelOpen, setModelOpen] = useState(false);
  const [syncCourseStart, setSyncCourseStart] = useState(false);
  // const [syncUserStart, setSyncUserStart] = useState(false);
  const [syncStatus, setSyncStatus] = useState([]);
  const syncStart = useRef(false);
  const [handleClick, setHandleClick] = useState(false);

  const fetchSyncStatus = () => {
    axios({
      method: "post",
      url: getApiLink('sync-status'),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      if (syncStart.current) {
        setSyncStatus(response.data);
        setTimeout(() => {
          fetchSyncStatus();
        }, 0)
      }
    });
  }

  useEffect(() => {
    if (syncStart) {
      fetchSyncStatus();
    }
  }, [syncStart.current]);

  const handleUserSync = (event) => {
    if (!appLocalizer.pro_active) {
      return setModelOpen(true);
    }
  }

  const handleCourseSync = (event) => {
    event.preventDefault();
    setHandleClick(true);
    if (syncCourseStart) {
      return;
    }

    syncStart.current = true;
    setSyncCourseStart(true);

    axios({
      method: "post",
      url: getApiLink(apilink),
      headers: { "X-WP-Nonce": appLocalizer.nonce },
    }).then((response) => {
      setSyncStatus(response.data);
      setSyncCourseStart(false);
      syncStart.current = false;
      setHandleClick(false);
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
          <button className="synchronize-now-button" onClick={handleCourseSync}>
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

        <p className="btn-description">{description}</p>
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
