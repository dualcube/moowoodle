/* global appLocalizer */
import React, { Component } from 'react';
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";
import './popupContent.scss';
import { __ } from "@wordpress/i18n";

const Modulepopup = (props) => {
    return (
        <>
            <DialogContent>
                <DialogContentText>
                <div className="admin-module-dialog-content">
                        <div className="admin-image-overlay">
                            <div className="admin-overlay-content">
                                <div className="admin-banner-content">
                                    {props.name && (
                                        <>
                                            <h2>
                                                {sprintf(__('To activate please enable the %s module first', 'catalogx'), props.name)}
                                            </h2>
                                            <a className="admin-go-pro-btn" href={appLocalizer.module_page_url}>{__("Enable Now", "catalogx")}</a>
                                        </>
                                    )}
                                </div>

                                {props.settings && (
                                    <>
                                    <h2>{__('Activate Sitewide Buy Mode', 'catalogx')}</h2>
                                    <p id="description">
                                        {__('The "Sitewide Buy Mode" is required to unlock purchase functionality across the site. Make sure it\'s activated to proceed.', 'catalogx')}
                                    </p>
                                    </>
                                )}

                                {props.plugin === 'notifima' && (
                                    <div>
                                        <h2>{ __('Download and install "Notifima"', 'catalogx') }</h2>
                                        <p id="description">
                                            { __('"Notifima" is a necessary product to enable notifications and other related settings. Download and install it to complete the setup.', 'catalogx') }
                                        </p>
                                        <a className="admin-go-pro-btn" target="_blank" href="https://wordpress.org/plugins/woocommerce-product-stock-alert/">{__("Download now", "catalogx")}</a>
                                    </div>
                                )}


                            </div>
                        </div>
                    </div>
                </DialogContentText>
            </DialogContent>
        </>
    );
}

export default Modulepopup;