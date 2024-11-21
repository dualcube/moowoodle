/* global appLocalizer */
import React, { Component } from 'react';
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import './popupContent.scss';

const PopupPluginDependency = () => {
    return (
        <>
            <DialogContent>
                <DialogContentText>
                    <div className="admin-module-dialog-content">
                        <div className="admin-image-overlay">
                            <div className="admin-overlay-content">
                                <h1 className="banner-header">Stock <span className="banner-pro-tag">Pro</span> </h1>
                                <div className="admin-banner-content">
                                    <h2>Activate 30+ Pro Modules</h2>
                                    <p id='description'>Lorem ipsum dolor sit amet consectetur adipisicing elit. Modi fugit quibusdam adipisci accusantium at aperiam minus eum laudantium tempora consequuntur.</p>
                                </div>
                                <a className="admin-go-pro-btn" target="_blank" href={appLocalizer.pro_url}>Upgrade to Pro</a>
                            </div>
                        </div>
                    </div>
                </DialogContentText>
            </DialogContent>
        </>
    );
}

export default PopupPluginDependency;