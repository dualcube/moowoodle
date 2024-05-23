/* global appLocalizer */
import React, { Component } from 'react';
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import './popupContent.scss';

const Propopup = () => {
    return (
        <>
            <DialogContent>
                <DialogContentText>
                    <div className="woo-module-dialog-content">
                        <div className="woo-image-overlay">
                            <div className="woo-overlay-content">
                                <h1 className="banner-header">Unlock <span className="banner-pro-tag">Pro</span> </h1>
                                <div className="woo-banner-content">
                                    <strong>Boost to MooWoodle Pro to access premium features!</strong>
                                    <p>&nbsp;</p>
                                    <p>1. Convenient Single Sign-On for Moodle™ and WordPress Login.</p>
                                    <p>2. Create steady income through course subscriptions.</p>
                                    <p>3. Increase earnings by offering courses in groups, variations, or individually.</p>
                                    <p>4. Select and sync courses with flexibility.</p>
                                    <p>5. Easily synchronize courses in bulk.</p>
                                    <p>6. Seamless, One-Password Access to Moodle™ and WordPress.</p>
                                    <p>7. Choose which user information to synchronize.</p>
                                    <p>8. Automatic User Synchronization for Moodle™ and WordPress.</p>
                                </div>
                                <a className="woo-go-pro-btn" target="_blank" href={appLocalizer.shop_url}>Upgrade to Pro</a>
                            </div>
                        </div>
                    </div>
                </DialogContentText>
            </DialogContent>
        </>
    );
}

export default Propopup;