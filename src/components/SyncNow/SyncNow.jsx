import React, { useEffect, useRef, useState } from "react";
import axios from "axios";
import Dialog from "@mui/material/Dialog";
import Popoup from "../PopupContent/PopupContent";
import { getApiLink } from "../../services/apiService";

const SyncNow = (props) => {
    const [modelOpen, setModelOpen] = useState(false);
    const [syncCourseStart, setSyncCourseStart] = useState(false);
    const [syncUserStart, setSyncUserStart] = useState(false);
    const [syncStatus, setSyncStatus] = useState([]);
    const syncStart = useRef(false);

    const fetchSyncStatus = () => {
        axios({
            method: "post",
            url: getApiLink('sync-status'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
        }).then((response) => {
            if ( syncStart.current ) {
                setSyncStatus(response.data);
                setTimeout(() => {
                    fetchSyncStatus();
                }, 0)
            }
        });
    }

    useEffect(() => {
        if ( syncStart ) {
            fetchSyncStatus();
        }
    }, [ syncStart.current ] );

    const handleUserSync = (event) => {
        if ( ! appLocalizer.pro_active ) {
            return setModelOpen(true);
        }
    }

    const handleCourseSync = (event) => {
        if (syncCourseStart) {
            return;
        }

        syncStart.current = true;
        setSyncCourseStart(true);

        axios({
            method: "post",
            url: getApiLink('sync-course'),
            headers: { "X-WP-Nonce": appLocalizer.nonce },
        }).then((response) => {
            setSyncStatus(response.data);
            setSyncCourseStart(false);
            syncStart.current = false;
        });
    }

    return (
        <>
            <Dialog
                className="admin-module-popup"
                open={modelOpen}
                onClose={() => setModelOpen(false) }
                aria-labelledby="form-dialog-title"
                >
                <span
                    className="admin-font font-cross"
                    onClick={() => setModelOpen(false) }
                ></span>
                <Popoup />
            </Dialog>
            <div>
                <button
                    onClick={handleUserSync}
                >
                    All User
                </button>
                <div className="">

                </div>
            </div>
            <div>
                <button
                    onClick={handleCourseSync}
                >
                    All Course
                </button>
                <div className="">

                </div>
            </div>
            {
                syncStatus.length && 
                <div>
                    {
                        syncStatus.map((status) => {
                            {console.log(status)}
                            return (
                                <>
                                    <h4>{status.action}</h4>
                                    <p> { status.current / status.total * 100 }</p>
                                </>
                            );
                        })
                    }
                </div>
            }
        </>
    )
}

export default SyncNow;