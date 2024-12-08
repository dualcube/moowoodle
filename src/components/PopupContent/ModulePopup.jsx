/* global appLocalizer */
import React, { Component } from 'react';
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import './popupContent.scss';

const Modulepopup = (props) => {
    return (
        <>
            <DialogContent>
                <DialogContentText>
                    <div className="admin-module-dialog-content">
                        <p>These settings are unavailable as the module is inactive. Please activate the module to enable them.</p>
                        <p>To active the module <a href={appLocalizer.module_page_url}>click here</a></p> 
                    </div>
                </DialogContentText>
            </DialogContent>
        </>
    );
}

export default Modulepopup;